{
	create: function (loadParams, moduleParams) {
		var form = new Ext.form.FormPanel({
			scalrOptions: {
				'maximize': 'maxHeight',
				'modal': true
			},
			tools: [{
				id: 'close',
				handler: function () {
					Scalr.Viewers.EventMessager.fireEvent('close');
				}
			}],
			title: 'Create new role',
			autoScroll: true,
			frame: true,
			width: 700,
			padding: '0px 20px 0px 5px',
			buttonAlign: 'center',
			items: [ new Scalr.Viewers.WarningPanel({
				html: moduleParams.showWarningMessage || '',
				hidden: !moduleParams.showWarningMessage
			}), {
				xtype: 'fieldset',
				title: 'Server details',
				defaults: {
					anchor: '100%'
				},
				items: [{
					xtype: 'displayfield',
					value: moduleParams.serverId,
					fieldLabel: 'Server ID'
				}, {
					xtype: 'displayfield',
					value: moduleParams.farmId,
					fieldLabel: 'Farm ID'
				}, {
					xtype: 'displayfield',
					value: moduleParams.farmName,
					fieldLabel: 'Farm name'
				}, {
					xtype: 'displayfield',
					value: moduleParams.roleName,
					fieldLabel: 'Role name'
				}]
			}, {
				xtype: 'fieldset',
				title: 'Replacement options',
				items: {
					xtype: 'radiogroup',
					columns: 1,
					hideLabel: true,
					items: [{
						name: 'replaceType',
						boxLabel: moduleParams.replaceNoReplace,
						inputValue: 'no_replace'
					}, {
						name: 'replaceType',
						boxLabel: moduleParams.replaceFarmReplace,
						inputValue: 'replace_farm'
					}, {
						name: 'replaceType',
						boxLabel: moduleParams.replaceAll,
						checked: true,
						inputValue: 'replace_all'
					}]
				}
			}, {
				xtype: 'fieldset',
				title: 'Role options',
				defaults: {
					anchor: '-20'
				},
				items: [{
					xtype: 'textfield',
					name: 'roleName',
					value: moduleParams.roleName,
					fieldLabel: 'Role name'
				}, {
					xtype: 'textarea',
					fieldLabel: 'Description',
					name: 'roleDescription',
					height: 100
				}, {
					xtype: 'compositefield',
					items: [{
						xtype: 'textfield',
						fieldLabel: 'Root EBS size',
						name: 'rootVolumeSize',
						width: 100
					}, {
						xtype:'displayfield',
						cls: 'x-form-check-wrap',
						value:'GB (Leave blank for default value)'
					}],
					hidden: !(moduleParams.platform == 'ec2' && moduleParams.isVolumeSizeSupported == 1)
				}, {
					xtype: 'hidden',
					name: 'serverId',
					value: moduleParams.serverId
				}]
			}]
		});
		
		form.addButton({
			text: 'Create role',
			handler: function () {
				Ext.MessageBox.wait('Please wait ...');
				Ext.Ajax.request({
					url: '/servers/xServerCreateSnapshot/',
					params: form.getForm().getValues(),
					success: function (response) {
						var result = Ext.decode(response.responseText);
						if (result.success == true) {
							Scalr.Viewers.SuccessMessage(result.message);
							Scalr.Viewers.EventMessager.fireEvent('close');
						} else if (result.error) {
							Scalr.Viewers.ErrorMessage(result.error);
						}
						Ext.MessageBox.hide();
					},
					failure: function() {
						Ext.MessageBox.hide();
					}
				});
			}
		});

		form.addButton({
			text: 'Cancel',
			handler: function () {
				Scalr.Viewers.EventMessager.fireEvent('close');
			}
		});

		return form;
	}
}
