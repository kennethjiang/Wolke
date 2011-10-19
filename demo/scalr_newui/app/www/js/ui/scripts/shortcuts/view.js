{
	create: function (loadParams, moduleParams) {
		var store = new Scalr.data.Store({
			baseParams: loadParams,
			reader: new Scalr.data.JsonReader({
				id: 'id',
				fields: [
					{name: 'id', type: 'int'},
					'farmid', 'farmname', 'farm_roleid', 'rolename', 'scriptname', 'event_name'
				]
			}),
			remoteSort: true,
			url: '/scripts/shortcuts/xListViewShortcuts/'
		});

		return new Scalr.Viewers.ListView({
			title: 'Scripts &raquo; Shortcuts &raquo; View',
			scalrOptions: {
				'reload': false,
				'maximize': 'all'
			},
			scalrReconfigure: function (loadParams) {
				Ext.applyIf(loadParams, { scriptId: '', eventName:'' });
				Ext.apply(this.store.baseParams, loadParams);
				this.store.load();
			},
			store: store,
			enableFilter: false,
			stateId: 'listview-scripts-shortcuts-view',

			rowOptionsMenu: [
     			{ itemId: "option.edit", text: 'Edit', href: "#/scripts/execute?eventName={event_name}&isShortcut=1"}
     		],

     		withSelected: {
     			menu: [{
					text: 'Delete',
					iconCls: 'scalr-menu-icon-delete',
					request: {
						confirmBox: {
							type: 'delete',
							msg: 'Delete selected shortcuts(s)?'
						},
						processBox: {
							type: 'delete',
							msg: 'Removing selected shortcut(s). Please wait...'
						},
						url: '/scripts/shortcuts/xRemove/',
						dataHandler: function(records) {
							var shortcuts = [];
							for (var i = 0, len = records.length; i < len; i++) {
								shortcuts[shortcuts.length] = records[i].get('id');
							}

							return { shortcuts: Ext.encode(shortcuts) };
						}
					}
				}],
     		},

     		listViewOptions: {
     			emptyText: "No shortcuts defined",
     			columns: [
     				{ header: "Target", width: 150, dataIndex: 'id', sortable: false, hidden: 'no', tpl:
     					'<a href="#/farms/{farmid}/view">{farmname}</a>' +
     					'<tpl if="farm_roleid &gt; 0">&rarr;<a href="#/farms/{farmid}/roles/{farm_roleid}/view">{rolename}</a></tpl>' +
     					'&nbsp;&nbsp;&nbsp;'
     				},
     				{ header: "Script", width: 500, dataIndex: 'scriptname', sortable: true, hidden: 'no' }
     			]
     		}
		});
	}
}
