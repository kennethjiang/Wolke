{
	create: function (loadParams, moduleParams) {
		var store = new Scalr.data.Store({
			baseParams: loadParams,
			reader: new Scalr.data.JsonReader({
				id: 'id',
				fields: [ 'id','name','storage','idtcreated','avail_zone','engine','status','port','dtcreated' ]
			}),
			remoteSort: true,
			url: '/tools/aws/rds/snapshots/xListSnapshots/'
		});


		return new Scalr.Viewers.ListView({
			title: 'Tools &raquo; Amazon Web Services &raquo; RDS &raquo; DB snapshots',
			scalrOptions: {
				'reload': false,
				'maximize': 'all'
			},
			store: store,
			stateId: 'listview-tools-aws-rds-snapshots-view',
			enableFilter: false,

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
				}), {
					icon: '/images/add.png',
					cls: 'x-btn-icon',
					tooltip: 'Launch new DB instance',
					handler: function() {
						document.location.href = '/aws_rds_create_instance.php';
					}
				}
			],

			rowOptionsMenu: [{
				text: 'Restore DB instance from this snapshot', href: "/aws_rds_create_instance.php?snapshot={id}"
			}],

			withSelected: {
				menu: [{
					text: 'Delete',
					iconCls: 'scalr-menu-icon-delete',
					request: {
						confirmBox: {
							msg: 'Delete selected db snapshot(s)?',
							type: 'delete'
						},
						processBox: {
							msg: 'Deleting selected db snapshot(s). Please wait...',
							type: 'delete'
						},
						url: '/tools/aws/rds/snapshots/xDeleteSnapshots/',
						dataHandler: function (records) {
							var data = [];
							for (var i = 0, len = records.length; i < len; i++) {
								data[data.length] = records[i].get('id');
							}

							return { snapshots: Ext.encode(data), cloudLocation: store.baseParams.cloudLocation };
						},
						success: function (data) {
							if (Ext.isArray(data.successMessages)) {
								for (i = 0; i < data.successMessages.length; i++)
									Scalr.Message.Success(data.successMessages[i]);
							}

							if (Ext.isArray(data.errorMessages)) {
								for (i = 0; i < data.errorMessages.length; i++)
									Scalr.Message.Error(data.errorMessages[i]);
							}
						}
					}
				}]
			},

			listViewOptions: {
				emptyText: 'No db snapshots found',
				columns: [
					{ header: "Name", width: 70, dataIndex: 'name', sortable: false, hidden: 'no' },
					{ header: "Storage", width: 25, dataIndex: 'storage', sortable: false, hidden: 'no' },
					{ header: "Created at", width: 50, dataIndex: 'dtcreated', sortable: false, hidden: 'no' },
					{ header: "Instance created at", width: 50, dataIndex: 'idtcreated', sortable: false, hidden: 'no' },
					{ header: "Status", width: 50, dataIndex: 'status', sortable: false, hidden: 'no' },
					{ header: "Port", width: 50, dataIndex: 'port', sortable: false, hidden: 'no' },
					{ header: "Placement", width: 50, dataIndex: 'avail_zone', sortable: false, hidden: 'no' },
					{ header: "Engine", width: 50, dataIndex: 'engine', sortable: false, hidden: 'no' }
				]
			}
		});
	}
}
