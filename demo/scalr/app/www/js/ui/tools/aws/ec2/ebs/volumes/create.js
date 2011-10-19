{
	create: function (loadParams, moduleParams) {
		
		//console.log(moduleParams);
		
		var form = new Ext.form.FormPanel({
			scalrOptions: {
				/*'maximize': 'height'*/
			},
			width:700,
			frame: true,
			autoScroll: true,
			title: 'Tools &raquo; Amazon Web Services &raquo; EBS &raquo; Volumes &raquo; Create',
			buttonAlign:'center',
			padding: '0px 20px 0px 5px',
			plugins: [ new Scalr.Viewers.Plugins.findOne() ],
			items: [{
				xtype: 'fieldset',
				title: 'Placement information',
				labelWidth: 130,
				items: [{
					fieldLabel: 'Cloud location',
					xtype: 'combo',
					allowBlank: false,
					editable: false, 
			        store: Scalr.data.createStore(moduleParams.locations, { idProperty: 'id', fields: [ 'id', 'name' ]}),
			        value: loadParams['cloudLocation'] || 'us-east-1',
			        displayField: 'name',
					valueField: 'id',
			        hiddenName:'cloudLocation',
			        typeAhead: false,
			        mode: 'local',
			        triggerAction: 'all',
			        selectOnFocus:false,
			        width:200,
			        listeners: { select:function(combo, record, index){
		        		form.findOne('name','availabilityZone').store.baseParams.cloudLocation = combo.getValue();
		        		form.findOne('name','availabilityZone').setValue();
		        		form.findOne('name','availabilityZone').store.load();
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
						baseParams: { cloudLocation: loadParams['cloudLocation'] }
					}),
					valueField: 'id',
					displayField: 'name',
					editable: false,
					mode: 'remote',
					name: 'availabilityZone',
					triggerAction: 'all',
					width: 200
				}]
			}, {
				xtype: 'fieldset',
				title: 'Volume information',
				labelWidth: 130,
				items: [{
					xtype:'compositefield',
					fieldLabel:'Size',
					anchor:"-20",
					items:[{
						xtype: 'textfield',
						name: 'size',
						value: '1'
					}, {
						xtype:'displayfield',
						cls: 'x-form-check-wrap',
						value:'GB'
					}]
				}, {
					xtype: 'textfield',
					fieldLabel:'Snapshot',
					readOnly:true,
					name: 'snapshotId',
					value: loadParams['snapshotId'] || ''
				}]
			}]
		});
		
		form.addButton({
			type: 'submit',
			text: 'Create',
			handler: function() {
				Ext.Msg.wait('Please wait ...', 'Saving ...');

				form.getForm().submit({
					url: '/tools/aws/ec2/ebs/volumes/xCreate',
					success: function(basicForm, action) {
						Ext.Msg.hide();
						Scalr.Viewers.SuccessMessage('EBS volume successfully created');
						
						document.location.href = '#/tools/aws/ec2/ebs/volumes/' +
							action.result.data.volumeId +
							'/view?cloudLocation=' +
							form.findOne('hiddenName','cloudLocation').getValue();
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
