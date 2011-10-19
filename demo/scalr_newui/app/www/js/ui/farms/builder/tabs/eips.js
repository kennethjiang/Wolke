Scalr.regPage('Scalr.ui.farms.builder.tabs.eips', function () {
	return new Scalr.ui.farms.builder.tab({
		tabTitle: '弹性IP设置',
		layout: 'form',
		labelWidth: 150,

		isEnabled: function (record) {
			return record.get('platform') == 'ec2';
		},

		getDefaultValues: function (record) {
			return {
				'aws.use_elastic_ips': 0
			};
		},

		activateTab: function () {
			new Ext.ToolTip({
				target: this.findOne('name', 'aws.use_elastic_ips_help').id,
				dismissDelay: 0,
				html:
					"If this option is enabled," +
					"Scalr will assign Elastic IPs to all instances of this role. It usually takes few minutes for IP to assign." +
					"The amount of allocated IPs increases when new instances start," +
					"but not decreases when instances terminated." +
					"Elastic IPs are assigned after instance initialization." +
					"This operation takes few minutes to complete. During this time instance is not available from" +
					"the outside and not included in application DNS zone."
			});
		},

		showTab: function (record) {
			var settings = record.get('settings');

			if (settings['aws.use_elastic_ips'] == 1) {
				this.findOne('name', 'aws.use_elastic_ips').setValue(true);
			} else {
				this.findOne('name', 'aws.use_elastic_ips').setValue(false);
			}
		},

		hideTab: function (record) {
			var settings = record.get('settings');
			settings['aws.use_elastic_ips'] = this.findOne('name', 'aws.use_elastic_ips').getValue() ? 1 : 0;
			record.set('settings', settings);
		},

		items: [{
			xtype: 'fieldset',
			items: {
				xtype: 'compositefield',
				hideLabel: true,
				items: [{
					xtype: 'checkbox',
					name: 'aws.use_elastic_ips',
					hideLabel: true,
					boxLabel: '使用弹性IP'
				}, {
					xtype: 'displayfield',
					name: 'aws.use_elastic_ips_help',
					value: '<img src="/images/ui-ng/icons/info_icon_16x16.png" style="padding: 2px; cursor: help;">'
				}]
			}
		}]
	});
});
