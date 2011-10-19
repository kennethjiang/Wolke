{
	create: function (loadParams, moduleParams) {
		var form = new Ext.form.FormPanel({
			scalrOptions: {
				/*'maximize': 'height'*/
			},
			width:700,
			frame: true,
			autoScroll: true,
			title: 'DNS Zones &raquo; Settings',
			buttonAlign:'center',
			padding: '0px 20px 0px 5px',
			items: [{
				xtype: 'fieldset',
				title: 'AXFR white list',
				labelWidth: 130,
				items: [{
					xtype: 'displayfield',
					hideLabel: true,
					readOnly:true,
					anchor:"-20",
					value: 'IP address(es) that are allowed to transfer (copy) the zone information sepparated by ; (eg. 5.6.7.8;9.1.2.3)'
				},{
					xtype: 'textarea',
					name: 'axfrAllowedHosts',
					allowBlank:true,
					anchor:"-20",
					hideLabel: true,
					value: moduleParams['axfrAllowedHosts']
				}]
			}, {
				xtype: 'fieldset',
				title: 'Scalr accounts authorized to create subdomains for this zone',
				labelWidth: 130,
				items: [{
					xtype: 'displayfield',
					hideLabel: true,
					readOnly:true,
					anchor:"-20",
					value: 'Email addresses sepparated by ; (eg. mysecondaccount@company.net;dev@company.net)'
				},{
					xtype: 'textarea',
					name: 'allowedAccounts',
					allowBlank:true,
					anchor:"-20",
					hideLabel: true,
					value: moduleParams['allowedAccounts']
				}]
			}, {
				xtype: 'fieldset',
				title: 'Settings',
				labelWidth: 130,
				items: [{
					xtype: 'compositefield',
					hideLabel: true,
					items: [{
						xtype: 'checkbox',
						hideLabel: true,
						name: 'allowManageSystemRecords',
						inputValue: 1,
						checked: (moduleParams['allowManageSystemRecords'] == 1) ? true : false
					}, {
						xtype: 'displayfield',
						cls: 'x-form-check-wrap',
						value: 'Allow me to edit system records</span>'
					}]
				}]
			}]
		});
		
		form.addButton({
			type: 'submit',
			text: 'Save',
			handler: function() {
				Ext.Msg.wait('Please wait ...', 'Saving ...');

				form.getForm().submit({
					url: '/dnszones/saveSettings/',
					params: loadParams,
					success: function(form, action) {
						Ext.Msg.hide();
						Scalr.Viewers.SuccessMessage('Changes have been saved. They will become active in few minutes.');
						Scalr.Viewers.EventMessager.fireEvent('close');
					},
					failure: Scalr.data.ExceptionFormReporter
				});
			}
		});

		form.addButton({
			type: 'reset',
			text: 'Cancel',
			handler: function() {
				Scalr.Viewers.EventMessager.fireEvent('close');
			}
		});

		return form;
	}
}
