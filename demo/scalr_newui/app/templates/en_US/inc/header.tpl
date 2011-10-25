<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<title>云拓智能化平台运维系统</title>
	<meta http-equiv="Content-Language" content="en-us" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="robots" content="none" />

	<link href="{get_static_url path='/css/ui-ng/main.css'}" rel="stylesheet" type="text/css" />

	{if $newUING == ''}
	<!-- candidate to delete -->
	<link href="{get_static_url path='/css/main.css'}" rel="stylesheet" type="text/css" />
	<link href="{get_static_url path='/css/style.css'}" rel="stylesheet" type="text/css" />
	<link href="{get_static_url path='/css/topbar.css'}" rel="stylesheet" type="text/css" />
	<link href="{get_static_url path='/css/ext-scalr-ui.css'}" type="text/css" rel="stylesheet" />
	<link href="{get_static_url path='/css/cp.css'}" type="text/css" rel="stylesheet" />
	{/if}

	<link href="/js/extjs-3.3.0/resources/css/ext-all.css" type="text/css" rel="stylesheet" />
	<link href="{get_static_url path='/css/ui-ng/viewers.css'}" type="text/css" rel="stylesheet" />
  <link href="/html/css/headtop.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript">
		var get_url = '{$get_url}';
		var newUING = '{$newUING}';

		{literal}
		// typical logic
		if (newUING == '' && document.location.hash && document.location.hash != '#') {
			// history back after redirect to /#/
			document.location.href = document.location.pathname;
		}
		{/literal}
	</script>

	{if $smarty.get.js_debug}
		<script type="text/javascript" src="{get_static_url path='/js/extjs-3.3.1/ext-core-debug.js'}"></script>
		<script type="text/javascript" src="{get_static_url path='/js/debug.js'}"></script>
	{else}
		<script type="text/javascript" src="{get_static_url path='/js/extjs-3.3.1/ext-core.js'}"></script>
	{/if}

	<script type="text/javascript" src="{get_static_url path='/js/ui-ng/viewers.js'}"></script>
	<script type="text/javascript" src="{get_static_url path='/js/utils.js'}"></script>
	{if newUING != ''}
		<script type="text/javascript" src="{get_static_url path='/js/init.js'}"></script>
	{/if}

	{if $newUING == ''}
	<script type="text/javascript" src="{get_static_url path='/js/extjs-3.3.1/ext-grids.js'}"></script>
	{/if}

	<script type="text/javascript" src="{get_static_url path='/js/extjs-3.3.1/Loader.js'}"></script>

	<script type="text/javascript" src="/js/ui-ng/data.js"></script>
	{if newUING != ''}
		<script type="text/javascript" src="/js/ui-ng/viewers/ListView.v2.js"></script>
	{else}
		<script type="text/javascript" src="/js/ui-ng/viewers/ListView.js"></script>
	{/if}

	{if $newUING == ''}
	<!-- candidate to delete -->
	<script type="text/javascript" src="{get_static_url path='/js/ext-ux.js'}"></script>
	<script type="text/javascript" src="{get_static_url path='/js/scalr-ui.js'}"></script>
	{/if}
<script type="text/javascript" language="JavaScript">	
{literal}

function change1()
{
	document.getElementById('menu1').className='menu1 menu1just';
	document.getElementById('menu2').className='menu2';
	document.getElementById('menu3').className='menu3';
}
function change2()
{
	document.getElementById('menu1').className='menu1';
	document.getElementById('menu2').className='menu2 menu2just';
	document.getElementById('menu3').className='menu3';
}
function change3()
{
	document.getElementById('menu1').className='menu1';
	document.getElementById('menu2').className='menu2';
	document.getElementById('menu3').className='menu3 menu3just';
}
{/literal}
</script>
	{$add_to_head}
</head>
<body onload="init();">
<div id="topbar-wrap">
	<div id="topbar">
		  <div id="navmenu"></div>
			<div id="toplinks">
				{if $smarty.session.Scalr_Session.clientId != 0}
					<!--<a href="http://wiki.scalr.net" target="_blank">{t}Wiki{/t}</a>
					<a href="http://support.scalr.net" target="_blank">{t}Support{/t}</a>-->
				{/if}
			</div>
      <div id="logout_button"></div>

		  <div id="logo"><a title="{t}Home{/t}" href="/html/index.htm">Scalr</a></div>
      <div id="menu" class="menu">
      
    	     <script type="text/javascript" language="JavaScript">	
					{literal}
			 		var thisURL = document.URL; 
			 			if (thisURL.indexOf("\/farms\/")>=0 ){
							var strwrite = '<a href="/html/index.htm" id="menu1"  class="menu1">首页</a><a href="/#/farms/view" id="menu2" class="menu2 menu2just"  onclick="change2();">云平台</a><a href="/#/servers/view" id="menu3" class="menu3" onclick="change3();">服务器</a>';
							document.write( strwrite ); 
						}else if (thisURL.indexOf("\/servers\/")>=0 ) {
							var strwrite = '<a href="/html/index.htm" id="menu1" class="menu1">首页</a><a href="/#/farms/view" id="menu2"  class="menu2" onclick="change2();">云平台</a><a href="/#/servers/view" id="menu3" class="menu3 menu3just" onclick="change3();">服务器</a>';
							document.write( strwrite ); 
						} else {
							var strwrite = '<a href="/html/index.htm" id="menu1" class="menu1 menu1just"  onclick="change1();">首页</a><a href="/#/farms/view" id="menu2"  class="menu2">云平台</a><a href="/#/servers/view" id="menu3" class="menu3">服务器</a>';
							document.write( strwrite ); 
						}	 	
	        {/literal}
				</script>

      </div>
      
		  <div id="loginout" class="loginout"><a href="/#/core/logout"><img src="/html/images/tuichu.png" alt="云拓智能化平台运维系统" /></a></div>




		<script type="text/javascript">
		var session_environments = eval({$session_environments});
		var newUING = '{$newUING}';
		var JSdebug = '{$smarty.get.js_debug}';
		var reloadPage = false;
		var runner = new Ext.util.TaskRunner();

		Ext.ns('Scalr.data');
		Scalr.data.InputParams = eval({$scalrJsonParams});
		Scalr.data.UrlCache = {ldelim}{rdelim};
		Scalr.data.UrlCurrent = '';

		// {literal}

		Scalr.cache = {};
		Scalr.regPage = function (type, fn) {
			Scalr.cache[type] = fn;
		};

		Scalr.User = {};
		
		Ext.onReady(function () {
			Ext.BLANK_IMAGE_URL = Ext.isIE6 || Ext.isIE7 || Ext.isAir ?
                            'https:/' + '/www.sencha.com/s.gif' :
                            'data:image/gif;base64,R0lGODlhAQABAID/AMDAwAAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==';

                        Ext.QuickTips.init();
                        Ext.form.Field.prototype.msgTarget = 'side';

			function calculateNavMenuWidth () {
				return Ext.get("toplinks").getX() - Ext.get("navmenu").getX() - 30;
			}

			var _ = function(value) {
				return value;
			}


			new Ext.Button({
				renderTo: "logout_button",
				text: "退出",
				handler: function () {
					// clear state
					//Ext.state.Manager.getProvider().clearAll();
					location.href='/#/core/logout';
				}
			});
		    var navmenuTb = new Ext.Toolbar({
			    renderTo: "navmenu",
			    enableOverflow: true,
			    width: calculateNavMenuWidth(),
			    items: /*{/literal}*/{$menuitems}/*{literal}*/
		    });

		    if (newUING == '1') {
				Scalr.data.WindowContainer = new Ext.Container({
					runner: new Ext.util.TaskRunner(),
					renderTo: 'body-container',
					style: 'position: relative',
					autoRender: true,
					monitorResize: true,
					layout: new Ext.layout.ContainerLayout({
						zIndex: 101,
						setActiveItem: function (item, param) {
							var ai = this.activeItem, ct = this.container;
							item = ct.getComponent(item);

							if (item) {
								if (ai != item) {
									if (ai) {
										if (! item.scalrOptions.modal) {
											if (ai.scalrOptions.reload) {
												ai.destroy();
											} else if (! item.scalrOptions.modal)
												ai.hide();
										}  else
											ai.el.mask();

										// hide component under modal component
										if (ai.scalrOptions.modal) {
										// TODO: переписать, учитывая несколько модальных окон над основным и прочее =)
											ct.items.each(function () {
												if (this.rendered && !this.hidden) {
													this.el.unmask();

													if (this != item) {
														if (this.scalrOptions.reload)
															this.destroy();
														else
															this.hide();
													}

													var pr = Ext.get('top-title').child('.b[sLink=' + this.scalrUrl + ']');
													if (pr)
														pr.hide();
												}
											});
										}
									}

									this.activeItem = item;
									this.setSize(item);

									if (! item.scalrOptions.modal) {
										//Ext.get('top-title').show();
										if (ai) {
											var pr = Ext.get('top-title').child('.b[sLink=' + ai.scalrUrl + ']');
											if (pr)
												pr.hide();
										}

										var r = Ext.get('top-title').child('.b[sLink=' + item.scalrUrl + ']');
										if (r)
											r.show();
										else
											Ext.get('top-title').createChild({
												tag: 'div',
												'class': 'b',
												display: 'block',
												sLink: item.scalrUrl,
												html: item.title
											}).setVisibilityMode(Ext.Element.DISPLAY);
									}

									if (item.el) {
										item.el.unmask();
									}

									if (item.rendered && item.scalrReconfigure)
										item.scalrReconfigure(param);

									item.show();
									ct.doLayout();

									this.setSizeAfter(item);

									if (item.el) {
										if (item.scalrOptions.modal)
											item.el.setStyle({ 'z-index': this.zIndex, position: 'relative' });
									}

								} else {
									if (item.scalrReconfigure)
										item.scalrReconfigure(param);
								}
							}
						},
						setSize: function (comp) {
							var r = this.container.container.getStyleSize();
							var top = comp.scalrOptions.modal ? 5 : 0;
							if (comp.scalrOptions.maximize == 'all') {
								comp.setPosition(0, 0);
								comp.setSize(r);

							} else if (comp.scalrOptions.maximize == 'height') {
								comp.setPosition((r.width - comp.width) / 2, top);
								comp.setSize(comp.width, r.height - top * 2);

							} else if (comp.scalrOptions.maximize == 'maxHeight') {
								comp.setPosition((r.width - comp.width) / 2, top);

							} else {
								comp.setPosition((r.width - comp.width) / 2, top);
							}
						},

						setSizeAfter: function (comp) {
							var r = this.container.container.getStyleSize();
							var top = comp.scalrOptions.modal ? 5 : 0;

							if (comp.scalrOptions.maximize == 'maxHeight') {
								comp.body.setStyle({
									'max-height': Math.max(0, r.height - top * 2 - comp.getFrameHeight() - comp.body.getPadding('tb') - comp.body.getBorderWidth('tb')) + 'px'
								});
							}
						},

						onOwnResize: function () {
							if (this.activeItem) {
								this.setSize(this.activeItem);
								this.setSizeAfter(this.activeItem);
							}
						}
					})
				});

				Ext.EventManager.onWindowResize(Scalr.data.WindowContainer.layout.onOwnResize, Scalr.data.WindowContainer.layout);
		    }

			Scalr.Viewers.EventMessager = new Ext.util.Observable();
			Scalr.Viewers.EventMessager.addEvents('update', 'close');

			Scalr.Viewers.EventMessager.on('close', function() {
				if (history.length > 2)
					history.back(-1);
				else
					document.location.href = "html/index.htm";
			});

			// TODO: delete
			Ext.Ajax.handleResponse = function (response) {
				try {
					response.responseJson = Ext.decode(response.responseText);
				} catch (e) {};

				Ext.data.Connection.prototype.handleResponse.call(this, response);
			};

			//Ext.state.Manager.setProvider(new Scalr.state.StorageProvider()); @DELETE

		    Ext.EventManager.onWindowResize(function () {
		    	navmenuTb.setWidth(calculateNavMenuWidth());
			});

			Ext.Ajax.defaultHeaders = { 'X-Ajax-Scalr': 1 }; // TODO: delete, can use X-Requested-With: XMLHttpRequest
			Ext.Ajax.timeout = 60000;
			Ext.data.Connection.defaultHeaders = { 'X-Ajax-Scalr': 1 }; // TODO: delete

			/* TODO: delete
			Ext.apply(Ext.form.FormPanel, { initComponent: function () {
				console.log(this);
				alert('1');
				this.baseParams = this.baseParams || {};
				this.baseParams['X_Ajax_Scalr'] = 1;

				Ext.form.FormPanel.prototype.initComponent.call(this);
			}});*/
			
			//Ext.apply(Ext.form.FormPanel, { baseParams: { 'X_Ajax_Scalr': 1 }});


			// old code
			if (Scalr.data.InputParams && Scalr.data.InputParams.scalrMessages) {
				for (var i = 0; i < Scalr.data.InputParams.scalrMessages.length; i++) {
					var m = Scalr.data.InputParams.scalrMessages[i];
					Scalr.Viewers.Message.Add(m.message, '', 0, m.type);
				}
			}

			// TODO: delete
			 if (newUING == '') {
				Ext.Ajax.on('requestexception', function() {
					if (Ext.MessageBox.isVisible())
						Ext.MessageBox.hide();

					Scalr.Viewers.ErrorMessage('Cannot proceed your request at the moment. Please try again later.');
				});
			}

			if (! ("onhashchange" in window)) {
				// poller :(
				var orig = window.location.hash, poll = function() {
					if (document.location.hash != orig) {
						orig = document.location.hash;
						window.onhashchange();
					}

					poll.defer(400);
				};

				poll.defer(400);
			}

			window.onhashchange = function (e) {
				// prepare
				if (newUING == '') {
					// typical logic
					if (document.location.hash && document.location.hash != '#') {
						// redirect to default new index page
						document.location.href = '/' + document.location.hash;
						return;
					}
				} else {
					// new ui logic

					//Ext.select("html").setStyle("overflow", "hidden");
					Ext.select("body").setStyle("overflow", "hidden");

					// remove old title
					var el = Ext.get('top-title').child('.a');
					if (el) {
						el.remove();
						Ext.get('top-title').hide(); // run only once on load
					}

				}

				var h = window.location.hash.substring(1).split('?'), link = '', param = {}, loaded = false;
				if (window.location.hash && window.location.hash != '#' /* for IE */) {
					// only if hash not null
					if (h[0])
						link = h[0]; //.replace(/\//g, ' ').trim();

					if (h[1])
						param = Ext.urlDecode(h[1]);

					/*var lk = link.split(' ');
					if (lk.length > 2) {
						link = '/' + [lk[0], lk[2]].join('/');
						param['id'] = lk[1];
					} else
						link = '/' + link.replace(/ /g, '/');*/
				} else if (newUING != '') {
					document.location.href = "html/index.htm";
				}

				var cacheLink = function (link, cache) {
					var re = cache.replace(/\/\{[^\}]+\}/g, '/([^\\}]+)').replace(/\//g, '\\/'), fieldsRe = /\/\{([^\}]+)\}/g, fields = [];

					while ((elem = fieldsRe.exec(cache)) != null) {
						fields[fields.length] = elem[1];
					}

					return {
						scalrRegExp: new RegExp('^' + re + '$', 'g'),
						scalrParamFields: fields,
						scalrParamGets: function (link) {
							var pars = {}, reg = new RegExp(this.scalrRegExp), params = reg.exec(link);
							if (Ext.isArray(params))
								params.shift(); // delete first element

							for (var i = 0; i < this.scalrParamFields.length; i++)
								pars[this.scalrParamFields[i]] = Ext.isArray(params) ? params.shift() : '';

							return pars;
						}
					};
				};

				if (link) {
					// stop all runner tasks
					Ext.TaskMgr.stopAll();
					
					// check in cache
					Scalr.data.WindowContainer.items.each(function () {
						if (this.scalrRegExp.test(link)) {
							loaded = true;
							Ext.apply(param, this.scalrParamGets(link));
							Scalr.data.WindowContainer.layout.setActiveItem(this, param);

							return false;
						}
					});

					if (! loaded) {
						Scalr.Utils.CreateProcessBox({
							type: 'action',
							msg: '加载页面，请稍候 ...'
						});

						Ext.Ajax.request({
							url: link,
							params: param,
							disableCaching: false,
							success: function (response) {
								var r = function (response, debug) {
									var result = Ext.decode(response.responseText), obj, cacheId = response.getResponseHeader('X-Scalr-Cache-Id');

									if (result.success == true) {
										if (Ext.isDefined(result.module)) {
											result.module = "(function() { return " + result.module + "; })();";
											obj = eval(result.module);

											var cache = cacheLink(link, cacheId);
											Ext.apply(param, cache.scalrParamGets(link));

											var container = obj.create(param, result.moduleParams);
											if (Ext.isObject(container)) {
												container.style = container.style || {};
												Ext.apply(container.style, { position: 'absolute' });
												Ext.apply(container, cache);
												container.scalrOptions = container.scalrOptions || {};
												Ext.applyIf(container.scalrOptions, {
													'reload': true, // close window before show other one
													'modal': false, // mask prev window and show new one
													'maximize': '' // maximize which sides (all, width, height, none (default))
												});

												Scalr.data.WindowContainer.add(container);
												Scalr.data.WindowContainer.layout.setActiveItem(container);
											}
											// if obj contains autorefresh value, set a reload runner task
											if (obj.autorefresh) {
												var task = {
													run: function(){
														if(container.store)
														container.store.reload();
													},
													interval: obj.autorefresh
												};
												Ext.TaskMgr.start(task);
											}
										} else {
											var c = result.moduleName.page;

											var p = function(c, response, result) {
												var cache = cacheLink(link, cacheId);
												Ext.apply(param, cache.scalrParamGets(link));

												var container = Scalr.cache[c](param, result.moduleParams);
												if (Ext.isObject(container)) {
													container.style = container.style || {};
													Ext.apply(container.style, { position: 'absolute' });
													Ext.apply(container, cache);
													container.scalrOptions = container.scalrOptions || {};
													Ext.applyIf(container.scalrOptions, {
														'reload': true, // close window before show other one
														'modal': false, // mask prev window and show new one
														'maximize': '' // maximize which sides (all, width, height, none (default))
													});

													Scalr.data.WindowContainer.add(container);
													Scalr.data.WindowContainer.layout.setActiveItem(container);
												}
											}, n = p.createCallback(c, response, result);

											if (Ext.isDefined(Scalr.cache[c])) {
												n();
											} else {
												var sc = [ result.moduleName.file ];
												if (result.moduleRequires)
													sc = sc.concat(result.moduleRequires);

												Ext.Loader.load(sc, function () {
													n();
												});
											}
										}

									} else if (result.error) {
										Scalr.Viewers.ErrorMessage(result.error);
										Scalr.Viewers.EventMessager.fireEvent('close');
									}
								};

								Ext.Msg.hide();

								if (JSdebug != '') {
									r(response, true);
								} else {
									try {
										r(response, false);
									} catch (e) {
										Scalr.Viewers.ErrorMessage(e);
										Scalr.Viewers.EventMessager.fireEvent('close');
									}
								}
							},
							failure: function(response) {
								Ext.Msg.hide();
								if (response.status == 403)
									reloadPage = true;
							}
						});
					}

					if (e)
						e.preventDefault();
				}

				return;











				// close old
				if (Scalr.data.UrlCurrent && Scalr.data.UrlCache[Scalr.data.UrlCurrent]) {
					var r = Scalr.data.UrlCache[Scalr.data.UrlCurrent];
					if (r.objectParams.modal)
						;
					else if (r.objectParams.reload)
						r.object.close();
					else if (Ext.isObject(r.window))
						r.window.hide();
					else
						r.object.close();

					var el = Ext.get('top-title').child('.b[sLink=' + Scalr.data.UrlCurrent + ']');
					if (el) {
						el.setVisibilityMode(Ext.Element.DISPLAY);
						el.hide();
					}
					Scalr.data.UrlCurrent = '';
				}

				if (link) {

					//Ext.get('body-container').hide();
					//Ext.get('top-title').child('.a').setVisibilityMode(Ext.Element.DISPLAY);
					//Ext.get('top-title').child('.a').hide();

				} else {
					if (newUING != '')
						document.location.href = "html/index.htm";
				}
			};

			window.onhashchange();

			Ext.EventManager.onWindowResize(function (w, h) {
				var win = Scalr.data.UrlCurrent ? Scalr.data.UrlCache[Scalr.data.UrlCurrent].object.win : '';
				if (win) {
					if (win.maximizeHeight) {
						win.setSize(win.maximizeWidth ? (Ext.lib.Dom.getViewWidth() - 10) : win.width, Math.max(300, Ext.lib.Dom.getViewHeight() - 100));
						//win.setPosition(5, 95);
					}
				}
			});

/*  default 菜单等
		    if (session_environments)
		    {
				new Ext.Button({
					template: new Ext.Template(
						'<div id="top-environment-item"><img src="/images/ui-ng/icons/environment_16x16.png" style="float: left"><div style="float: left; padding-left: 6px;"></div></div>'
					),
					buttonSelector: 'div',
					renderTo: 'top-environment',
					text: session_environments.current,
					menu: new Ext.menu.Menu({
						items: session_environments.list,
						listeners: {
							'itemclick': function (item, e) {
								if (item.checked == false) {
									Ext.MessageBox.wait('Switching environment, please wait ...', 'Proccessing');
									Ext.Ajax.request({
										url: '/core/changeEnvironment/',
										params: { environmentId: item.envId },
										success: function (response) {
											if (response.responseJson.success == true) {
												document.location.reload();
											} else {
												Scalr.Viewers.ErrorMessage(response.responseJson.error);
											}
										}
									});
								}
							}
						}
					})
				});
		    }
*/
			{/literal}
			var errmsg = '';

			{if !$errmsg && $err}
				{assign var="errmsg" value='The following errors have occured:'}
			{/if}

			{if $errmsg != ''}
				errmsg = '{$errmsg|replace:"'":"\'"}';

				{if $err}
					errmsg += '<span style="color: #CB3216">';
						{foreach from=$err key=id item=field}
							errmsg += '<br />&bull;&nbsp;&nbsp;{$field|replace:"'":"\'"}';
						{/foreach}
					errmsg += '</span>';
				{/if}
				Scalr.Viewers.ErrorMessage(errmsg);
			{/if}

			{if $okmsg}
				Scalr.Viewers.SuccessMessage('{$okmsg}');
			{/if}

			{if $help}
				Ext.get('top-title-info').applyStyles('display: inline');
				new Ext.ToolTip({ldelim}
					target: 'top-title-info',
					html: '{$help|replace:"'":"\'"}',
					dismissDelay: 0
				{rdelim});
			{/if}

			{literal}
		});
		{/literal}
		</script>
	</div>
	<div id="topfoot">
		<div id="top-title"><div class="a" style="display: block">{$title} <img src="/images/ui-ng/icons/info_icon_14x14.png" id="top-title-info"></div></div>
		<div id="top-environment"></div>
		<div id="top-messages"></div>
		<div id="top-messages-icons"><img src="/images/ui-ng/icons/message/close.png" class="close" alt="Close all messages"><img src="/images/ui-ng/icons/message/eye.png" class="eye"></div>
	</div>
</div>

<div id="body-login" class="x-hide-display">
	<form method="POST" action="/login.php">
		<input type="text" name="login">
		<input type="password" name="pass">
		<button type="submit"></button>
	</form>
</div>

{if $newUING == '1'}
<div id="body-container" style="position: absolute; left: 0px; right: 0px; top: 72px; bottom: 5px;">
{else}
<div id="body-container">
	<div id="header_messages_container" style="margin-bottom:5px;">
		<a name="top"></a>
		{if $warnmsg}
			<div class="header-message warn-message">
				{$warnmsg}
			</div>
		{/if}

		{if $experimental}
			<div class="header-message warn-message">
				{t}This page contains new features that should be considered "experimental". <a href="http://support.scalr.net">Drop us a line</a> if you notice any issues.{/t}
			</div>
		{/if}
		{$aws_problems}

		<div class="header-message success-message" id="Webta_OkMsg" style="display: none"></div>
	</div>
	{if !$noheader}
		<form style="margin:0px;padding:0px;" name="frm" id="frm" action="{$form_action}" method="post" {if $upload_files}enctype="multipart/form-data"{/if} {if $onsubmit}onsubmit="{$onsubmit}"{/if}>
	{/if}
{/if}
