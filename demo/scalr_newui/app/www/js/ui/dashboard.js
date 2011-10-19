{
	create: function (loadParams, moduleParams) {
		var dashboard = new Ext.Panel({
			scalrOptions: {
				reload: false,
				maximize: 'all'
			},
			title: '控制台',
			header: false,
			border: false,

			layout: 'column',
			autoScroll: true,
			items: [{
				columnWidth:.99,
				style:'padding: 5px',
				items: [{
					xtype: 'panel',
					title: '快捷方式',
					layout: 'column',
					border: false,
					collapsible: true,
					defaultType: 'button',
					plugins: [ new Scalr.Viewers.Plugins.localStorage() ],
					stateId: 'dashboard-widget-quick-access',
					items: [],
					listeners: {
						'render': function () {
							var links = [{
								text: '查看云平台',
								href: '#/farms/view',
								icon: 'farms_32x32.png'
							}];

							for (var i = 0; i < links.length; i++)
								this.add({
									text: links[i].text,
									href: links[i].href,
									style: 'padding: 10px',
									width:'90px',
									height:'90px',
									cls: 'x-btn-text-icon',
									scale: 'large',
									icon: '/images/ui-ng/icons/quick_access/' + links[i].icon,
									iconAlign: 'top',
									handler: function () {
										document.location.href = this.href;
									}
								});

							if (this.localGet('state') == 'collapsed')
								this.collapsed = true;

						},
						'expand': function () {
							this.localSet('state', 'expanded');
						},
						'collapse': function () {
							this.localSet('state', 'collapsed');
						}
					}
				}]
			}]
		});

		return dashboard;
	}
}
