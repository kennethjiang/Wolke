Ext.ns('Scalr.FarmRole');

Scalr.Viewers.FarmRolesEditPanel = Ext.extend(Ext.Container, {
	layout: 'card',
	layoutConfig: {
		deferredRender: true
	},

	currentRole: null,

	getLayoutTarget: function () {
		return this.elcontent;
	},

	addRoleDefaultValues: function (record) {
		var settings = record.get('settings');

		this.items.each(function(item) {
			if (item.isEnabled(record))
				Ext.apply(settings, item.getDefaultValues(record));
		});

		record.set('settings', settings);
	},

	setCurrentRole: function (record) {
		this.currentRole = record;
	},

	render: function (container, position) {
		this.initItems();

		var order = [ 'scaling', 'mysql', 'balancing', 'placement', 'rsplacement', 'params', 'rds', 'eips',
			'ebs', 'dns', 'scripting', 'timeouts', 'cloudwatch', 'vpc', 'euca', 'nimbula', 'servicesconfig' ];

		for (var i = 0; i < order.length; i++)
			this.items.add(Scalr.cache['Scalr.ui.farms.builder.tabs.' + order[i]](this.moduleParams));

		Scalr.Viewers.FarmRolesEditPanel.superclass.render.call(this, container, position);

		this.eltabs = this.el.createChild({ tag: 'div', 'class': 'viewers-farmrolesedit-tabs' });
		this.elcontent = this.el.createChild({ tag: 'div', 'class': 'viewers-farmrolesedit-content' });
		var size = this.el.getSize();
		this.eltabs.setHeight(size.height);
		this.elcontent.setSize(size.width - 201, size.height);

		this.items.each(function(item) {
			var el = this.eltabs.createChild({ tag: 'div', html: '<span>' + item.tabTitle + '</span>', elementid: item.id, 'class': 'viewers-farmrolesedit-tab' });
			el.setVisibilityMode(Ext.Element.DISPLAY);
			el.on('click', function (e) {
				var t = e.getTarget('div'), childs = this.eltabs.query('div');

				for (var i = 0, len = childs.length; i < len; i++)
					Ext.get(childs[i]).removeClass('viewers-farmrolesedit-tab-selected');

				this.layout.setActiveItem(t.getAttribute('elementid'));
				Ext.get(t).addClass('viewers-farmrolesedit-tab-selected');
			}, this);

			item.loadMask = this.loadMask;
			item.farmId = this.farmId;
		}, this);

		this.on('resize', function () {
			var size = this.el.getSize();
			this.eltabs.setHeight(size.height);
			this.elcontent.setSize(size.width - 201, size.height);
		}, this);

		this.on('activate', function () {
			var record = this.currentRole;
			this.items.each(function (item) {
				item.setCurrentRole(record);

				if (item.isEnabled(record)) {
					this.eltabs.child("[elementid=" + item.id + "]").show();
				} else {
					this.eltabs.child("[elementid=" + item.id + "]").hide();
				}
			}, this);

			var childs = this.eltabs.query('div');
			for (var i = 0, len = childs.length; i < len; i++)
				Ext.get(childs[i]).removeClass('viewers-farmrolesedit-tab-selected');

			var i = 0;
			this.items.each(function (item) {
				if (item.isEnabled(record)) {
					this.layout.setActiveItem(item);
					Ext.get(childs[i]).addClass('viewers-farmrolesedit-tab-selected');
					return false;
				}
				i++;
			}, this);
		}, this);

		this.on('deactivate', function () {
			if (this.layout.activeItem) {
				this.layout.activeItem.hide();
				this.layout.activeItem.fireEvent('deactivate', this.layout.activeItem);
				this.layout.activeItem = null;
			}
		}, this);
	}
});

Scalr.FarmRole.Farm = Ext.extend(Ext.util.Observable, {
	farmId: null,
	farmRoleId: null,
	farmRolesStore: null,

	selected_role_id: 0,
	rolesStore: null,
	platforms: null,
	groups: null,
	arch64bitTypes: null,
	arch32bitTypes: null,
	farmRoles: null,
	defaultScalingAlgos: null,

	// boolean flag
	addRolesLoaded: false,

	panel: null,
	loadMask: null,

	constructor: function (farmId, roleId) {
		this.farmId = farmId;
		this.farmRoleId = roleId;

		this.rolesStore = new Ext.data.JsonStore({
			idProperty: 'role_id',
			fields: [
				{ name: 'role_id', type: 'int' },
				'arch',
				'group',
				'name',
				'generation',
				'behaviors',
				'origin',
				{ name: 'isstable', type: 'boolean' },
				'platforms',
				'locations',
				'os',
				'tags'
			]
		});

		this.farmRolesStore = Scalr.utils.CreateStore([], {
			idProperty: 'id',
			fields: [
				'id',
				{ name: 'new', type: 'boolean' },
				'role_id',
				'platform',
				'generation',
				'cloud_location',
				'arch',
				'name',
				'group',
				'behaviors',
				'launch_index',
				'settings',
				'scaling',
				'scripting',
				'config_presets',
				'tags'
			]
		});

		this.loadMask = new Ext.LoadMask(Ext.getBody(), { msg: "请稍受..." });
		this.saveMask = new Ext.LoadMask(Ext.getBody(), { msg: "保存中请稍候..." });

		Scalr.FarmRole.Farm.superclass.constructor.call(this);
	},

	loadFarm: function() {
		this.loadMask.show();
		this.panel = this.createPanel();
		Ext.Ajax.request({
			url: '/farms/' + this.farmId + '/builder/xGetFarm',
			success: this.onLoadFarm,
			scope: this
		});

		return this.panel;
	},

	onLoadFarm: function(response, options) {
		var result = Ext.decode(response.responseText);

		if (result.roles)
			this.farmRolesStore.loadData(result.roles);

		this.panel.getComponent('roles').getComponent('roles').on('addrole', function () {
			if (! this.addRolesLoaded) {
				this.addRolesLoaded = true;
				this.loadRoles();
			} else
				this.panel.getComponent('roles').getComponent('card').layout.setActiveItem('roles');
		}, this);
		this.panel.buttons[0].setHandler(this.save, this);
		this.panel.buttons[1].setHandler(function () {
			document.location.href = '#/farms/view';
		});
		this.loadMask.hide();

		// auto select role in farm
		if (this.farmRoleId != '0') {
			this.panel.setActiveTab('roles');
			this.panel.getComponent('roles').getComponent('roles').dataView.select(this.farmRolesStore.find('role_id', this.farmRoleId));
		}

		if (result.farm) {
			var farm_settings = this.panel.getComponent('farm').getForm();
			farm_settings.findField('farm_name').setValue(result.farm.name);
			farm_settings.findField('farm_description').setValue(result.farm.description);
			this.panel.getComponent('farm').getComponent('settings').getComponent('farm_roles_launch_order').setValue(result.farm.roles_launch_order);
		}
	},

	loadRoles: function() {
		this.loadMask.show();
		Ext.Ajax.request({
			url: '/server/farm_builder_roles_list.php?list=roles',
			success: this.onLoadRoles,
			scope: this
		});
	},

	onLoadRoles: function(response, options) {
		var result = Ext.decode(response.responseText);

		if (result.roles)
			this.rolesStore.loadData(result.roles);

		if (result.arch64bitTypes)
			this.arch64bitTypes = result.arch64bitTypes;

		if (result.arch32bitTypes)
			this.arch32bitTypes = result.arch32bitTypes;

		if (result.defaultScalingAlgos)
			this.defaultScalingAlgos = result.defaultScalingAlgos;

		this.panel.getComponent('roles').getComponent('card').add(this.createAddRolePanel());
		this.panel.getComponent('roles').getComponent('card').layout.setActiveItem('roles');

		this.loadMask.hide();
	},

	save: function () {
		var p = {}, i;

		this.panel.getComponent('roles').getComponent('roles').dataView.clearSelections();
		this.panel.getComponent('roles').getComponent('roles').clearFilter();
		this.saveMask.show();

		// farm settings
		var farm_settings = this.panel.getComponent('farm').getForm(), farm_settings_order = this.panel.getComponent('farm').getComponent('settings').getComponent('farm_roles_launch_order').getValue();
		p['farm[id]'] = this.farmId;
		p['farm[name]'] = farm_settings.findField('farm_name').getValue();
		p['farm[description]'] = farm_settings.findField('farm_description').getValue();
		p['farm[roles_launch_order]'] = farm_settings_order ? farm_settings_order.inputValue : 0;

		i = 0;
		this.farmRolesStore.each(function (rec) {
			var settings = rec.get('settings'), sets = {};

			sets = {
				role_id: rec.get('role_id'),
				launch_index: rec.get('launch_index'),
				platform: rec.get('platform'),
				cloud_location: rec.get('cloud_location'),
				settings: rec.get('settings'),
				scaling: rec.get('scaling'),
				scripting: rec.get('scripting'),
				config_presets: rec.get('config_presets')
			};

			if (Ext.isObject(rec.get('params'))) {
				sets['params'] = rec.get('params');
			}

			p['roles[' + i + ']'] = Ext.encode(sets);
			i++;
		});

		Ext.Ajax.request({
			url: '/server/farm_creator.php',
			params: p,
			success: function (response) {
				var result = Ext.decode(response.responseText);
				if (result && result.success == true) {
					if (this.farmId != '')
						Scalr.Message.Success('Farm successfully saved');
					else
						Scalr.Message.Success('Farm is now launching. It will take few minutes to start all servers.');

					this.saveMask.hide();
					document.location.href = '#/farms/' + result.farm_id + '/view';

				} else {
					if (result && result.error)
						Scalr.Viewers.ErrorMessage(result.error);
					this.saveMask.hide();
				}
			},
			scope: this
		});
	},

	/* Override - { return Panel; } */
	createPanel: null,
	/* Override - { return Panel; } */
	createAddRolePanel: null
});

Scalr.regPage('Scalr.ui.farms.builder', function (loadParams, moduleParams) {
	var farm = new Scalr.FarmRole.Farm(moduleParams['farmId'], moduleParams['roleId']);

	farm.platforms = moduleParams['platforms'];
	farm.locations = moduleParams['locations'];
	farm.groups = moduleParams['groups'];

	var currentTimeZone = 'Current time zone is: <span style="font-weight: bold;">' + moduleParams['currentTimeZone'] + '</span> (' + moduleParams['currentTime'] + '). <a target="_blank" href="#/environments/' + moduleParams['currentEnvId'] + '/edit">Click here</a> if you want to change it.</div>';
	var moduleTabParams = { 'currentTimeZone': currentTimeZone };

	farm.createPanel = function() {
		var panel = new Ext.TabPanel({
			activeTab: 0,
			border: false,
			bodyStyle: 'border-style: none solid none solid;',
			items: [{
				title: '服务器组',
				itemId: 'farm',
				xtype: 'form',
				layout: 'form',
				style: {
					'padding': '10px'
				},
				items: [{
					xtype: 'fieldset',
					title: '基本信息',
					items: [{
						xtype: 'textfield',
						name: 'farm_name',
						fieldLabel: '名称',
						width: 500
					}, {
						xtype: 'textarea',
						name: 'farm_description',
						fieldLabel: '描述',
						width: 500,
						height: 100
					}]
				}, {
					xtype: 'fieldset',
					title: '设置',
					itemId: 'settings',
					items: [{
						xtype: 'radiogroup',
						hideLabel: true,
						itemId: 'farm_roles_launch_order',
						columns: 1,
						items: [{
							boxLabel: '同时启动所有服务角色 ',
							name: 'farm_roles_launch_order',
							checked: true,
							inputValue: '0'
						}/*, {
							boxLabel: 'Launch roles one-by-one in the order I set (slower) ',
							name: 'farm_roles_launch_order',
							inputValue: '1'
						}*/]
					}]
				}]
			}, {
				title: '服务角色',
				itemId: 'roles',
				layout: 'vbox',
				layoutConfig: {
					align: 'stretch',
					pack: 'start'
				},
				items: [
					new Scalr.Viewers.SelRolesViewer({
						height: 130,
						title: 'Roles',
						itemId: 'roles',
						border: false,
						store: this.farmRolesStore,
						listeners: {
							'deleterole': function(record) {
								this.farmRolesStore.remove(record);
							},
							'selectionchange': function(selections) {
								if (selections[0]) {
									this.panel.getComponent('roles').getComponent('card').layout.setActiveItem('blank');
									this.panel.getComponent('roles').getComponent('card').getComponent('edit').setCurrentRole(selections[0]);
									this.panel.getComponent('roles').getComponent('card').layout.setActiveItem('edit');
								} else {
									this.panel.getComponent('roles').getComponent('card').layout.setActiveItem('blank');
								}
							},
							scope: this
						}
					}), {
						xtype: 'panel',
						height: 19,
						html: '&nbsp',
						bodyStyle: 'background-color: #DFE8F6; border-style: solid none solid none;'
					}, {
						xtype: 'panel',
						layout: 'card',
						itemId: 'card',
						border: false,
						flex: 1,
						activeItem: 'blank',
						items: [{
							xtype: 'panel',
							itemId: 'blank',
							border: false
						}, new Scalr.Viewers.FarmRolesEditPanel({
							itemId: 'edit',
							border: false,
							loadMask: this.loadMask,
							farmId: this.farmId,
							moduleParams: moduleTabParams
						})]
					}
				]
			}],
			footerCssClass: 'viewers-panel-footer',
			buttonAlign: 'center',
			buttons: [{
				text: '保存'
			}, {
				text: '取消'
			}]
		});

		var autoSize = new Scalr.Viewers.autoSize();
		autoSize.init(panel);

		return panel;
	}

	farm.createAddRolePanel = function () {
		var panel = new Scalr.Viewers.AllRolesViewer({
			itemId: 'roles',
			platforms: this.platforms,
			groups: this.groups,
			locations: this.locations,
			store: this.rolesStore,
			legacyRoles: this.farmId == 0 ? false : true,
			listeners: {
				addrole: function(rec) {
					if (
						this.farmRolesStore.findBy(function(record) {
							if (
								record.get('platform') == rec.platform &&
								record.get('role_id') == rec.role_id &&
								record.get('cloud_location') == rec.cloud_location
							)
								return true;
						}) != -1
					) {
						Scalr.Viewers.ErrorMessage('服务角色 "' + rec['name'] + '" 已存在');
						return;
					}

					// check before adding
					if (rec.behaviors.match('mysql')) {
						if (
							this.farmRolesStore.findBy(function(record) {
								if (record.get('behaviors').match('mysql'))
									return true;
							}) != -1
						) {
							Scalr.Viewers.ErrorMessage('一个服务器组仅能添加一个MySQL服务角色');
							return;
						}
					}

					rec['new'] = true;
					rec['settings'] = {};
					var record = new this.farmRolesStore.recordType(rec);
					this.panel.getComponent('roles').getComponent('card').getComponent('edit').addRoleDefaultValues(record);
					this.farmRolesStore.add(record);
					Scalr.Viewers.SuccessMessage('服务角色 "' + rec['name'] + '" 已添加', '', 4);
				},
				scope: this
			}
		});

		return panel;
	};

	return new Ext.Panel({
		scalrOptions: {
			'maximize': 'all'
		},
		title: '服务器组生成器',
		layout: 'fit',
		items: farm.loadFarm()
	});
});
