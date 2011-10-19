{
	create: function (loadParams, moduleParams) {
		var store = new Scalr.data.Store({
			baseParams: moduleParams['params'],
			reader: new Scalr.data.JsonReader({
				id: 'id',
				timeProperty: 'time',
				fields: [ 'id','serverid','message','severity','time','source','farmid','servername','farm_name', 's_severity' ]
			}),
			remoteSort: true,
			url: '/logs/xGetLogs/'
		});

		var filterSeverity = function (combo, checked) {
			store.baseParams['severity[' + combo.severityLevel + ']'] = checked ? 1 : 0;
			store.load();
		};

		var list = new Scalr.Viewers.ListView({
			title: '日志 &raquo; 系统',
			scalrOptions: {
				'reload': false,
				'maximize': 'all'
			},
			scalrReconfigure: function (loadParams) {
				Ext.applyIf(loadParams, { farmId: '0', serverId: '' });
				Ext.apply(this.store.baseParams, loadParams);

				list.listView.setHiddenColumn('farm_name', this.store.baseParams['farmId'] != 0);
				this.getTopToolbar().getComponent('farmId').setValue(this.store.baseParams['farmId']);
				this.store.load();
			},
			store: store,
			stateId: 'listview-logs-system-view',

			tbar: [{
				text: '服务器组',
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
			}, '-', {
				text: '严重性',
				menu: new Ext.menu.Menu({
					items: [{
						text: 'Fatal error',
						checked: true,
						severityLevel: 5,
						listeners: {
							checkchange: filterSeverity
						}
					}, {
						text: 'Error',
						checked: true,
						severityLevel: 4,
						listeners: {
							checkchange: filterSeverity
						}
					}, {
						text: 'Warning',
						checked: true,
						severityLevel: 3,
						listeners: {
							checkchange: filterSeverity
						}
					}, {
						text: 'Information',
						checked: true,
						severityLevel: 2,
						listeners: {
							checkchange: filterSeverity
						}
					}, {
						text: 'Debug',
						checked: false,
						severityLevel: 1,
						listeners: {
							checkchange: filterSeverity
						}
					}]
				})
			}, '->', {
				text: '下载日志',
				iconCls: 'x-btn-download-icon',
				handler: function () {
					var params = store.baseParams;
					params['action'] = 'download';
					Scalr.Viewers.userLoadFile('/logs/xGetLogs?' + Ext.urlEncode(params));
				}
			}],

			bbar: [ '->', new Scalr.Toolbar.TimeItem({ time: moduleParams['time'], timeOffset: moduleParams['timeOffset'] })],

			listViewOptions: {
				emptyText: "未发现日志",
				getRowClass: function (data) {
					//console.log(data);
					// TODO
					return (data.severity > 3) ? 'viewers-listview-row-red' : '';
				},
				columns: [
					{ header: "", width: 7, dataIndex: 'severity', sortable: false, align:'center', hidden: 'no', tpl:
						'<tpl if="severity == 1"><img src="/images/ui-ng/icons/log/debug.png"></tpl>' +
						'<tpl if="severity == 2"><img src="/images/ui-ng/icons/log/info.png"></tpl>' +
						'<tpl if="severity == 3"><img src="/images/ui-ng/icons/log/warning.png"></tpl>' +
						'<tpl if="severity == 4"><img src="/images/ui-ng/icons/log/error.png"></tpl>' +
						'<tpl if="severity == 5"><img src="/images/ui-ng/icons/log/fatal_error.png"></tpl>'
					},
					{ header: "时间", width: 25, dataIndex: 'time', sortable: false, hidden: 'no' },
					{ header: "服务器组", width: 25, dataIndex: 'farm_name', sortable: false, hidden: 'no', tpl:
						"<a href='#/farms/{farmid}/view'>{farm_name}</a>"
					},
					{ header: "产生于", width: 40, dataIndex: 'source', sortable: false, hidden: 'no', tpl:
						"<a href='#/servers/{servername}/view'>{servername}</a>/{source}"
					},
					{ header: "信息", width: 160, dataIndex: 'message', sortable: false, hidden: 'no', tpl:
						'<p ondblclick="Ext.get(this).parent(\'dl.viewers-listview-row\').toggleClass(\'viewers-listview-row-expand\'); return false;">{message}</p>'
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
