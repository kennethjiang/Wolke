{
	create: function (loadParams, moduleParams) {
		var store = new Scalr.data.Store({
			reader: new Scalr.data.JsonReader({
				id: 'ipaddress',
				fields: [
				         'ipaddress','instance_id', 'farm_id', 'farm_name', 'role_name', 'indb', 'farm_roleid', 'server_id', 'server_index'
				]
			}),
			remoteSort: true,
			url: '/tools/aws/ec2/eips/xListEips/'
		});

		store.baseParams['cloudLocation'] = 'us-east-1';
		
		return new Scalr.Viewers.ListView({
			title: 'Tools &raquo; Amazon Web Services &raquo; EC2 &raquo; Elastic IPs',
			scalrOptions: {
				'reload': false,
				'maximize': 'all'
			},
			enableFilter: false,

			store: store,
			stateId: 'listview-tools-aws-ec2-eips-view',

			tbar: [ 'Location:',
				new Ext.form.ComboBox({
					itemId: 'cloudLocation',
					editable: false,
					store: Scalr.data.createStore(moduleParams.locations, { idProperty: 'id', fields: [ 'id', 'name' ]}),
					typeAhead: false,
					displayField: 'name',
					valueField: 'id',
					value: '',
					mode: 'local',
					triggerAction: 'all',
					selectOnFocus: false,
					width: 150,
					listeners: {
						select: function(combo, record, index) {
							store.baseParams.cloudLocation = combo.getValue();
							store.load();
						},
						added: function() {
							this.setValue(this.store.getAt(0).get('id'));
							store.baseParams.cloudLocation = this.store.getAt(0).get('id');
						}
					}
				})
			],

			getRowMenuVisibility: function (data) {
				return !(data.server_id);
			},
			
			rowOptionsMenu: [
			    /*
     			{ itemId: "option.associate", text:'Associate', 
     				menuHandler: function (item) {
						document.location.href = "#/tools/aws/ec2/eips/{ipaddress}/associate?cloudLocation="+store.baseParams.cloudLocation;
					}
     			}, */{
					itemId: 'option.delete',
					text: 'Delete',
					iconCls: 'scalr-menu-icon-delete',
					request: {
						confirmBox: {
							type: 'delete',
							msg: 'Are you sure want to delete elastic ip "{ipaddress}"?'
						},
						processBox: {
							type: 'delete',
							msg: 'Deleting elastic IP address. Please wait...'
						},
						url: '/tools/aws/ec2/eips/xDelete/',
						dataHandler: function (record) {
							return { elasticIp: record.get('ipaddress'), cloudLocation: store.baseParams.cloudLocation };
						},
						success: function () {
							store.reload();
						}
					}
				}
     		],

     		listViewOptions: {
     			emptyText: "No elastic IPs found",
     			columns: [
     				{ header: "Used By", width: 10, dataIndex: 'farm_name', sortable: true, hidden: 'no', tpl:
     					'<tpl if="farm_id">Farm: <a href="#/farms/{values.farm_id}/view" title="Farm {values.farm_name}">{values.farm_name}</a>' +
     						'<tpl if="role_name">&nbsp;&rarr;&nbsp;<a href="#/farms/{values.farm_id}/roles/{values.farm_roleid}/view"' + 
     							'title="Role {values.role_name}">{values.role_name}</a> #{values.server_index}' + 
     						'</tpl>' +
     					'</tpl>' + 
     					'<tpl if="! farm_id"><img src="/images/false.gif" /></tpl>'
     				},
     				{ header: "IP address", width: '150px', dataIndex: 'ipaddress', sortable: false, hidden: 'no' },
     				{ header: "Auto-assigned", width: '150px', dataIndex: 'role_name', sortable: true, hidden: 'no', align:'center', tpl: 
     					'<tpl if="indb"><img src="images/true.gif"></tpl>' +
     					'<tpl if="!indb"><img src="images/false.gif"></tpl>'
     				},
     				{ header: "Server", width: '360px', dataIndex: 'server_id', sortable: true, hidden: 'no', tpl:
     					'<tpl if="server_id"><a href="#/servers/{values.server_id}/view">{values.server_id}</a></tpl>' +
     					'<tpl if="!server_id">{values.instance_id}</tpl>'
     				}
     			]
     		}
		});
	}
}
