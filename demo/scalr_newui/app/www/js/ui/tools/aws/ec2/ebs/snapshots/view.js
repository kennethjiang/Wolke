{
	create: function (loadParams, moduleParams) {
		var store = new Scalr.data.Store({
			reader: new Scalr.data.JsonReader({
				id: 'snapshotId',
				fields: [
				         'snapshotId', 'volumeId', 'volumeSize', 'status', 'startTime', 'comment', 'progress', 'owner','volumeSize'
				]
			}),
			remoteSort: true,
			baseParams: loadParams,
			url: '/tools/aws/ec2/ebs/snapshots/xListSnapshots/'
		});
		
		return new Scalr.Viewers.ListView({
			title: 'Tools &raquo; Amazon Web Services &raquo; EC2 &raquo; EBS &raquo; Snapshots',
			scalrOptions: {
				'reload': false,
				'maximize': 'all'
			},
			enableFilter: true,
			scalrReconfigure: function (loadParams) {
				Ext.applyIf(loadParams, { volumeId: '', snapshotId: ''});
				Ext.apply(this.store.baseParams, loadParams);
				this.store.load();
				//this.getTopToolbar().items.items[1].setValue(this.store.baseParams.cloudLocation);
			},
			store: store,
			stateId: 'listview-tools-aws-ec2-ebs-snaps-view',

			tbar: ['Location:',
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
				}), '&nbsp;&nbsp;',
				{
					xtype:'checkbox',
					boxLabel: 'Show public (Shared) snapshots',
					listeners: {
						check: function(item, checked) {
							store.baseParams.showPublicSnapshots = checked ? 1 : 0; 
							store.load();
						}
					}
				}
			],

			rowOptionsMenu: [
     			{itemId: "option.create", text:'Create new volume based on this snapshot', menuHandler: function(menuItem) {
     				document.location.href = '#/tools/aws/ec2/ebs/volumes/create?snapshotId=' + menuItem.currentRecordData.snapshotId + '&cloudLocation=' + store.baseParams.cloudLocation;
     			}},
     			new Ext.menu.Separator({itemId: "option.Sep"}),
     			{
					itemId: 'option.delete',
					text: 'Delete',
					iconCls: 'scalr-menu-icon-delete',
					request: {
						confirmBox: {
							type: 'delete',
							msg: 'Are you sure want to delete EBS snapshot "{snapshotId}"?'
						},
						processBox: {
							type: 'delete',
							msg: 'Deleting EBS snapshot. Please wait...'
						},
						url: '/tools/aws/ec2/ebs/snapshots/xDelete/',
						dataHandler: function (record) {
							return { snapshotId: Ext.encode([record.get('snapshotId')]), cloudLocation: store.baseParams.cloudLocation };
						},
						success: function () {
							Scalr.Message.Success('Snapshot successfully removed');
							store.reload();
						}
					}
				}
     		],

     		withSelected: {
     			menu: [{
					text: 'Delete',
					iconCls: 'scalr-menu-icon-delete',
					request: {
						confirmBox: {
							msg: 'Delete selected EBS snapshot(s)?',
							type: 'delete'
						},
						processBox: {
							msg: 'Deleting selected EBS snapshot(s). Please wait...',
							type: 'delete'
						},
						url: '/tools/aws/ec2/ebs/snapshots/xDelete/',
						dataHandler: function (records) {
							var data = [];
							for (var i = 0, len = records.length; i < len; i++) {
								data[data.length] = records[i].get('snapshotId');
							}

							return { snapshotId: Ext.encode(data), cloudLocation: store.baseParams.cloudLocation };
						},
						success: function (data) {
							Scalr.Message.Success('Snapshot(s) successfully removed');
							store.reload();
						}
					}
				}]
     		},

     		listViewOptions: {
     			emptyText: "No snapshots found",
     			columns: [
     				{ header: "Snapshot ID", width: '120px', dataIndex: 'snapshotId', sortable: true, hidden: 'no' },
     				{ header: "Owner", width: '150px', dataIndex: 'owner', sortable: true, hidden: 'no' },
     				{ header: "Created on", width: 35, dataIndex: 'volumeId', sortable: true, hidden: 'no' },
     				{ header: "Size (GB)", width: '100px', dataIndex: 'volumeSize', sortable: true, hidden: 'no' },
     				{ header: "Status", width: '120px', dataIndex: 'status', sortable: true, hidden: 'no' },
     				{ header: "Local start time", width: '160px', dataIndex: 'startTime', sortable: true, hidden: 'no' },
     				{ header: "Completed", width: '100px', dataIndex: 'progress', sortable: false, align:'center', hidden: 'no', tpl: '{progress}%' },
     				{ header: "Comment", width: 120, dataIndex: 'comment', sortable: true, hidden: 'no', tpl:
     					'<tpl if="comment">{comment}</tpl>'
     				}
     			]
     		}
		});
	}
}
