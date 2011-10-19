{
	create: function (loadParams, moduleParams) {
				
		function waitHello() {			
			Ext.Ajax.request({
				url: '/servers/xImportWaitHello/',
				params:{ serverId: moduleParams['serverId']},
				success: function (response) {
					var result = Ext.decode(response.responseText);
					if (result.success == true) {
						Scalr.Viewers.SuccessMessage('Communication successfully established. Role creation process initialized.');
						document.location.href = '#/bundletasks/'+result.bundleTaskId+'/logs';
					}
					else
						waitHello.defer(2000);
				}
			});
		}
		
		var form = new Ext.form.FormPanel({
			scalrOptions: {
				/*'maximize': 'height'*/
			},
			width:700,
			frame: true,
			autoScroll: true,
			title: 'Import server - Step 2 (Establish communication)',
			buttonAlign:'center',
			padding: '0px 20px 0px 5px',
			items: [{
				xtype: 'fieldset',
				title: 'Install scalarizr',
				labelWidth: 130,
				items: [{
					xtype:'displayfield',
					hideLabel:true,
					value:'<a target="_blank" href="http://wiki.scalr.net/Tutorials/Import_a_non_Scalr_server">Please follow this instruction to install scalarizr on your server</a>'
				}]
			}, {
				xtype: 'fieldset',
				title: 'Launch scalarizr',
				labelWidth: 130,
				items: [{
					xtype:'displayfield',
					hideLabel:true,
					value:'When scalarizr installed please use the following command to launch it:'
				},
				{
					xtype:'textarea',
					hideLabel:true,
					anchor:"-20",
					height:100,
					value:moduleParams['cmd']
				}]
			}, {
				xtype: 'fieldset',
				title: 'Establishing communication',
				labelWidth: 130,
				items: [{
					xtype:'displayfield',
					hideLabel:true,
					value:'<img style="vertical-align:middle;" src="/images/ui-ng/loading.gif"> Waiting for running scalarizr on server ...'
				}]
			}]
		});

		form.addButton({
			type: 'reset',
			text: 'Cancel server import',
			handler: function() {
				Ext.Msg.wait("Please wait ...");

				Ext.Ajax.request({
					url: '/servers/xServerCancelOperation/',
					params: { serverId: moduleParams['serverId'] },
					success: function (response) {
						var result = Ext.decode(response.responseText);
						if (result.success == true) {
							Scalr.Viewers.SuccessMessage(result.message);
							
							document.location.href = '#/servers/view';
							
						} else if (result.error)
							Scalr.Viewers.ErrorMessage(result.error);

						Ext.Msg.hide();
					},
					failure: function() {
						Ext.Msg.hide();
					}
				});
			}
		});

		waitHello();
		
		return form;
	}
}
