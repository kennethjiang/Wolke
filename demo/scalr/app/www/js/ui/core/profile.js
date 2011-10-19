{
	create: function (loadParams, moduleParams) {
		var form = new Ext.form.FormPanel({
			scalrOptions: {
				/*'maximize': 'height'*/
			},
			width:700,
			frame: true,
			autoScroll: true,
			title: 'Profile',
			buttonAlign:'center',
			padding: '0px 20px 0px 5px',
			items: [{
				xtype: 'fieldset',
				title: 'General information',
				labelWidth: 130,
				items: [{
					xtype: 'displayfield',
					name: 'email',
					fieldLabel: 'Email',
					readOnly:true,
					anchor:"-20",
					value: moduleParams['email']
				},{
					xtype: 'textfield',
					inputType:'password',
					name: 'password',
					allowBlank:false,
					anchor:"-20",
					fieldLabel: 'Password',
					value: '******'
				},{
					xtype: 'textfield',
					inputType:'password',
					name: 'cpassword',
					allowBlank:false,
					anchor:"-20",
					fieldLabel: 'Confirm password',
					value: '******'
				},{
					xtype: 'textfield',
					name: 'fullname',
					anchor:"-20",
					fieldLabel: 'Full name',
					value: moduleParams['fullname']
				},{
					xtype: 'textfield',
					name: 'org',
					anchor:"-20",
					fieldLabel: 'Organization',
					value: moduleParams['org']
				},{
					xtype: 'textfield',
					name: 'phone',
					anchor:"-20",
					fieldLabel: 'Phone',
					readonly:true,
					value: moduleParams['phone']
				},{
					xtype: 'textfield',
					name: 'country',
					anchor:"-20",
					fieldLabel: 'Country',
					readonly:true,
					value: moduleParams['country']
				}
				]
			}]
		});
		
		form.addButton({
			type: 'submit',
			text: 'Save',
			handler: function() {
				Ext.Msg.wait('Please wait ...', 'Saving ...');

				form.getForm().submit({
					url: '/core/profileSave/',
					success: function(form, action) {
						Ext.Msg.hide();
						Scalr.Viewers.SuccessMessage('Profile successfully updated');
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
