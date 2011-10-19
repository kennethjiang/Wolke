{
	create: function (loadParams, moduleParams) {
		var form = new Ext.form.FormPanel({
			scalrOptions: {
				/*'maximize': 'height'*/
			},
			plugins: [ new Scalr.Viewers.Plugins.findOne() ],
			width:700,
			frame: true,
			autoScroll: true,
			title: 'Tools &raquo; Amazon Web Services &raquo; VPC &raquo; VPN gateways &raquo; Create',
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
			        value: '',
			        displayField:'cloudLocation',
			        hiddenName:'cloudLocation',
			        itemId: 'cloudLocation',
			        typeAhead: false,
			        mode: 'local',
			        triggerAction: 'all',
			        selectOnFocus:false,
			        width:200,
			        listeners:{select: function(combo, record){
			        	
			        	form.el.mask('Loading availability zones');
			        	
			        	form.findOne('name', 'availabilityZone').store.baseParams.cloudLocation = combo.getValue();
			        	form.findOne('name', 'availabilityZone').store.load({
			        		callback: function() {
			        			form.findOne('name', 'availabilityZone').setValue('');
			        			form.el.unmask();
			        		}
			        	});
			        }}
				}, {
					fieldLabel:'Availability zone',
					xtype: 'combo',
					store: new Scalr.data.Store({
						url: '/platforms/ec2/xGetAvailZones',
						reader: new Scalr.data.JsonReader({
							id: 'id',
							fields: [ 'id', 'name' ]
						}),
						baseParams: { cloudLocation:'us-east-1' }
					}),
					valueField: 'id',
					displayField: 'name',
					editable: false,
					mode: 'local',
					name: 'availabilityZone',
					triggerAction: 'all',
					width: 200
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
					url: '/tools/aws/vpc/gateways/xSaveVpn',
					params: loadParams,
					success: function(form, action) {
						Ext.Msg.hide();
						Scalr.Viewers.SuccessMessage('VPN gateway successfully created');
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
