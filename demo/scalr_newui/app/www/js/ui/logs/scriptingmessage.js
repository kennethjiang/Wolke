{
	create: function (loadParams, moduleParams) {
		return new Ext.Panel({
			title: 'Logs &raquo; Scripting &raquo; Message',
			scalrOptions: {
				'modal': true,
				'maximize': 'maxHeight'
			},
			tools: [{
				id: 'close',
				handler: function () {
					Scalr.Viewers.EventMessager.fireEvent('close');
				}
			}],
			items: moduleParams,
			autoScroll: true,
			frame: true,
			autoHeight: true,
			width: 800
		});
	}
}
