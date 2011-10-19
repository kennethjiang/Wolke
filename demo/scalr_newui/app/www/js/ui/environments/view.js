{
	create: function (loadParams, moduleParams) {
		return new Scalr.Viewers.ListView({
			scalrOptions: {
				'reload': false,
				'maximize': 'all'
			},
			title: '环境 &raquo; 列表',
			store: new Scalr.data.Store({
				reader: new Scalr.data.JsonReader({
					id: 'id',
					fields: [
						'id', 'name', 'dtAdded', 'isSystem','platforms'
					]
				}),
				url: '/environments/xListViewEnv/'
			}),
			enableFilter: false,
			enablePaging: false,
			stateId: 'listview-environments-view',
			listViewOptions: {
				emptyText: '未找到任何环境',
				columns: [
					{ header: _("名称"), width: '300px', dataIndex: 'name', sortable: true, hidden: 'no' },
					{ header: _("已开启云平台"), width: 30, dataIndex: 'platforms', sortable: true, hidden: 'no' },
					{ header: _("生成日期"), width: '180px', dataIndex: 'dtAdded', sortable: true, hidden: 'no' },
					{ header: _("系统环境"), width: "70px", dataIndex: 'isSystem', sortable: false, hidden: 'no', align: 'center', tpl:
						'<tpl if="isSystem == 1"><img src="/images/true.gif"></tpl>' +
						'<tpl if="isSystem != 1">-</tpl>'
					}
				]
			},
			rowOptionsMenu: [{
				text:'编辑', href: "#/environments/{id}/edit/"
			}]
		});
	}
}
