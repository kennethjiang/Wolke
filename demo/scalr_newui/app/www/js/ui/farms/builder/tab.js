Ext.ns("Scalr.ui.farms.builder");

Scalr.ui.farms.builder.tab = Ext.extend(Ext.Panel, {
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
