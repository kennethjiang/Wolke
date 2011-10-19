{
	create: function (loadParams, moduleParams) {
		var form = new Ext.form.FormPanel({
			scalrOptions: {
				/*'maximize': 'height'*/
				modal:true
			},
			width:700,
			frame: true,
			autoScroll: true,
			title: 'Bundle task information',
			buttonAlign:'center',
			padding: '0px 20px 0px 5px',
			items: [{
				xtype: 'fieldset',
				title: 'General information',
				labelWidth: 130,
				items: [{
					xtype: 'displayfield',
					name: 'email',
					fieldLabel: 'Failure reason',
					readOnly:true,
					anchor:"-20",
					value: '<span style="color:red;">'+moduleParams['failure_reason']+'</span>'
				}
				]
			}]
		});

		form.addButton({
			type: 'reset',
			text: 'Close',
			handler: function() {
				Scalr.Viewers.EventMessager.fireEvent('close');
			}
		});

		return form;
	}
}
