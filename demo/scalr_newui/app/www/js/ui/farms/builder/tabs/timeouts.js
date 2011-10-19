Scalr.regPage('Scalr.ui.farms.builder.tabs.timeouts', function () {
	return new Scalr.ui.farms.builder.tab({
		tabTitle: '超时设置',
		layout: 'form',
		labelWidth: 30,

		getDefaultValues: function (record) {
			return {
				'system.timeouts.reboot': 360,
				'system.timeouts.launch': 2400
			};
		},

		showTab: function (record) {
			var settings = record.get('settings');

			this.findOne('name', 'system.timeouts.reboot').setValue(settings['system.timeouts.reboot'] || 360);
			this.findOne('name', 'system.timeouts.launch').setValue(settings['system.timeouts.launch'] || 2400);
		},

		hideTab: function (record) {
			var settings = record.get('settings');

			settings['system.timeouts.reboot'] = this.findOne('name', 'system.timeouts.reboot').getValue();
			settings['system.timeouts.launch'] = this.findOne('name', 'system.timeouts.launch').getValue();

			record.set('settings', settings);
		},

		items: [{
			xtype: 'fieldset',
			items: [{
				xtype: 'compositefield',
				hideLabel: true,
				items: [{
					xtype: 'displayfield',
					cls: 'x-form-check-wrap',
					value: "当服务器重启后"
				}, {
					xtype: 'textfield',
					name: 'system.timeouts.reboot',
					hideLabel: true,
					width: 50
				}, {
					xtype: 'displayfield',
					cls: 'x-form-check-wrap',
					value: '秒内未发送rebootFinish事件通知，则终止该服务器。'
				}]
			}, {
				xtype: 'compositefield',
				hideLabel: true,
				items: [{
					xtype: 'displayfield',
					cls: 'x-form-check-wrap',
					value: "当服务器启动后"
				}, {
					xtype: 'textfield',
					name: 'system.timeouts.launch',
					hideLabel: true,
					width: 50
				}, {
					xtype: 'displayfield',
					cls: 'x-form-check-wrap',
					value: '秒内发送hostUP或hostInit事件通知，则终止该服务器。'
				}]
			}]
		}]
	});
});
