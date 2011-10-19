{literal}
new Scalr.Viewers.FarmRolesEditTab({
	tabTitle: 'DNS',
	layout: 'form',
	labelWidth: 30,

	isEnabled: function (record) {
		return record.get('platform') != 'rds';
	},

	getDefaultValues: function (record) {
		return {
			'aws.use_ebs': 0
		};
	},

	showTab: function (record) {
		var settings = record.get('settings');

		this.findOne('name', 'dns.exclude_role').setValue((settings['dns.exclude_role'] == 1) ? true : false);
		this.findOne('name', 'dns.int_record_alias').setValue(settings['dns.int_record_alias'] || '');
		this.findOne('name', 'dns.ext_record_alias').setValue(settings['dns.ext_record_alias'] || '');
	},

	hideTab: function (record) {
		var settings = record.get('settings');

		settings['dns.exclude_role'] = this.findOne('name', 'dns.exclude_role').getValue() ? 1 : 0;
		settings['dns.int_record_alias'] = this.findOne('name', 'dns.int_record_alias').getValue();
		settings['dns.ext_record_alias'] = this.findOne('name', 'dns.ext_record_alias').getValue();

		record.set('settings', settings);
	},

	items: [{
		xtype: 'fieldset',
		//bodyStyle: 'padding-top: 4px',
		items: {
			xtype: 'checkbox',
			name: 'dns.exclude_role',
			hideLabel: true,
			boxLabel: 'Exclude role from DNS zone'
		}
	}, {
		xtype: 'fieldset',
		items: [ new Scalr.Viewers.WarningPanel({
			html: 'Will affect only new records. old ones WILL REMAIN the same.',
			style: 'margin-bottom: 4px'
		}), {
			xtype: 'compositefield',
			hideLabel: true,
			items: [{
				xtype: 'displayfield',
				cls: 'x-form-check-wrap',
				value: 'Create'
			}, {
				xtype: 'textfield',
				name: 'dns.int_record_alias',
				hideLabel: true
			}, {
				xtype: 'displayfield',
				cls: 'x-form-check-wrap',
				value: 'records instead of <b>int-%rolename%</b> ones'
			}]
		}, {
			xtype: 'compositefield',
			hideLabel: true,
			items: [{
				xtype: 'displayfield',
				cls: 'x-form-check-wrap',
				value: 'Create'
			}, {
				xtype: 'textfield',
				name: 'dns.ext_record_alias',
				hideLabel: true
			}, {
				xtype: 'displayfield',
				cls: 'x-form-check-wrap',
				value: 'records instead of <b>ext-%rolename%</b> ones'
			}]
		}]
	}]
})
{/literal}
