{
	create: function (loadParams, moduleParams) {
		
		var form = new Ext.form.FormPanel({
			scalrOptions: {
				/*'maximize': 'height'*/
			},
			width:700,
			frame: true,
			autoScroll: true,
			title: 'Tools &raquo; Amazon Web Services &raquo; EBS &raquo; Volumes &raquo; '+loadParams['volumeId']+' &raquo;Attach',
			buttonAlign:'center',
			padding: '0px 20px 0px 5px',
			plugins: [ new Scalr.Viewers.Plugins.findOne() ],
			items: [{
				xtype: 'fieldset',
				title: 'Attach options',
				labelWidth: 130,
				items: [{
					fieldLabel: 'Server',
					xtype: 'combo',
					allowBlank: false,
					editable: false, 
			        store: Scalr.data.createStore(moduleParams.servers, { idProperty: 'id', fields: [ 'id', 'name' ]}),
			        value: '',
			        displayField: 'name',
					valueField: 'id',
			        hiddenName:'serverId',
			        typeAhead: false,
			        mode: 'local',
			        triggerAction: 'all',
			        selectOnFocus:false,
			        anchor:'-20',
			        listeners: {
						added: function() {
							this.setValue(this.store.getAt(0).get('id'));
						}
					}
				}]
			}, {
				xtype: 'fieldset',
				title: 'Always attach this volume to selected server',
				collapsed: true,
				deferredRender: false,
				checkboxName: 'attachOnBoot',
				checkboxToggle:true,
				forceLayout: true,
				labelWidth: 100,
				defaults: {
					anchor: '-20'
				},
				items: [{
					xtype: 'compositefield',
					hideLabel: true,
					items: [{
						xtype:'checkbox',
						name:'mount',
						inputValue:1,
						checked: false
					}, {
						xtype:'displayfield',
						cls: 'x-form-check-wrap',
						value:'Automatically mount this volume after attach to '
					}, {
						xtype:'textfield',
						name:'mountPoint',
						value:'/mnt/storage',
						cls: 'x-form-check-wrap'
					}]
				}]
			}]
		});
		
		form.addButton({
			type: 'submit',
			text: 'Attach',
			handler: function() {
				Ext.Msg.wait('Please wait ...', 'Attaching ...');

				form.getForm().submit({
					url: '/tools/aws/ec2/ebs/volumes/xAttach',
					params: loadParams,
					success: function(basicForm, action) {
						Ext.Msg.hide();
						Scalr.Viewers.SuccessMessage('EBS volume successfully attached');
						
						document.location.href = '#/tools/aws/ec2/ebs/volumes/' +
							loadParams['volumeId'] +
							'/view?cloudLocation=' +
							loadParams['cloudLocation'];
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
