{
	create: function (loadParams, moduleParams) {
		var store = new Scalr.data.Store({
			baseParams: loadParams,
			reader: new Scalr.data.JsonReader({
				id: 'id',
				fields: [ 'id', 'vpcId', 'state', 'cidrBlock', 'availableIpAddressCount', 'availabilityZone' ]
			}),
			remoteSort: true,
			url: '/tools/aws/vpc/subnets/xListViewSubnets/'
		});

		store.baseParams['cloudLocation'] = 'us-east-1';
		
		return new Scalr.Viewers.ListView({
			title: 'Tools &raquo; Amazon Web Services &raquo; VPC &raquo; Subnets',
			scalrOptions: {
				'reload': false,
				'maximize': 'all'
			},
			scalrReconfigure: function (loadParams) {
				Ext.applyIf(loadParams, { vpcId: ''});
				Ext.apply(this.store.baseParams, loadParams);
				this.store.load();
			},
			enableFilter: false,
			store: store,
			stateId: 'listview-tools-aws-subnets-view',

			tbar: [{text: 'Location:'}, new Ext.form.ComboBox({
				allowBlank: false,
				editable: false, 
		        store: moduleParams['locations'],
		        value: store.baseParams['cloudLocation'],
		        displayField:'state',
		        typeAhead: false,
		        mode: 'local',
		        triggerAction: 'all',
		        selectOnFocus:false,
		        width:200,
		        listeners: { select:function(combo, record, index){
	        		store.baseParams.cloudLocation = combo.getValue(); 
	        		store.load();
	        	}}
		    	})
			],

			withSelected: {
				menu: [{
					text: 'Delete',
					iconCls: 'scalr-menu-icon-delete',
					request: {
						confirmBox: {
							type: 'delete',
							msg: 'Remove selected subnet(s)?
						},
						processBox: {
							type: 'delete',
							msg: 'Removing subnet(s). Please wait...'
						},
						url: '/tools/aws/vpc/subnets/xRemove',
						dataHandler: function(records) {
							var subnets = [];
							for (var i = 0, len = records.length; i < len; i++)
								subnets[subnets.length] = records[i].id;

							return { subnets: Ext.encode(subnets), cloudLocation: store.baseParams.cloudLocation };
						}
					}
				}]
			},

			listViewOptions: {
				emptyText: 'No VPC subnets were found',
				columns: [
					{ header: "Subnet ID", width: 60, dataIndex: 'id', sortable: false, hidden: 'no' },
					{ header: "VPC ID", width: 60, dataIndex: 'vpcId', sortable: false, hidden: 'no' },
					{ header: "CIDR", width: 60, dataIndex: 'cidrBlock', sortable: false, hidden: 'no' },
					{ header: "State", width: 60, dataIndex: 'state', sortable: false, hidden: 'no' },
					{ header: "Available IPs", width: 80, dataIndex: 'availableIpAddressCount', sortable: false, hidden: 'no' },
					{ header: "Available Zone", width: 80, dataIndex: 'availabilityZone', sortable: false, hidden: 'no' }
				]
			}
		});
	}
}
