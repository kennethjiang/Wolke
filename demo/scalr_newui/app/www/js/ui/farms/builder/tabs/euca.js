Scalr.regPage('Scalr.ui.farms.builder.tabs.euca', function () {
	return new Scalr.ui.farms.builder.tab({
		tabTitle: 'Placement and Type',
		layout: 'form',
		labelWidth: 30,
		availZones: {},

		isEnabled: function (record) {
			return record.get('platform') == 'eucalyptus';
		},

		getDefaultValues: function (record) {
			return {

			};
		},

		activateTab: function () {
			this.findOne('name', 'euca.availability_zone').store.loadData({
				data: [
					{ id: '', name: 'Default' }
				]
			});

			this.findOne('name', 'euca.availability_zone').on('beforequery', function (qe) {
				var field = this.findOne('name', 'euca.availability_zone');
				if (this.availZones[field.region]) {
					field.store.loadData(this.availZones[field.region]);
				} else {
					field.store.baseParams['Region'] = field.region;
					field.store.load({
						saveFlag: true
					});
					field.expand();
					qe.cancel = true;
				}
			}, this);

			this.findOne('name', 'euca.availability_zone').store.on('load', function (store, record, options) {
				var t = [];

				if (options.saveFlag) {
					store.insert(0, [
						new store.recordType({ id: '', name: 'Default' })
					]);

					var f = this.findOne('name', 'euca.availability_zone'), r = f.findRecord(f.valueField, f.value);
					f.view.refresh();

					if (r)
						f.select(f.store.indexOf(r) - 1, true); // hack instead of f.selectByValue
				}

				store.each(function (rec) {
					t[t.length] = rec.data;
				});
				this.availZones[this.findOne('name', 'euca.availability_zone').region] = { data: t };
			}, this);
		},

		showTab: function (record) {
			var settings = record.get('settings');

			if (record.get('arch') == 'i386') {
				this.findOne('name', 'euca.instance_type').store.loadData(['m1.small', 'c1.medium']);
				this.findOne('name', 'euca.instance_type').setValue(settings['euca.instance_type'] || 'm1.small');
			} else {
				this.findOne('name', 'euca.instance_type').store.loadData(['m1.large', 'm1.xlarge', 'c1.xlarge']);
				this.findOne('name', 'euca.instance_type').setValue(settings['euca.instance_type'] || 'm1.large');
			}

			this.findOne('name', 'euca.availability_zone').setValue(settings['euca.availability_zone'] || '');
			this.findOne('name', 'euca.availability_zone').region = record.get('cloud_location');
		},

		hideTab: function (record) {
			var settings = record.get('settings');

			settings['euca.instance_type'] = this.findOne('name', 'euca.instance_type').getValue();
			settings['euca.availability_zone'] = this.findOne('name', 'euca.availability_zone').getValue();

			record.set('settings', settings);
		},

		items: [{
			xtype: 'fieldset',
			items: [{
				xtype: 'combo',
				store: new Scalr.data.Store({
					url: '/server/ajax-ui-server-eucalyptus.php',
					reader: new Scalr.data.JsonReader({
						id: 'id',
						fields: [ 'id', 'name' ]
					}),
					baseParams: { action: 'GetAvailZonesList' }
				}),
				fieldLabel: 'Placement',
				valueField: 'id',
				displayField: 'name',
				editable: false,
				mode: 'local',
				name: 'euca.availability_zone',
				triggerAction: 'all',
				width: 200
			},{
				xtype: 'combo',
				store: [],
				fieldLabel: 'Instances type',
				editable: false,
				mode: 'local',
				name: 'euca.instance_type',
				triggerAction: 'all',
				width: 200
			}]
		}]
	});
});
