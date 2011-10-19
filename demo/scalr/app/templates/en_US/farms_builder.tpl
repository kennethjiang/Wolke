{include file="inc/header.tpl" noheader=1}
<script type="text/javascript" src="/js/ui-ng/data.js"></script>
<script type="text/javascript" src="/js/ui-ng/FarmRole.js"></script>
<script type="text/javascript" src="/js/ui-ng/viewers/ListView.js"></script>
<script type="text/javascript" src="/js/ui-ng/viewers/SelRolesViewer.js"></script>
<script type="text/javascript" src="/js/ui-ng/viewers/AllRolesViewer.js"></script>
<script type="text/javascript" src="/js/ui-ng/viewers/FarmRolesEdit.js"></script>
<script type="text/javascript" src="/js/highlight/highlight.pack.js"></script>

<div id="tabpanel-farms-add"></div>
<script type="text/javascript">
	var FARM_ID = '{$id}';
	var ROLE_ID = '{$role_id}';
	var currentTimeZone = 'Current time zone is: <span style="font-weight: bold;">{$current_time_zone}</span> ({$current_time}). <a href="#/environments/{$current_env_id}/edit">Click here</a> if you want to change it.</div>'

	{literal}
	Ext.onReady(function() {
		hljs.initHighlightingOnLoad();

		var farm = new Scalr.FarmRole.Farm(FARM_ID, ROLE_ID);

		{/literal}
		farm.platforms = {$platforms};
		farm.locations = {$locations};
		farm.groups = {$groups};
		{literal}
		
		farm.createPanel = function() {
			var panel = new Ext.TabPanel({
				renderTo: 'tabpanel-farms-add',
				autoRender: true,
				activeTab: 0,
				bodyStyle: 'border-style: none solid none solid;',
				items: [{
					title: 'Farm',
					itemId: 'farm',
					xtype: 'form',
					layout: 'form',
					style: {
						'padding': '10px'
					},
					items: [{
						xtype: 'fieldset',
						title: 'General info',
						items: [{
							xtype: 'textfield',
							name: 'farm_name',
							fieldLabel: 'Name',
							width: 500
						}, {
							xtype: 'textarea',
							name: 'farm_description',
							fieldLabel: 'Description',
							width: 500,
							height: 100
						}]
					}, {
						xtype: 'fieldset',
						title: 'Settings',
						itemId: 'settings',
						items: [{
							xtype: 'radiogroup',
							hideLabel: true,
							itemId: 'farm_roles_launch_order',
							columns: 1,
							items: [{
								boxLabel: 'Launch roles simultaneously ',
								name: 'farm_roles_launch_order',
								checked: true,
								inputValue: '0'
							}, {
								boxLabel: 'Launch roles one-by-one in the order I set (slower) ',
								name: 'farm_roles_launch_order',
								inputValue: '1'
							}]
						}]
					}]
				}, {
					title: 'Roles',
					itemId: 'roles',
					layout: 'vbox',
					layoutConfig: {
						align: 'stretch',
						pack: 'start'
					},
					items: [
						new Scalr.Viewers.SelRolesViewer({
							height: 130,
							title: 'Roles',
							itemId: 'roles',
							border: false,
							store: this.farmRolesStore,
							listeners: {
								'deleterole': function(record) {
									this.farmRolesStore.remove(record);
								},
								'selectionchange': function(selections) {
									if (selections[0]) {
										this.panel.getComponent('roles').getComponent('card').layout.setActiveItem('blank');
										this.panel.getComponent('roles').getComponent('card').getComponent('edit').setCurrentRole(selections[0]);
										this.panel.getComponent('roles').getComponent('card').layout.setActiveItem('edit');
									} else {
										this.panel.getComponent('roles').getComponent('card').layout.setActiveItem('blank');
									}
								},
								scope: this
							}
						}), {
							xtype: 'panel',
							height: 19,
							html: '&nbsp',
							bodyStyle: 'background-color: #DFE8F6; border-style: solid none solid none;'
						}, {
							xtype: 'panel',
							layout: 'card',
							itemId: 'card',
							border: false,
							flex: 1,
							activeItem: 'blank',
							items: [{
								xtype: 'panel',
								itemId: 'blank',
								border: false
							}, new Scalr.Viewers.FarmRolesEditPanel({
								itemId: 'edit',
								border: false,
								loadMask: this.loadMask,
								farmId: this.farmId,
								items: [
									{/literal}
									{include file="tab_fb_scaling.tpl"},
									{include file="tab_fb_mysql.tpl"},
									{include file="tab_fb_balancing.tpl"},
									{include file="tab_fb_placement.tpl"},
									{include file="tab_fb_rs_placement.tpl"},
									{include file="tab_fb_params.tpl"},
									{include file="tab_fb_rds.tpl"},
									{include file="tab_fb_eips.tpl"},
									{include file="tab_fb_ebs.tpl"},
									{include file="tab_fb_dns.tpl"},
									{include file="tab_fb_scripting.tpl"},
									{include file="tab_fb_timeouts.tpl"},
									{include file="tab_fb_cloudwatch.tpl"},
									{include file="tab_fb_vpc.tpl"},
									{include file="tab_fb_euca.tpl"},
									{include file="tab_fb_nimbula.tpl"},
									{include file="tab_fb_servicesconfig.tpl"}
									{literal}
								]
							})]
						}
					]
				}],
				footerCssClass: 'viewers-panel-footer',
				buttonAlign: 'center',
				buttons: [{
					text: 'Save'
				}, {
					text: 'Cancel'
				}]
			});

			var autoSize = new Scalr.Viewers.autoSize();
			autoSize.init(panel);

			return panel;
		}

		farm.createAddRolePanel = function () {
			var panel = new Scalr.Viewers.AllRolesViewer({
				itemId: 'roles',
				platforms: this.platforms,
				groups: this.groups,
				locations: this.locations,
				store: this.rolesStore,
				legacyRoles: this.farmId == 0 ? false : true,
				listeners: {
					addrole: function(rec) {
						if (
							this.farmRolesStore.findBy(function(record) {
								if (
									record.get('platform') == rec.platform &&
									record.get('role_id') == rec.role_id &&
									record.get('cloud_location') == rec.cloud_location
								)
									return true;
							}) != -1
						) {
							Scalr.Viewers.ErrorMessage('Role "' + rec['name'] + '" already added');
							return;
						}

						// check before adding
						if (rec.behaviors.match('mysql')) {
							if (
								this.farmRolesStore.findBy(function(record) {
									if (record.get('behaviors').match('mysql'))
										return true;
								}) != -1
							) {
								Scalr.Viewers.ErrorMessage('Only one MySQL role can be added to farm');
								return;
							}
						}

						rec['new'] = true;
						rec['settings'] = {};
						var record = new this.farmRolesStore.recordType(rec);
						this.panel.getComponent('roles').getComponent('card').getComponent('edit').addRoleDefaultValues(record);
						this.farmRolesStore.add(record);
						Scalr.Viewers.SuccessMessage('Role "' + rec['name'] + '" added', '', 4);
					},
					scope: this
				}
			});

			return panel;
		};

		farm.loadFarm();
	});
	{/literal}
</script>
{include file="inc/footer.tpl"}
