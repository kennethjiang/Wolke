{
	create: function (loadParams, moduleParams) {		
		var form = new Ext.form.FormPanel({
			scalrOptions: {
				'maximize': 'height'
			},
			plugins: [ new Scalr.Viewers.Plugins.findOne() ],
			width:900,
			frame: true,
			autoScroll: true,
			title: 'Services &raquo; Configurations &raquo; Presets &raquo Create',
			buttonAlign:'center',
			padding: '0px 22px 0px 5px',
			items: [{
				xtype: 'fieldset',
				title: 'Preset details',
				labelWidth: 150,
				items:[{
					xtype: 'textfield',
					name: 'presetName',
					fieldLabel: 'Name',
					width:200,
					value: moduleParams['presetName'],
					readOnly: moduleParams['presetName'] ? true : false
				   }, {
					xtype: 'combo',
					name: 'roleBehavior',
					fieldLabel: 'Service',
					width:200,
					typeAhead:false,
					selectOnFocus:false,
					forceSelection:true,
					readOnly: moduleParams['roleBehavior'] ? true : false,
					triggerAction:'all',
					editable:false,
					emptyText:'Please select service...',
					autoSelect:true,
					mode:'local',
					store:new Ext.data.ArrayStore({
						id:0,
						fields: ['rid','title'],
						data:[['mysql','MySQL'],['app','Apache'],['memcached','Memcached'],['cassandra','Cassandra'],['www','Nginx']]
					}),
					valueField:'rid',
					displayField:'title',
					hiddenName:'roleBehavior',
					listeners: {
						'select': function(combo, record) {
							form.el.mask("Loading configuration options");
							Ext.Ajax.request({
								url: '/services/configurations/presets/xGetPresetOptions',
								params: {'presetId':moduleParams['presetId'], 'presetName': form.findOne('name', 'presetName').getValue(), 'roleBehavior': form.findOne('name', 'roleBehavior').getValue()},
								success: function (response) {
									var result = Ext.decode(response.responseText), field = form.findOne('itemId', 'optionsSet');
									if (result.success) {
										field.removeAll();
										form.getForm().cleanDestroyed();
										field.add(result.presetOptions);
										field.show();										
										field.doLayout();
										
										field.items.each(function () {
											var el = this.el.child("img.tipHelp");
											new Ext.ToolTip({
												target: el.id,
												dismissDelay: 0,
												html: this.initialConfig.items[1].hText
											});
										});

									} else {
										Scalr.Viewers.ErrorMessage(result.error);
									}
									form.el.unmask();
								}
							});
						}
					}
				}]
		    }, {
		    	xtype: 'fieldset',
				title: 'Configuration options',
				labelWidth: 240,
				itemId:'optionsSet',
				hidden:true,
				items: []
		    }]
		});
		
		form.on('afterrender', function(){
			if (moduleParams['roleBehavior']) {
				form.findOne('name', 'roleBehavior').setValue(moduleParams['roleBehavior']);
				form.findOne('name', 'roleBehavior').fireEvent('select');
			}
		});
		
		form.addButton({
			type: 'submit',
			text: 'Save',
			handler: function() {
				Ext.Msg.wait('Please wait ...', 'Saving configuration preset ...');
				form.getForm().submit({
					url: '/services/configurations/presets/xSave/',
					params: {'presetId': moduleParams['presetId']},
					success: function(form, action) {
						Scalr.Viewers.SuccessMessage('Preset successfully saved');
						document.location.href = '#/services/configurations/presets';
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
