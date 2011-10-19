{
	create: function (loadParams, moduleParams) {
		var form = new Ext.form.FormPanel({
			scalrOptions: {
				/*'maximize': 'height'*/
			},
			width:700,
			frame: true,
			autoScroll: true,
			title: '服务器组 &raquo; 生成',
			buttonAlign:'center',
			plugins: [ new Scalr.Viewers.Plugins.findOne(), new Scalr.Viewers.Plugins.prepareForm() ],
			padding: '0px 20px 0px 5px',
			items: [{
				xtype: 'fieldset',
				title: '服务器组信息',
				labelWidth: 130,
				items: [{
					xtype: 'textfield',
					name: 'farm_name',
					fieldLabel: '名称',
					readOnly:false,
					anchor:"-20",
					value: ''
				}, {
					xtype: 'textfield',
					name: 'farm_description',
					fieldLabel: '描述',
					readOnly:false,
					anchor:"-20",
					value: ''
				}, {
					xtype: 'combo',
					fieldLabel: '模板',
					name: 'scriptVersion',
					hiddenName: 'scriptVersion',
					store: ['WordPress模板'],
					editable: false,
					forceSelection: true,
					value: ['WordPress模板'],
					mode: 'local',
					triggerAction: 'all',
					selectOnFocus: false,
					listeners: {
						select: function () {
							
						}
					}
				}]
			}]
		});
		
		form.addButton({
			type: 'submit',
			text: '保存',
			handler: function() {
				Ext.Msg.wait('请稍等 ...', '正在保存 ...');

				var p = {}, i;

				//p['farm[id]'] = this.farmId;
				p['farm[name]'] = form.getForm().findField('farm_name').getValue();
				p['farm[description]'] = form.getForm().findField('farm_description').getValue();
				p['farm[roles_launch_order]'] = 0;

				p['roles[0]'] = '{"role_id":"18619","platform":"ec2","cloud_location":"us-east-1","settings":{"scaling.min_instances":"1","scaling.max_instances":"2","scaling.polling_interval":"1","scaling.keep_oldest":0,"scaling.safe_shutdown":0,"lb.use_elb":0,"aws.availability_zone":"us-east-1a","aws.instance_type":"m1.small","aws.use_elastic_ips":0,"aws.use_ebs":0,"system.timeouts.reboot":360,"system.timeouts.launch":2400,"aws.enable_cw_monitoring":0,"scaling.upscale.timeout_enabled":0,"scaling.downscale.timeout_enabled":0},"scaling":{},"scripting":[]}';
				p['roles[1]'] = '{"role_id":"18621","platform":"ec2","cloud_location":"us-east-1","settings":{"scaling.min_instances":"1","scaling.max_instances":"2","scaling.polling_interval":"1","scaling.keep_oldest":0,"scaling.safe_shutdown":0,"mysql.enable_bundle":1,"mysql.bundle_every":48,"mysql.pbw1_hh":"05","mysql.pbw1_mm":"00","mysql.pbw2_hh":"09","mysql.pbw2_mm":"00","mysql.data_storage_engine":"ebs","mysql.ebs_volume_size":100,"mysql.enable_bcp":0,"lb.use_elb":0,"aws.availability_zone":"us-east-1a","aws.instance_type":"m1.small","aws.use_elastic_ips":0,"aws.use_ebs":0,"system.timeouts.reboot":360,"system.timeouts.launch":2400,"aws.enable_cw_monitoring":0,"scaling.upscale.timeout_enabled":0,"scaling.downscale.timeout_enabled":0},"scaling":{},"scripting":[{"script":"deploy_wordpress_db","event":"HostInit","target":"farm","script_id":"4","timeout":1200,"issync":"0","version":"1","order_index":0,"params":{}}],"params":{}}';
				p['roles[2]'] = '{"role_id":"18627","platform":"ec2","cloud_location":"us-east-1","settings":{"scaling.min_instances":"1","scaling.max_instances":"2","scaling.polling_interval":"1","scaling.keep_oldest":0,"scaling.safe_shutdown":0,"lb.use_elb":0,"aws.availability_zone":"us-east-1a","aws.instance_type":"m1.small","aws.use_elastic_ips":0,"aws.use_ebs":0,"system.timeouts.reboot":360,"system.timeouts.launch":2400,"aws.enable_cw_monitoring":0,"scaling.upscale.timeout_enabled":0,"scaling.downscale.timeout_enabled":0},"scaling":{},"scripting":[]}';
				
				Ext.Ajax.request({
					url: '/server/farm_creator.php',
					params: p,
					success: function (response) {
						var result = Ext.decode(response.responseText);
						if (result && result.success == true) {
							if (this.farmId != '')
								Scalr.Message.Success('服务器组保存成功。');
							else
								Scalr.Message.Success('服务器组新建成功。');

							Ext.Msg.hide();
							Scalr.Viewers.EventMessager.fireEvent('close');
							
							document.location.href = '#/farms/' + result.farm_id + '/view';

						} else {
							if (result && result.error)
								Scalr.Viewers.ErrorMessage(result.error);
							Ext.Msg.hide();
						}
					},
					scope: this
				});
			}
		});
		

		form.addButton({
			type: 'reset',
			text: '取消',
			handler: function() {
				Scalr.Viewers.EventMessager.fireEvent('close');
			}
		});

		return form;
	}
}
