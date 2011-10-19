{
	create: function (loadParams, moduleParams) {
		var store = new Scalr.data.Store({
			baseParams: loadParams,
			reader: new Scalr.data.JsonReader({
				id: 'id',
				fields: [ 'id', 'state', 'cidrBlock', 'dhcpOptionsId' ]
			}),
			remoteSort: true,
			url: '/tools/aws/vpc/xListViewVpc/'
		});

		store.baseParams['cloudLocation'] = 'us-east-1';
		
		return new Scalr.Viewers.ListView({
			title: 'Tools &raquo; Amazon Web Services &raquo; VPC &raquo; View',
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
			stateId: 'listview-tools-aws-vpc-view',

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
		    	}), '-', {
				icon: '/images/add.png',
				cls: 'x-btn-icon',
				tooltip: 'Add new VPC',
				handler: function() {
					document.location.href = '#/tools/aws/vpc/create';
				}
			}],

			// Row menu
			rowOptionsMenu: [
				{ itemId: "option.CreateSubnet",       text: 'Create a Subnet', 
					menuHandler: function (item) {
						document.location.href = "#/tools/aws/vpc/{id}/subnets/create?cloudLocation="+store.baseParams.cloudLocation;
					}
				},
				{ itemId: "option.attachVpnGateway",   text: 'Attach a VPN Gateway', href: "/aws_vpc_attach_vpn_gateway.php?id={id}"},
				{ itemId: "option.setDhcpOptions",     text: 'Set DHCP Options', 	
					menuHandler: function (item) {
						document.location.href = "#/tools/aws/vpc/{id}/dhcps/attach?cloudLocation="+store.baseParams.cloudLocation;
					}
				}
			],

			getRowMenuVisibility: function (data) {
				return true;
			},

			withSelected: {
				menu: [{
					text: 'Delete',
					iconCls: 'scalr-menu-icon-delete',
					request: {
						confirmBox: {
							type: 'delete',
							msg: 'Remove selected VPC(s)?'
						},
						processBox: {
							type: 'delete',
							msg: 'Removing VPC(s). Please wait...'
						},
						url: '/tools/aws/vpc/xRemove',
						dataHandler: function(records) {
							var vpcs = [];
							for (var i = 0, len = records.length; i < len; i++) {
								vpcs[vpcs.length] = records[i].id;
							}
							return { vpcs: Ext.encode(vpcs), cloudLocation: store.baseParams.cloudLocation };
						}
					}
				}]
			},

			listViewOptions: {
				emptyText: 'No VPC clouds were found',
				columns: [
					{ header: "VPC ID", width: 70, dataIndex: 'id', sortable: false, hidden: 'no' },
					{ header: "CIDR", width: 70, dataIndex: 'cidrBlock', sortable: false, hidden: 'no' },
					{ header: "State", width: 70, dataIndex: 'state', sortable: false, hidden: 'no' },
					{ header: "DHCP Options", width: 80, dataIndex: 'dhcpOptionsId', sortable: false, hidden: 'no' }
				]
			}
		});
	}
}
