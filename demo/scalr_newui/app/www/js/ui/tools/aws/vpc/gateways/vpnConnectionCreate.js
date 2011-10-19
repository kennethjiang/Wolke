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
			title: 'Tools &raquo; Amazon Web Services &raquo; VPC &raquo; VPN connection &raquo; Create',
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
			        	
			        	form.el.mask('Loading gateways');
			        	
						Ext.Ajax.request({
							url: '/tools/aws/vpc/gateways/getVpnConnectionCreateData',
							params:{cloudLocation: combo.getValue()},
							success: function(response, options) {
								var result = Ext.decode(response.responseText);

								if (result.data) {
									form.findOne('name', 'customerGatewayId').store.loadData(result.data.customerGatewayIds);
									form.findOne('name', 'vpnGatewayId').store.loadData(result.data.vpnGatewayIds);
								}

								form.el.unmask();
							}
						});
						
			        }}
				}, {
					fieldLabel:'Customer gateway',
					xtype: 'combo',
					store: new Ext.data.ArrayStore({
						idIndex: 0,
						fields: ['id']
					}),
					valueField: 'id',
					displayField: 'id',
					editable: false,
					mode: 'local',
					name: 'customerGatewayId',
					triggerAction: 'all',
					width: 200
				}, {
					fieldLabel:'VPN gateway',
					xtype: 'combo',
					store: new Ext.data.ArrayStore({
						idIndex: 0,
						fields: ['id']
					}),
					valueField: 'id',
					displayField: 'id',
					editable: false,
					mode: 'local',
					name: 'vpnGatewayId',
					triggerAction: 'all',
					width: 200
				}]
			}]
		});
		
		form.addButton({
			type: 'submit',
			text: 'Save',
			handler: function() {
				Ext.Msg.wait('Please wait ...', 'Saving ...');

				form.getForm().submit({
					url: '/tools/aws/vpc/gateways/xSaveVpnConnection',
					params: loadParams,
					success: function(form, action) {
						Ext.Msg.hide();
						Scalr.Viewers.SuccessMessage('VPN connection successfully created');
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
