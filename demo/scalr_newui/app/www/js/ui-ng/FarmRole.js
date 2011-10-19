Ext.ns('Scalr.FarmRole');

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

		this.farmRolesStore = new Ext.data.JsonStore({
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

		this.loadMask = new Ext.LoadMask(Ext.getBody(), { msg: "请稍候..." });
		this.saveMask = new Ext.LoadMask(Ext.getBody(), { msg: "保存中请稍候..." });

		Scalr.FarmRole.Farm.superclass.constructor.call(this);
	},

	loadFarm: function() {
		this.loadMask.show();
		Ext.Ajax.request({
			url: '/server/farm_builder_roles_list.php?list=farm_roles',
			params: {
				farmid: this.farmId
			},
			success: this.onLoadFarm,
			scope: this
		});
	},

	onLoadFarm: function(response, options) {
		var result = Ext.decode(response.responseText);

		if (result.farm_roles)
			this.farmRolesStore.loadData(result.farm_roles);

		this.panel = this.createPanel();
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
						document.location.href = '/farms_builder.php?saved=true&id=' + (result.farm_id);
					else {
						Scalr.Viewers.ErrorMessage('Farm is now launching. It will take few minutes to start all servers.');
						document.location.href = '#/farms/' + result.farm_id + '/view';
					}
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
