{
	create: function (loadParams, moduleParams) {
		var form =  new Ext.form.FormPanel({
			scalrOptions: {
				'maximize': 'maxHeight'
			},
			width: 900,
			//title: 'Environments &raquo; Edit &raquo; ' + moduleParams.env.name,
			title: 'Amazon Web Services &raquo; Amazon IAM &raquo; Server Certificates &raquo; Add',
			frame: true,
			fileUpload:true,
			labelWidth: 200,
			autoScroll: true,
			padding: '0px 20px 0px 5px',
			plugins: [ new Scalr.Viewers.Plugins.findOne() ],
			buttonAlign: 'center',
			items: [{
				xtype: 'fieldset',
				title: 'General information',
				labelWidth: 130,
				items: [{
					xtype: 'textfield',
					name: 'name',
					fieldLabel: 'Name',
					value: '',
					anchor: '-20'
				},{
					xtype: 'textfield',
					name: 'certificate',
					fieldLabel: 'Certificate',
					inputType: 'file',
					value: ''
				}, {
					xtype: 'textfield',
					name: 'privateKey',
					fieldLabel: 'Private key',
					inputType: 'file',
					value: ''
				},
				{
					xtype: 'textfield',
					name: 'certificateChain',
					fieldLabel: 'Certificate chain',
					inputType: 'file',
					value: ''
				}]
			}],
			buttonAlign: 'center'
		});
		
		form.addButton({
			type: 'submit',
			text: 'Upload',
			handler: function() {
				if (form.getForm().isValid()) {
					Ext.Msg.wait('Please wait');
					form.getForm().submit({
						url: '/awsIam/serverCertificatesSave',
						success: function(form, action) {
							Ext.Msg.hide();
							if (action.result.success == true) {
								Scalr.Viewers.SuccessMessage('Certificate successfully uploaded');
								Scalr.Viewers.EventMessager.fireEvent('close');
							}
							else
								Scalr.Viewers.ErrorMessage(action.result.error);
						},
						scope: this,
						failure: Scalr.data.ExceptionFormReporter
					});
				}
			},
			scope: this
		});
		
		form.addButton({
			type: 'submit',
			text: 'Cancel',
			handler: function() {
				document.location.href = '#/dnszones/view';
			}
		});
		
		return form;
	}
}
