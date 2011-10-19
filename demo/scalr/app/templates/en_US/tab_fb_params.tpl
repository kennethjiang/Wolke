{literal}
new Scalr.Viewers.FarmRolesEditTab({
	tabTitle: 'Parameters',
	layout: 'form',
	labelWidth: 200,
	paramsCache: {},

	showTab: function (record) {
		if (! Ext.isArray(this.paramsCache[record.get('role_id')])) {
			this.loadMask.show();
			Ext.Ajax.request({
				url: '/server/ajax-ui-server.php',
				params: {
					action: 'GetRoleParams',
					roleId: record.get('role_id'),
					farmId: this.farmId
				},
				success: function(response, options) {
					var result = Ext.decode(response.responseText);
					this.paramsCache[record.get('role_id')] = result.data ? result.data : [];
					this.loadMask.hide();
					this.showTab.call(this, record);
				},
				failure: function() {
					this.paramsCache[record.get('role_id')] = [];
					this.loadMask.hide();
					this.showTab.call(this, record);
				},
				scope: this
			});
		} else {
			var pars = this.paramsCache[record.get('role_id')], params = record.get('params'), comp = this.findOne('itemId', 'params'), obj;
			comp.removeAll();

			// set loaded values
			if (! Ext.isObject(params)) {
				params = {};
				for (var i = 0; i < pars.length; i++)
					params[pars[i]['hash']] = pars[i]['value'];

				record.set('params', params);
			}

			if (pars.length) {
				obj = {};
				for (var i = 0; i < pars.length; i++) {
					obj['name'] = pars[i]['hash'];
					obj['fieldLabel'] = pars[i]['name'];
					obj['allowBlank'] = pars[i]['isrequired'] == 1 ? false : true;
					obj['value'] = params[pars[i]['hash']];

					if (pars[i]['type'] == 'text') {
						obj['xtype'] = 'textfield';
						obj['width'] = 200;
					}

					if (pars[i]['type'] == 'textarea') {
						obj['xtype'] = 'textarea';
						obj['width'] = 400;
						obj['height'] = 100;
					}

					if (pars[i]['type'] == 'boolean') {
						obj['xtype'] = 'checkbox';
						obj['checked'] = params[pars[i]['hash']] == 1 ? true : false;
					}

					comp.add(obj);
				}

			} else {
				comp.add({
					xtype: 'displayfield',
					hideLabel: true,
					value: 'No parameters for this role'
				});
			}

			comp.doLayout(false, true);
		}
	},

	hideTab: function (record) {
		var params = record.get('params'), comp = this.findOne('itemId', 'params');

		comp.items.each(function (item) {
			if (item.xtype == 'textfield' | item.xtype == 'textarea')
				params[item.name] = item.getValue()
			else if (item.xtype == 'checkbox')
				params[item.name] = item.getValue() ? 1 : 0;
		});

		record.set('params', params);
	},

	items: [{
		xtype: 'fieldset',
		itemId: 'params'
	}]

})
{/literal}
