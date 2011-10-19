{
	create: function (loadParams, moduleParams) {
		var result = {};

		if (! Ext.isObject(moduleParams.platforms)) {
			Scalr.Viewers.ErrorMessage('你需要至少开启一个云平台');
			document.location.href = moduleParams['environment'];
			return;
		}

		var platforms = [];
		for (i in moduleParams.platforms)
			platforms[platforms.length] = new Ext.Button({
				style: 'padding: 10px',
				enableToggle: true,
				allowDepress: false,
				toggleGroup: 'platform',
				platform: i,
				text: moduleParams.platforms[i],
				cls: 'x-btn-text-icon',
				scale: 'ultra-large',
				icon: '/images/ui-ng/icons/platform/' + i + '_64x64.png',
				iconAlign: 'top',
				toggleHandler: function () {
					if (this.pressed) {
						result['platform'] = this.platform;
						panel.stepNext(panel.layout.activeItem);
						panel.findOne('itemId', 'step1').setTitle('第一步 - 选择平台 [' + this.text + ']');
					} else {
						panel.stepChanged(panel.layout.activeItem);
					}
				}
			});

		var checkboxBehaviorListener = function(checkbox, checked) {
			var value = '';

			if (this.inputValue == 'mysql')
			{
				if (checked)
					panel.findOne('itemId', 'softwareSet').show();
				else
					panel.findOne('itemId', 'softwareSet').hide();
			}
			
			if (this.inputValue == 'app') {
				if (checked)
					panel.findOne('itemId', 'settings-behaviors-www').disable();
				else
					panel.findOne('itemId', 'settings-behaviors-www').enable();
			}

			if (this.inputValue == 'www') {
				if (checked)
					panel.findOne('itemId', 'settings-behaviors-app').disable();
				else
					panel.findOne('itemId', 'settings-behaviors-app').enable();
			}

			panel.findOne('itemId', 'settings-behaviors').items.each(function() {
				if (this.checked && value != '')
					value = 'Mixed images'
				else if (this.checked) {
					if (this.inputValue == 'app')
						value = 'Application servers'
					else if (this.inputValue == 'base')
						value = 'Base images'
					else if (this.inputValue == 'mysql')
						value = 'Database servers'
					else if (this.inputValue == 'www')
						value = 'Load balancers'
					else if (this.inputValue == 'memcached')
						value = 'Caching servers'
					//else if (this.inputValue == 'cassandra')
					//	value = 'Database servers';
				}
			});

			if (this.inputValue == 'base') {
				if (this.checked) {
					panel.findOne('itemId', 'settings-behaviors-app').disable();
					panel.findOne('itemId', 'settings-behaviors-mysql').disable();
					panel.findOne('itemId', 'settings-behaviors-www').disable();
					panel.findOne('itemId', 'settings-behaviors-memcached').disable();
				} else {
					panel.findOne('itemId', 'settings-behaviors-app').enable();
					panel.findOne('itemId', 'settings-behaviors-mysql').enable();
					panel.findOne('itemId', 'settings-behaviors-www').enable();
					panel.findOne('itemId', 'settings-behaviors-memcached').enable();
				}
			} else {
				if (this.checked || value) {
					panel.findOne('itemId', 'settings-behaviors-base').disable();
				} else {
					panel.findOne('itemId', 'settings-behaviors-base').enable();
				}
			}

			panel.findOne('itemId', 'settings-group').setValue(value);
		};

		var panel = new Ext.Panel({
			title: '服务角色生成器',
			scalrOptions: {
				'maximize': 'maxHeight'
			},
			autoScroll: true,
			width: 700,
			plugins: [ new Scalr.Viewers.Plugins.findOne() ],
			layout: 'accordion',
			layoutConfig: {
				hideCollapseTool: true,
				fill: false
			},
			defaults: {
				border: false,
				toggleCollapse: function (animate) {
					if (this.collapsed)
						this.expand(animate);
					return this;
				}
			},
			stepChanged: function (comp) {
				var i = this.items.indexOf(comp);
				for (var len = this.items.length, i = i + 1; i < len; i++) 
					this.items.get(i).disable();
			},
			stepNext: function (comp) {
				var i = this.items.indexOf(comp) + 1;
				if (i < this.items.length) {
					this.items.get(i).enable();
					this.items.get(i).expand();
				}
			},
			items: [{
				title: '第一步 - 选择云平台',
				layout: 'column',
				itemId: 'step1',
				items: platforms
			}, {
				title: '第二步 - 选择操作系统',
				disabled: true,
				itemId: 'step2',
				layout: 'anchor',
				items: [{
					xtype: 'fieldset',
					style: 'margin: 10px',
					title: '选择位置和系统架构',
					layout: 'column',
					items: [{
						xtype: 'combo',
						allowBlank: false,
						editable: false,
						forceSelection: true,
						mode: 'local',
						triggerAction: 'all',
						selectOnFocus: false,

						fieldLabel: 'Location',
						columnWidth: .50,
						itemId: 'location',
						store: [],
						listeners: {
							select: function (combo) {
								result['location'] = combo.getValue();
								panel.findOne('itemId', 'step2').applyFilter();
							}
						}
					}, {
						xtype: 'container',
						columnWidth: .15,
						html: '&nbsp;'
					}, {
						xtype: 'radiogroup',
						itemId: 'architecture',
						columnWidth: .20,
						layout: 'anchor',
						items: [{
							boxLabel: 'i386',
							name: 'architecture',
							inputValue: 'i386'
						}, {
							boxLabel: 'x86_64',
							name: 'architecture',
							inputValue: 'x86_64'
						}],
						listeners: {
							change: function (group, radio) {
								result['architecture'] = radio.inputValue;
								panel.findOne('itemId', 'step2').applyFilter();
							}
						}
					}]
				}, {
					xtype: 'panel',
					layout: 'column',
					border: false,
					style: 'padding: 10px',
					itemId: 'images'
				}],
				applyFilter: function () {
					var architecture = result['architecture'], location = result['location'], r = moduleParams.images[result['platform']];

					panel.findOne('itemId', 'images').items.each(function () {
						var d = true;
						for (i in r) {
							if (r[i].name == this.text && r[i].location == location && r[i].architecture == architecture) {
								d = false;
								this.imageId = i;
								break;
							}
						}

						if (this.pressed) {
							result['imageId'] = this.imageId;
							panel.findOne('itemId', 'step2').setTitle('第二步 - 选择操作系统 [' + this.text + ' (' + result['architecture'] + ') 位于 ' + result['location'] + ']');
						}

						if (d) {
							if (this.pressed)
								this.toggle(false);

							this.disable(); // deactivate, if selected
						} else
							this.enable();
					});
				},
				listeners: {
					enable: function () {
						var r = moduleParams.images[result['platform']], d = [], l = [], cont = panel.findOne('itemId', 'images');

						cont.removeAll();
						panel.findOne('itemId', 'step2').setTitle('第二步 - 选择操作系统');
						var added = {};
						for (i in r) {
							if (! added[r[i].name])
								cont.add(new Ext.Button({
									style: 'padding: 5px',
									enableToggle: true,
									allowDepress: false,
									toggleGroup: 'image',
									text: r[i].name,
									cls: 'x-btn-text-icon',
									scale: 'ultra-large',
									icon: '/images/ui-ng/icons/os/' + r[i]['os_dist'] + '_64x64.png',
									iconAlign: 'top',
									toggleHandler: function () {
										if (this.pressed) {
											result['imageId'] = this.imageId;
											panel.stepNext(panel.layout.activeItem);
											panel.findOne('itemId', 'step2').setTitle('第二步 - 选择操作系统 [' + this.text + ' (' + result['architecture'] + ') 位于 ' + result['location'] + ']');
										} else {
											panel.stepChanged(panel.layout.activeItem);
											panel.findOne('itemId', 'step2').setTitle('第二步 - 选择操作系统');
										}
									}
								}));

							added[r[i].name] = true;

							if (l.indexOf(r[i]['location']) == -1)
								l.push(r[i]['location']);
						}
						cont.doLayout();

						var c = panel.findOne('itemId', 'location');
						c.store.loadData(l);
						c.setValue(result['platform'] == 'ec2' ? 'us-east-1' : l[0]);
						panel.findOne('itemId', 'architecture').setValue('architecture', 'x86_64');

						result['architecture'] = 'x86_64';
						result['location'] = panel.findOne('itemId', 'location').getValue();

						panel.findOne('itemId', 'step2').applyFilter();
					}
				}
			}, {
				title: '第三步 - 设置',
				itemId: 'step3',
				disabled: true,
				items: [{
					xtype: 'fieldset',
					style: 'margin: 10px',
					title: '基础设置',
					labelWidth: 80,
					items: [{
						xtype: 'textfield',
						fieldLabel: '服务角色名',
						itemId: 'settings-rolename',
						anchor: '-20',
						validator: function (value) {
							var r = /^[A-z0-9-]+$/, r1 = /^-/, r2 = /-$/;
							if (r.test(value) && !r1.test(value) && !r2.test(value))
								return true;
							else
								return 'Illegal name';
						}
					}]
				}, {
					xtype: 'fieldset',
					style: 'margin: 10px',
					title: '用途',
					labelWidth: 80,
					items: [{
						xtype: 'checkboxgroup',
						columns: 4,
						fieldLabel: '用途',
						itemId: 'settings-behaviors',
						anchor: '-20',
						allowBlank: false,
						items: [{
							boxLabel: 'Base',
							inputValue: 'base',
							itemId: 'settings-behaviors-base',
							name: 'behaviors[]',
							handler: checkboxBehaviorListener
						}, {
							boxLabel: 'MySQL',
							inputValue: 'mysql',
							itemId: 'settings-behaviors-mysql',
							name: 'behaviors[]',
							handler: checkboxBehaviorListener
						}, {
							boxLabel: 'Apache',
							inputValue: 'app',
							itemId: 'settings-behaviors-app',
							name: 'behaviors[]',
							handler: checkboxBehaviorListener
						}, {
							boxLabel: 'Nginx',
							inputValue: 'www',
							itemId: 'settings-behaviors-www',
							name: 'behaviors[]',
							handler: checkboxBehaviorListener
						}, {
							boxLabel: 'Memcached',
							inputValue: 'memcached',
							itemId: 'settings-behaviors-memcached',
							name: 'behaviors[]',
							handler: checkboxBehaviorListener
						}/*, {
							boxLabel: 'Cassandra',
							inputValue: 'cassandra',
							name: 'behaviors[]',
							handler: checkboxBehaviorListener
						}*/]
					}, {
						xtype: 'textfield',
						readOnly: true,
						fieldLabel: '组',
						itemId: 'settings-group',
						anchor: '-20',
						width: 200
					}]
				}, {
					xtype: 'fieldset',
					style: 'margin: 10px',
					title: '软件',
					itemId:'softwareSet',
					hidden:true,
					labelWidth: 80,
					items: [{
						fieldLabel: 'MySQL',
						xtype: 'combo',
						allowBlank: false,
						editable: false, 
				        store: [['mysql', 'MySQL 5.x'], ['percona', 'Percona Server 5.1']],
				        value: 'mysql',
				        hiddenName:'mysqlServerType',
				        typeAhead: false,
				        mode: 'local',
				        triggerAction: 'all',
				        selectOnFocus:false,
				        width:200
					}]
				}],
				buttonAlign: 'center',
				buttons: [{
					text: '生成',
					handler: function () {
						var valid = panel.findOne('itemId', 'settings-rolename').isValid();
						valid = panel.findOne('itemId', 'settings-behaviors').isValid() && valid;

						if (valid) {
							Ext.Msg.wait('请稍候 ...', '生成中 ...');

							result['behaviors'] = [];
							panel.findOne('itemId', 'settings-behaviors').items.each(function() {
								if (this.checked)
									result['behaviors'].push(this.inputValue);
							});
							result['behaviors'] = Ext.encode(result['behaviors']);
							result['roleName'] = panel.findOne('itemId', 'settings-rolename').getValue();

							result['mysqlServerType'] = panel.findOne('hiddenName', 'mysqlServerType').getValue();
							
							Ext.Ajax.request({
								url: '/roles/xBuild',
								params: result,
								success: function (response, options) {
									var result = Ext.decode(response.responseText);
									if (result.success == true) {
										document.location.href = '#/bundletasks/'+result.bundleTaskId+'/view';
									} else {
										Scalr.Viewers.ErrorMessage(result.error);
									}

									Ext.Msg.hide();
								}
							});
						}
					}
				}]
			}]
		});

		return panel;
	}
}
