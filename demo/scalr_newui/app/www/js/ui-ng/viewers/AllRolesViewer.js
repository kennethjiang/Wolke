Ext.ns("Scalr.Viewers");

Scalr.Viewers.AllRolesViewer = Ext.extend(Ext.Panel, {
	border: false,
	layout: 'fit',

	changeSelectedPlatforms: function (comp, checked) {
		this.platformFilter[comp.platform] = checked;
		this.dataView.filterRoles.call(this);
	},

	changeSelectedLocations: function (comp, checked) {
		this.locationFilter[comp.platformLocation] = checked;
		this.dataView.filterRoles.call(this);
	},

	initComponent: function() {
		var platforms = [];
		var locations = [];
		this.platformFilter = {};
		this.locationFilter = {};

		for (var i in this.platforms) {
			platforms[platforms.length] = {
				text: this.platforms[i],
				platform: i,
				checked: true,
				hideOnClick: false,
				listeners: {
					checkchange: this.changeSelectedPlatforms,
					scope: this
				}
			};
			this.platformFilter[i] = true;
		}

		var t_locations = {};
		for (var i in this.locations) {
			for (var j in this.locations[i]) {
				t_locations[j] = this.locations[i][j];
			}
		}

		for (var i in t_locations) {
			locations[locations.length] = {
				text: t_locations[i],
				platformLocation: i,
				checked: true,
				hideOnClick: false,
				listeners: {
					'checkchange': this.changeSelectedLocations,
					scope: this
				}
			};
			this.locationFilter[i] = true;
		}

		this.tbar = [{
			itemId: 'legacy',
			xtype: 'checkbox',
			boxLabel: '显示历史服务角色',
			checked: this.legacyRoles,
			style: 'margin: 0px'
		}, '-', {
			itemId: 'stable',
			xtype: 'checkbox',
			inputValue: 1,
			boxLabel: '稳定',
			checked: true,
			style: 'margin: 0px'
		}, '-', '提供自:', {
			itemId: 'origin',
			xtype: 'combo',
			store: [ [ '', '' ], [ 'Shared', 'Scalr' ], [ 'Custom', 'Private' ] ],
			value: '',
			mode: 'local',
			forceSelection: true,
			editable: false,
			triggerAction: 'all',
			emptyText: 'Role origin...',
			width: 100
		}, '-', {
			text: '平台',
			itemId: 'platform',
			menu: new Ext.menu.Menu({
				items: platforms
			})
		}, {
			text: '位置',
			itemId: 'location',
			hidden: true,
			menu: new Ext.menu.Menu({
				items: locations
			})
		}, '->', '过滤:', {
			itemId: 'filter',
			xtype: 'textfield'
		}],

		this.dataView = new Ext.DataView({
			roles: this.roles,
			filterRoles: function() {
				var filters = [];

				filters[filters.length] = {
					fn: function(record) {
						return record.get('isstable') == this.getValue();
					},
					scope: this.getTopToolbar().getComponent('stable')
				};

				filters[filters.length] = {
					fn: function (record) {
						return this.getValue() || record.get('generation') == 2;
					},
					scope: this.getTopToolbar().getComponent('legacy')
				};

				if (this.getTopToolbar().getComponent('origin').getValue())
					filters[filters.length] = {
						property: 'origin',
						value: this.getTopToolbar().getComponent('origin').getValue()
					};

				filters[filters.length] = {
					fn: function (record) {
						var locations = record.get('locations');
						for (var key in this.platformFilter) {
							if (this.platformFilter[key] && locations[key]) {
								return true;
								var loc = locations[key];
								for (var i = 0, len = loc.length; i < len; i++) {
									if (this.locationFilter[loc[i]])
										return true;
								}
							}
						}
					},
					scope: this
				};

				if (this.getTopToolbar().getComponent('filter').getValue()) {
					filters[filters.length] = {
						fn: function(record) {
							return (record.get('name').toLowerCase().search(this.getValue().toLowerCase()) != -1) ? true : false;
						},
						scope: this.getTopToolbar().getComponent('filter')
					};
				}

				this.dataView.getStore().filter(filters);
			},

			collectData: function(records, startIndex) {
				var groups = [];
				for (key in this.groupsInfo) {
					var el = this.groupsInfo[key];
					groups[el.index] = { title: el.name, groupid: key, status: el.status, records: [] };
				}

				for (var i = 0, len = records.length; i < len; i++) {
					var index = this.groupsInfo[records[i].get('group')].index;
					groups[index].records[groups[index].records.length] = records[i].data;
				}

				return groups;
			},

			refresh: function() {
				this.clearSelections(false, true);
				var el = this.getTemplateTarget();
				el.update("");
				var records = this.store.getRange();

				// always show groups
				this.tpl.overwrite(el, this.collectData(records, 0));
				this.all.fill(Ext.query(this.itemSelector, el.dom));
				this.updateIndexes(0);

				// update links
				this.addCollapseLinks();
			},

			addCollapseLinks: function() {
				Ext.select("#viewers-addrolesviewer div.title").each(function(el) {
					handler = function(e) {
						var el = e.getTarget("", 10, true).findParent("div.title", 10, true);
						var groupid = el.getAttribute("groupid");

						if (this.groupsInfo[groupid]) {
							this.groupsInfo[groupid].status = (this.groupsInfo[groupid].status == "contract") ? "" : "contract";
						}

						el.toggleClass("title-contract");
						var ul = el.next("ul");
						if (ul) {
							ul.toggleClass("hidden");
						}
					};

					if (! el.is("div.title-disabled")) {
						el.on('click', handler, this);
					}
				}, this);
			},

			id: 'viewers-addrolesviewer',
			store: this.store,
			autoScroll: true,
			tpl: new Ext.XTemplate(
				'<tpl for=".">',
					'<div class="block">',
					'<div groupid="{groupid}" class="title',
					'<tpl if="records.length &gt; 0 && status == \'contract\'"> title-contract</tpl>',
					'<tpl if="records.length == 0"> title-disabled</tpl>',
					'"><div><span>{title}</span></div></div>',

					'<tpl if="records.length">',
					'<ul',
					'<tpl if="status == &quot;contract&quot;"> class="hidden"</tpl>',
					'>',
						'<tpl for="records">',
							'<li itemid="{role_id}" itemname="{name}">',
								'<div class="fixed">',
									'<div class="platforms"><tpl for="platforms.split(\',\')"><img src="/images/ui-ng/icons/platform/{.}.png"></tpl></div>',
									'<div class="arch"><img src="/images/ui-ng/icons/arch/{arch}.png"></div>',
								'</div>',
								'<b>{name}</b><br />',
								'<span>{os}</span><br /><br />',
								'<div class="info"><img class="add" src="/images/ui-ng/viewers/addrolesviewer/add.png" style="cursor: pointer">&nbsp;<img class="info" src="/images/ui-ng/viewers/addrolesviewer/info.png" style="cursor: pointer"></div>',
							'</li>',
						'</tpl>',
					'</ul>',
					'</tpl>',
					'</div>',
				'</tpl>'
			),
			itemSelector: 'li'
		});

		this.dataView.groupsInfo = {};
		var i = 0;
		for (var key in this.groups) {
			this.dataView.groupsInfo[key] = {status: "contract", name: this.groups[key], index: i++};
		}

		this.dataView.on('afterrender', this.dataView.filterRoles, this);

		this.dataView.on('afterrender', function() {
			this.getTopToolbar().getComponent('legacy').on('check', this.dataView.filterRoles, this);
			this.getTopToolbar().getComponent('stable').on('check', this.dataView.filterRoles, this);
			this.getTopToolbar().getComponent('origin').on('select', this.dataView.filterRoles, this);
			this.getTopToolbar().getComponent('platform').on('select', this.dataView.filterRoles, this);
			Scalr.fireOnInputChange(this.getTopToolbar().getComponent('filter').getEl(), this, this.dataView.filterRoles);

			Ext.get('viewers-addrolesviewer').on('click', function(e) {
				var t = e.getTarget('li', 10, true), current = Ext.get('viewers-addrolesviewer').child('li.addrolesviewer-selected');
				if (current && (t && !t.hasClass('addrolesviewer-selected') || !t)) {
					current.removeClass('addrolesviewer-selected');
					var d = current.child('div.info');
					d.slideOut('t', {
						duration: 0.3
					});
				}

				if (t && !t.hasClass('addrolesviewer-selected')) {
					var ul = e.getTarget('ul', 10, true), offsets = t.getOffsetsTo(ul);

					t.addClass('addrolesviewer-selected');
					var d = t.child('div.info');
					d.setLeftTop(offsets[0], offsets[1] + 119);
					d.slideIn('t', {
						duration: 0.3
					});

					var role = this.store.getById(t.getAttribute('itemid')), rLocations = role.get('locations');

					d.child('img.info').removeAllListeners();
					d.child('img.info').on('click', function (e) {
						// temporary, in future: #/roles/1/info
						Ext.Msg.wait("Loading role information ...");
						Ext.Ajax.request({
							url: '/roles/info',
							params: { roleId: t.getAttribute("itemid") },
							success: function (response) {
								var result = Ext.decode(response.responseText);
								if (result.success == true) {
									result.module = "(function() { return " + result.module + "; })();";
									var obj = eval(result.module);
									var formObj = obj.create({}, result.moduleParams);

									new Ext.Window({
										modal: true,
										title: formObj.title,
										closable: true,
										width: 700,
										autoHeight: true,
										resizable: false,
										bodyStyle: 'background-color: white; padding: 5px',
										items: formObj.initialConfig.items
									}).show();

									delete formObj;
								}
								Ext.Msg.hide();
							},
							failure: function() {
								Ext.Msg.hide();
							}
						});
						e.preventDefault();
					});

					d.child('img.add').removeAllListeners();
					d.child('img.add').on('click', function (e) {
						var cnt = 0, plat = '', loc = '', platforms = [], locations = [];
						for (var i in rLocations) {
							plat = i;
							cnt++;
							platforms[platforms.length] = [i, this.platforms[i]];
						}

						if (cnt > 1) {
							plat = platforms[0][0];
						} else {

							if (rLocations[plat].length == 1) {
								// TODO: create one place add function (see later)
								this.fireEvent('addrole', {
									role_id: t.getAttribute('itemid'),
									platform: plat,
									cloud_location: rLocations[plat][0],
									arch: role.get('arch'),
									generation: role.get('generation'),
									name: role.get('name'),
									behaviors: role.get('behaviors'),
									group: role.get('group'),
									tags: role.get('tags')
								});
								return;
							}
						}

						loc = rLocations[plat][0];
						for (var i = 0, len = rLocations[plat].length; i < len; i++)
							locations[locations.length] = [rLocations[plat][i], this.locations[plat][rLocations[plat][i]]];

						// если роль не имеет выбора платформа/регион - сразу добавлять
						var win = new Ext.Window({
							title: '添加服务角色 "' + t.getAttribute('itemname') + '"',
							modal: true,
							draggable: false,
							resizable: false,
							border: false,
							width: 335,
							items: [{
								xtype: 'form',
								itemId: 'form',
								frame: true,
								border: false,
								items: [{
									xtype: 'combo',
									fieldLabel: '平台',
									store: platforms,
									allowBlank: false,
									editable: false,
									name: 'platform',
									value: plat,
									typeAhead: false,
									mode: 'local',
									triggerAction: 'all',
									selectOnFocus: false,
									width: 200,
									emptyText: 'Please select platform',
									listeners: {
										change: function(field, value) {
											locations = [];
											plat = win.getComponent('form').getForm().findField('platform').getValue();

											for (var i = 0, len = rLocations[plat].length; i < len; i++)
												locations[locations.length] = [rLocations[plat][i], this.locations[plat][rLocations[plat][i]]];

											win.getComponent('form').getForm().findField('cloud_location').reset();
											win.getComponent('form').getForm().findField('cloud_location').store.loadData(locations);
										},
										scope: this
									}
								}, {
									xtype: 'combo',
									fieldLabel: '位置',
									store: new Ext.data.ArrayStore({
										id: '0',
										fields: [ 'value', 'text' ],
										data: locations,
										autoDestroy: true
									}),
									valueField: 'value',
									displayField: 'text',
									allowBlank: false,
									editable: false,
									name: 'cloud_location',
									value: loc,
									typeAhead: false,
									mode: 'local',
									triggerAction: 'all',
									selectOnFocus: false,
									width: 200,
									emptyText: 'Please select location'
								}],
								buttonAlign: 'center',
								buttons: [{
									text: '添加',
									handler: function() {
										if (win.getComponent('form').getForm().isValid()) {
											// TODO: don't forget this too
											this.fireEvent('addrole', {
												role_id: t.getAttribute('itemid'),
												platform: win.getComponent('form').getForm().findField('platform').getValue(),
												cloud_location: win.getComponent('form').getForm().findField('cloud_location').getValue(),
												generation: role.get('generation'),
												arch: role.get('arch'),
												name: role.get('name'),
												behaviors: role.get('behaviors'),
												group: role.get('group'),
												tags: role.get('tags')
											});
											win.close();
										}
									},
									scope: this
								}, {
									text: '取消',
									handler: function() {
										win.close();
									}
								}]
							}]
						});
						win.show();
						e.preventDefault();
					}, this);
				}
			}, this);
		}, this);

		this.items = [this.dataView];

		Scalr.Viewers.AllRolesViewer.superclass.initComponent.call(this);

		this.addEvents(
			"addrole"
		);
	}
});
