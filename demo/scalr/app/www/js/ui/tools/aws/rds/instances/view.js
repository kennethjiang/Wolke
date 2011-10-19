Scalr.regPage('Scalr.ui.tools.aws.rds.instances.view', function (loadParams, moduleParams) {
	var store = new Scalr.data.Store({
		reader: new Scalr.data.JsonReader({
			id: 'name',
			fields: [
				"engine", "status", "hostname", "port", "name", "username", "type", "storage",
				"dtadded", "avail_zone"
			]
		}),
		remoteSort: true,
		url: '/tools/aws/rds/instances/xListInstances/'
	});

	return new Scalr.Viewers.ListView({
		title: 'Tools &raquo; Amazon Web Services &raquo; RDS &raquo; DB Instances',
		scalrOptions: {
			'reload': false,
			'maximize': 'all'
		},
		enableFilter: false,

		store: store,
		stateId: 'listview-tools-aws-rds-instances-view',

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
			text: 'Details',
			iconCls: 'scalr-menu-icon-info',
			menuHandler: function (item) {
				document.location.href = '#/tools/aws/rds/instances/' + item.currentRecordData.name + '/details?cloudLocation' + store.baseParams.cloudLocation;
			}
		}, {
			iconCls: 'scalr-menu-icon-edit',
			text: 'Modify',
			href: "/aws_rds_instance_modify.php?name={name}"
		}, {
			xtype: 'menuseparator'
		}, {
			text: 'Create snapshot',
			request: {
				processBox: {
					type: 'action'
				},
				url: '/tools/aws/rds/snapshots/xCreateSnapshot/',
				dataHandler: function (record) {
					return {
						dbinstance: record.get('name'),
						cloudLocation: store.baseParams.cloudLocation
					}
				},
				success: function () {
					document.location.href = '#/tools/aws/rds/snapshots?dbinstance=' + this.params.dbinstance;
				}
			}
		},


			{id: "option.autoSnap",			text: 'Auto snapshot settings', href: "/autosnapshots.php?name={name}"},
		{
			text: 'Manage snapshots',
			href: '#/tools/aws/rds/snapshots?dbinstance={name}'
		},
			new Ext.menu.Separator({id: "option.cwSep"}),
			{id: "option.cw",				text: 'CloudWatch monitoring',	href: "/aws_cw_monitor.php?ObjectId={name}&Object=DBInstanceIdentifier&NameSpace=AWS/RDS"},
			new Ext.menu.Separator({id: "option.snapsSep"}),
		{
			text: 'Events log',
			iconCls: 'scalr-menu-icon-logs',
			href: '/aws_rds_events_log.php?type=db-instance&name={name}'
		},
			new Ext.menu.Separator({id: "option.eventsSep"}),
			{
				text: 'Reboot',
				iconCls: 'scalr-menu-icon-reboot',
				request: {
					confirmBox: {
						msg: 'Reboot server "{name}"?',
						type: 'reboot'
					},
					processBox: {
						type: 'reboot',
						msg: 'Sending reboot command to the server. Please wait...'
					},
					url: '/tools/aws/rds/instances/xReboot/',
					dataHandler: function (record) {
						return {
							instanceId: record.get('name'),
							cloudLocation: store.baseParams.cloudLocation
						};
					},
					success: function(data) {
						store.reload();
					}
				}
			}, {
				text: 'Terminate',
				iconCls: 'scalr-menu-icon-terminate',
				request: {
					confirmBox: {
						msg: 'Terminate server "{name}"?',
						type: 'terminate'
					},
					processBox: {
						type: 'terminate',
						msg: 'Sending terminate command to the server. Please wait...'
					},
					url: '/tools/aws/rds/instances/xTerminate/',
					dataHandler: function (record) {
						return {
							instanceId: record.get('name'),
							cloudLocation: store.baseParams.cloudLocation
						};
					},
					success: function(data) {
						store.reload();
					}
				}
			}
		],

		listViewOptions: {
			emptyText: 'No db instances found',
			columns: [
				{ header: "Name", width: 50, dataIndex: 'name', sortable: false, hidden: 'no' },
				{ header: "Hostname", width: 110, dataIndex: 'hostname', sortable: false, hidden: 'no' },
				{ header: "Port", width: 30, dataIndex: 'port', sortable: false, hidden: 'no' },
				{ header: "Status", width: 30, dataIndex: 'status', sortable: false, hidden: 'no' },
				{ header: "Username", width: 30, dataIndex: 'username', sortable: false, hidden: 'no' },
				{ header: "Type", width: 30, dataIndex: 'type', sortable: true, hidden:false, hidden: 'no' },
				{ header: "Storage", width: 20, dataIndex: 'storage', sortable: false, hidden: 'no' },
				{ header: "Placement", width: 30, dataIndex: 'avail_zone', sortable: false, hidden: 'no' },
				{ header: "Created at", width: 30, dataIndex: 'dtadded', sortable: false, hidden: 'no' }
			]
		}
	});
});
