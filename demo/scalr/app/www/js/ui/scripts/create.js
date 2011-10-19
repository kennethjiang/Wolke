{
	create: function (loadParams, moduleParams) {
		var form = new Ext.form.FormPanel({
			scalrOptions: {
				/*'maximize': 'height'*/
			},
			width:700,
			frame: true,
			autoScroll: true,
			title: (moduleParams['scriptId']) ? 'Scripts &raquo; Edit' : 'Scripts &raquo; Create',
			buttonAlign:'center',
			plugins: [ new Scalr.Viewers.Plugins.findOne(), new Scalr.Viewers.Plugins.prepareForm() ],
			padding: '0px 20px 0px 5px',
			items: [{
				xtype: 'fieldset',
				title: 'General information',
				labelWidth: 130,
				items: [{
					xtype: 'textfield',
					name: 'scriptName',
					fieldLabel: 'Script name',
					readOnly:false,
					anchor:"-20",
					value: moduleParams['scriptName']
				}, {
					xtype: 'textfield',
					name: 'scriptDescription',
					fieldLabel: 'Description',
					readOnly:false,
					anchor:"-20",
					value: moduleParams['description']
				}, {
					xtype: 'combo',
					fieldLabel: 'Version',
					name: 'scriptVersion',
					hiddenName: 'scriptVersion',
					store: moduleParams['versions'],
					editable: false,
					forceSelection: true,
					value: moduleParams['version'],
					mode: 'local',
					triggerAction: 'all',
					selectOnFocus: false,
					listeners: {
						select: function () {
							var rev = form.getForm().findField('scriptVersion').getValue();

							if (moduleParams['scriptId']) {
								Scalr.Request({
									url: '/scripts/' + moduleParams['scriptId'] + '/xGetScriptContent',
									params: { version: rev },
									processBox: {
										type: 'load',
										msg: 'Loading script contents. Please wait ...'
									},
									success: function (data) {
										form.getForm().findField('scriptContents').setValue(data.scriptContents);
									}
								});
							}
						}
					}
				}]
			}, {
				xtype: 'fieldset',
				title: 'Script',
				labelWidth: 130,
				items: [new Scalr.Viewers.InfoPanel({
					html: "Built in variables:<br />" + moduleParams['variables'] + "<br /><br /> You may use own variables as %variable%. Variable values can be set for each role in farm settings.",
					style: 'margin-bottom: 10px',
					anchor:'-20'
				}), new Scalr.Viewers.WarningPanel({
					html: 'First line must contain shebang (#!/path/to/interpreter)',
					style: 'margin-bottom: 10px',
					anchor:'-20'
				}), {
					xtype: 'textarea',
					name: 'scriptContents',
					hideLabel: true,
					height:200,
					anchor:"-20",
					value: moduleParams['scriptContents']
				}]
			}]
		});
		
		if (moduleParams['scriptId']) {
			form.addButton({
				type: 'submit',
				text: 'Save changes in current version',
				handler: function() {
					Ext.Msg.wait('Please wait ...', 'Saving ...');
	
					form.getForm().submit({
						url: '/scripts/xSave/',
						params:{saveCurrentRevision:1, scriptId:moduleParams['scriptId']},
						success: function(form, action) {
							Ext.Msg.hide();
							Scalr.Viewers.SuccessMessage('Script successfully saved');
							Scalr.Viewers.EventMessager.fireEvent('close');
						},
						failure: Scalr.data.ExceptionFormReporter
					});
				}
			});
			
			form.addButton({
				type: 'submit',
				text: 'Save changes as new version (' + (parseInt(moduleParams['latestVersion'])+1) + ")",
				handler: function() {
					Ext.Msg.wait('Please wait ...', 'Saving ...');
	
					form.getForm().submit({
						url: '/scripts/xSave/',
						params:{saveCurrentRevision:0, scriptId:moduleParams['scriptId']},
						success: function(form, action) {
							Ext.Msg.hide();
							Scalr.Viewers.SuccessMessage('Script successfully saved');
							Scalr.Viewers.EventMessager.fireEvent('close');
						},
						failure: Scalr.data.ExceptionFormReporter
					});
				}
			});
		} else {
			form.addButton({
				type: 'submit',
				text: 'Save',
				handler: function() {
					Ext.Msg.wait('Please wait ...', 'Saving ...');
	
					form.getForm().submit({
						url: '/scripts/xSave/',
						success: function(form, action) {
							Ext.Msg.hide();
							Scalr.Viewers.SuccessMessage('Script successfully saved');
							Scalr.Viewers.EventMessager.fireEvent('close');
						},
						failure: Scalr.data.ExceptionFormReporter
					});
				}
			});
		}

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
