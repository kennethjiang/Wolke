Scalr.regPage('Scalr.ui.farms.builder.tabs.cloudwatch', function () {
	return new Scalr.ui.farms.builder.tab({
		tabTitle: 'CloudWatch设置',
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
				boxLabel: '允许对该服务角色开启详细的<a href="http://aws.amazon.com/cloudwatch/">CloudWatch</a>监控(间隔1分钟)',
				name: 'aws.enable_cw_monitoring'
			}]
		}]
	});
});
