{
	create: function (loadParams, moduleParams) {
		var form = new Ext.form.FormPanel({
			scalrOptions: {
				/*'maximize': 'height'*/
			},
			width:700,
			frame: true,
			autoScroll: true,
			title: 'Tools &raquo; Amazon Web Services &raquo; VPC &raquo; Custom gateways &raquo; Create',
			buttonAlign:'center',
			padding: '0px 20px 0px 5px',
			items: [{
				xtype: 'fieldset',
				title: 'General information',
				labelWidth: 130,
				items: [{
					fieldLabel: 'Cloud location',
					xtype: 'combo',
					allowBlank: false,
					editable: false, 
			        store: moduleParams['locations'],
			        value: 'us-east-1',
			        displayField:'state',
			        hiddenName:'cloudLocation',
			        typeAhead: false,
			        mode: 'local',
			        triggerAction: 'all',
			        selectOnFocus:false,
			        width:200
				}, {
					xtype:'compositefield',
					fieldLabel:'Static IP address',
					anchor:"-20",
					items:[{
						xtype: 'textfield',
						name: 'ip',
						value: ''
					}, {
						xtype:'displayfield',
						cls: 'x-form-check-wrap',
						value:'(e.g.) 10.0.0.0'
					}]
				}, {
					xtype:'compositefield',
					fieldLabel:'BGP ASN',
					anchor:"-20",
					items:[{
						xtype: 'textfield',
						name: 'bgp',
						value: ''
					}, {
						xtype:'displayfield',
						cls: 'x-form-check-wrap',
						value:'You can use a private ASN (in the 64512 - 65534 range)'
					}]
				}, {
					fieldLabel: 'Type',
					xtype: 'combo',
					allowBlank: false,
					editable: false, 
			        store: ['ipsec.1'],
			        value: 'ipsec.1',
			        hiddenName:'type',
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
			text: 'Save',
			handler: function() {
				Ext.Msg.wait('Please wait ...', 'Saving ...');

				form.getForm().submit({
					url: '/tools/aws/vpc/gateways/xSaveCustom',
					params: loadParams,
					success: function(form, action) {
						Ext.Msg.hide();
						Scalr.Viewers.SuccessMessage('Custom gateway successfully created');
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
