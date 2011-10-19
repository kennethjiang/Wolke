Scalr.regPage('Scalr.ui.farms.builder.tabs.scaling', function (moduleParams) {
	return new Scalr.ui.farms.builder.tab({
		tabTitle: '自适应设置',
		layout: 'form',
		labelWidth: 150,

		loaded: false,
		algos: {},

		addAlgoTab: function (metric, values, activate) {
			var tabpanel = this.findOne('itemId', 'algos'), p = null, alias = metric.get('alias'), field = 'scaling.' + metric.get('id') + '.';
			if (alias == 'bw') {
				p = tabpanel.add({
					title: metric.get('name'),
					alias: metric.get('alias'),
					metricId: metric.get('id'),
					field: field,
					closable: true,
					layout: 'form',
					border: false,
					style: {
						'padding': '10px'
					},
					autoHeight: true,
					autoRender: true,
					getValues: function (comp) {
						return {
							type: this.findOne('name', comp.field + 'type').getValue(),
							min: this.findOne('name', comp.field + 'min').getValue(),
							max: this.findOne('name', comp.field + 'max').getValue()
						};
					},
					items: [{
						xtype: 'compositefield',
						hideLabel: true,
						items: [{
							xtype: 'displayfield',
							value: 'Use',
							cls: 'x-form-check-wrap'
						}, {
							xtype: 'combo',
							hideLabel: true,
							store: [ 'inbound', 'outbound' ],
							allowBlank: false,
							editable: false,
							name: field + 'type',
							typeAhead: false,
							mode: 'local',
							triggerAction: 'all',
							selectOnFocus: false,
							width: 100
						}, {
							xtype: 'displayfield',
							value: ' bandwidth usage value for scaling',
							cls: 'x-form-check-wrap'
						}]
					}, {
						xtype: 'compositefield',
						hideLabel: true,
						items: [{
							xtype: 'displayfield',
							value: 'Scale in (release instances) when average bandwidth usage on role is less than',
							cls: 'x-form-check-wrap'
						}, {
							xtype: 'textfield',
							name: field + 'min',
							width: 40
						}, {
							xtype: 'displayfield',
							value: 'Mbit/s',
							cls: 'x-form-check-wrap'
						}]
					}, {
						xtype: 'compositefield',
						hideLabel: true,
						items: [{
							xtype: 'displayfield',
							value: 'Scale out (add more instances) when average bandwidth usage on role is more than',
							cls: 'x-form-check-wrap'
						}, {
							xtype: 'textfield',
							name: field + 'max',
							width: 40
						}, {
							xtype: 'displayfield',
							value: 'Mbit/s',
							cls: 'x-form-check-wrap'
						}]
					}]
				});

				tabpanel.doLayout(false, true);

				this.findOne('name', field + 'min').setValue(values['min'] || '10');
				this.findOne('name', field + 'max').setValue(values['max'] || '40');
				this.findOne('name', field + 'type').setValue(values['type'] || 'outbound');

			} else if (alias == 'la') {
				p = tabpanel.add({
					title: metric.get('name'),
					alias: metric.get('alias'),
					metricId: metric.get('id'),
					field: field,
					closable: true,
					layout: 'form',
					border: false,
					style: {
						'padding': '10px'
					},
					autoHeight: true,
					autoRender: true,
					getValues: function (comp) {
						return {
							period: this.findOne('name', comp.field + 'period').getValue(),
							min: this.findOne('name', comp.field + 'min').getValue(),
							max: this.findOne('name', comp.field + 'max').getValue()
						};
					},
					items: [{
						xtype: 'compositefield',
						hideLabel: true,
						items: [{
							xtype: 'displayfield',
							value: 'Use',
							cls: 'x-form-check-wrap'
						}, {
							xtype: 'combo',
							hideLabel: true,
							store: ['1','5','15'],
							allowBlank: false,
							editable: false,
							name: field + 'period',
							typeAhead: false,
							mode: 'local',
							triggerAction: 'all',
							selectOnFocus: false,
							width: 100
						}, {
							xtype: 'displayfield',
							value: 'minute(s) load averages for scaling',
							cls: 'x-form-check-wrap'
						}]
					}, {
						xtype: 'compositefield',
						hideLabel: true,
						items: [{
							xtype: 'displayfield',
							value: 'Scale in (release instances) when LA goes under',
							cls: 'x-form-check-wrap'
						}, {
							xtype: 'textfield',
							name: field + 'min',
							width: 40
						}]
					}, {
						xtype: 'compositefield',
						hideLabel: true,
						items: [{
							xtype: 'displayfield',
							value: 'Scale out (add more instances) when LA goes over',
							cls: 'x-form-check-wrap'
						}, {
							xtype: 'textfield',
							name: field + 'max',
							width: 40
						}]
					}]
				});

				tabpanel.doLayout(false, true);

				this.findOne('name', field + 'min').setValue(values['min'] || '2');
				this.findOne('name', field + 'max').setValue(values['max'] || '5');
				this.findOne('name', field + 'period').setValue(values['period'] || '15');

			} else if (alias == 'sqs') {
				p = tabpanel.add({
					title: metric.get('name'),
					alias: metric.get('alias'),
					metricId: metric.get('id'),
					field: field,
					closable: true,
					layout: 'form',
					border: false,
					style: {
						'padding': '10px'
					},
					autoHeight: true,
					autoRender: true,
					getValues: function (comp) {
						return {
							queue_name: this.findOne('name', comp.field + 'queue_name').getValue(),
							min: this.findOne('name', comp.field + 'min').getValue(),
							max: this.findOne('name', comp.field + 'max').getValue()
						};
					},
					items: [{
						fieldLabel: 'Queue name',
						xtype: 'textfield',
						name: field + 'queue_name',
						width: 100
					}, {
						xtype: 'compositefield',
						hideLabel: true,
						items: [{
							xtype: 'displayfield',
							value: 'Scale out (add more instances) when queue size goes over',
							cls: 'x-form-check-wrap'
						}, {
							xtype: 'textfield',
							name: field + 'max',
							width: 40
						}, {
							xtype: 'displayfield',
							value: 'items',
							cls: 'x-form-check-wrap'
						}]
					}, {
						xtype: 'compositefield',
						hideLabel: true,
						items: [{
							xtype: 'displayfield',
							value: 'Scale in (release instances) when queue size goes under',
							cls: 'x-form-check-wrap'
						}, {
							xtype: 'textfield',
							name: field + 'min',
							width: 40
						}, {
							xtype: 'displayfield',
							value: 'items',
							cls: 'x-form-check-wrap'
						}]
					}]
				});

				tabpanel.doLayout(false, true);

				this.findOne('name', field + 'min').setValue(values['min'] || '');
				this.findOne('name', field + 'max').setValue(values['max'] || '');
				this.findOne('name', field + 'queue_name').setValue(values['queue_name'] || '');

			} else if (alias == 'custom') {
				p = tabpanel.add({
					title: metric.get('name'),
					alias: metric.get('alias'),
					metricId: metric.get('id'),
					field: field,
					closable: true,
					layout: 'form',
					border: false,
					style: {
						'padding': '10px'
					},
					autoHeight: true,
					autoRender: true,
					getValues: function (comp) {
						return {
							min: this.findOne('name', comp.field + 'min').getValue(),
							max: this.findOne('name', comp.field + 'max').getValue()
						};
					},
					items: [{
						xtype: 'compositefield',
						hideLabel: true,
						items: [{
							xtype: 'displayfield',
							value: 'Scale out (add more instances) when metric value goes over',
							cls: 'x-form-check-wrap'
						}, {
							xtype: 'textfield',
							name: field + 'max',
							width: 40
						}]
					}, {
						xtype: 'compositefield',
						hideLabel: true,
						items: [{
							xtype: 'displayfield',
							value: 'Scale in (release instances) when metric value goes under',
							cls: 'x-form-check-wrap'
						}, {
							xtype: 'textfield',
							name: field + 'min',
							width: 40
						}]
					}]
				});

				tabpanel.doLayout(false, true);

				this.findOne('name', field + 'min').setValue(values['min'] || '');
				this.findOne('name', field + 'max').setValue(values['max'] || '');

			} else if (alias == 'ram') {
				p = tabpanel.add({
					title: metric.get('name'),
					alias: metric.get('alias'),
					metricId: metric.get('id'),
					field: field,
					closable: true,
					layout: 'form',
					border: false,
					style: {
						'padding': '10px'
					},
					autoHeight: true,
					autoRender: true,
					getValues: function (comp) {
						return {
							min: this.findOne('name', comp.field + 'min').getValue(),
							max: this.findOne('name', comp.field + 'max').getValue()
						};
					},
					items: [{
						xtype: 'compositefield',
						hideLabel: true,
						items: [{
							xtype: 'displayfield',
							value: 'Scale out (add more instances) when free RAM goes under',
							cls: 'x-form-check-wrap'
						}, {
							xtype: 'textfield',
							name: field + 'min',
							width: 40
						}, {
							xtype: 'displayfield',
							value: 'MB',
							cls: 'x-form-check-wrap'
						}]
					}, {
						xtype: 'compositefield',
						hideLabel: true,
						items: [{
							xtype: 'displayfield',
							value: 'Scale in (release instances) when free RAM goes over',
							cls: 'x-form-check-wrap'
						}, {
							xtype: 'textfield',
							name: field + 'max',
							width: 40
						}, {
							xtype: 'displayfield',
							value: 'MB',
							cls: 'x-form-check-wrap'
						}]
					}]
				});

				tabpanel.doLayout(false, true);

				this.findOne('name', field + 'min').setValue(values['min'] || '');
				this.findOne('name', field + 'max').setValue(values['max'] || '');

			} else if (alias == 'http') {
				p = tabpanel.add({
					title: metric.get('name'),
					alias: metric.get('alias'),
					metricId: metric.get('id'),
					field: field,
					closable: true,
					layout: 'form',
					border: false,
					style: {
						'padding': '10px'
					},
					autoHeight: true,
					autoRender: true,
					labelWidth: 150,
					getValues: function (comp) {
						return {
							url: this.findOne('name', comp.field + 'url').getValue(),
							min: this.findOne('name', comp.field + 'min').getValue(),
							max: this.findOne('name', comp.field + 'max').getValue()
						};
					},
					items: [{
						xtype: 'compositefield',
						hideLabel: true,
						items: [{
							xtype: 'displayfield',
							value: 'Scale out (add more instances) when URL response time more than',
							cls: 'x-form-check-wrap'
						}, {
							xtype: 'textfield',
							name: field + 'max',
							width: 40
						}, {
							xtype: 'displayfield',
							value: 'seconds',
							cls: 'x-form-check-wrap'
						}]
					}, {
						xtype: 'compositefield',
						hideLabel: true,
						items: [{
							xtype: 'displayfield',
							value: 'Scale in (release instances) when URL response time less than',
							cls: 'x-form-check-wrap'
						}, {
							xtype: 'textfield',
							name: field + 'min',
							width: 40
						}, {
							xtype: 'displayfield',
							value: 'seconds',
							cls: 'x-form-check-wrap'
						}]
					}, {
						xtype: 'textfield',
						fieldLabel: 'URL (with http(s)://)',
						name: field + 'url',
						width: 400
					}]
				});

				tabpanel.doLayout(false, true);

				this.findOne('name', field + 'min').setValue(values['min'] || '1');
				this.findOne('name', field + 'max').setValue(values['max'] || '5');
				this.findOne('name', field + 'url').setValue(values['url'] || '');

			} else if (alias == 'time') {
				var store = new Ext.data.JsonStore({
					fields: [ 'start_time', 'end_time', 'week_days', 'instances_count', 'id' ]
				});

				p = tabpanel.add({
					title: metric.get('name'),
					alias: metric.get('alias'),
					metricId: metric.get('id'),
					field: field,
					closable: true,
					border: false,
					autoRender: true,
					autoHeight: true,
					store: store,
					getValues: function (comp) {
						var data = [], records = comp.store.getRange();
						for (var i = 0; i < records.length; i++)
							data[data.length] = records[i].data;

						return data;
					},
					tbar: {
						style: 'font-size: 11px; padding: 5px',
						html: moduleParams['currentTimeZone']
					},
					bbar: [{
						icon: '/images/add.png', // icons can also be specified inline
						cls: 'x-btn-icon',
						tooltip: 'Add new period',
						handler: function() {
							var win = new Ext.Window({
								layout: 'fit',
								width: 390,
								height: 280,
								modal: true,
								draggable: false,
								title: 'Add new time scaling period',
								items: {
									xtype: 'form',
									layout: 'form',
									itemId: 'form',
									frame: true,
									labelWidth: 100, // label settings here cascade unless overridden
									border: false,
									defaults: { width: 240 },
									items: [
										new Ext.form.TimeField({
											fieldLabel: 'Start time',
											name: 'ts_s_time',
											minValue: '0:15am',
											maxValue: '23:45pm',
											allowBlank: false
										}),
										new Ext.form.TimeField({
											fieldLabel: 'End time',
											name: 'ts_e_time',
											minValue: '0:15am',
											maxValue: '23:45pm',
											allowBlank: false
										}), {
											xtype: 'checkboxgroup',
											fieldLabel: 'Days of week',
											columns: 3,
											items: [
												{ boxLabel: 'Sun', name: 'ts_dw_Sun', width: 50 },
												{ boxLabel: 'Mon', name: 'ts_dw_Mon' },
												{ boxLabel: 'Tue', name: 'ts_dw_Tue' },
												{ boxLabel: 'Wed', name: 'ts_dw_Wed' },
												{ boxLabel: 'Thu', name: 'ts_dw_Thu' },
												{ boxLabel: 'Fri', name: 'ts_dw_Fri' },
												{ boxLabel: 'Sat', name: 'ts_dw_Sat' }
											]
										}, {
											xtype: 'numberfield',
											fieldLabel: 'Instances count',
											name: 'ts_instances_count',
											anchor:'95%',
											allowNegative: false,
											allowDecimals: false,
											allowBlank: false
										}
									]
								},
								buttons: [{
									text: 'Add'
								},{
									text: 'Cancel',
									handler:function()
									{
										win.hide();
									}
								}]
							});

							win.buttons[0].on('click', function () {
								var form_values = this.getForm().getValues();

								if (! this.getForm().isValid())
									return false;

								var week_days_list = '';
								var i = 0;

								for (k in form_values) {
									if (k.indexOf('ts_dw_') != -1 && form_values[k] == 'on') {
										week_days_list += k.replace('ts_dw_','')+', ';
										i++;
									}
								}

								if (i == 0) {
									Ext.MessageBox.show({
										title: 'Error',
										msg: 'You should select at least one week day',
										buttons: Ext.MessageBox.OK,
										animEl: 'mb9',
										icon: Ext.MessageBox.ERROR
									});
									return false;
								}
								else
									week_days_list = week_days_list.substr(0, week_days_list.length-2);

								var int_s_time = parseInt(form_values.ts_s_time.replace(/\D/g,''));
								var int_e_time = parseInt(form_values.ts_e_time.replace(/\D/g,''));

								if (form_values.ts_s_time.indexOf('AM') && int_s_time >= 1200)
									int_s_time = int_s_time-1200;

								if (form_values.ts_e_time.indexOf('AM') && int_e_time >= 1200)
									int_e_time = int_e_time-1200;

								if (form_values.ts_s_time.indexOf('PM') != -1)
									int_s_time = int_s_time+1200;

								if (form_values.ts_e_time.indexOf('PM') != -1)
									int_e_time = int_e_time+1200;

								if (int_e_time <= int_s_time) {
									Ext.MessageBox.show({
										title: 'Error',
										msg: 'End time value must be greater than Start time value',
										buttons: Ext.MessageBox.OK,
										animEl: 'mb9',
										icon: Ext.MessageBox.ERROR
									});
									return false;
								}

								var record_id = int_s_time+':'+int_e_time+':'+week_days_list+':'+form_values.ts_instances_count;

								var recordData = {
									start_time: form_values.ts_s_time,
									end_time: form_values.ts_e_time,
									instances_count: form_values.ts_instances_count,
									week_days: week_days_list,
									id: record_id
								};

								var list_exists = false;
								var list_exists_overlap = false;
								var week_days_list_array = week_days_list.split(", ");

								store.each(function(item, index, length) {
									if (item.data.id == recordData.id) {
										Ext.MessageBox.show({
											title: 'Error',
											msg: 'Such record already exists',
											buttons: Ext.MessageBox.OK,
											animEl: 'mb9',
											icon: Ext.MessageBox.ERROR
										});
										list_exists = true;
										return false;
									}

									var chunks = item.data.id.split(':');
									var s_time = chunks[0];
									var e_time = chunks[1];
									if (
										(int_s_time >= s_time && int_s_time <= e_time) ||
										(int_e_time >= s_time && int_e_time <= e_time)
									)
									{
										var week_days_list_array_item = (chunks[2]).split(", ");
										for (var ii = 0; ii < week_days_list_array_item.length; ii++)
										{
											for (var kk = 0; kk < week_days_list_array.length; kk++)
											{
												if (week_days_list_array[kk] == week_days_list_array_item[ii] && week_days_list_array[kk] != '')
												{
													list_exists_overlap = "Period "+week_days_list+" "+form_values.ts_s_time+" - "+form_values.ts_e_time+" overlaps with period "+chunks[2]+" "+item.data.start_time+" - "+item.data.end_time;
													return true;
												}
											}
										}
									}

								}, this);

								if (!list_exists && !list_exists_overlap) {
									store.add(new store.reader.recordType(recordData));
									win.close();
								} else {
									Ext.MessageBox.show({
										title: 'Error',
										msg: (!list_exists_overlap) ? 'Such record already exists' : list_exists_overlap,
										buttons: Ext.MessageBox.OK,
										animEl: 'mb9',
										icon: Ext.MessageBox.ERROR
									});
								}
							}, win.getComponent('form'));
							win.show();
						}
					}],
					items: new Scalr.Viewers.list.ListView({
						store: store,
						autoHeight: true,
						emptyText: "No periods defined",
						deferEmptyText: false,
						actionColumnPlugin: true,
						columns: [
							{ header: "Start time", width: '100px', sortable: true, dataIndex: 'start_time', hidden: 'no' },
							{ header: "End time", width: '100px', sortable: true, dataIndex: 'end_time', hidden: 'no' },
							{ header: "Week days", width: '150px', sortable: true, dataIndex: 'week_days', hidden: 'no' },
							{ header: "Instances count", width: '180px', sortable: true, dataIndex: 'instances_count', hidden: 'no', align: 'center' },
							{ header: "&nbsp;", width: '20px', sortable: false, dataIndex: 'id', align:'center', hidden: 'no',
								tpl: '<img src="/images/ui-ng/icons/delete_icon_16x16.png">', clickHandler: function (comp, store, record) {
									store.remove(record);
								}
							}
						]
					})
				});

				p.on('removed', function () {
					var el = this.findOne('name', 'scaling.max_instances');
					if (el)
						el.enable();
				}, this);
				this.findOne('name', 'scaling.max_instances').disable();

				store.loadData(values);
			}

			if (p) {
				p.on('removed', function () {
					var el = this.findOne('itemId', 'algos');
					if (el) {
						if (this.findOne('itemId', 'algos').items.length == 0) {
							this.findOne('itemId', 'algos').hide();
							this.findOne('itemId', 'algos_disabled').show();
						}
					}
				}, this);

				this.findOne('itemId', 'algos_disabled').hide();
				this.findOne('itemId', 'algos').show();

				if (activate)
					tabpanel.activate(p);
			}
		},

		isEnabled: function (record) {
			return record.get('platform') != 'rds';
		},

		getDefaultValues: function (record) {
			return {
				'scaling.min_instances': 1,
				'scaling.max_instances': 2,
				'scaling.polling_interval': 1,
				'scaling.keep_oldest': 0,
				'scaling.safe_shutdown': 0
			};
		},

		activateTab: function () {
			new Ext.ToolTip({
				target: this.findOne('name', 'scaling.min_instances_help').id,
				dismissDelay: 0,
				html: "永远保证至少有这么多服务器在运行"
			});

			new Ext.ToolTip({
				target: this.findOne('name', 'scaling.max_instances_help').id,
				dismissDelay: 0,
				html: "最多启动的服务器数目"
			});

			new Ext.ToolTip({
				target: this.findOne('name', 'scaling.safe_shutdown_help').id,
				dismissDelay: 0,
				html: "仅在'/usr/local/scalarizr/hooks/auth-shutdown'返回1时关闭服务器。 "+
					"如果未发现脚本或脚本返回不为1，则不会关闭服务器。"
			});

			this.findOne('name', 'scaling.upscale.timeout_enabled').on('check', function (checkbox, checked) {
				if (checked)
					this.findOne('name', 'scaling.upscale.timeout').enable();
				else
					this.findOne('name', 'scaling.upscale.timeout').disable();
			}, this);

			this.findOne('name', 'scaling.downscale.timeout_enabled').on('check', function (checkbox, checked) {
				if (checked)
					this.findOne('name', 'scaling.downscale.timeout').enable();
				else
					this.findOne('name', 'scaling.downscale.timeout').disable();
			}, this);

			this.findOne('name', 'enable_scaling_add').getEl().on('click', function () {
				var combo = this.findOne('name', 'enable_scaling'), value = combo.store.getById(combo.getValue());
				if (value) {
					var items = this.findOne('itemId', 'algos').items.items;

					for (var i = 0; i < items.length; i++) {
						if (items[i].alias == value.get('alias')) {
							Scalr.Viewers.ErrorMessage('This algoritm already added');
							return;
						}
					}

					if (value.get('alias') == 'time' && items.length) {
						Scalr.Viewers.ErrorMessage('This algoritm cannot be used with others');
						return;
					} else if (value.get('alias') != 'time' && items.length) {
						for (var i = 0; i < items.length; i++) {
							if (items[i].alias == 'time') {
								Scalr.Viewers.ErrorMessage("This algoritm cannot be used with 'Time and Day of week'");
								return;
							}
						}
					}

					this.addAlgoTab(value, {}, true);
					combo.reset();
				} else {
					combo.markInvalid();
				}
			}, this);
		},

		showTab: function (record) {
			if (! this.loaded) {
				Scalr.Request({
					processBox: {
						type: 'action'
					},
					url: '/scaling/metrics/getList',
					scope: this,
					success: function (data) {
						this.findOne('name', 'enable_scaling').store.loadData(data.metrics);
						this.loaded = true;
						this.showTab.call(this, record);
					}
				});
			} else {
				var settings = record.get('settings'), scaling = record.get('scaling');

				if (record.get('generation') == 2)
					this.findOne('itemId', 'scaling.safe_shutdown_compositefield').show();
				else
					this.findOne('itemId', 'scaling.safe_shutdown_compositefield').hide();

				this.findOne('name', 'scaling.min_instances').setValue(settings['scaling.min_instances'] || 1);
				this.findOne('name', 'scaling.max_instances').setValue(settings['scaling.max_instances'] || 2);
				this.findOne('name', 'scaling.polling_interval').setValue(settings['scaling.polling_interval'] || 1);
				this.findOne('name', 'scaling.keep_oldest').setValue(settings['scaling.keep_oldest'] == 1 ? true : false);
				this.findOne('name', 'scaling.safe_shutdown').setValue(settings['scaling.safe_shutdown'] == 1 ? true : false);

				if (settings['scaling.upscale.timeout_enabled'] == 1) {
					this.findOne('name', 'scaling.upscale.timeout_enabled').setValue(true);
					this.findOne('name', 'scaling.upscale.timeout').enable();
				} else {
					this.findOne('name', 'scaling.upscale.timeout_enabled').setValue(false);
					this.findOne('name', 'scaling.upscale.timeout').disable();
				}
				this.findOne('name', 'scaling.upscale.timeout').setValue(settings['scaling.upscale.timeout'] || 10);

				if (settings['scaling.downscale.timeout_enabled'] == 1) {
					this.findOne('name', 'scaling.downscale.timeout_enabled').setValue(true);
					this.findOne('name', 'scaling.downscale.timeout').enable();
				} else {
					this.findOne('name', 'scaling.downscale.timeout_enabled').setValue(false);
					this.findOne('name', 'scaling.downscale.timeout').disable();
				}
				this.findOne('name', 'scaling.downscale.timeout').setValue(settings['scaling.downscale.timeout'] || 10);
				this.findOne('name', 'enable_scaling').reset();

				// algos
				this.findOne('itemId', 'algos').removeAll();
				var store = this.findOne('name', 'enable_scaling').store;

				if (Ext.isObject(scaling)) {
					for (var i in scaling) {
						this.addAlgoTab(store.getById(i), scaling[i], false);
					}
				}

				if (this.findOne('itemId', 'algos').items.length) {
					this.findOne('itemId', 'algos').show();
					this.findOne('itemId', 'algos_disabled').hide();
					this.findOne('itemId', 'algos').activate(this.findOne('itemId', 'algos').items.items[0]);
				} else {
					this.findOne('itemId', 'algos').hide();
					this.findOne('itemId', 'algos_disabled').show();
				}
			}
		},

		hideTab: function (record) {
			var settings = record.get('settings');
			var scaling = {};

			settings['scaling.min_instances'] = this.findOne('name', 'scaling.min_instances').getValue();
			settings['scaling.max_instances'] = this.findOne('name', 'scaling.max_instances').getValue();
			settings['scaling.polling_interval'] = this.findOne('name', 'scaling.polling_interval').getValue();
			settings['scaling.keep_oldest'] = this.findOne('name', 'scaling.keep_oldest').getValue() == true ? 1 : 0;
			settings['scaling.safe_shutdown'] = this.findOne('name', 'scaling.safe_shutdown').getValue() == true ? 1 : 0;

			if (this.findOne('name', 'scaling.upscale.timeout_enabled').getValue()) {
				settings['scaling.upscale.timeout_enabled'] = 1;
				settings['scaling.upscale.timeout'] = this.findOne('name', 'scaling.upscale.timeout').getValue();
			} else {
				settings['scaling.upscale.timeout_enabled'] = 0;
				delete settings['scaling.upscale.timeout'];
			}

			if (this.findOne('name', 'scaling.downscale.timeout_enabled').getValue()) {
				settings['scaling.downscale.timeout_enabled'] = 1;
				settings['scaling.downscale.timeout'] = this.findOne('name', 'scaling.downscale.timeout').getValue();
			} else {
				settings['scaling.downscale.timeout_enabled'] = 0;
				delete settings['scaling.downscale.timeout'];
			}

			// algos
			this.findOne('itemId', 'algos').items.each(function (it) {
				scaling[it.metricId.toString()] = it.getValues.call(this, it);
			}, this);

			record.set('settings', settings);
			record.set('scaling', scaling);
		},

		items: [{
			xtype: 'fieldset',
			title: '通用',
			labelWidth: 120,
			items: [{
				xtype: 'compositefield',
				fieldLabel: '最小服务器数',
				items: [{
					xtype: 'textfield',
					name: 'scaling.min_instances',
					width: 40
				}, {
					xtype: 'displayfield',
					name: 'scaling.min_instances_help',
					value: '<img src="/images/ui-ng/icons/info_icon_16x16.png" style="padding: 2px; cursor: help;">'
				}]
			}, {
				xtype: 'compositefield',
				fieldLabel: '最大服务器数',
				items: [{
					xtype: 'textfield',
					name: 'scaling.max_instances',
					width: 40
				}, {
					xtype: 'displayfield',
					name: 'scaling.max_instances_help',
					value: '<img src="/images/ui-ng/icons/info_icon_16x16.png" style="padding: 2px; cursor: help;">'
				}]
			}, {
				xtype: 'compositefield',
				hideLabel: true,
				items: [{
					xtype: 'displayfield',
					value: '监控间隔(每次)',
					cls: 'x-form-check-wrap'
				}, {
					xtype: 'textfield',
					name: 'scaling.polling_interval',
					width: 40
				}, {
					xtype: 'displayfield',
					value: '分钟',
					cls: 'x-form-check-wrap'
				}]
			}, {
				xtype: 'checkbox',
				name: 'scaling.keep_oldest',
				hideLabel: true,
				boxLabel: '减少服务器时保留最老的服务器'
			}, {
				xtype: 'compositefield',
				itemId: 'scaling.safe_shutdown_compositefield',
				hideLabel: true,
				items: [{
					xtype: 'checkbox',
					name: 'scaling.safe_shutdown',
					width: 250,
					boxLabel: '减少服务器时使用安全关机'
				}, {
					xtype: 'displayfield',
					name: 'scaling.safe_shutdown_help',
					value: '<img src="/images/ui-ng/icons/info_icon_16x16.png" style="padding: 2px; cursor: help;">'
				}]
			}]
		}, {
			xtype: 'fieldset',
			title: '延时',
			labelWidth: 120,
			items: [{
				xtype: 'compositefield',
				hideLabel: true,
				items: [{
					xtype: 'checkbox',
					hideLabel: true,
					boxLabel: '当服务器已启动并正常运行后，等待',
					name: 'scaling.upscale.timeout_enabled'
				}, {
					xtype: 'textfield',
					name: 'scaling.upscale.timeout',
					width: 40
				}, {
					xtype: 'displayfield',
					value: '分钟再启动新的服务器',
					cls: 'x-form-check-wrap'
				}]
			}, {
				xtype: 'compositefield',
				hideLabel: true,
				items: [{
					xtype: 'checkbox',
					hideLabel: true,
					boxLabel: '当服务器正常关机后，等待',
					name: 'scaling.downscale.timeout_enabled'
				}, {
					xtype: 'textfield',
					name: 'scaling.downscale.timeout',
					width: 40
				}, {
					xtype: 'displayfield',
					value: '分钟再关闭另一个服务器',
					cls: 'x-form-check-wrap'
				}]
			}]
		}, {
			xtype: 'compositefield',
			fieldLabel: '自适应基数',
			items: [{
				xtype: 'combo',
				store: Scalr.utils.CreateStore([], { idProperty: 'id', fields: [ 'id', 'name', 'alias' ]}),
				valueField: 'id',
				displayField: 'name',
				editable: false,
				mode: 'local',
				name: 'enable_scaling',
				triggerAction: 'all',
				width: 200
			}, {
				xtype: 'displayfield',
				value: '<img src="/images/ui-ng/icons/add_icon_16x16.png">',
				name: 'enable_scaling_add',
				cls: 'x-form-check-wrap'
			}]
		}, {
			itemId: 'algos_disabled',
			autoHeight: true,
			padding: 10,
			style:'font-size:12px;',
			html: '本服务角色禁用自适应功能'
		}, {
			xtype: 'tabpanel',
			itemId: 'algos',
			enableTabScroll: true,
			deferredRender: false,
			autoHeight: true,
			defaults: { autoHeight: true },
			items: []
		}]
	});
});
