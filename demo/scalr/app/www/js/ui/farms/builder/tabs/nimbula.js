Scalr.regPage('Scalr.ui.farms.builder.tabs.nimbula', function () {
	return new Scalr.ui.farms.builder.tab({
		tabTitle: 'Nimbula settings',
		layout: 'form',
		labelWidth: 30,
		loaded: false,

		isEnabled: function (record) {
			return record.get('platform') == 'nimbula';
		},

		getDefaultValues: function (record) {
			return {
				'nimbula.shape': 'small'
			};
		},

		showTab: function (record) {
			var settings = record.get('settings');

			if (! this.loaded) {
				this.loadMask.show();
				Ext.Ajax.request({
					url: '/platforms/nimbula/xGetShapes/',
					reader: new Scalr.data.JsonReader({
						id: 'id',
						fields: [ 'id', 'name' ]
					}),
					success: function(response, options) {
						var result = Ext.decode(response.responseText);

						if (result.data)
							this.findOne('name', 'nimbula.shape').store.loadData(result.data);

						this.loadMask.hide();
						this.loaded = true;

						this.showTab.call(this, record);
					},
					scope: this
				});
			} else {
				this.findOne('name', 'nimbula.shape').reset();
			}

			this.findOne('name', 'nimbula.shape').setValue(settings['nimbula.shape'] || 'small');
		},

		hideTab: function (record) {
			var settings = record.get('settings');

			settings['nimbula.shape'] = this.findOne('name', 'nimbula.shape').getValue();

			record.set('settings', settings);
		},

		items: [{
			xtype: 'fieldset',
			items: [{
				xtype: 'combo',
				store: new Ext.data.ArrayStore({
					idIndex: 0,
					fields: [ 'id', 'name' ]
				}),
				valueField: 'id',
				displayField: 'name',
				fieldLabel: 'Shape',
				editable: false,
				mode: 'local',
				name: 'nimbula.shape',
				triggerAction: 'all',
				width: 200
			}]
		}]
	});
});
