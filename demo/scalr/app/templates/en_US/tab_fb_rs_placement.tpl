{literal}
new Scalr.Viewers.FarmRolesEditTab({
	tabTitle: 'Placement and type',
	layout: 'form',
	labelWidth: 30,
	loaded: false,

	isEnabled: function (record) {
		return record.get('platform') == 'rackspace';
	},

	getDefaultValues: function (record) {
		return {
			'rs.flavor-id': 1
		};
	},

	showTab: function (record) {
		var settings = record.get('settings');

		if (! this.loaded) {
			this.loadMask.show();
			Ext.Ajax.request({
				url: '/server/ajax-ui-server-rackspace.php?action=GetFlavorsList',
				reader: new Scalr.data.JsonReader({
					id: 'id',
					fields: [ 'id', 'name' ]
				}),
				params:{cloudLocation: record.get('cloud_location')},
				success: function(response, options) {
					var result = Ext.decode(response.responseText);

					if (result.data)
						this.findOne('name', 'rs.flavor-id').store.loadData(result.data);

					this.loadMask.hide();
					this.loaded = true;

					this.showTab.call(this, record);
				},
				scope: this
			});
		} else {
			this.findOne('name', 'rs.flavor-id').reset();
		}

		this.findOne('name', 'rs.flavor-id').setValue(settings['rs.flavor-id'] || '1');
	},

	hideTab: function (record) {
		var settings = record.get('settings');

		settings['rs.flavor-id'] = this.findOne('name', 'rs.flavor-id').getValue();

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
			fieldLabel: 'Flavor',
			editable: false,
			mode: 'local',
			name: 'rs.flavor-id',
			triggerAction: 'all',
			width: 200
		}]
	}]
})
{/literal}
