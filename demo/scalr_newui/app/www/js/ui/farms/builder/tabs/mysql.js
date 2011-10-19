Scalr.regPage('Scalr.ui.farms.builder.tabs.mysql', function () {
	return new Scalr.ui.farms.builder.tab({
		tabTitle: 'MySQL设置',
		layout: 'form',
		labelWidth: 150,

		isEnabled: function (record) {
			return (record.get('behaviors').match('mysql') &&
				(
					record.get('platform') == 'ec2' ||
					record.get('platform') == 'rackspace'
				)
			);
		},

		getDefaultValues: function (record) {

			if (record.get('platform') == 'ec2')
				var default_storage_engine = 'ebs';
			else if (record.get('platform') == 'rackspace')
				var default_storage_engine = 'eph';

			return {
				'mysql.enable_bundle': 1,
				'mysql.bundle_every': 48,
				'mysql.pbw1_hh': '05',
				'mysql.pbw1_mm': '00',
				'mysql.pbw2_hh': '09',
				'mysql.pbw2_mm': '00',
				'mysql.data_storage_engine': default_storage_engine,
				'mysql.ebs_volume_size': 100,
				'mysql.enable_bcp': 0
			};
		},

		activateTab: function () {
			new Ext.ToolTip({
				target: this.findOne('name', 'mysql.bundle_every_help').id,
				dismissDelay: 0,
				html:
					"MySQL快照包含一个MySQL数据目录和文件的热拷贝包括二进制日志和debain.cnf" +
					"<br>" +
					"当云平台启动时:<br>" +
					"1. MySQL主服务器会从不同云平台的存储系统中下载并解压快照 <br>" +
					"2. 当主服务器成功下载并读取数据后，从服务器也会下载提取该镜像内容<br>" +
					"3. 从服务器会定期与主服务器进行数据同步"
			});

			this.findOne('name', 'mysql.enable_bcp').on('check', function (checkbox, checked) {
				if (checked)
					this.findOne('name', 'mysql.bcp_every').enable();
				else
					this.findOne('name', 'mysql.bcp_every').disable();
			}, this);

			this.findOne('name', 'mysql.ebs.rotate_snaps').on('check', function (checkbox, checked) {
				if (checked)
					this.findOne('name', 'mysql.ebs.rotate').enable();
				else
					this.findOne('name', 'mysql.ebs.rotate').disable();
			}, this);
		},

		showTab: function (record) {
			var settings = record.get('settings');

			if (settings['mysql.enable_bundle'] == 1)
				this.findOne('checkboxName', 'mysql.enable_bundle').expand();
			else
				this.findOne('checkboxName', 'mysql.enable_bundle').collapse();

			this.findOne('name', 'mysql.bundle_every').setValue(settings['mysql.bundle_every'] || 48);
			this.findOne('name', 'mysql.pbw1_hh').setValue(settings['mysql.pbw1_hh'] || '05');
			this.findOne('name', 'mysql.pbw1_mm').setValue(settings['mysql.pbw1_mm'] || '00');
			this.findOne('name', 'mysql.pbw2_hh').setValue(settings['mysql.pbw2_hh'] || '09');
			this.findOne('name', 'mysql.pbw2_mm').setValue(settings['mysql.pbw2_mm'] || '00');

			if (settings['mysql.enable_bcp'] == 1) {
				this.findOne('name', 'mysql.enable_bcp').setValue(true);
				this.findOne('name', 'mysql.bcp_every').enable();
			} else {
				this.findOne('name', 'mysql.enable_bcp').setValue(false);
				this.findOne('name', 'mysql.bcp_every').disable();
			}
			this.findOne('name', 'mysql.bcp_every').setValue(settings['mysql.bcp_every'] || 360);

			if (settings['mysql.data_storage_engine'] == 'ebs') {
				this.findOne('name', 'mysql.ebs_volume_size_composite').show();
				this.findOne('name', 'mysql.ebs.snaps_rotation').show();

				if (record.get('new'))
					this.findOne('name', 'mysql.ebs_volume_size').enable();
				else
					this.findOne('name', 'mysql.ebs_volume_size').disable();

				if (settings['mysql.ebs.rotate_snaps'] == 1) {
					this.findOne('name', 'mysql.ebs.rotate_snaps').setValue(true);
					this.findOne('name', 'mysql.ebs.rotate').enable();
				} else {
					this.findOne('name', 'mysql.ebs.rotate_snaps').setValue(false);
					this.findOne('name', 'mysql.ebs.rotate').disable();
				}
				this.findOne('name', 'mysql.ebs.rotate').setValue(settings['mysql.ebs.rotate'] || 5);

				this.findOne('name', 'mysql.ebs_volume_size').setValue(settings['mysql.ebs_volume_size'] || 100);

				if (record.get('new'))
					this.findOne('name', 'mysql.ebs_volume_size').enable();
				else
					this.findOne('name', 'mysql.ebs_volume_size').disable();

			} else {
				this.findOne('name', 'mysql.ebs_volume_size_composite').hide();
				this.findOne('name', 'mysql.ebs.snaps_rotation').hide();
			}

			this.findOne('name', 'mysql.storage_data_engine').setValue(settings['mysql.data_storage_engine']);
		},

		hideTab: function (record) {
			var settings = record.get('settings');

			if (! this.findOne('checkboxName', 'mysql.enable_bundle').collapsed) {
				settings['mysql.enable_bundle'] = 1;
				settings['mysql.bundle_every'] = this.findOne('name', 'mysql.bundle_every').getValue();
				settings['mysql.pbw1_hh'] = this.findOne('name', 'mysql.pbw1_hh').getValue();
				settings['mysql.pbw1_mm'] = this.findOne('name', 'mysql.pbw1_mm').getValue();
				settings['mysql.pbw2_hh'] = this.findOne('name', 'mysql.pbw2_hh').getValue();
				settings['mysql.pbw2_mm'] = this.findOne('name', 'mysql.pbw2_mm').getValue();
			} else {
				settings['mysql.enable_bundle'] = 0;
				delete settings['mysql.bundle_every'];
				delete settings['mysql.pbw1_hh'];
				delete settings['mysql.pbw1_mm'];
				delete settings['mysql.pbw2_hh'];
				delete settings['mysql.pbw2_mm'];
			}

			if (this.findOne('name', 'mysql.enable_bcp').getValue()) {
				settings['mysql.enable_bcp'] = 1;
				settings['mysql.bcp_every'] = this.findOne('name', 'mysql.bcp_every').getValue();
			} else {
				settings['mysql.enable_bcp'] = 0;
				delete settings['mysql.bcp_every'];
			}

			if (settings['mysql.data_storage_engine'] == 'ebs') {
				if (record.get('new'))
					settings['mysql.ebs_volume_size'] = this.findOne('name', 'mysql.ebs_volume_size').getValue();

				if (this.findOne('name', 'mysql.ebs.rotate_snaps').getValue()) {
					settings['mysql.ebs.rotate_snaps'] = 1;
					settings['mysql.ebs.rotate'] = this.findOne('name', 'mysql.ebs.rotate').getValue();
				} else {
					settings['mysql.ebs.rotate_snaps'] = 0;
					delete settings['mysql.ebs.rotate'];
				}
			} else {
				delete settings['mysql.ebs_volume_size'];
				delete settings['mysql.ebs.rotate_snaps'];
				delete settings['mysql.ebs.rotate'];
			}

			record.set('settings', settings);
		},

		items: [{
			xtype: 'fieldset',
			checkboxToggle:  true,
			checkboxName: 'mysql.enable_bundle',
			inputValue: 1,
			title: '绑定并保存数据快照',
			labelWidth: 150,
			items: [{
				xtype: 'compositefield',
				hideLabel: true,
				items: [{
					xtype: 'displayfield',
					cls: 'x-form-check-wrap',
					value: '执行数据绑定周期'
				}, {
					xtype: 'textfield',
					width: 40,
					name: 'mysql.bundle_every'
				}, {
					xtype: 'displayfield',
					cls: 'x-form-check-wrap',
					value: '小时'
				}, {
					xtype: 'displayfield',
					name: 'mysql.bundle_every_help',
					value: '<img src="/images/ui-ng/icons/info_icon_16x16.png" style="padding: 2px; cursor: help;">'
				}]
			}, {
				xtype: 'compositefield',
				fieldLabel: '执行数据绑定窗口',
				items: [{
					xtype: 'textfield',
					name: 'mysql.pbw1_hh',
					width: 40
				}, {
					xtype: 'displayfield',
					value: ':',
					cls: 'x-form-check-wrap'
				}, {
					xtype: 'textfield',
					name: 'mysql.pbw1_mm',
					width: 40
				}, {
					xtype: 'displayfield',
					value: '-',
					cls: 'x-form-check-wrap'
				}, {
					xtype: 'textfield',
					name: 'mysql.pbw2_hh',
					width: 40
				}, {
					xtype: 'displayfield',
					value: ':',
					cls: 'x-form-check-wrap'
				},{
					xtype: 'textfield',
					name: 'mysql.pbw2_mm',
					width: 40
				}, {
					xtype: 'displayfield',
					value: '格式: hh24:mi - hh24:mi',
					style: 'font-style: italic',
					cls: 'x-form-check-wrap'
				}]
			}]
		}, {
			xtype: 'fieldset',
			title: '备份设置',
			items: [{
				xtype: 'compositefield',
				hideLabel: true,
				items: [{
					xtype: 'checkbox',
					hideLabel: true,
					boxLabel: '执行备份周期',
					inputValue: 1,
					name: 'mysql.enable_bcp'
				}, {
					xtype: 'textfield',
					name: 'mysql.bcp_every',
					width: 40
				}, {
					xtype: 'displayfield',
					value: '分钟',
					cls: 'x-form-check-wrap'
				}]
			}]
		}, {
			xtype: 'fieldset',
			title: '数据存储设置',
			labelWidth: 150,
			items: [{
				xtype: 'displayfield',
				fieldLabel: '存储引擎',
				cls: 'x-form-check-wrap',
				name: 'mysql.storage_data_engine',
				value: 'ebs'
			}, {
				xtype: 'compositefield',
				name: 'mysql.ebs_volume_size_composite',
				items: [{
					xtype: 'textfield',
					fieldLabel: 'EBS 容量 (最大 1000 GB)',
					width: 40,
					name: 'mysql.ebs_volume_size'
				}]
			}, {
				xtype: 'compositefield',
				name: 'mysql.ebs.snaps_rotation',
				hideLabel: true,
				items: [{
					xtype: 'checkbox',
					hideLabel: true,
					name: 'mysql.ebs.rotate_snaps',
					inputValue: 1,
					boxLabel: '在被卸载前执行快照'
				}, {
					xtype: 'textfield',
					hideLabel: true,
					name: 'mysql.ebs.rotate',
					width: 40
				}, {
					xtype: 'displayfield',
					cls: 'x-form-check-wrap',
					value: '次'
				}]
			}]
		}]
	});
});
