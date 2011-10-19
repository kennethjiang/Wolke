{literal}
new Scalr.Viewers.FarmRolesEditTab({
	tabTitle: 'Scripting',
	layout: 'hbox',
	layoutConfig: {
		align: 'stretch',
		pack: 'start'
	},
	loaded: false,

	scripts: {},

	isEnabled: function (record) {
		return record.get('platform') != 'rds';
	},

	activateTab: function () {
		var toolbar = this.findOne('itemId', 'scripting.add').getTopToolbar();

		toolbar.getComponent('add').on('click', function () {
			var script = toolbar.getComponent('script').getValue(),
				event = toolbar.getComponent('event').getValue();

			if (!script || !event) {
				Scalr.Viewers.ErrorMessage('Please select script and event', null, 5);
			} else {
				var store = this.findOne('itemId', 'scripting.all').store, ind = store.findBy(function (rec) {
					if (rec.get('script_id') == script && rec.get('event') == event)
						return true;
				});

				if (ind == -1) {
					var rec = toolbar.getComponent('script').store.getById(script), version = 0, revisions = rec.get('revisions');
					for (var i = 0; i < revisions.length; i++) {
						if (revisions[i].revision > version)
							version = revisions[i].revision;
					}

					var newRec = new store.recordType({
						script: rec.get('name'),
						event: event,
						target: 'farm',
						script_id: rec.get('id'),
						timeout: rec.get('timeout'),
						issync: rec.get('issync'),
						version: version,
						order_index: store.getCount() * 10 // TODO: replace
					});

					store.add(newRec);

					toolbar.getComponent('script').reset();
					toolbar.getComponent('event').reset();
					this.findOne('itemId', 'scripting.all').select(newRec);
				} else
					this.findOne('itemId', 'scripting.all').select(store.getAt(ind));
			}
		}, this);

		this.findOne('itemId', 'scripting.all').on('selectionchange', function (dataview, selections) {
			// save previous record
			var rec = this.findOne('itemId', 'scripting.edit').currentRecord, fieldset = this.findOne('itemId', 'scripting.edit.parameters'), params = {};
			if (rec) {
				rec.set('target', this.findOne('name', 'scripting.edit.target').getValue());
				rec.set('issync', this.findOne('name', 'scripting.edit.issync').getValue());
				rec.set('timeout', this.findOne('name', 'scripting.edit.timeout').getValue());
				rec.set('version', this.findOne('name', 'scripting.edit.version').getValue());
				rec.set('order_index', this.findOne('name', 'scripting.edit.order_index').getValue());

				for (var i = 0; i < fieldset.items.items.length; i++)
					params[fieldset.items.items[i].paramName] = fieldset.items.items[i].getValue();

				rec.set('params', params);
				this.findOne('itemId', 'scripting.edit').currentRecord = null;
			}

			if (selections.length) {
				this.findOne('itemId', 'scripting.edit').show();
				var rec = dataview.getRecord(selections[0]), script = toolbar.getComponent('script').store.getById(rec.get('script_id'));

				this.findOne('itemId', 'scripting.edit').currentRecord = rec;
				this.findOne('name', 'scripting.edit.when').setValue(
					toolbar.getComponent('event').store.getById(rec.get('event')).get('description')
				);
				this.findOne('name', 'scripting.edit.do').setValue(
					script.get('description')
				);

				var data = [ [ 'farm', 'All instances in the farm' ] ];
				if (rec.get('event') != 'DNSZoneUpdated')
					data[data.length] = ['role', 'All instances of this role'];

				if (rec.get('event') != 'HostDown' && rec.get('event') != 'DNSZoneUpdated')
					data[data.length] = ['instance', 'That instance only'];

				this.findOne('name', 'scripting.edit.target').store.loadData(data);

				this.findOne('name', 'scripting.edit.target').setValue(rec.get('target'));
				this.findOne('name', 'scripting.edit.issync').setValue(rec.get('issync'));
				this.findOne('name', 'scripting.edit.timeout').setValue(rec.get('timeout'));
				this.findOne('name', 'scripting.edit.order_index').setValue(rec.get('order_index'));

				this.findOne('name', 'scripting.edit.version').store.loadData(script.get('revisions'));
				this.findOne('name', 'scripting.edit.version').setValue(rec.get('version'));
				this.findOne('name', 'scripting.edit.version').fireEvent('select', this.findOne('name', 'scripting.edit.version'),
					this.findOne('name', 'scripting.edit.version').store.getById(rec.get('version'))
				);

				var fieldset = this.findOne('itemId', 'scripting.edit.parameters'), params = rec.get('params');
				for (var i in params) {
					var f = fieldset.find('paramName', i);
					if (f.length)
						f[0].setValue(params[i]);
				}

				this.findOne('itemId', 'scripting.edit.script').collapse();

			} else {
				this.findOne('itemId', 'scripting.edit').currentRecord = null;
				this.findOne('itemId', 'scripting.edit').hide();
			}
		}, this);

		//console.log(this.findOne('itemId', 'scripting.edit.script').el);
		this.findOne('itemId', 'scripting.edit.script').on('expand', function () {
			var rec = this.findOne('itemId', 'scripting.edit').currentRecord;

			this.loadMask.show();
			Ext.Ajax.request({
				url: '/server/server.php',
				params: {
					_cmd: 'get_script_template_source',
					version: rec.get('version'),
					scriptid: rec.get('script_id')
				},
				success: function(response, options) {
					var content = response.responseText.replace(/&/gm, '&amp;').replace(/</gm, '&lt;').replace(/>/gm, '&gt;');
					content = '<pre style="margin: 0px"><code>' + content + '</code></pre>';
					this.findOne('itemId', 'scripting.edit.script').body.update(content);


					hljs.highlightBlock(this.findOne('itemId', 'scripting.edit.script').body.dom.firstChild.firstChild);
					this.loadMask.hide();

				},
				scope: this
			});



		}, this);

		new Ext.Resizable(this.findOne('itemId', 'scripting.edit.script').el, {
			handles: 'se',
			wrap: false,
			pinned: true,
			width: 400,
			height: 200,
			minWidth: 50,
			maxWidth: 800,
			minHeight: 50,
			maxHeight: 400
		});

		this.findOne('name', 'scripting.edit.version').on('select', function (field, record) {
			var fields = Ext.decode(record.get('fields') || ''), fieldset = this.findOne('itemId', 'scripting.edit.parameters');

			if (Ext.isObject(fields)) {
				fieldset.show();
				fieldset.removeAll();

				for (var i in fields) {
					fieldset.add({
						xtype: 'textfield',
						fieldLabel: fields[i],
						paramName: i,
						width: 300
					});
				}

				fieldset.doLayout(false, true);

			} else
				fieldset.hide();

		}, this);

		this.findOne('itemId', 'scripting.delete.script').on('click', function () {
			var rec = this.findOne('itemId', 'scripting.edit').currentRecord;
			this.findOne('itemId', 'scripting.all').clearSelections();
			this.findOne('itemId', 'scripting.all').store.remove(rec);
		}, this);
	},

	showTab: function (record) {
		this.findOne('itemId', 'scripting.edit').hide();
		var toolbar = this.findOne('itemId', 'scripting.add').getTopToolbar();

		if (! this.loaded) {
			this.loadMask.show();
			Ext.Ajax.request({
				url: '/server/farm_builder_roles_list.php?list=scripting',
				success: function(response, options) {
					var result = Ext.decode(response.responseText);

					if (result.scripts)
						toolbar.getComponent('script').store.loadData(result.scripts);

					if (result.events)
						toolbar.getComponent('event').store.loadData(result.events);

					this.loadMask.hide();
					this.loaded = true;

					this.showTab.call(this, record);
				},
				scope: this
			});
		} else {
			toolbar.getComponent('script').reset();
			toolbar.getComponent('event').reset();

			this.findOne('itemId', 'scripting.all').store.loadData(record.get('scripting'));
		}
	},

	hideTab: function (record) {

		this.findOne('itemId', 'scripting.all').clearSelections();

		var scripting = [];

		this.findOne('itemId', 'scripting.all').store.each(function (it) {
			scripting[scripting.length] = it.data;
		});

		var fieldset = this.findOne('itemId', 'scripting.edit.parameters'), params = {};
		for (var i = 0; i < fieldset.items.items.length; i++)
			params[fieldset.items.items[i].paramName] = fieldset.items.items[i].getValue();

		record.set('params', params);

		record.set('scripting', scripting);
	},

	items: [{
		border: true,
		width: 300,
		xtype: 'panel',
		itemId: 'scripting.add',
		layout: 'vbox',
		layoutConfig: {
			align: 'stretch',
			pack: 'start'
		},
		tbar: [{
				xtype: 'button',
				icon: '/images/add.png',
				itemId: 'add'
			}, {
				xtype: 'combo',
				store: new Ext.data.JsonStore({
					idProperty: 'id',
					fields: [ 'id', 'name', 'description', 'issync', 'timeout', 'revisions' ]
				}),
				valueField: 'id',
				displayField: 'name',
				editable: false,
				mode: 'local',
				itemId: 'script',
				triggerAction: 'all',
				resizable: true,
				listWidth: 160,
				width: 90
			}, {
				xtype: 'tbtext',
				text: 'script on',
				style: 'font-size: 12px'
			}, {
				xtype: 'combo',
				store: new Ext.data.ArrayStore({
					idIndex: 0,
					fields: [ 'name', 'description' ]
				}),
				valueField: 'name',
				displayField: 'name',
				editable: false,
				mode: 'local',
				itemId: 'event',
				triggerAction: 'all',
				resizable: true,
				listWidth: 160,
				width: 90
			}, {
				xtype: 'tbtext',
				text: 'event',
				style: 'font-size: 12px'
			}
		],
		items: new Ext.DataView({
			id: 'viewers-farmrolesedit-tab-scripting',
			flex: 1,
			store: new Ext.data.JsonStore({
				fields: [ 'script_id', 'script', 'event', 'target', 'issync', 'timeout', 'version', 'params', 'order_index' ]
			}),
			border: true,
			deferEmptyText: false,
			tpl: '<tpl for="."><div class="element"><img src="/images/ui-ng/icons/script.gif"><span class="script">{script}</span> on <span class="event">{event}</span></div></tpl>',
			itemId: 'scripting.all',
			autoScroll: true,
			itemSelector: 'div.element',
			selectedClass: 'selected',
			singleSelect: true,
			emptyText: '<div class="empty">No scripts assigned to events</div>'
		})
	}, {
		flex: 1,
		border: false,
		layout: 'form',
		autoScroll: true,
		itemId: 'scripting.edit',
		style: 'padding-left: 10px',
		items: [{
			xtype: 'fieldset',
			title: 'General',
			width: '80%',
			items: [{
				xtype: 'displayfield',
				fieldLabel: 'When',
				name: 'scripting.edit.when'
			}, {
				xtype: 'displayfield',
				fieldLabel: 'Do',
				name: 'scripting.edit.do'
			}, {
				xtype: 'combo',
				store: [ ['instance', 'That instance only'], ['role', 'All instances of this role'], [ 'farm', 'All instances in the farm' ]],
				editable: false,
				mode: 'local',
				name: 'scripting.edit.target',
				triggerAction: 'all',
				fieldLabel: 'Where'
			}, {
				xtype: 'combo',
				store: [ ['1', 'Synchronous'], ['0', 'Asynchronous']],
				editable: false,
				mode: 'local',
				name: 'scripting.edit.issync',
				triggerAction: 'all',
				fieldLabel: 'Execution mode'
			}, {
				xtype: 'textfield',
				fieldLabel: 'Timeout',
				name: 'scripting.edit.timeout',
				width: 60
			},{
				xtype: 'textfield',
				fieldLabel: 'Execution order',
				name: 'scripting.edit.order_index',
				width: 60
			}, {
				xtype: 'combo',
				store: new Ext.data.JsonStore({
					idProperty: 'revision',
					fields: [ 'revision', 'fields' ]
				}),
				valueField: 'revision',
				displayField: 'revision',
				editable: false,
				mode: 'local',
				name: 'scripting.edit.version',
				triggerAction: 'all',
				width: 60,
				listWidth: 60,
				fieldLabel: 'Version'
			}]
		}, {
			xtype: 'fieldset',
			itemId: 'scripting.edit.parameters',
			title: 'Parameters'
		}, {
			xtype: 'fieldset',
			hidden: true,
			itemId: 'scripting.edit.script',
			checkboxToggle: true,
			title: 'Script source',
			autoScroll: true
		}, {
			xtype: 'button',
			itemId: 'scripting.delete.script',
			text: 'Delete'
		}]
	}]

})
{/literal}
