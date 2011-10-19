{
	create: function (loadParams, moduleParams) {
		var dashboard = new Ext.Panel({
			scalrOptions: {
				reload: false,
				maximize: 'all'
			},
			title: '控制台',
			header: false,
			border: false,

			layout: 'column',
			autoScroll: true,
			items: [{
				columnWidth:.33,
				style:'padding: 5px',
				items: [{
					title: '账户信息',
					border: false,
					layout: 'form',
					loaded: false,
					minHeight: 200,
					plugins: [ new Scalr.Viewers.Plugins.localStorage() ],
					stateId: 'dashboard-widget-account-info',
					bodyStyle: 'padding: 5px',
					tools: [{
						id: 'refresh',
						handler: function (event, tool, panel) {
							if (! panel.collapsed)
								panel.updateInfo();
						}
					}],
					collapsible: true,
					updateInfo: function () {
						this.el.mask();
						Ext.Ajax.request({
							url: '/dashboard/widgetAccountInfo/',
							disableCaching: false,
							success: function (response) {
								var result = Ext.decode(response.responseText);
								if (result.success) {
									this.removeAll();
									this.add(result.module);
									this.doLayout();
									this.loaded = true;
									this.el.unmask();
								} else {
									this.collapse();
									Scalr.Viewers.ErrorMessage(result.error);
								}
							},
							failure: function () {
								this.el.unmask();
								this.collapse();
							},
							scope: this
						});
					},
					buttonAlign:'center',
					buttons: [
					    {
					    	text:'修改基本信息',
					    	handler:function(){
					    		document.location.href = '#/core/profile';
					    	}
					    }
					],
					listeners: {
						'render': function () {
							if (this.localGet('state') == 'collapsed')
								this.collapsed = true;
						},
						'afterrender': function () {
							if (! this.collapsed) {
								this.updateInfo();
							}
						},
						'expand': function () {
							this.localSet('state', 'expanded');

							if (! this.loaded)
								this.updateInfo();
						},
						'collapse': function () {
							this.localSet('state', 'collapsed');
						}
					}
				}]
			}, {
				columnWidth:.33,
				style: 'padding: 5px',
				items: [{
					xtype: 'panel',
					title: '最近的错误&警告信息',
					border: false,
					collapsible: true,
					layout: 'fit',
					loaded: false,
					plugins: [ new Scalr.Viewers.Plugins.localStorage() ],
					stateId: 'dashboard-widget-last-logs',
					updateInfo: function () {
						this.get(0).store.reload();
						this.loaded = true;
					},
					tools: [{
						id: 'refresh',
						handler: function (event, tool, panel) {
							if (! panel.collapsed)
								panel.updateInfo();
						}
					}],
					items: new Scalr.Viewers.list.ListView({
						hideHeaders: true,
						columnHide: false,
						store: new Scalr.data.Store({
							reader: new Scalr.data.JsonReader({
								id: 'id',
								fields: [
									'id','serverid','message','severity','time','source','farmid','servername','farm_name', 's_severity'
								]
							}),
							remoteSort: true,
							url: '/logs/xGetLogs?severity[3]=1&severity[4]=1&severity[5]=1&start=0&limit=10'
						}),
						columns: [{
							header: 'Message', width: 1, dataIndex: 'message', sortable: 'no', hidden: 'no', tpl: new Ext.XTemplate(
								'<p style="font-size: 11px; height: 24px; white-space: normal" ondblclick="var el = Ext.get(this), o = el.getStyles(\'height\'); if (o.height == \'auto\') el.applyStyles(\'height: 24px;\'); else el.applyStyles(\'height: auto;\');">' +
								'<img style="float: left; margin-right: 8px;"' +
								'<tpl if="severity == 0"> src="/images/ui-ng/icons/log/debug.png" title="{s_severity}"></tpl>' +
								'<tpl if="severity == 2"> src="/images/ui-ng/icons/log/info.png" title="{s_severity}"></tpl>' +
								'<tpl if="severity == 3"> src="/images/ui-ng/icons/log/warning.png" title="{s_severity}"></tpl>' +
								'<tpl if="severity == 4"> src="/images/ui-ng/icons/log/error.png" title="{s_severity}"></tpl>' +
								'<tpl if="severity == 5"> src="/images/ui-ng/icons/log/fatal_error.png" title="{s_severity}"></tpl>' +
								'<span style="color: blue">{time}</span>&nbsp;&nbsp;&nbsp;{message}' +
								'</p>'
							)
						}]
					}),
					listeners: {
						'render': function () {
							if (this.localGet('state') == 'collapsed')
								this.collapsed = true;

							new Ext.LoadMask(this.el, { store: this.get(0).store });
						},
						'afterrender': function () {
							if (! this.collapsed) {
								this.updateInfo();
							}
						},
						'expand': function () {
							this.localSet('state', 'expanded');

							if (! this.loaded)
								this.updateInfo();
						},
						'collapse': function () {
							this.localSet('state', 'collapsed');
						}
					}
				}]
			}, {
				columnWidth:.33,
				style:'padding: 5px',
				items: [{
					xtype: 'panel',
					title: '快捷方式',
					layout: 'column',
					border: false,
					collapsible: true,
					defaultType: 'button',
					plugins: [ new Scalr.Viewers.Plugins.localStorage() ],
					stateId: 'dashboard-widget-quick-access',
					items: [],
					listeners: {
						'render': function () {
							var links = [{
								text: 'Farms',
								href: '#/farms/view',
								icon: 'farms_32x32.png'
							}, {
								text: 'Roles',
								href: '#/roles/view',
								icon: 'roles_32x32.png'
							}, {
								text: 'Logs',
								href: '#/logs/system',
								icon: 'logs_32x32.png'
							}, {
								text: 'Scheduler',
								href: '/scheduler.php',
								icon: 'scheduler_32x32.png'
							}, {
								text: 'Settings',
								href: '#/core/settings',
								icon: 'settings_32x32.png'
							}];

							for (var i = 0; i < links.length; i++)
								this.add({
									text: links[i].text,
									href: links[i].href,
									style: 'padding: 10px',
									width:'90px',
									height:'90px',
									cls: 'x-btn-text-icon',
									scale: 'large',
									icon: '/images/ui-ng/icons/quick_access/' + links[i].icon,
									iconAlign: 'top',
									handler: function () {
										document.location.href = this.href;
									}
								});

							if (this.localGet('state') == 'collapsed')
								this.collapsed = true;

						},
						'expand': function () {
							this.localSet('state', 'expanded');
						},
						'collapse': function () {
							this.localSet('state', 'collapsed');
						}
					}
				}]
			}],

			/*initComponent: function(){
				Ext.Panel.prototype.initComponent.call(this);
				this.addEvents({
					validatedrop: true,
					beforedragover: true,
					dragover: true,
					beforedrop: true,
					drop: true
				});
			},

			initEvents : function(){
				Ext.Panel.prototype.initEvents.call(this);

				this.body.ddScrollConfig = {
					vthresh: 50,
					hthresh: -1,
					animate: true,
					increment: 200
				};

				Ext.dd.ScrollManager.register(this.body);

				this.dd = new Ext.dd.DropTarget(this.el, {
					portal: this,

					createEvent: function(dd, e, data, col, c, pos) {
						return {
							portal: this.portal,
							panel: data.panel,
							columnIndex: col,
							column: c,
							position: pos,
							data: data,
							source: dd,
							rawEvent: e,
							status: this.dropAllowed
						};
					},

					notifyOver: function(dd, e, data) {
						var xy = e.getXY(), portal = this.portal, px = dd.proxy;

						// case column widths
						if(!this.grid){
							this.grid = this.getGrid();
						}

						// handle case scroll where scrollbars appear during drag
						var cw = portal.body.dom.clientWidth;
						if(!this.lastCW){
							this.lastCW = cw;
						}else if(this.lastCW != cw){
							this.lastCW = cw;
							portal.doLayout();
							this.grid = this.getGrid();
						}

						// determine column
						var col = 0, xs = this.grid.columnX, cmatch = false;
						for(var len = xs.length; col < len; col++){
							if(xy[0] < (xs[col].x + xs[col].w)){
								cmatch = true;
								break;
							}
						}
						// no match, fix last index
						if(!cmatch){
							col--;
						}

						// find insert position
						var p, match = false, pos = 0,
							c = portal.items.itemAt(col),
							items = c.items.items, overSelf = false;

						for(var len = items.length; pos < len; pos++){
							p = items[pos];
							var h = p.el.getHeight();
							if(h === 0){
								overSelf = true;
							}
							else if((p.el.getY()+(h/2)) > xy[1]){
								match = true;
								break;
							}
						}

						pos = (match && p ? pos : c.items.getCount()) + (overSelf ? -1 : 0);
						var overEvent = this.createEvent(dd, e, data, col, c, pos);

						if(portal.fireEvent('validatedrop', overEvent) !== false &&
						portal.fireEvent('beforedragover', overEvent) !== false){

							// make sure proxy width is fluid
							px.getProxy().setWidth('auto');

							if(p){
								px.moveProxy(p.el.dom.parentNode, match ? p.el.dom : null);
							}else{
								px.moveProxy(c.el.dom, null);
							}

							this.lastPos = {c: c, col: col, p: overSelf || (match && p) ? pos : false};
							this.scrollPos = portal.body.getScroll();

							portal.fireEvent('dragover', overEvent);

							return overEvent.status;
						}else{
							return overEvent.status;
						}

					},

					notifyOut: function() {
						delete this.grid;
					},

					notifyDrop: function(dd, e, data) {
						delete this.grid;
						if(!this.lastPos){
							return;
						}
						var c = this.lastPos.c,
							col = this.lastPos.col,
							pos = this.lastPos.p,
							panel = dd.panel,
							dropEvent = this.createEvent(dd, e, data, col, c,
								pos !== false ? pos : c.items.getCount());

						if(this.portal.fireEvent('validatedrop', dropEvent) !== false &&
						this.portal.fireEvent('beforedrop', dropEvent) !== false){

							dd.proxy.getProxy().remove();
							panel.el.dom.parentNode.removeChild(dd.panel.el.dom);

							if(pos !== false){
								c.insert(pos, panel);
							}else{
								c.add(panel);
							}

							c.doLayout();

							this.portal.fireEvent('drop', dropEvent);

							// scroll position is lost on drop, fix it
							var st = this.scrollPos.top;
							if(st){
								var d = this.portal.body.dom;
								setTimeout(function(){
									d.scrollTop = st;
								}, 10);
							}

						}
						delete this.lastPos;
					},

					// internal cache of body and column coords
					getGrid: function() {
						var box = this.portal.bwrap.getBox();
						box.columnX = [];
						this.portal.items.each(function(c){
							box.columnX.push({x: c.el.getX(), w: c.el.getWidth()});
						});
						return box;
					} //,

					// unregister the dropzone from ScrollManager
					/*unreg: function() {
						Ext.dd.ScrollManager.unregister(this.portal.body);
						Ext.ux.Portal.DropZone.superclass.unreg.call(this);
					}*/
				/*});
			},

			beforeDestroy : function() {
				if (this.dd) {
					this.dd.unreg();
				}
				Ext.Panel.prototype.beforeDestroy.call(this);
			}*/
		});

		return dashboard;
	}
}
