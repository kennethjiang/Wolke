{literal}
new Scalr.Viewers.FarmRolesEditTab({
	tabTitle: 'VPC settings',
	layout: 'form',
	labelWidth: 30,
	availZones: {},

	isEnabled: function (record) {
		return record.get('platform') == 'ec2';
	},

	getDefaultValues: function (record) {
		return {
			
		};
	},

	activateTab: function () {

	},

	showTab: function (record) {
		var settings = record.get('settings');

		this.findOne('name', 'aws.vpc.subnetId').reset();
		
		this.loadMask.show();
		Ext.Ajax.request({
			url: '/tools/aws/vpc/subnets/xListViewSubnets',
			params: {
				cloudLocation: record.get('cloud_location')
			},
			success: function(response, options) {
				var result = Ext.decode(response.responseText);

				var sStore = [[""]];

				if (result.data) {
					for (var i = 0; i < result.data.length; i++) {
						sStore[sStore.length] = [result.data[i].id];
					}
				}

				this.findOne('name', 'aws.vpc.subnetId').store.loadData(sStore);
				this.findOne('name', 'aws.vpc.subnetId').setValue(settings['aws.vpc.subnetId'] || '');
				
				this.loadMask.hide();
			},
			scope: this
		});
		
		this.findOne('name', 'aws.vpc.privateIpAddress').setValue(settings['aws.vpc.privateIpAddress'] || '');
	},

	hideTab: function (record) {
		var settings = record.get('settings');

		settings['aws.vpc.subnetId'] = this.findOne('name', 'aws.vpc.subnetId').getValue();
		settings['aws.vpc.privateIpAddress'] = this.findOne('name', 'aws.vpc.privateIpAddress').getValue();

		record.set('settings', settings);
	},

	items: [{
		xtype: 'fieldset',
		labelWidth: 200,
		items: [{
			fieldLabel: 'Private IP Address',
			xtype: 'textfield',
			name: 'aws.vpc.privateIpAddress'
		}, {
			xtype: 'combo',
			name: 'aws.vpc.subnetId',
			fieldLabel: 'VPC Subnet',
			editable: false,
			forceSelection: true,
			width: 200,
			typeAhead: true,
			selectOnFocus: true,
			triggerAction: 'all',
			valueField: 'subnetId',
			displayField: 'subnetId',
			mode: 'local',
			store: new Ext.data.ArrayStore({fields:['subnetId']})
		}
		]
	}]
})
{/literal}
