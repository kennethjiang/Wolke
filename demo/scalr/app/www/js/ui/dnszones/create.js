{
	create: function (loadParams, moduleParams) {
		var zone = moduleParams['zone'], records = moduleParams['records'];
		
		var dnsField = function (config, values) {
			values = values || {};
			config = Ext.apply({
				getValues: function () {
					var vals = {};
					this.items.each(function () {
						vals[this.itemId] = this.getValue();
					});

					var values = { name: vals['name'], value: vals['value'], issystem: vals['issystem'] };
					if (values.value != '') {
						values['id'] = this.itemId;
						values['ttl'] = vals['ttl'];
						values['type'] = vals['type'];

						if (values['type'] == 'MX' || values['type'] == 'SRV')
							values['priority'] = vals['priority'];

						if (values['type'] == 'SRV') {
							values['weight'] = vals['weight'];
							values['port'] = vals['port'];
						}

						return values;
					} else {
						return null;
					}
				},
				layout: 'hbox',
				layoutConfig: {
					align: 'stretch',
					pack: 'start'
				},
				hideLabel: true,
				anchor: '-20',
				disabled: (moduleParams['allowManageSystemRecords'] != '1' && values['issystem'] == '1') ? true : false,
				markInvalid: function (msg) {
					if (this.rendered && !this.preventMark) {
						this.items.each(function (c) {
							if (c.el && c.xtype != 'displayfield') {
								c.el.addClass(this.invalidClass);
							}
						});
					}

					Ext.form.CompositeField.prototype.markInvalid.call(this, msg);
				},

				items: [{
					xtype: 'hidden',
					itemId: 'issystem',
					value: values['issystem'] || '0'
				}, {
					xtype: 'textfield',
					itemId: 'name',
					emptyText: 'Domain',
					width: 160,
					value: values['name'] || ''
				}, {
					xtype: 'textfield',
					itemId: 'ttl',
					emptyText: 'TTL',
					width: 60,
					value: values['ttl'] || ''
				}, {
					xtype: 'combo',
					width: 80,
					listWidth: 80,
					store: [ 'A', 'CNAME', 'MX', 'TXT', 'NS', 'SRV'],
					editable: false,
					triggerAction: 'all',
					emptyText: 'Type',
					itemId: 'type',
					value: values['type'] || 'A',
					listeners: {
						select: function () {
							var value = this.getValue();

							this.ownerCt.getComponent('port').hide();
							this.ownerCt.getComponent('weight').hide();
							this.ownerCt.getComponent('priority').hide();

							if (value == 'MX' || value == 'SRV')
								this.ownerCt.getComponent('priority').show();

							if (value == 'SRV') {
								this.ownerCt.getComponent('weight').show();
								this.ownerCt.getComponent('port').show();
							}

							this.ownerCt.doLayout();
						},
						afterrender: function () {
							this.fireEvent('select');
						}
					}
				}, {
					xtype: 'textfield',
					itemId: 'priority',
					emptyText: 'priority',
					flex: 1,
					value: values['priority'] || ''
				}, {
					xtype: 'textfield',
					itemId: 'weight',
					emptyText: 'weight',
					flex: 1,
					value: values['weight'] || ''
				}, {
					xtype: 'textfield',
					itemId: 'port',
					emptyText: 'port',
					flex: 1,
					value: values['port'] || ''
				}, {
					xtype: 'textfield',
					itemId: 'value',
					emptyText: 'Record value',
					flex: 4,
					value: values['value'] || ''
				}, {
					xtype: 'displayfield',
					value: '<img src="/images/ui-ng/icons/add_icon_16x16.png">',
					itemId: 'add',
					cls: 'x-form-check-wrap',
					hidden: !(config.showAddButton || false),
					width: 20,
					listeners: {
						afterrender: function () {
							this.el.child('img').on('click', function () {
								var container = form.findOne('itemId', 'newDnsRecords');
								container.add(dnsField({ showAddButton: true }));
								container.doLayout();
								this.hide();
								this.ownerCt.doLayout();
							}, this);
						}
					}
				}, {
					xtype: 'displayfield',
					value: '<img src="/images/ui-ng/icons/remove_icon_16x16.png">',
					cls: 'x-form-check-wrap',
					hidden: !(config.showRemoveButton || false),
					width: 20,
					itemId: 'remove'
				}]
			}, config);

			var el = new Ext.form.CompositeField(config);

			el.items.each(function (c) {
				if (c.itemId == 'remove') {
					c.on('afterrender', function (c) {
						c.el.child('img').on('click', function () {
							var container = form.findOne('itemId', 'dnsRecords');
							container.remove(this);
						}, this);
					}, el);
				}

				c.on('blur', function () {
					this.items.each (function (c) {
						c.validate();
					});
				}, this);
			}, el);

			return el;
		};

		var form = new Ext.form.FormPanel({
			scalrOptions: {
				'maximize': 'maxHeight'
			},
			width: 900,
			//title: 'Environments &raquo; Edit &raquo; ' + moduleParams.env.name,
			title: (zone['domainId'] || 0) ? 'DNS Zones &raquo; Edit' : 'DNS Zones &raquo; Create',
			frame: true,
			labelWidth: 200,
			autoScroll: true,
			padding: '0px 20px 0px 5px',
			plugins: [ new Scalr.Viewers.Plugins.findOne() ],
			buttonAlign: 'center',
			items: [{
				xtype: 'fieldset',
				title: 'Domain name',
				layout: 'column',
				items: [{
					xtype: 'combo',
					store: [ [ 'scalr', 'Use domain automatically generated and provided by Scalr'], [ 'own', 'Use own domain name'] ],
					editable: false,
					triggerAction: 'all',
					columnWidth: 0.5,
					itemId: 'domainType',
					hiddenName: 'domainType',
					value: zone['domainType'],
					hidden: moduleParams['action'] == 'create' ? false : true,
					listeners: {
						select: function () {
							var field = form.findOne('itemId', 'domainName');
							if (form.findOne('itemId', 'domainType').getValue() == 'own') {
								field.enable();
								field.setValue('');
							} else {
								field.disable();
								field.setValue(zone['domainName']);
							}
						}
					}
				}, {
					xtype: 'textfield',
					itemId: 'domainName',
					name: 'domainName',
					disabled: zone['domainType'] == 'scalr' ? true : false,
					value: zone['domainName'],
					hidden: moduleParams['action'] == 'create' ? false : true,
					style: 'margin-left: 5px; height: 20px',
					columnWidth: 0.5
				}, {
					xtype: 'displayfield',
					cls: 'x-form-check-wrap',
					value: zone['domainName'],
					hidden: moduleParams['action'] == 'edit' ? false : true,
					columnWidth: 1
				}]
			}, {
				xtype: 'fieldset',
				title: 'Automatically create A records for',
				labelWidth: 70,
				items: [{
					xtype: 'compositefield',
					fieldLabel: 'Farm',
					items: [{
						xtype: 'combo',
						store: Scalr.data.createStore(moduleParams['farms'], { idProperty: 'id', fields: [ 'id', 'name' ]}),
						editable: false,
						triggerAction: 'all',
						itemId: 'domainFarm',
						hiddenName: 'domainFarm',
						value: zone['domainFarm'] != '0' ? zone['domainFarm'] : '',
						mode: 'local',
						valueField: 'id',
						displayField: 'name',
						width: 150,
						listWidth: 200,
						listeners: {
							select: function (combo, record) {
								if (record.get('id') != '0') {
									form.el.mask('Loading farm roles ...');

									Ext.Ajax.request({
										url: '/dnszones/getFarmRoles/',
										params: { farmId: record.get('id') },
										success: function (response) {
											var result = Ext.decode(response.responseText);
											if (result.success) {
												form.findOne('itemId', 'domainFarmRole').store.loadData(result.farmRoles);
											} else {
												Scalr.Viewers.ErrorMessage(result.error);
											}
											form.el.unmask();
										}
									});
								} else {
									form.findOne('itemId', 'domainFarmRole').store.loadData([]);
									form.findOne('itemId', 'domainFarmRole').setValue('');
								}
							}
						}
					}, {
						xtype: 'displayfield',
						value: '<i>Each server in this farm will add int-rolename ext-rolename records. Leave blank if you don\'t need such records.</i>',
						cls: 'x-form-check-wrap'
					}]
				}, {
					xtype: 'compositefield',
					fieldLabel: 'Role',
					items: [{
						xtype: 'combo',
						store: Scalr.data.createStore(moduleParams['farmRoles'], { idProperty: 'id', fields: [ 'id', 'name', 'platform', 'role_id' ]}),
						editable: false,
						triggerAction: 'all',
						itemId: 'domainFarmRole',
						hiddenName: 'domainFarmRole',
						value: zone['domainFarmRole'] != '0' ? zone['domainFarmRole'] : '',
						mode: 'local',
						valueField: 'id',
						displayField: 'name',
						width: 150,
						listWidth: 200
					}, {
						xtype: 'displayfield',
						value: '<i>Servers of this role will create root records. Leave blank to add root records manually.</i>',
						cls: 'x-form-check-wrap'
					}]
				}]
			}, {
				xtype: 'fieldset',
				title: 'SOA settings',
				labelWidth: 70,
				itemId: 'soaSettings',
				items: [{
					xtype: 'compositefield',
					fieldLabel: 'SOA Retry',
					items: [{
						xtype: 'combo',
						store: [[ '1800', '30 minutes' ], [ '3600', '1 hour' ], [ '7200', '2 hours' ], [ '14400', '4 hours' ], [ '28800', '8 hours' ], [ '86400', '1 day' ]],
						editable: false,
						triggerAction: 'all',
						hideLabel: true,
						itemId: 'soaRetry',
						hiddenName: 'soaRetry',
						width: 150,
						value: zone['soaRetry']
					}, {
						xtype: 'displayfield',
						value: '<img class="tipHelp" src="/images/ui-ng/icons/info_icon_16x16.png" style="padding: 2px; cursor: help;">',
						cls: 'x-form-check-wrap'
					}]
				}, {
					xtype: 'compositefield',
					fieldLabel: 'SOA refresh',
					items: [{
						xtype: 'combo',
						store: [[ '3600', '1 hour' ], [ '7200', '2 hours' ], [ '14400', '4 hours' ], [ '28800', '8 hours' ], [ '86400', '1 day' ]],
						editable: false,
						triggerAction: 'all',
						itemId: 'soaRefresh',
						hiddenName: 'soaRefresh',
						width: 150,
						value: zone['soaRefresh']
					}, {
						xtype: 'displayfield',
						value: '<img class="tipHelp" src="/images/ui-ng/icons/info_icon_16x16.png" style="padding: 2px; cursor: help;">',
						cls: 'x-form-check-wrap'
					}]
				}, {
					xtype: 'compositefield',
					fieldLabel: 'SOA expire',
					items: [{
						xtype: 'combo',
						store: [[ '86400', '1 day' ], [ '259200', '3 days' ], [ '432000', '5 days' ], [ '604800', '1 week' ], [ '3024000', '5 weeks' ], [ '6048000', '10 weeks' ] ],
						editable: false,
						triggerAction: 'all',
						itemId: 'soaExpire',
						hiddenName: 'soaExpire',
						width: 150,
						value: zone['soaExpire']
					}, {
						xtype: 'displayfield',
						value: '<img class="tipHelp" src="/images/ui-ng/icons/info_icon_16x16.png" style="padding: 2px; cursor: help;">',
						cls: 'x-form-check-wrap'
					}]
				}]
			}, {
				xtype: 'fieldset',
				labelWidth: 100,
				title: 'DNS Records',
				itemId: 'dnsRecords',
				hidden: true
			}, {
				xtype: 'fieldset',
				labelWidth: 100,
				title: 'Add New DNS Records',
				itemId: 'newDnsRecords',
				items: [ dnsField({ showAddButton: true }) ]
			}]
		});

		var fieldset = form.findOne('itemId', 'dnsRecords');
		for (var i = 0; i < records.length; i++) {
			fieldset.add(dnsField({ itemId: records[i].id, showRemoveButton: records[i].issystem == '0' || moduleParams['allowManageSystemRecords'] == '1' }, records[i]));
		}

		if (fieldset.items.getCount())
			fieldset.show();

		form.addButton({
			type: 'submit',
			text: 'Save',
			handler: function() {
				form.getForm().isValid();

				var records = {}, valid = true;
				form.findOne('itemId', 'dnsRecords').items.each(function () {
					var r = this.getValues();
					if (r)
						records[this.getItemId()] = { el: this, r: r };
					else
						form.findOne('itemId', 'dnsRecords').remove(this);
				});

				form.findOne('itemId', 'newDnsRecords').items.each(function () {
					var r = this.getValues();

					// hide add button
					this.items.each(function () {
						if (this.itemId == 'add')
							this.hide();
					});
					this.doLayout();

					if (r)
						records[this.getItemId()] = { el: this, r: r };
					else
						form.findOne('itemId', 'newDnsRecords').remove(this);
				});

				form.findOne('itemId', 'newDnsRecords').add(dnsField({ showAddButton: true }));
				form.findOne('itemId', 'newDnsRecords').doLayout();

				// check for dublicate CNAME records
				var uniq = {}; // [name] = (exist, cname, conflict)
				for (i in records) {
					var n = (records[i].r.name == '' || records[i].r.name == '@') ? zone['domainName'] + '.' : records[i].r.name;

					if (records[i].r.type == 'CNAME') {
						if (uniq[n] != undefined)
							uniq[n] = 'conflict';
						else
							uniq[n] = 'cname';
					} else {
						if (uniq[n] == 'cname' || uniq[n] == 'conflict')
							uniq[n] = 'conflict';
						else
							uniq[n] = 'exist';
					}
				}

				for (name in uniq) {
					if (uniq[name] == 'conflict') {
						valid = false;
						for (i in records) {
							if (records[i].r.name == name || (records[i].r.name == '' || records[i].r.name == '@') && (name == zone['domainName'] + '.'))
								records[i].el.markInvalid('Conflict name ' + name);
						}
					}
				}

				var r = {};
				for (i in records) {
					if (! records[i].el.disabled)
						r[i] = records[i].r;
				}

				if (valid) {
					Ext.Msg.wait('Please wait ...', 'Saving ...');

					form.getForm().submit({
						url: '/dnszones/save/',
						params: {
							domainId: zone['domainId'] || 0,
							records: Ext.encode(r)
						},
						success: function(form, action) {
							var result = Ext.decode(action.response.responseText);
							Scalr.Viewers.SuccessMessage(result.message);
							Ext.Msg.hide();
							document.location.href = '#/dnszones/view';
						},
						failure: Scalr.data.ExceptionFormReporter
					});
				}
			}
		});

		form.addButton({
			type: 'submit',
			text: 'Cancel',
			handler: function() {
				document.location.href = '#/dnszones/view';
			}
		});

		form.on('actionfailed', function (fm, action) {
			try {
				var result = Ext.decode(action.response.responseText);
				if (Ext.isObject(result.errors.records)) {
					var er = result.errors.records, dnsRecords = form.findOne('itemId', 'dnsRecords'), newDnsRecords = form.findOne('itemId', 'newDnsRecords');
					for (i in er) {
						var k = dnsRecords.getComponent(i);
						if (k) {
							k.markInvalid(er[i]);
							continue;
						}

						k = newDnsRecords.getComponent(i);
						if (k) {
							k.markInvalid(er[i]);
							continue;
						}
					}
				}
			} catch (e) { alert(e); }
		});

		form.on('afterrender', function () {
			form.findOne('itemId', 'soaRetry').ownerCt.on('afterrender', function () {
				new Ext.ToolTip({
					target: this.el.child('img.tipHelp').id,
					dismissDelay: 0,
					html: 'Signed 32 bit value in seconds. Defines the time between retries if the slave (secondary) fails to contact the master when refresh (above) has expired. Typical values would be 180 (3 minutes) to 900 (15 minutes) or higher.'
				});
			});

			form.findOne('itemId', 'soaExpire').ownerCt.on('afterrender', function () {
				new Ext.ToolTip({
					target: this.el.child('img.tipHelp').id,
					dismissDelay: 0,
					html: 'Signed 32 bit value in seconds. Indicates when the zone data is no longer authoritative. Used by Slave or (Secondary) servers only. BIND9 slaves stop responding to queries for the zone when this time has expired and no contact has been made with the master. Thus every time the refresh values expires the slave will attempt to read the SOA record from the zone master - and request a zone transfer AXFR/IXFR if sn is HIGHER. If contact is made the expiry and refresh values are reset and the cycle starts again. If the slave fails to contact the master it will retry every retry period but continue to supply authoritative data for the zone until the expiry value is reached at which point it will stop answering queries for the domain. RFC 1912 recommends 1209600 to 2419200 seconds (2-4 weeks) to allow for major outages of the zone master.'
				});
			});

			form.findOne('itemId', 'soaRefresh').ownerCt.on('afterrender', function () {
				new Ext.ToolTip({
					target: this.el.child('img.tipHelp').id,
					dismissDelay: 0,
					html: 'Signed 32 bit time value in seconds. Indicates the time when the slave will try to refresh the zone from the master (by reading the master DNS SOA RR). RFC 1912 recommends 1200 to 43200 seconds, low (1200) if the data is volatile or 43200 (12 hours) if it\'s not. If you are using NOTIFY you can set for much higher values, for instance, 1 or more days (> 86400 seconds).'
				});
			});
		});

		return form;
	}
}

