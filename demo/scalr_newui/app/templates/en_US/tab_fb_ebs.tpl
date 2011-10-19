{literal}
new Scalr.Viewers.FarmRolesEditTab({
	tabTitle: 'EBS',
	layout: 'form',
	labelWidth: 30,
	snapshotCloud: {},

	isEnabled: function (record) {
		return record.get('platform') == 'ec2';
	},

	getDefaultValues: function (record) {
		return {
			'aws.use_ebs': 0
		};
	},

	activateTab: function () {

/*
						When new instance initialized, Scalr will<br>
						1. Attach a first detached volume, left by terminated or crashed instance or create a new EBS volume, attach it, and create an ext3 filesystem on it.<br />
						2. If "Automatically mount device" option selected, volume will be mounted.<br>
*/

/*		new Ext.ToolTip({
			target: this.findOne('name', 'aws.use_elastic_ips_help').id,
			dismissDelay: 0,
			html:
				"If this option is enabled," +
				"Scalr will assign Elastic IPs to all instances of this role. It usually takes few minutes for IP to assign." +
				"The amount of allocated IPs increases when new instances start," +
				"but not decreases when instances terminated." +
				"Elastic IPs are assigned after instance initialization." +
				"This operation takes few minutes to complete. During this time instance is not available from" +
				"the outside and not included in application DNS zone."
		});*/

		this.findOne('name', 'aws.ebs_snapid').on('beforequery', function (qe) {
			var field = this.findOne('name', 'aws.ebs_snapid');
			if (this.snapshotCloud[field.region]) {
				field.store.loadData(this.snapshotCloud[field.region]);
			} else {
				field.store.baseParams['Region'] = field.region;
				field.store.load();
				field.expand();
				qe.cancel = true;
			}
		}, this);

		this.findOne('name', 'aws.ebs_snapid').store.on('load', function (r, options, success) {
			if (success) {
				var t = [];
				this.findOne('name', 'aws.ebs_snapid').store.each(function (rec) {
					t[t.length] = rec.data;
				});
				this.snapshotCloud[this.findOne('name', 'aws.ebs_snapid').region] = { data: t };
			}

		}, this);

		this.findOne('name', 'aws.use_ebs').on('expand', function () {
			this.doLayout(false, true);
		}, this);

		this.findOne('name', 'aws.ebs_mount').on('check', function (checkbox, checked) {
			if (checked)
				this.findOne('name', 'aws.ebs_mountpoint').enable();
			else
				this.findOne('name', 'aws.ebs_mountpoint').disable();
		}, this);

	},

	showTab: function (record) {
		var settings = record.get('settings');

		this.findOne('name', 'aws.ebs_snapid').reset();
		this.findOne('name', 'aws.ebs_snapid').region = record.get('cloud_location');

		this.findOne('name', 'aws.ebs_size').setValue(settings['aws.ebs_size'] || '5');
		this.findOne('name', 'aws.ebs_snapid').setValue(settings['aws.ebs_snapid'] || '');
		this.findOne('name', 'aws.ebs_mountpoint').setValue(settings['aws.ebs_mountpoint'] || '/mnt/storage');

		if (settings['aws.use_ebs'] == 1) {
			this.findOne('name', 'aws.use_ebs').expand();
		} else {
			this.findOne('name', 'aws.use_ebs').collapse();
			this.findOne('name', 'aws.ebs_mountpoint').disable();
		}

		if (settings['aws.ebs_mount'] == 1) {
			this.findOne('name', 'aws.ebs_mount').setValue(true);
			this.findOne('name', 'aws.ebs_mountpoint').enable();
		} else {
			this.findOne('name', 'aws.ebs_mount').setValue(false);
			this.findOne('name', 'aws.ebs_mountpoint').disable();
		}
	},

	hideTab: function (record) {
		var settings = record.get('settings');

		if (! this.findOne('name', 'aws.use_ebs').collapsed) {
			settings['aws.use_ebs'] = 1;
			settings['aws.ebs_size'] = this.findOne('name', 'aws.ebs_size').getValue();
			settings['aws.ebs_snapid'] = this.findOne('name', 'aws.ebs_snapid').getValue();

			if (this.findOne('name', 'aws.ebs_mount').getValue()) {
				settings['aws.ebs_mount'] = 1;
				settings['aws.ebs_mountpoint'] = this.findOne('name', 'aws.ebs_mountpoint').getValue();
			} else {
				settings['aws.ebs_mount'] = 0;
				delete settings['aws.ebs_mountpoint'];
			}
		} else {
			settings['aws.use_ebs'] = 0;
			delete settings['aws.ebs_mountpoint'];
			delete settings['aws.ebs_size'];
			delete settings['aws.ebs_snapid'];
			delete settings['aws.ebs_mount'];
			delete settings['aws.ebs_mountpoint'];
		}

		record.set('settings', settings);
	},

	items: [{
		xtype: 'fieldset',
		name: 'aws.use_ebs',
		checkboxToggle: true,
		title: 'Automatically attach EBS volume with the following options:',
		items: [{
			xtype: 'compositefield',
			hideLabel: true,
			items: [{
				xtype: 'displayfield',
				cls: 'x-form-check-wrap',
				value: 'Size'
			}, {
				xtype: 'textfield',
				name: 'aws.ebs_size',
				width: 40
			}, {
				xtype: 'displayfield',
				cls: 'x-form-check-wrap',
				value: 'GB'
			}]
		}, {
			xtype: 'combo',
			name: 'aws.ebs_snapid',
			fieldLabel: 'Snapshot',
			editable: true,
			forceSelection: true,
			width: 400,
			typeAhead: true,
			selectOnFocus: true,
			triggerAction: 'all',
			valueField: 'snapid',
			displayField: 'snapid',
			mode: 'local',
			tpl: '<tpl for="."><div class="x-combo-list-item">{snapid} (Created: {createdat}, Size: {size}GB)</div></tpl>',
			store: new Scalr.data.Store({
				url: '/server/ajax-ui-server-aws-ec2.php',
				reader: new Scalr.data.JsonReader({
					id: 'snapid',
					fields: ['snapid', 'createdat', 'size']
				}),
				baseParams: { action: 'GetSnapshotsList' }
			})
		}, {
			xtype: 'compositefield',
			hideLabel: true,
			items: [{
				xtype: 'checkbox',
				hideLabel: true,
				boxLabel: 'Automatically mount device to',
				name: 'aws.ebs_mount'
			}, {
				xtype: 'textfield',
				name: 'aws.ebs_mountpoint'
			}, {
				xtype: 'displayfield',
				cls: 'x-form-check-wrap',
				value: 'mount point.'
			}]
		}]
	}]
})
{/literal}
