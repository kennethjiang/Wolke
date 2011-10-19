{
	create: function (loadParams, moduleParams) {
		var store = new Scalr.data.Store({
			baseParams: moduleParams['params'],
			reader: new Scalr.data.JsonReader({
				id: 'id',
				fields: [ 'id','farmid','event','server_id','dtadded','message','farm_name' ]
			}),
			remoteSort: true,
			url: '/logs/xGetScriptingLogs/'
		});

		var list = new Scalr.Viewers.ListView({
			title: 'Logs &raquo; Scripting',
			scalrOptions: {
				'reload': false,
				'maximize': 'all'
			},
			scalrReconfigure: function (loadParams) {
				Ext.applyIf(loadParams, { farmId: '0' });
				Ext.apply(this.store.baseParams, loadParams);

				list.listView.setHiddenColumn('farm_name', this.store.baseParams['farmId'] != 0);
				this.getTopToolbar().getComponent('farmId').setValue(this.store.baseParams['farmId']);
				this.store.load();
			},
			store: store,
			stateId: 'listview-logs-scripting-view',

			tbar: [{
				text: 'Farm',
			}, {
				xtype: 'combo',
				store: Scalr.data.createStore(moduleParams['farms'], { idProperty: 'id', fields: [ 'id', 'name' ]}),
				editable: false,
				triggerAction: 'all',
				mode: 'local',
				value: '0',
				valueField: 'id',
				displayField: 'name',
				itemId: 'farmId',
				listeners: {
					select: function () {
						list.listView.setHiddenColumn('farm_name', this.getValue() != 0)
						store.baseParams['farmId'] = this.getValue();
						store.load();
					}
				}
			}],

			bbar: [ '->', new Scalr.Toolbar.TimeItem({ time: moduleParams['time'], timeOffset: moduleParams['timeOffset'] })],

			rowOptionsMenu: [
				{ itemId: "option.details", 		text: 'Show full message', 			  	href: "#/logs/scriptingmessage?eventId={id}" }
			],

			listViewOptions: {
				emptyText: "No logs found",
				getRowClass: function (data) {
					// TODO
					return (data.severity > 3) ? 'viewers-listview-row-red' : '';
				},
				columns: [
					{ header: "Time", width: 40, dataIndex: 'dtadded', sortable: false, hidden: 'no' },
					{ header: "Event", width: 35, dataIndex: 'event', sortable: false, align: 'center', hidden: 'no' },
					{ header: "Farm", width: 35, dataIndex: 'farm_name', sortable: false, hidden: 'no', tpl:
						'<a href="#/farms/{farmid}/view">{farm_name}</a>'
					},
					{ header: "Target", width: 35, dataIndex: 'server_id', sortable: false, hidden: 'no', tpl:
						'<a href="#/servers/{server_id}/view">{server_id}</a>'
					},
					{ header: "Message", width: 150, dataIndex: 'message', sortable: false, hidden: 'no', tpl:
						'<span ondblclick="Ext.get(this).parent(\'dl.viewers-listview-row\').toggleClass(\'viewers-listview-row-expand\'); return false;">{message} ...</span>'
					}
				]
			}
		});

		return list;
	}
}


/*
        	getRowClass: function (record, index) {
        		if (record.data.severity > 3) {
        			return 'ux-row-red';
        		}
*/
