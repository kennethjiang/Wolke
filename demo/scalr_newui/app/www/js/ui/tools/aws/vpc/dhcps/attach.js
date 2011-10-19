{
	create: function (loadParams, moduleParams) {
		var form = new Ext.form.FormPanel({
			scalrOptions: {
				/*'maximize': 'height'*/
			},
			width:700,
			frame: true,
			autoScroll: true,
			title: 'Tools &raquo; Amazon Web Services &raquo; VPC &raquo; DHCP options &raquo; Attach',
			buttonAlign:'center',
			padding: '0px 20px 0px 5px',
			items: [{
				xtype: 'fieldset',
				title: 'General information',
				labelWidth: 130,
				items: [{
					fieldLabel: 'DHCP option',
					xtype: 'combo',
					allowBlank: false,
					editable: false, 
			        store: moduleParams['options'],
			        value: moduleParams['value'],
			        displayField:'state',
			        hiddenName:'dhcpId',
			        typeAhead: false,
			        mode: 'local',
			        triggerAction: 'all',
			        selectOnFocus:false,
			        width:200
				}]
			}]
		});
		
		form.addButton({
			type: 'submit',
			text: 'Attach',
			handler: function() {
				Ext.Msg.wait('Please wait ...', 'Attaching ...');

				form.getForm().submit({
					url: '/tools/aws/vpc/dhcps/xAttach',
					params: loadParams,
					success: function(form, action) {
						Ext.Msg.hide();
						Scalr.Viewers.SuccessMessage('DHCP option successfully attached');
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
