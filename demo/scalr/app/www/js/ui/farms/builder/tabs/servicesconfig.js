Scalr.regPage('Scalr.ui.farms.builder.tabs.servicesconfig', function () {
	return new Scalr.ui.farms.builder.tab({
		tabTitle: '服务设置',
		layout: 'form',
		labelWidth: 30,
		availPresets: {},

		isEnabled: function (record) {
			return record.get('platform') != 'rds';
		},

		showTab: function (record) {
			var behaviors = record.get('behaviors').split(','), config_presets = record.get('config_presets') || {}, fieldset = this.findOne('itemId', 'aws.servicesconfig.fieldset'), beh = [];

			for (var i = 0; i < behaviors.length; i++) {
				if (! this.availPresets[behaviors[i]])
					beh[beh.length] = behaviors[i];
			}

			if (beh.length) {
				this.loadMask.show();
				Ext.Ajax.request({
					url: '/server/ajax-ui-server.php',
					params: {
						action: 'GetServiceConfigurationsList',
						behaviors: Ext.encode(beh)
					},
					success: function(response, options) {
						var result = Ext.decode(response.responseText);

						if (result.data) {
							for (var i in result.data) {
								result.data[i][result.data[i].length] = { id: 0, name: 'Service defaults' };
								this.availPresets[i] = result.data[i];
							}
						}

						this.loadMask.hide();
						this.showTab.call(this, record);
					},
					scope: this
				});
			} else {
				for (var i = 0; i < behaviors.length; i++) {
					var field = fieldset.add({
						xtype: 'combo',
						store: new Ext.data.JsonStore({
							idProperty: 'id',
							fields: [ 'id', 'name' ],
							data: this.availPresets[behaviors[i]]
						}),
						behavior: behaviors[i],
						fieldLabel: behaviors[i],
						valueField: 'id',
						displayField: 'name',
						editable: false,
						mode: 'local',
						name: 'aws.servicesconfig.fieldset.' + behaviors[i],
						triggerAction: 'all',
						width: 200,
						value: config_presets[behaviors[i]] ? (config_presets[behaviors[i]] || 0) : 0
					});
				}

				fieldset.doLayout(false, true);
			}
		},

		hideTab: function (record) {
			var config_presets = {}, fieldset = this.findOne('itemId', 'aws.servicesconfig.fieldset');

			fieldset.items.each(function (item) {
				var value = item.getValue();
				if (value != '0')
					config_presets[item.behavior] = value;
			});

			fieldset.removeAll();
			record.set('config_presets', config_presets);
		},

		items: [{
			xtype: 'fieldset',
			itemId: 'aws.servicesconfig.fieldset'
		}]
	});
});
