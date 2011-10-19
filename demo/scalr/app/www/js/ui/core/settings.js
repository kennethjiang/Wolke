{
	create: function (loadParams, moduleParams) {
		var form = new Ext.form.FormPanel({
			scalrOptions: {
				/*'maximize': 'height'*/
			},
			width:700,
			frame: true,
			autoScroll: true,
			title: 'System settings',
			buttonAlign:'center',
			padding: '0px 20px 0px 5px',
			items: [{
				xtype: 'fieldset',
				title: 'RSS feed settings',
				labelWidth: 80,
				items: [ new Scalr.Viewers.InfoPanel({
					html: 'Each farm has an events and notifications page. You can get these events outside of Scalr on an RSS reader with the below credentials.',
					style: 'margin-bottom: 5px'
				}), {
					xtype: 'textfield',
					name: 'rss_login',
					width: 200,
					fieldLabel: 'Login',
					value: moduleParams['rss_login']
				}, {
					xtype: 'compositefield',
					fieldLabel: 'Password',
					items: [{
						xtype: 'textfield',
						name: 'rss_pass',
						width: 200,
						hideLabel: true,
						itemId: 'rss_pass',
						value: moduleParams['rss_pass']
					}, {
						xtype: 'button',
						text: 'Generate',
						handler: function() {
							function getRandomNum() {
								var rndNum = Math.random()
								rndNum = parseInt(rndNum * 1000);
								rndNum = (rndNum % 94) + 33;
								return rndNum;
							};

							function checkPunc(num) {
								if ((num >=33) && (num <=47)) { return true; }
								if ((num >=58) && (num <=64)) { return true; }
								if ((num >=91) && (num <=96)) { return true; }
								if ((num >=123) && (num <=126)) { return true; }
								return false;
							};

							var length=16;
							var sPassword = "";

							for (i=0; i < length; i++) {
								numI = getRandomNum();
								while (checkPunc(numI)) { numI = getRandomNum(); }
								sPassword = sPassword + String.fromCharCode(numI);
							}

							this.ownerCt.getComponent('rss_pass').setValue(sPassword);
						}
					}]
				}]
			}, {
				xtype: 'fieldset',
				title: 'Security settings',
				items: [{
					xtype: 'checkbox',
					name: 'system_auth_noip',
					hideLabel: true,
					inputValue: 1,
					boxLabel: 'Do not use IP address for session signature',
					checked: moduleParams['system_auth_noip'] == 1 ? true : false
				}]
			}, {
				xtype: 'fieldset',
				title: 'UI settings',
				items: [{
					xtype: 'button',
					text: 'Reset UI settings to defaults',
					handler: function () {
						localStorage.clear();
						Scalr.message.Success('Settings successfully reset');
					}
				}]
			}]
		});

		form.addButton({
			type: 'submit',
			text: 'Save',
			handler: function() {
				Ext.Msg.wait('Please wait ...', 'Saving ...');

				form.getForm().submit({
					url: '/core/xSettingsSave/',
					success: function(form, action) {
						Ext.Msg.hide();
						Scalr.Viewers.SuccessMessage('Settings successfully updated');
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
