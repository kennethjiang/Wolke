Scalr.regPage('Scalr.ui.scaling.metrics.create', function (loadParams, moduleParams) {
	var action = (!loadParams['metricId']) ? 'Create' : 'Edit';

	var form = new Ext.form.FormPanel({
		scalrOptions: {
			/*'maximize': 'height'*/
		},
		width:700,
		frame: true,
		autoScroll: true,
		title: 'Scaling &raquo; Metrics &raquo; ' + action,
		buttonAlign:'center',
		padding: '0px 20px 0px 5px',
		items: [
			{
				xtype: 'fieldset',
				title: 'General information',
				labelWidth: 320,
				items:[{
					xtype: 'textfield',
					name: 'name',
					width:200,
					fieldLabel: 'Name',
					value: moduleParams['name']
				}, {
					xtype: 'textfield',
					name: 'filePath',
					width:200,
					fieldLabel: 'File path',
					value: moduleParams['filePath']
				}, {
					xtype: 'combo',
					name: 'retrieveMethod',
					fieldLabel: 'Retrieve method',
					width:100,
					typeAhead:false,
					selectOnFocus:false,
					forceSelection:true,
					triggerAction:'all',
					editable:false,
					value: (moduleParams['retrieveMethod'] || 'read'),
					mode:'local',
					store:new Ext.data.ArrayStore({
						id:0,
						fields: ['rid','title'],
						data:[['read','File-Read'],['execute','File-Execute']]
					}),
					valueField:'rid',
					displayField:'title',
					hiddenName:'retrieveMethod'
				}, {
					xtype: 'combo',
					name: 'calcFunction',
					fieldLabel: 'Calculation function',
					width:100,
					typeAhead:false,
					selectOnFocus:false,
					forceSelection:true,
					triggerAction:'all',
					editable:false,
					value: (moduleParams['calcFunction'] || 'avg'),
					mode:'local',
					store:new Ext.data.ArrayStore({
						id:0,
						fields: ['rid','title'],
						data:[['avg','Average'],['sum','Sum']]
					}),
					valueField:'rid',
					displayField:'title',
					hiddenName:'calcFunction'
				}]
			}
		],
		buttonAlign: 'center'
	});

	form.addButton({
		type: 'submit',
		text: 'Save',
		handler: function() {
			Ext.Msg.wait('Please wait ...', 'Saving ...');

			form.getForm().submit({
				url: '/scaling/metrics/xSave',
				params: {metricId: loadParams['metricId']},
				success: function(form, action) {
					Ext.Msg.hide();
					Scalr.Viewers.SuccessMessage('Scaling metric successfully saved');
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
});
