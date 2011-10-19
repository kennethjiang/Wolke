Scalr.regPage('Scalr.ui.farms.builder.tabs.placement', function () {
	return new Scalr.ui.farms.builder.tab({
		tabTitle: '位置与类型',
		layout: 'form',
		labelWidth: 30,
		availZones: {},

		isEnabled: function (record) {
			return record.get('platform') == 'ec2';
		},

		getDefaultValues: function (record) {
			return {
				'aws.availability_zone': '',
				'aws.instance_type': record.get('arch') == 'i386' ? 'm1.small' : 'm1.large'
			};
		},

		activateTab: function () {
			this.findOne('name', 'aws.availability_zone').store.loadData({
				data: [
					{ id: 'x-scalr-diff', name: 'Place in different zones' },
					{ id: '', name: 'Choose randomly' }
				]
			});

			new Ext.ToolTip({
				target: this.findOne('itemId', 'aws.availability_zone_warn').id,
				dismissDelay: 0,
				autoHide: false,
				html: ('If you want to change placement, you need to remove Master EBS volume first on <a href="/farm_mysql_info.php?farmid=%FARMID%">MySQL status page</a>.').replace('%FARMID%', this.farmId)
			});

			this.findOne('name', 'aws.availability_zone').on('beforequery', function (qe) {
				var field = this.findOne('name', 'aws.availability_zone');
				if (this.availZones[field.region]) {
					field.store.loadData(this.availZones[field.region]);
				} else {
					field.store.baseParams['Region'] = field.region;
					field.store.load({
						saveFlag: true
					});
					field.expand();
					qe.cancel = true;
				}
			}, this);

			this.findOne('name', 'aws.availability_zone').store.on('load', function (store, record, options) {
				var t = [];

				if (options.saveFlag) {
					store.insert(0, [
						new store.recordType({ id: 'x-scalr-diff', name: 'Place in different zones' }),
						new store.recordType({ id: '', name: 'Choose randomly' })
					]);

					var f = this.findOne('name', 'aws.availability_zone'), r = f.findRecord(f.valueField, f.value);
					f.view.refresh();

					if (r)
						f.select(f.store.indexOf(r) - 1, true); // hack instead of f.selectByValue
				}

				store.each(function (rec) {
					t[t.length] = rec.data;
				});
				this.availZones[this.findOne('name', 'aws.availability_zone').region] = { data: t };
			}, this);
		},

		showTab: function (record) {
			var settings = record.get('settings');

			var tagsString = record.get('tags').join(" ");
			var typeArray = new Array();
			var typeValue = '';

			if (record.get('arch') == 'i386') {

				if (tagsString.indexOf('ec2.ebs') != -1 || settings['aws.instance_type'] == 't1.micro')
					typeArray = ['t1.micro', 'm1.small', 'c1.medium'];
				else
					typeArray = ['m1.small', 'c1.medium'];

				typeValue = (settings['aws.instance_type'] || 'm1.small');
			} else {

				if (tagsString.indexOf('ec2.ebs') != -1 || settings['aws.instance_type'] == 't1.micro')
				{
					if (tagsString.indexOf('ec2.hvm') != -1)
					{
						typeArray = ['t1.micro', 'm1.large', 'm1.xlarge', 'c1.xlarge', 'm2.xlarge', 'm2.2xlarge', 'm2.4xlarge', 'cc1.4xlarge', 'cg1.4xlarge'];
					}
					else
					{
						typeArray = ['t1.micro', 'm1.large', 'm1.xlarge', 'c1.xlarge', 'm2.xlarge', 'm2.2xlarge', 'm2.4xlarge'];
					}
				}
				else
					typeArray = ['m1.large', 'm1.xlarge', 'c1.xlarge', 'm2.xlarge', 'm2.2xlarge', 'm2.4xlarge'];

				typeValue = (settings['aws.instance_type'] || 'm1.large');
			}

			this.findOne('name', 'aws.instance_type').store.loadData(typeArray);
			this.findOne('name', 'aws.instance_type').setValue(typeValue);


			this.findOne('name', 'aws.availability_zone').setValue(settings['aws.availability_zone'] || '');
			this.findOne('name', 'aws.availability_zone').region = record.get('cloud_location');

			if (
				record.get('behaviors').match('mysql') &&
				settings['mysql.data_storage_engine'] == 'ebs' &&
				settings['mysql.master_ebs_volume_id'] != '' &&
				settings['mysql.master_ebs_volume_id'] != undefined &&
				this.findOne('name', 'aws.availability_zone').getValue() != '' &&
				this.findOne('name', 'aws.availability_zone').getValue() != 'x-scalr-diff'
			) {
				this.findOne('name', 'aws.availability_zone').disable();
				this.findOne('itemId', 'aws.availability_zone_warn').show();
			} else {
				this.findOne('name', 'aws.availability_zone').enable();
				this.findOne('itemId', 'aws.availability_zone_warn').hide();
			}
		},

		hideTab: function (record) {
			var settings = record.get('settings');

			settings['aws.instance_type'] = this.findOne('name', 'aws.instance_type').getValue();
			settings['aws.availability_zone'] = this.findOne('name', 'aws.availability_zone').getValue();

			record.set('settings', settings);
		},

		items: [{
			xtype: 'fieldset',
			items: [{
				xtype: 'compositefield',
				fieldLabel: '服务器位置',
				items: [{
					xtype: 'combo',
					store: new Scalr.data.Store({
						url: '/server/ajax-ui-server-aws-ec2.php',
						reader: new Scalr.data.JsonReader({
							id: 'id',
							fields: [ 'id', 'name' ]
						}),
						baseParams: { action: 'GetAvailZonesList' }
					}),
					valueField: 'id',
					displayField: 'name',
					editable: false,
					mode: 'local',
					name: 'aws.availability_zone',
					triggerAction: 'all',
					width: 200
				}, {
					xtype: 'displayfield',
					itemId: 'aws.availability_zone_warn',
					value: '<img src="/images/ui-ng/icons/warn_icon_16x16.png" style="padding: 2px; cursor: help;">'
				}]
			}, {
				xtype: 'combo',
				store: [],
				fieldLabel: '服务器类型',
				editable: false,
				mode: 'local',
				name: 'aws.instance_type',
				triggerAction: 'all',
				width: 200
			}]
		}]
	});
});
