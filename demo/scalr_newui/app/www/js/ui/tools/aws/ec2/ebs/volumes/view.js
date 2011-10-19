{
	create: function (loadParams, moduleParams) {
		var store = new Scalr.data.Store({
			reader: new Scalr.data.JsonReader({
				id: 'volumeId',
				fields: [
					'farmId', 'farmRoleId', 'farmName', 'roleName', 'mysql_master_volume', 'mountStatus', 'serverIndex',
					'volumeId', 'size', 'snapshotId', 'availZone', 'status', 'attachmentStatus', 'device', 'instanceId', 'autoSnaps'
				]
			}),
			remoteSort: true,
			baseParams: loadParams,
			url: '/tools/aws/ec2/ebs/volumes/xListVolumes/'
		});
		
		return new Scalr.Viewers.ListView({
			title: 'Tools &raquo; Amazon Web Services &raquo; EC2 &raquo; EBS &raquo; Volumes',
			scalrOptions: {
				'reload': false,
				'maximize': 'all'
			},
			enableFilter: true,
			scalrReconfigure: function (loadParams) {
				
				//console.log(loadParams);
				
				Ext.applyIf(loadParams, { volumeId: ''});
				Ext.apply(this.store.baseParams, loadParams);
				this.store.load();
				//this.getTopToolbar().items.items[1].setValue(this.store.baseParams.cloudLocation);
			},
			store: store,
			stateId: 'listview-tools-aws-ec2-ebs-volumes-view',

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
							this.setValue(loadParams['cloudLocation'] || this.store.getAt(0).get('id'));
							store.baseParams.cloudLocation = loadParams['cloudLocation'] || this.store.getAt(0).get('id');
						}
					}
				}),
			'-',
			{
				icon: '/images/add.png', // icons can also be specified inline
				cls: 'x-btn-icon',
				tooltip: 'Create a new EBS volume',
				handler: function() {
					document.location.href = '#/tools/aws/ec2/ebs/volumes/create';
				}
			}],

			withSelected: {
     			menu: [{
					text: 'Delete',
					iconCls: 'scalr-menu-icon-delete',
					request: {
						confirmBox: {
							msg: 'Delete selected EBS volume(s)?',
							type: 'delete'
						},
						processBox: {
							msg: 'Deleting selected EBS volume(s). Please wait...',
							type: 'delete'
						},
						url: '/tools/aws/ec2/ebs/volumes/xDelete/',
						dataHandler: function (records) {
							var data = [];
							for (var i = 0, len = records.length; i < len; i++) {
								data[data.length] = records[i].get('volumeId');
							}

							return { volumeId: Ext.encode(data), cloudLocation: store.baseParams.cloudLocation };
						},
						success: function (data) {
							Scalr.Message.Success('Volume(s) successfully removed');
							store.reload();
						}
					}
				}]
     		},
			
			rowOptionsMenu: [
     			{itemId: "option.attach", text: 'Attach', menuHandler: function(menuItem) {
     				document.location.href = "#/tools/aws/ec2/ebs/volumes/"+menuItem.currentRecordData.volumeId+"/attach?cloudLocation="+store.baseParams.cloudLocation;
 				}},
 				{
					itemId: 'option.detach',
					text: 'Detach',
					request: {
						confirmBox: {
							type: 'action',
							//TODO: Add form: checkbox: forceDetach
							msg: 'Are you sure want to detach "{volumeId}" volume?'
						},
						processBox: {
							type: 'action',
							msg: 'Detaching EBS volume. Please wait...'
						},
						url: '/tools/aws/ec2/ebs/volumes/xDetach/',
						dataHandler: function (record) {
							return { volumeId: record.get('volumeId'), cloudLocation: store.baseParams.cloudLocation };
						},
						success: function (data) {
							Scalr.Message.Success('Volume successfully detached');
							store.reload();
						}
					}
				},
     			new Ext.menu.Separator({itemId: "option.attachSep"}),
     			{itemId: "option.autosnap", text:'Auto-snapshot settings', menuHandler: function(menuItem) {
     				document.location.href = '/autosnapshots.php?task=settings&volumeId=' + menuItem.currentRecordData.volumeId + '&region=' + store.baseParams.cloudLocation;
     			}},
     			new Ext.menu.Separator({itemId: "option.snapSep"}),
     			{
					itemId: 'option.createSnap',
					text: 'Create snapshot',
					request: {
						confirmBox: {
							type: 'action',
							msg: 'Are you sure want to create snapshot for EBS volume "{volumeId}"?'
						},
						processBox: {
							type: 'action',
							msg: 'Creating EBS snapshot. Please wait...'
						},
						url: '/tools/aws/ec2/ebs/snapshots/xCreate/',
						dataHandler: function (record) {
							return { volumeId: record.get('volumeId'), cloudLocation: store.baseParams.cloudLocation };
						},
						success: function (data) {
							Scalr.Message.Success('Snapshot successfully created');
							document.location.href = '#/tools/aws/ec2/ebs/snapshots/'+data.data.snapshotId+'/view?cloudLocation='+store.baseParams.cloudLocation;
						}
					}
				},
     			{itemId: "option.viewSnaps", text:'View snapshots', menuHandler: function(menuItem) {
     				document.location.href = '#/tools/aws/ec2/ebs/snapshots/view?volumeId=' + menuItem.currentRecordData.volumeId + '&cloudLocation=' + store.baseParams.cloudLocation;
     			}}, 
     			new Ext.menu.Separator({itemId: "option.vsnapSep"}),
     			{
					itemId: 'option.delete',
					text: 'Delete',
					iconCls: 'scalr-menu-icon-delete',
					request: {
						confirmBox: {
							type: 'delete',
							msg: 'Are you sure want to delete EBS volume "{volumeId}"?'
						},
						processBox: {
							type: 'delete',
							msg: 'Deleting EBS volume. Please wait...'
						},
						url: '/tools/aws/ec2/ebs/volumes/xDelete/',
						dataHandler: function (record) {
							return { volumeId: Ext.encode([record.get('volumeId')]), cloudLocation: store.baseParams.cloudLocation };
						},
						success: function () {
							Scalr.Message.Success('Volume successfully removed');
							store.reload();
						}
					}
				}
     		],

     		getRowOptionVisibility: function (item, record) {
     			if (item.itemId == 'option.attach' || item.itemId == 'option.detach' || item.itemId == 'option.attachSep')
     			{
     				if (!record.data.mysqMasterVolume)
     				{
     					if (item.itemId == 'option.attachSep')
     						return true;

     					if (item.itemId == 'option.detach' && record.data.instanceId)
     						return true;

     					if (item.itemId == 'option.attach' && !record.data.instanceId)
     						return true;
     				}

     				return false;
     			}
     			
     			return true;
     		},

     		listViewOptions: {
    			emptyText: "No volumes found",
    			columns: [
    				{ header: "Used by", width: 100, dataIndex: 'id', sortable: false, hidden: 'no', tpl:
    					'<tpl if="farmId">' +
    						'Farm: <a href="#/farms/{farmId}/view" title="Farm {farmName}">{farmName}</a>' +
    						'<tpl if="roleName">' +
    							'&nbsp;&rarr;&nbsp;<a href="#/farms/{farmId}/roles/{farmRoleId}/view" title="Role {roleName}">' +
    							'{roleName}</a> #{serverIndex}' +
    						'</tpl>' +
    						'<tpl if="!roleName && mysql_master_volume">&nbsp;&rarr;&nbsp;MySQL master volume</tpl>' +
    					'</tpl>' +
    					'<tpl if="!farmId"><img src="/images/false.gif" /></tpl>'
    				},
    				{ header: "Volume ID", width: '120px', dataIndex: 'volumeId', sortable: true, hidden: 'no' },
    				{ header: "Size (GB)", width: '80px', dataIndex: 'size', sortable: true, hidden: 'no' },
    				{ header: "Snapshot ID", width: 35, dataIndex: 'snapshotId', sortable: true, hidden: 'yes' },
    				{ header: "Placement", width: '100px', dataIndex: 'availZone', sortable: true, hidden: 'no' },
    				{ header: "Status", width: '250px', dataIndex: 'status', sortable: true, hidden: 'no', tpl:
    					'{status}' +
    					'<tpl if="attachmentStatus"> / {attachmentStatus}</tpl>' +
    					'<tpl if="device"> ({device})</tpl>'
    				},
    				{ header: "Mount status", width: 20, dataIndex: 'mountStatus', sortable: false, hidden: 'no', tpl:
    					'<tpl if="mountStatus">{mountStatus}</tpl>' +
    					'<tpl if="!mountStatus"><img src="/images/false.gif" /></tpl>'
    				},
    				{ header: "Instance ID", width: '110px', dataIndex: 'instanceId', sortable: true, hidden: 'no', tpl:
    					'<tpl if="instanceId">{instanceId}</tpl>'
    				},
    				{ header: "Auto-snaps", width: '110px', dataIndex: 'autoSnaps', sortable: false, align:'center', hidden: 'no', tpl:
    					'<tpl if="autoSnaps"><img src="/images/true.gif" /></tpl>' +
    					'<tpl if="!autoSnaps"><img src="/images/false.gif" /></tpl>'
    				}
    			]
    		}
		});
	}
}
