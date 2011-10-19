{
	create: function (loadParams, moduleParams) {
				
		function isValidIPAddress(ipaddr) {
		   var re = /^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/;
		   if (re.test(ipaddr)) {
		      var parts = ipaddr.split(".");
		      if (parseInt(parseFloat(parts[0])) == 0) { return false; }
		      for (var i=0; i<parts.length; i++) {
		         if (parseInt(parseFloat(parts[i])) > 255) { return false; }
		      }
		      return true;
		   } else {
		      return false;
		   }
		}
		
		var form = new Ext.form.FormPanel({
			scalrOptions: {
				/*'maximize': 'height'*/
			},
			plugins: [ new Scalr.Viewers.Plugins.findOne() ],
			width:700,
			frame: true,
			autoScroll: true,
			title: 'Import server - Step 1 (Server details)',
			buttonAlign:'center',
			padding: '0px 20px 0px 5px',
			items: [{
				xtype: 'fieldset',
				title: 'Server information',
				labelWidth: 130,
				items: [{
					anchor: '-20',
					xtype: 'combo',
					fieldLabel: 'Platform',
					store: moduleParams['platforms'],
					allowBlank: false,
					editable:false,
					hiddenName: 'platform',
					value: 'ec2',
					itemId:'platform_combo',
					forceSelection: true,
					mode: 'local',
					triggerAction: 'all',
					selectOnFocus: false,
					listeners:{'select':function(combo, record){
						if (record.get('field1') == 'eucalyptus' || record.get('field1') == 'rackspace') {
							
							if (record.get('field1') == 'eucalyptus') {
								var lstore = moduleParams['euca_locations'];
							} else if (record.get('field1') == 'rackspace') {
								var lstore = moduleParams['rs_locations'];
							}
							
							form.findOne('itemId', 'loc_combo').store.loadData(lstore);
							
							form.findOne('itemId', 'loc_combo').setValue(form.findOne('itemId', 'loc_combo').store.getAt(0).get('id'));
								
							
							form.findOne('itemId', 'loc_combo').show();
							form.findOne('itemId', 'loc_combo').enable();
						} else {
							form.findOne('itemId', 'loc_combo').hide();
							form.findOne('itemId', 'loc_combo').disable();
						}
						
						form.doLayout();
					}}
				}, {
					anchor: '-20',
					xtype: 'combo',
					fieldLabel: 'Cloud location',
					store: Scalr.data.createStore([], { idProperty: 'id', fields: [ 'id', 'name' ]}),
					allowBlank: false,
					valueField:'id',
					displayField:'name',
					itemId: 'loc_combo',
					editable: false,
					hiddenName: 'cloudLocation',
					autoSelect: true,
					value: '',
					forceSelection: true,
					mode: 'local',
					triggerAction: 'all',
					selectOnFocus: false,
					hidden:true
				}, {
					anchor: '-20',
					xtype: 'combo',
					fieldLabel: 'Behavior',
					store: moduleParams['behaviors'],
					allowBlank: false,
					hiddenName: 'behavior',
					value: 'base',
					typeAhead: true,
					forceSelection: true,
					mode: 'local',
					triggerAction: 'all',
					selectOnFocus: false
				}, {
					xtype: 'textfield',
					name: 'remoteIp',
					fieldLabel: 'Server IP address',
					validator: isValidIPAddress,
					anchor: "-20"
				}, {
					xtype: 'textfield',
					name: 'roleName',
					fieldLabel: 'Role name',
					anchor: "-20",
					value: ''
				}]
			}]
		});
		
		if (form.findOne('itemId', 'platform_combo').getValue() == 'eucalyptus' || form.findOne('itemId', 'platform_combo').getValue() == 'rackspace') {
			
			if (form.findOne('itemId', 'platform_combo').getValue() == 'eucalyptus') {
				var lstore = moduleParams['euca_locations'];
			} else if (form.findOne('itemId', 'platform_combo').getValue() == 'rackspace') {
				var lstore = moduleParams['rs_locations'];
			}
			
			form.findOne('itemId', 'loc_combo').store.loadData(lstore);
			
			form.findOne('itemId', 'loc_combo').setValue(form.findOne('itemId', 'loc_combo').store.getAt(0).get('id'));
			
			form.findOne('itemId', 'loc_combo').show();
			form.findOne('itemId', 'loc_combo').enable();
		} else {
			form.findOne('itemId', 'loc_combo').hide();
			form.findOne('itemId', 'loc_combo').disable();
		}
		
		form.addButton({
			type: 'submit',
			text: 'Continue',
			handler: function() {
				if (form.getForm().isValid()) {
					Ext.Msg.wait('Please wait ...', 'Initializing import ...');

					form.getForm().submit({
						url: '/servers/xImportStart/',
						success: function(form, action) {
							document.location.href = '#/servers/' + action.result.serverId + '/importCheck';
						},
						failure: Scalr.data.ExceptionFormReporter
					});
				}
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
