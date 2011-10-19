{
	create: function (loadParams, moduleParams) {
		var checkboxBehaviorListener = function(checkbox, checked) {
			var value = '';
			panel.findOne('itemId', 'behaviors').items.each(function() {
				if (this.checked && value != '')
					value = 'Mixed images'
				else if (this.checked) {
					if (this.inputValue == 'app')
						value = 'Application servers'
					else if (this.inputValue == 'base')
						value = 'Base images'
					else if (this.inputValue == 'mysql')
						value = 'Database servers'
					else if (this.inputValue == 'www')
						value = 'Load balancers'
					else if (this.inputValue == 'memcached')
						value = 'Caching servers'
					else if (this.inputValue == 'cassandra')
						value = 'Database servers';
				}
			});

			panel.findOne('name', 'group').setValue(value);
		};

		var removeImages = [];

		var imagesStore = new Ext.data.JsonStore({
			data: moduleParams.role.images,
			fields: [ 'platform', 'location', 'platform_name', 'location_name', 'image_id' ]
		});

		var optionsStore = new Ext.data.JsonStore({
			id: 'name',
			data: moduleParams.role.parameters,
			fields: [ 'name', 'type', 'required', 'defval' ]
		});

		var platformsStore = new Ext.data.JsonStore({
			id: 'id',
			fields: [ 'id', 'name', 'locations' ],
			data: moduleParams.platforms
		});

		var locationsStore = new Ext.data.JsonStore({
			id: 'id',
			fields: [ 'id', 'name' ]
		});

		var panel = new Ext.TabPanel({
			activeTab: 0,
			bodyStyle: 'border-style: none solid none solid;',
			border: false,
			buttonAlign: 'center',
			plugins: [ new Scalr.Viewers.Plugins.findOne() ],
			items: [{
				title: 'Role Information',
				xtype: 'form',
				itemId: 'form',
				bodyStyle: { padding: '10px' },
				autoHeight: true,
				border: false,
				items: [{
					xtype: 'fieldset',
					items: [{
						xtype: 'textfield',
						fieldLabel: 'Name',
						width: 200,
						name: 'name',
						readOnly: moduleParams.role.id != 0 ? true : false,
						value: moduleParams.role.name
					}, {
						xtype: 'combo',
						fieldLabel: 'Arch',
						name: 'arch',
						width: 200,
						readOnly: moduleParams.role.id != 0 ? true : false,
						store: [ 'i386', 'x86_64' ],
						value: moduleParams.role.arch,
						mode: 'local',
						allowBlank: false,
						editable: false,
						triggerAction: 'all'
					}, {
						xtype: 'combo',
						fieldLabel: 'Scalr agent',
						name: 'agent',
						width: 200,
						readOnly: moduleParams.role.id != 0 ? true : false,
						store: [ [ '1', 'ami-scripts' ], [ '2', 'scalarizr' ] ],
						value: moduleParams.role.agent,
						mode: 'local',
						allowBlank: false,
						editable: false,
						triggerAction: 'all'
					}, {
						xtype: 'textfield',
						fieldLabel: 'Agent version',
						name: 'szr_version',
						width: 200,
						readOnly: moduleParams.role.id != 0 ? true : false,
						value: moduleParams.role.szr_version
					}, {
						xtype: 'textfield',
						fieldLabel: 'OS',
						width: 200,
						name: 'os',
						readOnly: moduleParams.role.id != 0 ? true : false,
						value: moduleParams.role.os
					}, {
						xtype: 'textarea',
						fieldLabel: 'Description',
						width: '90%',
						height: 100,
						name: 'description',
						value: moduleParams.role.description
					}, {
						xtype: 'textarea',
						fieldLabel: 'Software',
						width: '90%',
						height: 100,
						name: 'software',
						hidden: moduleParams.role.id != 0 ? true : false,
						hideLabel: moduleParams.role.id != 0 ? true : false,
						value: ''
					}, {
						xtype: 'hidden',
						name: 'roleId',
						value: moduleParams.role.id
					}]
				}, {
					xtype: 'fieldset',
					title: 'Behaviors',
					items: [{
						xtype: 'checkboxgroup',
						columns: 4,
						fieldLabel: 'Behaviors',
						itemId: 'behaviors',
						disabled: moduleParams.role.id != 0 ? true : false,
						items: [{
							boxLabel: 'Base',
							inputValue: 'base',
							name: 'behaviors[]',
							handler: checkboxBehaviorListener
						}, {
							boxLabel: 'MySQL',
							inputValue: 'mysql',
							name: 'behaviors[]',
							handler: checkboxBehaviorListener
						}, {
							boxLabel: 'Apache',
							inputValue: 'app',
							name: 'behaviors[]',
							handler: checkboxBehaviorListener
						}, {
							boxLabel: 'Nginx',
							inputValue: 'www',
							name: 'behaviors[]',
							handler: checkboxBehaviorListener
						}, {
							boxLabel: 'Memcached',
							inputValue: 'memcached',
							name: 'behaviors[]',
							handler: checkboxBehaviorListener
						}, {
							boxLabel: 'Cassandra',
							inputValue: 'cassandra',
							name: 'behaviors[]',
							handler: checkboxBehaviorListener
						}]
					}, {
						xtype: 'textfield',
						readOnly: true,
						fieldLabel: 'Group',
						name: 'group',
						width: 200
					}]
				}, {
					xtype: 'fieldset',
					items: [{
						xtype: 'checkboxgroup',
						columns: 4,
						fieldLabel: 'Tags',
						itemId: 'tags',
						disabled: !moduleParams.isScalrAdmin,
						items: [{
							boxLabel: 'ec2.ebs',
							inputValue: 'ec2.ebs',
							checked: moduleParams.tags['ec2.ebs'] != undefined || false,
							name: 'tags[]'
						}, {
							boxLabel: 'ec2.hvm',
							inputValue: 'ec2.hvm',
							checked: moduleParams.tags['ec2.hvm'] != undefined || false,
							name: 'tags[]'
						}]
					}]
				}]
			}, {
				title: 'Images',
				layout: 'hbox',
				itemId: 'images',
				plugins: [ new Scalr.Viewers.Plugins.findOne() ],
				layoutConfig: {
					align: 'stretch',
					pack: 'start'
				},
				items: [{
					layout: 'form',
					bodyStyle: { 'border-style': 'none solid none none', padding: '10px' },
					width: 300,
					labelWidth: 60,
					items: [{
						xtype: 'combo',
						fieldLabel: 'Platform',
						store: platformsStore,
						valueField: 'id',
						displayField: 'name',
						allowBlank: false,
						editable: false,
						name: 'image_platform',
						typeAhead: false,
						mode: 'local',
						triggerAction: 'all',
						selectOnFocus: false,
						width: 200,
						listeners: {
							select: function (field, rec) {
								panel.findOne('name', 'image_location').enable().reset();
								locationsStore.loadData(rec.get('locations'));
							}
						}
					}, {
						xtype: 'combo',
						fieldLabel: 'Location',
						store: locationsStore,
						valueField: 'id',
						displayField: 'name',
						disabled: true,
						allowBlank: false,
						editable: false,
						name: 'image_location',
						typeAhead: false,
						mode: 'local',
						triggerAction: 'all',
						selectOnFocus: false,
						width: 200
					}, {
						xtype: 'textfield',
						fieldLabel: 'Image ID',
						allowBlank: false,
						name: 'image_id',
						width: 200
					}, {
						layout: 'column',
						border: false,
						items: [{
							text: 'Add',
							itemId: 'image_add',
							xtype: 'button',
							hideMode: 'offsets',
							width: 70
						}, {
							text: 'Save',
							itemId: 'image_save',
							xtype: 'button',
							hideMode: 'offsets',
							width: 70
						}, {
							xtype: 'displayfield',
							value: '&nbsp;'
						}, {
							text: 'Cancel',
							itemId: 'image_cancel',
							xtype: 'button',
							hideMode: 'offsets',
							width: 70
						}, {
							xtype: 'displayfield',
							value: '&nbsp;'
						}, {
							text: 'Delete',
							itemId: 'image_delete',
							xtype: 'button',
							hideMode: 'offsets',
							width: 70
						}]
					}]
				}, new Scalr.Viewers.list.ListView({
					flex: 1,
					itemId: 'images_view',
					border: false,
					emptyText: 'No images found',
					singleSelect: true,
					columns: [
						{ header: "Platform", width: 35, dataIndex: 'platform_name', sortable: false, hidden: 'no' },
						{ header: "Location", width: 35, dataIndex: 'location_name', sortable: false, hidden: 'no' },
						{ header: "Image ID", width: 35, dataIndex: 'image_id', sortable: false, hidden: 'no' }
					],
					store: imagesStore,
					deferEmptyText: false
				})]
			}, {
				title: 'Properties',
				itemId: 'properties',
				layout: 'form',
				bodyStyle: { padding: '10px' },
				items: {
					xtype: 'fieldset',
					items: {
						xtype: 'compositefield',
						hideLabel: true,
						items: [{
							xtype: 'displayfield',
							cls: 'x-form-check-wrap',
							value: 'SSH port'
						}, {
							xtype: 'textfield',
							hideLabel: true,
							name: 'default_ssh_port',
							value: moduleParams.role['properties']['system.ssh-port']
						}, {
							xtype: 'displayfield',
							itemId: 'default_ssh_port_help',
							cls: 'x-form-check-wrap',
							value: '<img src="/images/ui-ng/icons/warn_icon_16x16.png" style="padding: 2px; cursor: help;">'
						}]
					}
				}
			}, {
				title: 'Parameters',
				layout: 'hbox',
				itemId: 'options',
				plugins: [ new Scalr.Viewers.Plugins.findOne() ],
				layoutConfig: {
					align: 'stretch',
					pack: 'start'
				},
				items: [{
					width: 400,
					bodyStyle: { 'border-style': 'none solid none none', padding: '10px' },
					items: [{
						xtype: 'fieldset',
						title: 'Field',
						layout: 'form',
						defaults: {
							anchor: '100%'
						},
						labelWidth: 80,
						items: [{
							xtype: 'combo',
							fieldLabel: 'Type',
							store: [ ['text', 'Text'], ['textarea', 'Textarea'], ['checkbox', 'Boolean']],
							editable: false,
							name: 'fieldtype',
							value: 'text',
							typeAhead: false,
							mode: 'local',
							triggerAction: 'all',
							allowBlank: false
						}, {
							xtype: 'textfield',
							name: 'fieldname',
							fieldLabel: 'Name',
							allowBlank: false
						}, {
							xtype: 'checkbox',
							fieldLabel: 'Required?',
							name: 'fieldrequired',
							labelSeparator: ''
						}, {
							xtype: 'textfield',
							fieldLabel: 'Default value',
							name: 'fielddefval_textfield'
						}, {
							xtype: 'textarea',
							fieldLabel: 'Default value',
							name: 'fielddefval_textarea'
						}, {
							xtype: 'hidden',
							name: 'fieldeditname'
						}]
					}, {
						layout: 'column',
						border: false,
						items: [{
							text: 'Add',
							itemId: 'field_add',
							xtype: 'button',
							hideMode: 'offsets',
							width: 70
						}, {
							text: 'Save',
							itemId: 'field_save',
							xtype: 'button',
							hideMode: 'offsets',
							width: 70
						}, {
							xtype: 'displayfield',
							value: '&nbsp;'
						}, {
							text: 'Cancel',
							itemId: 'field_cancel',
							xtype: 'button',
							hideMode: 'offsets',
							width: 70
						}]
					}]
				}, new Scalr.Viewers.list.ListView({
					flex: 1,
					itemId: 'options_view',
					border: false,
					emptyText: 'No parameters found',
					singleSelect: true,
					actionColumnPlugin: true,
					columns: [
						{ header: "Name", width: 35, dataIndex: 'name', sortable: false, hidden: 'no' },
						{ header: "Type", width: 35, dataIndex: 'type', sortable: false, hidden: 'no' },
						{ header: "Required", width: 35, dataIndex: 'required', sortable: false, hidden: 'no', tpl:
							'<tpl if="required == 1"><img src="/images/true.gif"></tpl>' +
							'<tpl if="required != 1"><img src="/images/false.gif"></tpl>'
						},
						{ header: "&nbsp;", width: '24px', sortable: false, dataIndex: 'id', align:'center', hidden: 'no',
							tpl: '<img src="/images/ui-ng/icons/delete_icon_16x16.png">', clickHandler: function (comp, store, record) {
								store.remove(record);
							}
						}
					],
					store: optionsStore,
					deferEmptyText: false
				})]
			}],
			buttonAlign: 'center',
			buttons: [{
				type: 'submit',
				text: 'Save',
				handler: function() {
					var params = {};
					var data = [], records = imagesStore.getRange();
					for (var i = 0; i < records.length; i++)
						data[data.length] = { image_id: records[i].get('image_id'), platform: records[i].get('platform'), location: records[i].get('location') };

					params['images'] = Ext.encode(data);
					params['remove_images'] = Ext.encode(removeImages);

					var parameters = [], records = optionsStore.getRange();
					for (var i = 0; i < records.length; i++)
						parameters[parameters.length] = records[i].data;

					params['parameters'] = Ext.encode(parameters);

					params['properties'] = Ext.encode({
						'system.ssh-port': panel.findOne('name', 'default_ssh_port') ?
							panel.findOne('name', 'default_ssh_port').getValue() :
							moduleParams.role['properties']['system.ssh-port']
					});

					Ext.Msg.wait('Please wait');

					panel.findOne('itemId', 'form').getForm().submit({
						url: '/roles/xSaveRole/',
						params: params,
						success: function(form, action) {
							Ext.Msg.hide();
							Scalr.Viewers.SuccessMessage('Role saved');
							document.location.href = '#/roles/view';
						},
						failure: Scalr.data.ExceptionFormReporter
					});
				}
			}, {
				type: 'reset',
				text: 'Cancel',
				handler: function() {
					Scalr.Viewers.EventMessager.fireEvent('close');
				}
			}]
		});

		var panel_image_add_reset = function() {
			panel.findOne('name', 'image_platform').reset();
			panel.findOne('name', 'image_location').disable().reset();
			panel.findOne('name', 'image_id').reset();

			panel.findOne('name', 'image_platform').enable();

			panel.findOne('itemId', 'image_add').show();
			panel.findOne('itemId', 'image_save').hide();
			panel.findOne('itemId', 'image_delete').hide();
			panel.findOne('itemId', 'image_cancel').hide();
		};

		panel.findOne('itemId', 'images').on('afterrender', function () {
			panel_image_add_reset();

			this.findOne('itemId', 'image_add').on('click', function () {
				if (
					this.findOne('name', 'image_platform').isValid() &&
					this.findOne('name', 'image_location').isValid() &&
					this.findOne('name', 'image_id').isValid()
				) {
					var platform = this.findOne('name', 'image_platform').getValue(),
						location = this.findOne('name', 'image_location').getValue(),
						image_id = this.findOne('name', 'image_id').getValue();

					if (imagesStore.findBy(function (record) {
						if (record.get('platform') == platform && record.get('location') == location) {
							Scalr.Viewers.ErrorMessage('Image on this platform/location already exist');
							return true;
						}

						if (record.get('image_id') == image_id) {
							Scalr.Viewers.ErrorMessage('Image ID ' + image_id + ' already used');
							return true;
						}
					}) == -1) {
						var record = new imagesStore.recordType({
							platform: platform,
							platform_name: platformsStore.getById(platform).get('name'),
							location: location,
							location_name: locationsStore.getById(location).get('name'),
							image_id: image_id
						});
						imagesStore.add(record);
						panel_image_add_reset();
					}
				}
			}, this);

			this.findOne('itemId', 'images_view').on('afterrender', function (comp) {
				comp.on('selectionchange', function(c, selections) {
					if (selections.length) {
						var rec = c.store.getAt(c.indexOf(selections[0]));
						panel.findOne('name', 'image_platform').disable().setValue(rec.get('platform'));
						panel.findOne('name', 'image_location').disable().setValue(rec.get('location'));
						panel.findOne('name', 'image_id').setValue(rec.get('image_id'));

						panel.findOne('itemId', 'image_add').hide();
						panel.findOne('itemId', 'image_save').show();
						panel.findOne('itemId', 'image_delete').show();
						panel.findOne('itemId', 'image_cancel').show();
					} else
						panel_image_add_reset();
				});
			}, this);

			this.findOne('itemId', 'image_save').on('click', function() {
				var view = panel.findOne('itemId', 'images_view'), records = view.getSelectedRecords();
				if (records[0]) {
					records[0].set('image_id', panel.findOne('name', 'image_id').getValue());
					panel_image_add_reset();
				}
			});

			this.findOne('itemId', 'image_cancel').on('click', function () {
				panel.findOne('itemId', 'images_view').clearSelections();
				panel_image_add_reset.call(this);
			});

			this.findOne('itemId', 'image_delete').on('click', function() {
				var view = panel.findOne('itemId', 'images_view'), records = view.getSelectedRecords();
				if (records[0]) {
					view.clearSelections();
					view.store.remove(records[0]);
					removeImages[removeImages.length] = records[0].get('image_id');
					panel_image_add_reset();
				}
			});
		});

		panel.findOne('itemId', 'properties').on('afterrender', function() {
			new Ext.ToolTip({
				target: this.findOne('itemId', 'default_ssh_port_help').id,
				dismissDelay: 0,
				html: "This setting WON'T change default SSH port on the servers. This port should be opened in the security groups."
			});
		}, panel);

		panel.findOne('itemId', 'options').on('afterrender', function () {
			(function() {
				var get_value = function () {
					var data = {}, valid = true;

					data['name'] = this.findOne('name', 'fieldname').getValue();
					data['type'] = this.findOne('name', 'fieldtype').getValue()
					data['required'] = this.findOne('name', 'fieldrequired').getValue() ? 1 : 0;

					valid = this.findOne('name', 'fieldname').isValid() && valid;
					valid = this.findOne('name', 'fieldtype').isValid() && valid;

					if (! valid)
						return;

					if (data['type'] == 'text')
						data['defval'] = this.findOne('name', 'fielddefval_textfield').getValue();

					if (data['type'] == 'textarea')
						data['defval'] = this.findOne('name', 'fielddefval_textarea').getValue();

					return data;
				};

				var clear_value = function () {
					this.findOne('name', 'fieldname').reset();
					this.findOne('name', 'fieldrequired').reset();
					this.findOne('name', 'fielddefval_textarea').reset();
					this.findOne('name', 'fielddefval_textfield').reset();
				};

				this.findOne('name', 'fieldtype').on('select', function (field, record) {
					this.findOne('name', 'fielddefval_textfield').container.up('div.x-form-item').setVisibilityMode(Ext.Element.DISPLAY);
					this.findOne('name', 'fielddefval_textarea').container.up('div.x-form-item').setVisibilityMode(Ext.Element.DISPLAY);

					this.findOne('name', 'fielddefval_textfield').container.up('div.x-form-item').hide();
					this.findOne('name', 'fielddefval_textarea').container.up('div.x-form-item').hide();

					if (record.get(field.valueField) == 'text') {
						this.findOne('name', 'fielddefval_textfield').container.up('div.x-form-item').show();
					}

					if (record.get(field.valueField) == 'textarea') {
						this.findOne('name', 'fielddefval_textarea').container.up('div.x-form-item').show();
					}

				}, this);

				this.findOne('itemId', 'field_add').on('click', function () {
					var data = get_value.call(this);

					if (Ext.isObject(data)) {
						var store = this.findOne('itemId', 'options_view').store;
						if (store.findExact('name', data['name']) == -1) {
							store.add(new store.recordType(data));
							clear_value.call(this);
						} else
							this.findOne('name', 'fieldname').markInvalid('Such param name already exist');
					}
				}, this);

				this.findOne('itemId', 'field_cancel').on('click', function () {
					clear_value.call(this);

					this.findOne('itemId', 'options_view').clearSelections();

					this.findOne('itemId', 'field_add').show();
					this.findOne('itemId', 'field_save').hide();
					this.findOne('itemId', 'field_cancel').hide();
				}, this);

				this.findOne('itemId', 'field_save').on('click', function () {
					var data = get_value.call(this);

					if (Ext.isObject(data)) {
						var store = this.findOne('itemId', 'options_view').store;
						var record = store.getAt(store.findExact('name', this.findOne('name', 'fieldeditname').getValue()));

						for (i in data)
							record.set(i, data[i]);

						clear_value.call(this);

						this.findOne('itemId', 'options_view').clearSelections();

						this.findOne('itemId', 'field_add').show();
						this.findOne('itemId', 'field_save').hide();
						this.findOne('itemId', 'field_cancel').hide();
					}
				}, this);

				this.findOne('itemId', 'options_view').on('selectionchange', function (c, selections) {
					if (selections.length) {
						var rec = c.store.getAt(c.indexOf(selections[0])), fieldtype = this.findOne('name', 'fieldtype');

						fieldtype.setValue(rec.get('type')).fireEvent('select', fieldtype,
							fieldtype.store.getAt(fieldtype.store.find(fieldtype.valueField, rec.get('type')))
						);
						this.findOne('name', 'fieldname').setValue(rec.get('name'));
						this.findOne('name', 'fieldeditname').setValue(rec.get('name'));
						this.findOne('name', 'fieldrequired').setValue(rec.get('required') == 1 ? true : false);

						if (rec.get('type') == 'text')
							this.findOne('name', 'fielddefval_textfield').setValue(rec.get('defval'));

						if (rec.get('type') == 'textarea')
							this.findOne('name', 'fielddefval_textarea').setValue(rec.get('defval'));

						this.findOne('itemId', 'field_add').hide();
						this.findOne('itemId', 'field_save').show();
						this.findOne('itemId', 'field_cancel').show();
					} else {
						clear_value.call(this);

						this.findOne('itemId', 'field_add').show();
						this.findOne('itemId', 'field_save').hide();
						this.findOne('itemId', 'field_cancel').hide();
					}
				}, this);

				this.findOne('itemId', 'field_save').hide();
				this.findOne('itemId', 'field_cancel').hide();

				this.findOne('name', 'fielddefval_textarea').container.up('div.x-form-item').setVisibilityMode(Ext.Element.DISPLAY);
				this.findOne('name', 'fielddefval_textarea').container.up('div.x-form-item').hide();

			}).defer(100, this);
		});

		panel.findOne('itemId', 'behaviors').on('afterrender', function () {
			this.findOne('itemId', 'behaviors').items.each(function() {
				var beh = moduleParams.role.behaviors.join(' ');
				if (beh.match(this.inputValue))
					this.setValue(true);
			});
		}, panel);

		return new Ext.Panel({
			scalrOptions: {
				'maximize': 'all'
			},
			title: moduleParams.role.id ? 'Roles &raquo; Edit &raquo; ' + moduleParams.role.name : 'Roles &raquo; Create new role',
			layout: 'fit',
			items: panel
		});
	}
}
