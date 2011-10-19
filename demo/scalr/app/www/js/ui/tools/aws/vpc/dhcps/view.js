{
	create: function (loadParams, moduleParams) {
		var store = new Scalr.data.Store({
			baseParams: loadParams,
			reader: new Scalr.data.JsonReader({
				id: 'id',
				fields: [ 'id', 'options' ]
			}),
			remoteSort: true,
			url: '/tools/aws/vpc/dhcps/xListViewDhcps/'
		});

		store.baseParams['cloudLocation'] = 'us-east-1';
		
		return new Scalr.Viewers.ListView({
			title: 'Tools &raquo; Amazon Web Services &raquo; VPC &raquo; DHCP options',
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
			stateId: 'listview-tools-aws-dhcps-view',

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
				tooltip: 'Add new DHCP',
				handler: function() {
					document.location.href = '#/tools/aws/vpc/dhcps/create';
				}
			}],

			/*
			// Row menu
			rowOptionsMenu: [
				{id: "option.config",       text: 'Configuration', 		href: "#/tools/aws/vpc/dhcps/{id}/config"}
			],

			getRowMenuVisibility: function (data) {
				return true;
			},
			*/
			
			withSelected: {
				menu: [{
					text: 'Delete',
					iconCls: 'scalr-menu-icon-delete',
					request: {
						confirmBox: {
							type: 'delete',
							msg: 'Remove selected subnet(s)?'
						},
						processBox: {
							type: 'delete',
							msg: 'Removing subnet(s). Please wait...'
						},
						url: '/tools/aws/vpc/dhcps/xRemove',
						dataHandler: function(records) {
							var dhcps = [];
							for (var i = 0, len = records.length; i < len; i++)
								dhcps[dhcps.length] = records[i].id;

							return { dhcps: Ext.encode(dhcps), cloudLocation: store.baseParams.cloudLocation };
						}
					}
				}]
			},

			listViewOptions: {
				emptyText: 'No DHCPs were found',
				columns: [
					{ header: "DHCP Options set ID", width: 10, dataIndex: 'id', sortable: false, hidden: 'no' },
					{ header: "Options", width: 50, dataIndex: 'options', sortable: false, hidden: 'no' }
				]
			}
		});
	}
}
