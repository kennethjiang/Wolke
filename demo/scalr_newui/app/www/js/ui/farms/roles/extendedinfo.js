{
	create: function (loadParams, moduleParams) {
		return new Ext.Panel({
			title: '云平台 &raquo; ' + moduleParams['farmName'] + ' &raquo; ' + moduleParams['roleName'] + ' &raquo; 信息',
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
			items: moduleParams['form'],
			autoScroll: true,
			frame: true,
			autoHeight: true,
			padding: '0px 20px 0px 5px',
			width: 800
		});
	}
}
