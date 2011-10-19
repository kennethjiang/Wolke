{literal}
new Scalr.Viewers.FarmRolesEditTab({
	tabTitle: 'Timeouts',
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
				value: "Terminate instance if it will not send 'rebootFinish' event after reboot in"
			}, {
				xtype: 'textfield',
				name: 'system.timeouts.reboot',
				hideLabel: true,
				width: 50
			}, {
				xtype: 'displayfield',
				cls: 'x-form-check-wrap',
				value: 'seconds.'
			}]
		}, {
			xtype: 'compositefield',
			hideLabel: true,
			items: [{
				xtype: 'displayfield',
				cls: 'x-form-check-wrap',
				value: "Terminate instance if it will not send 'hostUp' or 'hostInit' event after launch in"
			}, {
				xtype: 'textfield',
				name: 'system.timeouts.launch',
				hideLabel: true,
				width: 50
			}, {
				xtype: 'displayfield',
				cls: 'x-form-check-wrap',
				value: 'seconds.'
			}]
		}]
	}]
})
{/literal}
