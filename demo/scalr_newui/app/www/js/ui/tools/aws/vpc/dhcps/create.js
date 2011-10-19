{
	create: function (loadParams, moduleParams) {
		var form = new Ext.form.FormPanel({
			scalrOptions: {
				/*'maximize': 'height'*/
			},
			width:700,
			frame: true,
			autoScroll: true,
			title: 'Tools &raquo; Amazon Web Services &raquo; VPC &raquo; DHCP options &raquo; Create',
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
					fieldLabel:'Domain name',
					anchor:"-20",
					items:[{
						xtype: 'textfield',
						name: 'domainName',
						value: ''
					}, {
						xtype:'displayfield',
						cls: 'x-form-check-wrap',
						value:'(e.g. example.com)'
					}]
				}, {
					xtype:'compositefield',
					fieldLabel:'Domain name servers',
					anchor:"-20",
					items:[{
						xtype: 'textfield',
						name: 'nameServers',
						value: ''
					}, {
						xtype:'displayfield',
						cls: 'x-form-check-wrap',
						value:'Enter up to 4 DNS server IP addresses separated by commas'
					}]
				}, {
					xtype:'compositefield',
					fieldLabel:'NTP servers',
					anchor:"-20",
					items:[{
						xtype: 'textfield',
						name: 'ntpServers',
						value: ''
					}, {
						xtype:'displayfield',
						cls: 'x-form-check-wrap',
						value:'Enter up to 4 DNS server IP addresses separated by commas'
					}]
				}, {
					xtype:'compositefield',
					fieldLabel:'NetBIOS name servers',
					anchor:"-20",
					items:[{
						xtype: 'textfield',
						name: 'netbiosServers',
						value: ''
					}, {
						xtype:'displayfield',
						cls: 'x-form-check-wrap',
						value:'Enter up to 4 NetBIOS name server IP addresses separated by commas'
					}]
				}, {
					xtype:'compositefield',
					fieldLabel:'NetBIOS node type',
					anchor:"-20",
					items:[{
						xtype: 'textfield',
						name: 'netbiosType',
						value: ''
					}, {
						xtype:'displayfield',
						cls: 'x-form-check-wrap',
						value:'Enter NetBIOS node type (1, 2, 4, or 8)'
					}]
				}]
			}]
		});
		
		form.addButton({
			type: 'submit',
			text: 'Save',
			handler: function() {
				Ext.Msg.wait('Please wait ...', 'Saving ...');

				form.getForm().submit({
					url: '/tools/aws/vpc/dhcps/xSaveDhcp',
					params: loadParams,
					success: function(form, action) {
						Ext.Msg.hide();
						Scalr.Viewers.SuccessMessage('DHCP option successfully created');
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
