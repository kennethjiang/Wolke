{literal}
new Scalr.Viewers.FarmRolesEditTab({
	tabTitle: 'CloudWatch',
	layout: 'form',
	labelWidth: 30,

	isEnabled: function (record) {
		return record.get('platform') == 'ec2';
	},

	getDefaultValues: function (record) {
		return {
			'aws.enable_cw_monitoring': 0
		};
	},

	showTab: function (record) {
		var settings = record.get('settings');

		if (settings['aws.enable_cw_monitoring'] == 1)
			this.findOne('name', 'aws.enable_cw_monitoring').setValue(true);
		else
			this.findOne('name', 'aws.enable_cw_monitoring').setValue(false);
	},

	hideTab: function (record) {
		var settings = record.get('settings');

		settings['aws.enable_cw_monitoring'] = this.findOne('name', 'aws.enable_cw_monitoring').getValue() ? 1 : 0;

		record.set('settings', settings);
	},

	items: [{
		xtype: 'fieldset',
		items: [{
			xtype: 'checkbox',
			hideLabel: true,
			boxLabel: 'Enable Detailed <a href="http://aws.amazon.com/cloudwatch/">CloudWatch</a> monitoring for instances of this role (1 min interval)',
			name: 'aws.enable_cw_monitoring'
		}]
	}]
})
{/literal}
