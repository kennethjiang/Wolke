{literal}
new Scalr.Viewers.FarmRolesEditTab({
	tabTitle: 'RDS settings',
	layout: 'form',
	labelWidth: 30,
	availZones: {},

	isEnabled: function (record) {
		return record.get('platform') == 'rds';
	},

	getDefaultValues: function (record) {
		return {
			'rds.availability_zone': '',
			'rds.instance_class': 'db.m1.small',
			'rds.storage': 5,
			'rds.master-user': 'root',
			'rds.port': '3306',
			'rds.engine': 'MySQL5.1'
		};
	},

	activateTab: function () {
		this.findOne('name', 'rds.availability_zone').store.loadData({
			data: [
				{ id: 'x-scalr-diff', name: 'Place in different zones' },
				{ id: '', name: 'Choose randomly' }
			]
		});

		this.findOne('name', 'rds.availability_zone').on('beforequery', function (qe) {
			var field = this.findOne('name', 'rds.availability_zone');
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

		this.findOne('name', 'rds.availability_zone').store.on('load', function (store, record, options) {
			var t = [];

			if (options.saveFlag) {
				store.insert(0, [
					new store.recordType({ id: 'x-scalr-diff', name: 'Place in different zones' }),
					new store.recordType({ id: '', name: 'Choose randomly' })
				]);

				var f = this.findOne('name', 'rds.availability_zone'), r = f.findRecord(f.valueField, f.value);
				f.view.refresh();

				if (r)
					f.select(f.store.indexOf(r) - 1, true); // hack instead of f.selectByValue
			}

			store.each(function (rec) {
				t[t.length] = rec.data;
			});
			this.availZones[this.findOne('name', 'rds.availability_zone').region] = { data: t };
		}, this);
	},

	showTab: function (record) {
		var settings = record.get('settings');

		this.findOne('name', 'rds.instance_class').store.loadData(['db.m1.small','db.m1.large','db.m1.xlarge','db.m2.2xlarge','db.m2.4xlarge']);
		this.findOne('name', 'rds.instance_class').setValue(settings['rds.instance_class'] || 'db.m1.small');

		this.findOne('name', 'rds.availability_zone').setValue(settings['rds.availability_zone'] || '');
		this.findOne('name', 'rds.availability_zone').region = record.get('cloud_location');
		
		this.findOne('name', 'rds.storage').setValue(settings['rds.storage'] || '5');
		this.findOne('name', 'rds.master-user').setValue(settings['rds.master-user'] || 'root');
		this.findOne('name', 'rds.master-pass').setValue(settings['rds.master-pass'] || '');
		this.findOne('name', 'rds.port').setValue(settings['rds.port'] || '3306');
		this.findOne('name', 'rds.engine').setValue(settings['rds.engine'] || 'MySQL5.1');
		
		if (settings['rds.multi-az'] == 1) {
			this.findOne('name', 'rds.multi-az').setValue(true);
		} else {
			this.findOne('name', 'rds.multi-az').setValue(false);
		}
	},

	hideTab: function (record) {
		var settings = record.get('settings');

		settings['rds.instance_class'] 		= this.findOne('name', 'rds.instance_class').getValue();
		settings['rds.availability_zone'] 	= this.findOne('name', 'rds.availability_zone').getValue();
		settings['rds.storage'] 			= this.findOne('name', 'rds.storage').getValue();
		settings['rds.master-user'] 		= this.findOne('name', 'rds.master-user').getValue();
		settings['rds.master-pass'] 		= this.findOne('name', 'rds.master-pass').getValue();
		settings['rds.port'] 				= this.findOne('name', 'rds.port').getValue();
		settings['rds.multi-az'] 			= this.findOne('name', 'rds.multi-az').getValue() ? 1 : 0;
		settings['rds.engine'] 				= this.findOne('name', 'rds.engine').getValue();
	
		record.set('settings', settings);
	},

	items: [{
		xtype: 'fieldset',
		items: [{
			xtype: 'combo',
			store: new Scalr.data.Store({
				url: '/server/ajax-ui-server-aws-ec2.php',
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
			name: 'rds.availability_zone',
			triggerAction: 'all',
			width: 200
		}, {
			xtype: 'combo',
			store: [],
			fieldLabel: 'Instance class',
			editable: false,
			mode: 'local',
			name: 'rds.instance_class',
			triggerAction: 'all',
			width: 200
		}, { 
			xtype: 'compositefield',
			hideLabel: true,
			items: [{
				xtype: 'displayfield',
				cls: 'x-form-check-wrap',
				value: 'Allocated storage (5-1024 GB)'
			}, {
				xtype: 'textfield',
				width:50,
				name: 'rds.storage',
				hideLabel: true
			}, {
				xtype: 'displayfield',
				cls: 'x-form-check-wrap',
				value: 'GB'
			}]
		}, {
			fieldLabel: 'Port',
			xtype: 'textfield',
			name: 'rds.port'
		}, {
			fieldLabel: 'Master username',
			xtype: 'textfield',
			name: 'rds.master-user'
		}, {
			fieldLabel: 'Master password',
			xtype: 'textfield',
			name: 'rds.master-pass'
		}, {
			xtype: 'combo',
			store: [['MySQL5.1','MySQL5.1']],
			fieldLabel: 'Engine',
			editable: false,
			mode: 'local',
			name: 'rds.engine',
			triggerAction: 'all',
			width: 200
		}, {
			fieldLabel: 'Enable <a target="_blank" href="http://aws.amazon.com/about-aws/whats-new/2010/05/18/announcing-multi-az-deployments-for-amazon-rds/">MultiAZ</a>',
			xtype: 'checkbox',
			name: 'rds.multi-az',
			value:1
		}
		
		]
	}]
})
{/literal}
