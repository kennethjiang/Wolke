{
	create: function (loadParams, moduleParams) {
		var avail = [], lst = moduleParams.info.platformsList;
		for (var i = 0, len = lst.length; i < len; i++)
			avail += '&bull; ' + lst[i].name + ' in ' + lst[i].locations + '<br>';

		return new Ext.form.FormPanel({
			title: '服务角色 "' + moduleParams['name'] + '" 信息',
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
			autoScroll: true,
			width: 700,
			bodyStyle: 'background-color: white; padding: 5px',
			items: [{
				layout: 'column',
				border: false,
				items: [{
					columnWidth: .4,
					layout: 'form',
					border: false,
					labelWidth: 100,
					items: [{
						xtype: 'displayfield',
						fieldLabel: '名称',
						value: moduleParams.info.name
					}, {
						xtype:'displayfield',
						fieldLabel: '类型',
						value: moduleParams.info.groupName
					}, {
						xtype:'displayfield',
						fieldLabel: '用途',
						value: moduleParams.info.behaviorsList
					}, {
						xtype:'displayfield',
						fieldLabel: '操作系统',
						value: moduleParams.info.os
					}, {
						xtype:'displayfield',
						fieldLabel: '系统架构',
						value: moduleParams.info.architecture
					}, {
						xtype:'displayfield',
						fieldLabel: '系统代理',
						value: (moduleParams.info.generation == 1 ? 'ami-scripts' : '系统代理') + 
						" ("+(moduleParams.info.szrVersion ? moduleParams.info.szrVersion : 'Unknown')+")"
					}, {
						xtype:'displayfield',
						fieldLabel: '标签',
						hidden: moduleParams.info.tagsString == '' ? true : false,
						value: moduleParams.info.tagsString
					}]
				}, {
					columnWidth: .6,
					layout: 'form',
					labelWidth: 110,
					border: false,
					items: [{
						xtype:'displayfield',
						fieldLabel: '描述',
						value: moduleParams.info.description ? moduleParams.info.description : '<i>Description not available for this role</i>'
					}, {
						xtype:'displayfield',
						fieldLabel: '已安装软件',
						value: moduleParams.info.softwareList ? moduleParams.info.softwareList : '<i>本服务角色未提供软件列表</i>'
					}]
				}]
			}, {
				xtype: 'displayfield',
				fieldLabel: '可用云平台',
				value: avail
			}]
		});
	}
}
