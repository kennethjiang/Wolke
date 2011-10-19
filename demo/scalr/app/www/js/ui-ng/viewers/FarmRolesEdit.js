Ext.ns("Scalr.Viewers");

Scalr.Viewers.FarmRolesEditPanel = Ext.extend(Ext.Container, {
	layout: 'card',
	layoutConfig: {
		deferredRender: true
	},

	currentRole: null,

	getLayoutTarget: function () {
		return this.elcontent;
	},

	addRoleDefaultValues: function (record) {
		var settings = record.get('settings');

		this.items.each(function(item) {
			if (item.isEnabled(record))
				Ext.apply(settings, item.getDefaultValues(record));
		});

		record.set('settings', settings);
	},

	setCurrentRole: function (record) {
		this.currentRole = record;
	},

	render: function (container, position) {
		Scalr.Viewers.FarmRolesEditPanel.superclass.render.call(this, container, position);

		this.eltabs = this.el.createChild({ tag: 'div', 'class': 'viewers-farmrolesedit-tabs' });
		this.elcontent = this.el.createChild({ tag: 'div', 'class': 'viewers-farmrolesedit-content' });
		var size = this.el.getSize();
		this.eltabs.setHeight(size.height);
		this.elcontent.setSize(size.width - 201, size.height);

		this.items.each(function(item) {
			var el = this.eltabs.createChild({ tag: 'div', html: '<span>' + item.tabTitle + '</span>', elementid: item.id, 'class': 'viewers-farmrolesedit-tab' });
			el.setVisibilityMode(Ext.Element.DISPLAY);
			el.on('click', function (e) {
				var t = e.getTarget('div'), childs = this.eltabs.query('div');

				for (var i = 0, len = childs.length; i < len; i++)
					Ext.get(childs[i]).removeClass('viewers-farmrolesedit-tab-selected');

				this.layout.setActiveItem(t.getAttribute('elementid'));
				Ext.get(t).addClass('viewers-farmrolesedit-tab-selected');
			}, this);

			item.loadMask = this.loadMask;
			item.farmId = this.farmId;
		}, this);

		this.on('resize', function () {
			var size = this.el.getSize();
			this.eltabs.setHeight(size.height);
			this.elcontent.setSize(size.width - 201, size.height);
		}, this);

		this.on('activate', function () {
			var record = this.currentRole;
			this.items.each(function (item) {
				item.setCurrentRole(record);

				if (item.isEnabled(record)) {
					this.eltabs.child("[elementid=" + item.id + "]").show();
				} else {
					this.eltabs.child("[elementid=" + item.id + "]").hide();
				}
			}, this);

			var childs = this.eltabs.query('div');
			for (var i = 0, len = childs.length; i < len; i++)
				Ext.get(childs[i]).removeClass('viewers-farmrolesedit-tab-selected');

			var i = 0;
			this.items.each(function (item) {
				if (item.isEnabled(record)) {
					this.layout.setActiveItem(item);
					Ext.get(childs[i]).addClass('viewers-farmrolesedit-tab-selected');
					return false;
				}
				i++;
			}, this);
		}, this);

		this.on('deactivate', function () {
			if (this.layout.activeItem) {
				this.layout.activeItem.hide();
				this.layout.activeItem.fireEvent('deactivate', this.layout.activeItem);
				this.layout.activeItem = null;
			}
		}, this);
	}
});

Scalr.Viewers.FarmRolesEditTab = Ext.extend(Ext.Panel, {
	tabTitle: '',
	border: false,
	autoScroll: true,
	activatedtab: false,
	bodyStyle: {
		'overflow-x': 'hidden',
		'padding': '10px 20px 10px 10px'
	},
	currentRole: null,
	loadMask: null,
	farmId: null,

	plugins: [ new Scalr.Viewers.Plugins.findOne() ],

	setCurrentRole: function (record) {
		this.currentRole = record;
	},

	listeners: {
		activate: function () {
			if (! this.activatedtab) {
				this.activateTab();
				this.activatedtab = true;
				this.showTab.defer(100, this, [this.currentRole]);
			} else
				this.showTab(this.currentRole);
		},
		deactivate: function () {
			this.hideTab(this.currentRole);
		}
	},

	// show tab
	showTab: function (record) {},

	// hide tab
	hideTab: function (record) {},

	// after render tab
	activateTab: function() {},

	// tab can show or used for this role
	isEnabled: function (record) {
		return true;
	},

	// default values for new role
	getDefaultValues: function (record) {
		return {};
	}
});
