{
	create: function (loadParams, moduleParams) {
		var store = new Scalr.data.Store({
			baseParams: loadParams,
			reader: new Scalr.data.JsonReader({
				id: 'server_id',
				fields: [
					'cloud_server_id', 'isrebooting', 'excluded_from_dns', 'server_id', 'remote_ip', 'local_ip', 'status', 'platform', 'farm_name', 'role_name', 'index', 'role_id', 'farm_id', 'farm_roleid', 'uptime', 'ismaster'
				]
			}),
			remoteSort: true,
			url: '/servers/xListViewServers/'
		});

		var laGetFunction = {
			cache: {},
			currentRequestId: 0,

			getCache: function(serverId) {
				var dt = new Date();
				return (typeof(this.cache[serverId]) != "undefined" && this.cache[serverId].dt > dt) ? this.cache[serverId].html : null;
			},

			updateCache: function(elem, html) {
				this.cache[elem.getAttribute('serverid')] = { html: html, dt: new Date().add(Date.MINUTE, 3) };
			},

			waitHtml: function(elem) {
				var el = Ext.get(elem);
				if (el)
					el.update('<img src="/images/snake-loader.gif">');
			},

			updateHtml: function(elem) {
				var el = Ext.get(elem);
				if (el) {
					if (this.cache[elem.getAttribute('serverid')]) {
						el.parent().update(this.cache[elem.getAttribute('serverid')].html); // replace <span>
					} else {
						el.update(''); // clear cell
					}
				}
			},

			getNext: function() {
				return (this.indexId < this.elements.length) ? this.elements[this.indexId++] : null;
			},

			getLA: function() {
				var elem = this.getNext();

				if (! elem)
					return;

				Ext.Ajax.suspendEvents();
				this.waitHtml(elem);

				this.currentRequestId = Ext.Ajax.request({
					url: '/servers/xServerGetLa/',
					params: { serverId: elem.getAttribute('serverid') },
					success: function(response, options) {
						Ext.Ajax.resumeEvents();

						var result = Ext.decode(response.responseText), html = '';
						if (result.success == true) {
							html = result.la;
						} else {
							html = '<img src="/images/warn.png" title="' + result.error + '">';
						}

						this.func.updateCache(this.elem, html);
						this.func.updateHtml(this.elem);
						this.func.getLA();
					},
					failure: function(response) {
						Ext.Ajax.resumeEvents();

						if (response.isAbort) {
							this.func.updateHtml(this.elem);
						} else {
							this.func.updateCache(this.elem, '<img src="/images/warn.png" title="Cannot proceed request">');
							this.func.updateHtml(this.elem);
							this.func.getLA();
						}
					},
					scope: {
						func: this,
						elem: elem
					}
				});
			},

			startUpdate: function(elem) {
				this.elements = elem.query('span.la');
				this.indexId = 0;

				this.getLA();
			},

			stopUpdate: function() {
				if (this.currentRequestId && Ext.Ajax.isLoading(this.currentRequestId)) {
					this.updateHtml(this.elements[this.indexId]); // abort doesn't handle by event 'failure'
					Ext.Ajax.abort(this.currentRequestId);
				}
			}
		};

		return new Scalr.Viewers.ListView({
			title: '服务器 &raquo; 查看',
			scalrOptions: {
				'reload': false,
				'maximize': 'all'
			},
			scalrReconfigure: function (loadParams) {
				Ext.applyIf(loadParams, { farmId: '', roleId: '', farmRoleId: '', serverId: '' });
				Ext.apply(this.store.baseParams, loadParams);
				this.store.load();
			},
			store: store,
			stateId: 'listview-servers-view',
			tbar: [
				' ',
				{
					xtype:'checkbox',
					itemId: 'hide_terminated',
					boxLabel: '不显示已终止的服务器',
					style: 'margin: 0px',
					listeners: {
						check: function(item, checked) {
							store.baseParams.hideTerminated = checked ? 'true' : 'false';
							store.load();
						}
					}
				}
			],

			rowOptionsMenu: [
				{itemId: "option.cancel", text: '取消', menuHandler: function (item) {
					Ext.Msg.wait("请等待 ...");

					Ext.Ajax.request({
						url: '/servers/xServerCancelOperation/',
						params: { serverId: item.currentRecordData.server_id },
						success: function (response) {
							var result = Ext.decode(response.responseText);
							if (result.success == true) {
								Scalr.Viewers.SuccessMessage(result.message);
								store.reload();
							} else if (result.error)
								Scalr.Viewers.ErrorMessage(result.error);

							Ext.Msg.hide();
						},
						failure: function() {
							Ext.Msg.hide();
						}
					});
				}},
				{ itemId: "option.info", iconCls: 'scalr-menu-icon-info', text: '服务器信息', href: "#/servers/{server_id}/extendedInfo" },
				{itemId: "option.loadStats", iconCls: 'scalr-menu-icon-stats', text: '负载统计', href: "/monitoring.php?farmid={farm_id}&role={farm_roleid}&server_index={index}"},
				new Ext.menu.Separator({itemId: "option.infoSep"}),
				{ itemId: "option.sync", text: '生成服务器快照', href: "#/servers/{server_id}/createSnapshot" },
				new Ext.menu.Separator({itemId: "option.syncSep"}),
				{itemId: "option.editRole", iconCls: 'scalr-menu-icon-configure', text: '配置服务角色', href: "#/farms/{farm_id}/edit?roleId={role_id}"},
				new Ext.menu.Separator({itemId: "option.procSep"}), //TODO:
				{
					itemId: 'option.dnsEx',
					text: '从DNS区域中去除',
					request: {
						processBox: {
							type: 'action'
						},
						url: '/servers/xServerExcludeFromDns/',
						dataHandler: function (record) {
							return { serverId: record.get('server_id') };
						},
						success: function (data) {
							Scalr.Message.Success(data.message);
							store.reload();
						}
					}
				}, {
					itemId: 'option.dnsIn',
					text: '包含在DNS域中',
					request: {
						processBox: {
							type: 'action'
						},
						url: '/servers/xServerIncludeInDns/',
						dataHandler: function (record) {
							return { serverId: record.get('server_id') };
						},
						success: function (data) {
							Scalr.Message.Success(data.message);
							store.reload();
						}
					}
				},
				new Ext.menu.Separator({itemId: "option.editRoleSep"}),
				{ itemId: "option.console", text: '查看控制台输出', href: '#/servers/{server_id}/consoleoutput' },
				/*{ itemId: "option.process", text: 'View process list', href: '#/servers/{server_id}/processlist' },*/
				{itemId: "option.messaging",	text: '系统内部消息', href: "#/servers/{server_id}/messages"},
				/*
				new Ext.menu.Separator({itemId: "option.mysqlSep"}),
				{itemId: "option.mysql",		text: 'Backup/bundle MySQL data', href: "/farm_mysql_info.php?farmid={farm_id}"},
				*/
				new Ext.menu.Separator({itemId: "option.execSep"}),
				{itemId: "option.exec", iconCls: 'scalr-menu-icon-execute', text: '执行脚本', href: "#/scripts/execute?serverId={server_id}"},
				new Ext.menu.Separator({itemId: "option.menuSep"}),
				{
					itemId: 'option.reboot',
					text: '重启',
					iconCls: 'scalr-menu-icon-reboot',
					request: {
						confirmBox: {
							type: 'reboot',
							msg: '确定重启服务器 "{server_id}"?'
						},
						processBox: {
							type: 'reboot',
							msg: '正向服务器发送重启指令。请稍候...'
						},
						url: '/servers/xServerRebootServers/',
						dataHandler: function (record) {
							return { servers: Ext.encode([ record.get('server_id') ]) };
						},
						success: function () {
							store.reload();
						}
					}
				},

				{ itemId: "option.term", iconCls: 'scalr-menu-icon-terminate', text: '终止',
					handler: function(item) {
						var request = { descreaseMinInstancesSetting: 0, forceTerminate: 0 };

						Ext.MessageBox.show({
							title: '确认',
							msg:
								'终止所选服务器?'+
								'<br \><br \>'+
								'<input type="checkbox" class="instance"> 减少 \'最小服务器数\' 设置<br \>' +
								'<input type="checkbox" class="force"> 强行终止服务器<br \>',

							buttons: Ext.Msg.YESNO,
							fn: function(btn) {
								if (btn == 'yes') {
									Ext.MessageBox.show({
										progress: true,
										msg: '正在终止服务器。请稍候...',
										wait: true,
										width: 450,
										icon: 'scalr-mb-instance-terminating'
									});

									Ext.Ajax.request({
										url: '/servers/xServerTerminateServers/',
										success: function(response, options) {
											Ext.MessageBox.hide();

											var result = Ext.decode(response.responseText);
											if (result.success == true) {
												store.reload();
											} else {
												Scalr.Viewers.ErrorMessage(result.error);
											}
										},
										params: {
											descreaseMinInstancesSetting: request.descreaseMinInstancesSetting,
											forceTerminate: request.forceTerminate,
											servers: Ext.encode([item.currentRecordData.server_id])
										}
									});
								}
							}
						});

						if (Ext.MessageBox.isVisible()) {
							var el = Ext.MessageBox.getDialog().getEl();

							el.child('input.instance').on('click', function() {
								request.descreaseMinInstancesSetting = this.dom.checked ? 1 : 0;
							});

							el.child('input.force').on('click', function() {
								request.forceTerminate = this.dom.checked ? 1 : 0;
							});
						}
					}
				},
				new Ext.menu.Separator({id: "option.logsSep"}),
				{id: "option.logs", iconCls: 'scalr-menu-icon-logs', text: '查看日志', href: "#/logs/system?serverId={server_id}"}
			],
			getRowOptionVisibility: function (item, record) {
				var data = record.data;
				
				if (item.itemId == 'option.dnsEx' && data.excluded_from_dns)
					return false;
				
				if (item.itemId == 'option.dnsIn' && !data.excluded_from_dns)
					return false;
				
				if (item.itemId == 'option.console')
					return (data.platform == 'ec2');
				
				if (data.status == 'Importing' || data.status == 'Pending launch' || data.status == 'Temporary')
				{
					if (item.itemId == 'option.cancel' || item.itemId == 'option.messaging')
						return true;
					else
						return false;
				}
				else
				{
					if (item.itemId == 'option.cancel')
						return false;

					if (data.status == 'Terminated')
						return false;
					else
						return true;
				}
			},

			getRowMenuVisibility: function (data) {
				return (data.status != 'Terminated');
			},

			listViewOptions: {
				emptyText: "未发现服务器",
				columns: [
					{ header: "平台", width: '85px', dataIndex: 'platform', sortable: true, hidden: 'no' },
					{ header: "云平台 & 角色", width: 60, dataIndex: 'farm_id', sortable: true, hidden: 'no', tpl:
						'<tpl if="farm_id">Farm: <a href="#/farms/{farm_id}/view" title="Farm {farm_name}">{farm_name}</a>' +
							'<tpl if="farm_roleid">&nbsp;&rarr;&nbsp;<a href="#/farms/{farm_id}/roles/{farm_roleid}/view" title="Role {role_name}">{role_name}</a></tpl>' +
						'</tpl>' +
						'<tpl if="ismaster == 1"> (Master)</tpl>' +
						'<tpl if="! farm_id"><img src="/images/false.gif" /></tpl>'
					},
					{ header: "服务器ID", width: '220px', dataIndex: 'server_id', sortable: true, hidden: 'no', tpl: new Ext.XTemplate(
						'<a href="#/servers/{server_id}/extendedInfo">{[this.serverId(values.server_id)]}</a>', {
							serverId: function(id) {
								var values = id.split('-');
								return values[0] + '-...-' + values[values.length - 1];
							}
						})
					},
					{ header: "云服务器ID", width: 60, dataIndex: 'cloud_server_id', sortable: false, hidden: 'yes', tpl:
						'{cloud_server_id}'
					},
					{ header: "状态", width: 30, dataIndex: 'status', sortable: true, hidden: 'no', tpl:
						'{status} <tpl if="isrebooting == 1"> (重启中 ...)</tpl>'
					},
					{ header: "公网IP", width: 30, dataIndex: 'remote_ip', sortable: true, hidden: 'no', tpl:
						'<tpl if="remote_ip">{remote_ip}</tpl>'
					},
					{ header: "内网IP", width: 30, dataIndex: 'local_ip', sortable: true, hidden: 'no', tpl:
						'<tpl if="local_ip">{local_ip}</tpl>'
					},
					{ header: "启动时间", width: 30, dataIndex: 'uptime', sortable: false, hidden: 'no' },
					{ header: "DNS", width: '38px', dataIndex: 'excluded_from_dns', sortable: false, hidden: 'no', align: 'center', tpl:
						'<tpl if="excluded_from_dns"><img src="/images/false.gif" /></tpl><tpl if="!excluded_from_dns"><img src="/images/true.gif" /></tpl>'
					},
					{ header: "LA", width: '50px', dataIndex: 'server_la', sortable: false, hidden: 'yes', align: 'center',
						tpl: new Ext.XTemplate(
							'<tpl if="status == &quot;Running&quot;">' +
								'<tpl if="this.laGetFunction.getCache(values.server_id)">{[this.laGetFunction.getCache(values.server_id)]}</tpl>' +
								'<tpl if="!this.laGetFunction.getCache(values.server_id)"><span class="la" serverid="{server_id}"></span></tpl>' +
							'</tpl>' +
							'<tpl if="status != &quot;Running&quot;">-</tpl>', { laGetFunction: laGetFunction }
						)
					},
					{ header: "操作", width: '80px', dataIndex: 'id', sortable: false, align:'center', hidden: 'no', tpl: new Ext.XTemplate(
						'<tpl if="(status == &quot;Running&quot; || status == &quot;Initializing&quot;) && index != &quot;0&quot;">' +
							'<a style="float:center;margin-right:2px;margin-left:4px;" href="#/servers/{server_id}/sshConsole" target="_blank" title="登录该服务器"><img style="margin-right:3px;" src="/images/terminal.png" alt="登录该服务器"></a>' +
						'</tpl>' +
						'<tpl if="! ((status == &quot;Running&quot; || status == &quot;Initializing&quot;) && index != &quot;0&quot;)">' +
							'<img src="/images/false.gif">' +
						'</tpl>', {
							getServerId: function (serverId) {
								return serverId.replace(/-/g, '');
							}
						})
					}
				]
			},

			withSelected: {
				menu: [{
					text: '重启',
					iconCls: 'scalr-menu-icon-reboot',
					request: {
						confirmBox: {
							type: 'reboot',
							msg: '确认重启所选服务器?'
						},
						processBox: {
							type: 'reboot',
							msg: '正向服务器发送重启指令。请稍候...'
						},
						url: '/servers/xServerRebootServers/',
						dataHandler: function (records) {
							var servers = [];
							for (var i = 0, len = records.length; i < len; i++) {
								servers[servers.length] = records[i].get('server_id');
							}

							return { servers: Ext.encode(servers) };
						}
					}
				}, {
					text: '终止',
					iconCls: 'scalr-menu-icon-terminate',
					request: {
						confirmBox: {
							type: 'terminate',
							msg: '确认终止所选服务器?'
						},
						processBox: {
							type: 'terminate',
							msg: '正在终止服务器。请稍等...'
						},
						url: '/servers/xServerTerminateServers/',
						dataHandler: function (records) {
							var servers = [];
							for (var i = 0, len = records.length; i < len; i++) {
								servers[servers.length] = records[i].get('server_id');
							}

							return { servers: Ext.encode(servers) };
						}
					}
				}],
				renderer: function(data) {
					return (data.status == 'Running' || data.status == 'Initializing');
				}
			},

			listeners: {
				'render': function() {
					this.listView.on('columnhide', function(column) {
						if (column.dataIndex == 'server_la') {
							laGetFunction.stopUpdate();
						}
					});

					this.listView.on('columnshow', function(column) {
						if (column.dataIndex == 'server_la') {
							laGetFunction.startUpdate(this.innerBody);
						}
					}, this.listView);

					this.listView.on('refresh', function() {
						for (var i = 0, len = this.columns.length; i < len; i++) {
							var column = this.columns[i];
							if (column.dataIndex == 'server_la' && column.hidden == 'no') {
								laGetFunction.stopUpdate();
								laGetFunction.startUpdate(this.innerBody);
							}
						}
					}, this.listView);
				}
			}
		});
	},
	// refresh this page in every 20 seconds
	autorefresh:20000
}
