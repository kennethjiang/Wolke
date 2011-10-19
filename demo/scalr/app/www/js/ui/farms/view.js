{
	create: function (loadParams, moduleParams) {
		var store = new Scalr.data.Store({
			baseParams: loadParams,
			reader: new Scalr.data.JsonReader({
				id: 'id',
				fields: [
					{name: 'id', type: 'int'},
					{name: 'clientid', type: 'int'},
					{name: 'dtadded', type: 'date'},
					'name', 'status', 'servers', 'roles', 'zones','client_email','havemysqlrole','shortcuts'
				]
			}),
			remoteSort: true,
			url: '/farms/xListViewFarms/'
		});

		return new Scalr.Viewers.ListView({
			title: '服务器组 &raquo; 查看',
			scalrOptions: {
				'reload': false,
				'maximize': 'all'
			},
			scalrReconfigure: function (loadParams) {
				Ext.applyIf(loadParams, { farmId: '', clientId: '', status: ''});
				Ext.apply(this.store.baseParams, loadParams);
				this.store.load();
			},
			store: store,
			stateId: 'listview-farms-view',

			listViewOptions: {
				emptyText: "未发现服务器组",
				columns: [
					{ header: "服务器组ID", width: 5, dataIndex: 'id', sortable: true, hidden: 'no' },
					{ header: "服务器组名称", width: 10, dataIndex: 'name', sortable: true, hidden: 'no' },
					{ header: "生成日期", width: 10, dataIndex: 'dtadded', tpl: '{[values.dtadded ? values.dtadded.dateFormat("M j, Y") : ""]}', sortable: true, hidden: 'no' },
					{ header: "服务角色", width:  10, dataIndex: 'roles', tpl: '{roles} [<a href="#/farms/{id}/roles">查看</a>]', sortable: false, hidden: 'no' },
					{ header: "服务器", width: 10, dataIndex: 'servers', tpl: '{servers} [<a href="#/servers/view?farmId={id}">查看</a>]', sortable: false, hidden: 'no' },
					{ header: "DNS区域", width: 10, dataIndex: 'zones', tpl: '{zones} [<a href="#/dnszones/view?farmId={id}">查看</a>]', sortable: false, hidden: 'no' },
					{ header: "状态", width: 5, dataIndex: 'status', tpl:
						new Ext.XTemplate('<span class="{[this.getClass(values.status)]}">{[this.getName(values.status)]}</span>', {
							getClass: function (value) {
								if (value == 1)
									return "status-ok";
								else if (value == 3)
									return "status-ok-pending";
								else
									return "status-fail";
							},
							getName: function (value) {
								var titles = {
									1: "运行",
									0: "已终止",
									2: "正在终止",
									3: "同步中"
								};
								return titles[value] || value;
							}
						}), sortable: true, hidden: 'no'
					}
			]},
			rowOptionsMenu: [{
				itemId: "option.launchFarm",
				text: '启动',
				iconCls: 'scalr-menu-icon-launch',
				request: {
					confirmBox: {
						type: 'launch',
						msg: '确定启动服务器组 "{name}" ?'
					},
					processBox: {
						type: 'launch',
						msg: '正在启动服务器组。 请稍候...'
					},
					url: '/farms/xLaunch/',
					dataHandler: function (record) {
						return { farmId: record.get('id') };
					},
					success: function () {
						store.reload();
						Scalr.Message.Success('服务器组成功启动');
					}
				}
			},
				{itemId: "option.terminateFarm", iconCls: 'scalr-menu-icon-terminate', text: '终止', href: "/farms_control.php?farmid={id}"},
				new Ext.menu.Separator({itemId: "option.controlSep"}),
				{itemId: "option.usageStats", text: '使用统计', href: "/farm_usage_stats.php?farmid={id}"},
				{itemId: "option.loadStats", iconCls: 'scalr-menu-icon-stats', text: '负载统计', href: "/monitoring.php?farmid={id}"},
	
				{itemId: "option.events",		text: '事件&通知', href: "#/farms/{id}/events"},
	
				new Ext.menu.Separator({itemId: "option.mysqlSep"}),
	
				{itemId: "option.mysql", iconCls: 'scalr-menu-icon-mysql', text: 'MySQL状态', href: "/farm_mysql_info.php?farmid={id}"},
				{itemId: "option.script", iconCls: 'scalr-menu-icon-execute', text: '执行脚本', href: "#/scripts/execute?farmId={id}"},
	
				new Ext.menu.Separator({itemId: "option.logsSep"}),
				{itemId: "option.logs", iconCls: 'scalr-menu-icon-logs', text: '查看日志', href: "#/logs/system?farmId={id}"},
				new Ext.menu.Separator({itemId: "option.editSep"}),
				{itemId: "option.edit", iconCls: 'scalr-menu-icon-configure', text: '编辑', href: "#/farms/{id}/edit"},
			{
				itemId: 'option.delete',
				iconCls: 'scalr-menu-icon-delete',
				text: '删除',
				request: {
					confirmBox: {
						type: 'delete',
						msg: '确认删除服务器组"{name}" ?'
					},
					processBox: {
						type: 'delete',
						msg: '正在删除服务器组。请等待...'
					},
					url: '/farms/xRemove/',
					dataHandler: function (record) {
						return { farmId: record.get('id') };
					},
					success: function () {
						store.reload();
						Scalr.Message.Success('服务器组已成功删除');
					}
				}
			}, {
				xtype: 'menuseparator',
				itemId: 'option.scSep'
			}],

			getRowOptionVisibility: function (item, record) {
				var data = record.data;
	
				if (item.itemId == "option.launchFarm")
					return (data.status == 0);
	
				if (item.itemId == "option.terminateFarm")
					return (data.status == 1);
	
				if (item.itemId == "option.scSep")
					return (data.shortcuts.length > 0);
	
				if (item.itemId == "option.viewMap" ||
						item.itemId == "option.viewMapSep" ||
						item.itemId == "option.loadStats" ||
						item.itemId == "option.mysqlSep" ||
						item.itemId == "option.mysql" ||
						item.itemId == "option.script"
					) {
	
					if (data.status == 0)
						return false;
					else
					{
						if (item.itemId != "option.mysql")
							return true;
						else
							return data.havemysqlrole;
					}
				}
				else
					return true;
			},
			listeners: {
				'beforeshowoptions': {fn: function (grid, record, romenu, ev) {
					romenu.record = record;
					var data = record.data;

					romenu.items.each(function (item) {
						if (item.isshortcut) {
							item.parentMenu.remove(item);
						}
					});

					if (data.shortcuts.length > 0)
					{
						for (i in data.shortcuts)
						{
							if (typeof(data.shortcuts[i]) != 'function')
							{
								romenu.add({
									//id:'option.'+(Math.random()*100000),
									isshortcut:1,
									xmenu:romenu,
									text:'执行 '+data.shortcuts[i].name,
									href:'#/scripts/execute?eventName='+data.shortcuts[i].event_name
								});
							}
						}
					}
				}}
			}
		});
	},
	// refresh this page in every 20 seconds
	autorefresh:20000
}
