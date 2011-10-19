{
	create: function (loadParams, moduleParams) {
		return new Ext.Panel({
			scalrOptions: {
				'modal': true,
				'maximize': 'all'
			},
			tools: [{
				id: 'close',
				handler: function () {
					Scalr.Viewers.EventMessager.fireEvent('close');
				}
			}],
			title: '服务器 "' + moduleParams.name + '" 控制台输出',
			html: moduleParams.content,
			autoScroll: true,
			frame: true
		});
	}
}
