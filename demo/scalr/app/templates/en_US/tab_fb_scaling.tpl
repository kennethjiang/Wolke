{literal}
new Scalr.Viewers.FarmRolesEditTab({
	tabTitle: 'Scaling options',
	layout: 'form',
	labelWidth: 150,

	loaded: false,
	algos: {},

	{/literal}
	{include file="tab_fb_scaling_algos.tpl"},
	{literal}

	isEnabled: function (record) {
		return record.get('platform') != 'rds';
	},

	getDefaultValues: function (record) {
		return {
			'scaling.min_instances': 1,
			'scaling.max_instances': 2,
			'scaling.polling_interval': 1,
			'scaling.keep_oldest': 0,
			'scaling.safe_shutdown': 0
		};
	},

	activateTab: function () {
		new Ext.ToolTip({
			target: this.findOne('name', 'scaling.min_instances_help').id,
			dismissDelay: 0,
			html: "Always keep at least this many running instances."
		});

		new Ext.ToolTip({
			target: this.findOne('name', 'scaling.max_instances_help').id,
			dismissDelay: 0,
			html: "Scalr will not launch more instances."
		});

		new Ext.ToolTip({
			target: this.findOne('name', 'scaling.safe_shutdown_help').id,
			dismissDelay: 0,
			html: "Scalr will terminate instance ONLY if script '/usr/local/scalarizr/hooks/auth-shutdown' return 1. "+ 
				"If script not found or return any other value Scalr WON'T terminate this server."
		});

		this.findOne('name', 'scaling.upscale.timeout_enabled').on('check', function (checkbox, checked) {
			if (checked)
				this.findOne('name', 'scaling.upscale.timeout').enable();
			else
				this.findOne('name', 'scaling.upscale.timeout').disable();
		}, this);

		this.findOne('name', 'scaling.downscale.timeout_enabled').on('check', function (checkbox, checked) {
			if (checked)
				this.findOne('name', 'scaling.downscale.timeout').enable();
			else
				this.findOne('name', 'scaling.downscale.timeout').disable();
		}, this);

		this.findOne('name', 'enable_scaling_add').getEl().on('click', function () {
			var combo = this.findOne('name', 'enable_scaling'), value = combo.store.getById(combo.getValue());
			if (value) {
				var items = this.findOne('itemId', 'algos').items.items;

				for (var i = 0; i < items.length; i++) {
					if (items[i].alias == value.get('alias')) {
						Scalr.Viewers.ErrorMessage('This algoritm already added');
						return;
					}
				}

				if (value.get('alias') == 'time' && items.length) {
					Scalr.Viewers.ErrorMessage('This algoritm cannot be used with others');
					return;
				} else if (value.get('alias') != 'time' && items.length) {
					for (var i = 0; i < items.length; i++) {
						if (items[i].alias == 'time') {
							Scalr.Viewers.ErrorMessage("This algoritm cannot be used with 'Time and Day of week'");
							return;
						}
					}
				}

				this.addAlgoTab(value, {}, true);
				combo.reset();
			} else {
				combo.markInvalid();
			}
		}, this);
	},

	showTab: function (record) {
		if (! this.loaded) {
			this.loadMask.show();
			Ext.Ajax.request({
				url: '/server/farm_builder_roles_list.php?list=scaling_metrics',
				success: function(response, options) {
					var result = Ext.decode(response.responseText);

					if (result.metrics)
						this.findOne('name', 'enable_scaling').store.loadData(result.metrics);

					this.loadMask.hide();
					this.loaded = true;
					this.showTab.call(this, record);
				},
				scope: this
			});
		} else {
			var settings = record.get('settings'), scaling = record.get('scaling');

			if (record.get('generation') == 2)
				this.findOne('itemId', 'scaling.safe_shutdown_compositefield').show();
			else
				this.findOne('itemId', 'scaling.safe_shutdown_compositefield').hide();

			this.findOne('name', 'scaling.min_instances').setValue(settings['scaling.min_instances'] || 1);
			this.findOne('name', 'scaling.max_instances').setValue(settings['scaling.max_instances'] || 2);
			this.findOne('name', 'scaling.polling_interval').setValue(settings['scaling.polling_interval'] || 1);
			this.findOne('name', 'scaling.keep_oldest').setValue(settings['scaling.keep_oldest'] == 1 ? true : false);
			this.findOne('name', 'scaling.safe_shutdown').setValue(settings['scaling.safe_shutdown'] == 1 ? true : false);

			if (settings['scaling.upscale.timeout_enabled'] == 1) {
				this.findOne('name', 'scaling.upscale.timeout_enabled').setValue(true);
				this.findOne('name', 'scaling.upscale.timeout').enable();
			} else {
				this.findOne('name', 'scaling.upscale.timeout_enabled').setValue(false);
				this.findOne('name', 'scaling.upscale.timeout').disable();
			}
			this.findOne('name', 'scaling.upscale.timeout').setValue(settings['scaling.upscale.timeout'] || 10);

			if (settings['scaling.downscale.timeout_enabled'] == 1) {
				this.findOne('name', 'scaling.downscale.timeout_enabled').setValue(true);
				this.findOne('name', 'scaling.downscale.timeout').enable();
			} else {
				this.findOne('name', 'scaling.downscale.timeout_enabled').setValue(false);
				this.findOne('name', 'scaling.downscale.timeout').disable();
			}
			this.findOne('name', 'scaling.downscale.timeout').setValue(settings['scaling.downscale.timeout'] || 10);
			this.findOne('name', 'enable_scaling').reset();

			// algos
			this.findOne('itemId', 'algos').removeAll();
			var store = this.findOne('name', 'enable_scaling').store;

			if (Ext.isObject(scaling)) {
				for (var i in scaling) {
					this.addAlgoTab(store.getById(i), scaling[i], false);
				}
			}

			if (this.findOne('itemId', 'algos').items.length) {
				this.findOne('itemId', 'algos').show();
				this.findOne('itemId', 'algos_disabled').hide();
				this.findOne('itemId', 'algos').activate(this.findOne('itemId', 'algos').items.items[0]);
			} else {
				this.findOne('itemId', 'algos').hide();
				this.findOne('itemId', 'algos_disabled').show();
			}
		}
	},

	hideTab: function (record) {
		var settings = record.get('settings');
		var scaling = {};

		settings['scaling.min_instances'] = this.findOne('name', 'scaling.min_instances').getValue();
		settings['scaling.max_instances'] = this.findOne('name', 'scaling.max_instances').getValue();
		settings['scaling.polling_interval'] = this.findOne('name', 'scaling.polling_interval').getValue();
		settings['scaling.keep_oldest'] = this.findOne('name', 'scaling.keep_oldest').getValue() == true ? 1 : 0;
		settings['scaling.safe_shutdown'] = this.findOne('name', 'scaling.safe_shutdown').getValue() == true ? 1 : 0;

		if (this.findOne('name', 'scaling.upscale.timeout_enabled').getValue()) {
			settings['scaling.upscale.timeout_enabled'] = 1;
			settings['scaling.upscale.timeout'] = this.findOne('name', 'scaling.upscale.timeout').getValue();
		} else {
			settings['scaling.upscale.timeout_enabled'] = 0;
			delete settings['scaling.upscale.timeout'];
		}

		if (this.findOne('name', 'scaling.downscale.timeout_enabled').getValue()) {
			settings['scaling.downscale.timeout_enabled'] = 1;
			settings['scaling.downscale.timeout'] = this.findOne('name', 'scaling.downscale.timeout').getValue();
		} else {
			settings['scaling.downscale.timeout_enabled'] = 0;
			delete settings['scaling.downscale.timeout'];
		}

		// algos
		this.findOne('itemId', 'algos').items.each(function (it) {
			scaling[it.metricId.toString()] = it.getValues.call(this, it);
		}, this);

		record.set('settings', settings);
		record.set('scaling', scaling);
	},

	items: [{
		xtype: 'fieldset',
		title: 'General',
		labelWidth: 120,
		items: [{
			xtype: 'compositefield',
			fieldLabel: 'Minimum instances',
			items: [{
				xtype: 'textfield',
				name: 'scaling.min_instances',
				width: 40
			}, {
				xtype: 'displayfield',
				name: 'scaling.min_instances_help',
				value: '<img src="/images/ui-ng/icons/info_icon_16x16.png" style="padding: 2px; cursor: help;">'
			}]
		}, {
			xtype: 'compositefield',
			fieldLabel: 'Maximum instances',
			items: [{
				xtype: 'textfield',
				name: 'scaling.max_instances',
				width: 40
			}, {
				xtype: 'displayfield',
				name: 'scaling.max_instances_help',
				value: '<img src="/images/ui-ng/icons/info_icon_16x16.png" style="padding: 2px; cursor: help;">'
			}]
		}, {
			xtype: 'compositefield',
			hideLabel: true,
			items: [{
				xtype: 'displayfield',
				value: 'Polling interval (every)',
				cls: 'x-form-check-wrap'
			}, {
				xtype: 'textfield',
				name: 'scaling.polling_interval',
				width: 40
			}, {
				xtype: 'displayfield',
				value: 'minute(s)',
				cls: 'x-form-check-wrap'
			}]
		}, {
			xtype: 'checkbox',
			name: 'scaling.keep_oldest',
			hideLabel: true,
			boxLabel: 'Keep oldest instance running after scale down'
		}, {
			xtype: 'compositefield',
			itemId: 'scaling.safe_shutdown_compositefield',
			hideLabel: true,
			items: [{
				xtype: 'checkbox',
				name: 'scaling.safe_shutdown',
				width: 250,
				boxLabel: 'Enable safe shutdown during downscaling'
			}, {
				xtype: 'displayfield',
				name: 'scaling.safe_shutdown_help',
				value: '<img src="/images/ui-ng/icons/info_icon_16x16.png" style="padding: 2px; cursor: help;">'
			}]
		}]
	}, {
		xtype: 'fieldset',
		title: 'Delays',
		labelWidth: 120,
		items: [{
			xtype: 'compositefield',
			hideLabel: true,
			items: [{
				xtype: 'checkbox',
				hideLabel: true,
				boxLabel: 'Wait',
				name: 'scaling.upscale.timeout_enabled'
			}, {
				xtype: 'textfield',
				name: 'scaling.upscale.timeout',
				width: 40
			}, {
				xtype: 'displayfield',
				value: 'minute(s) after a new instance have been started and is running before the next up-scale',
				cls: 'x-form-check-wrap'
			}]
		}, {
			xtype: 'compositefield',
			hideLabel: true,
			items: [{
				xtype: 'checkbox',
				hideLabel: true,
				boxLabel: 'Wait',
				name: 'scaling.downscale.timeout_enabled'
			}, {
				xtype: 'textfield',
				name: 'scaling.downscale.timeout',
				width: 40
			}, {
				xtype: 'displayfield',
				value: 'minute(s) after a shutdown before shutting down another instance',
				cls: 'x-form-check-wrap'
			}]
		}]
	}, {
		xtype: 'compositefield',
		fieldLabel: 'Enable scaling based on',
		items: [{
			xtype: 'combo',
			store: new Ext.data.JsonStore({
				id: 'id',
				fields: [ 'id', 'name', 'alias' ]
			}),
			valueField: 'id',
			displayField: 'name',
			editable: false,
			mode: 'local',
			name: 'enable_scaling',
			triggerAction: 'all',
			width: 200
		}, {
			xtype: 'displayfield',
			value: '<img src="/images/ui-ng/icons/add_icon_16x16.png">',
			name: 'enable_scaling_add',
			cls: 'x-form-check-wrap'
		}]
	}, {
		itemId: 'algos_disabled',
		autoHeight: true,
		padding: 10,
		style:'font-size:12px;',
		html: 'Scaling disabled for this role'
	}, {
		xtype: 'tabpanel',
		itemId: 'algos',
		enableTabScroll: true,
		deferredRender: false,
		autoHeight: true,
		defaults: { autoHeight: true },
		items: []
	}]
})
{/literal}
