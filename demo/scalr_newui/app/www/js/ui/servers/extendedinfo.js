{
	create: function (loadParams, moduleParams) {
		return new Ext.Panel({
			title: '服务器 "' + loadParams['serverId'] + '" 信息',
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
			padding: '0px 20px 0px 5px',
			width: 600
		});
	}
}
