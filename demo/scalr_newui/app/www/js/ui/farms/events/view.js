{
	create: function (loadParams, moduleParams) {
		var store = new Scalr.data.Store({
			baseParams: loadParams,
			reader: new Scalr.data.JsonReader({
				id: 'id',
				fields: [
			         	'id','dtadded', 'type', 'message'
				]
			}),
			remoteSort: true,
			url: '/farms/' + loadParams['farmId'] + '/events/xListViewEvents'
		});

		return new Scalr.Viewers.ListView({
			title: '云平台 &raquo; ' + moduleParams['farmName'] + ' &raquo; 事件',
			scalrOptions: {
				'reload': false,
				'maximize': 'all'
			},
			
			bbar: [ '->', new Scalr.Toolbar.TimeItem({ time: moduleParams['time'], timeOffset: moduleParams['timeOffset'] })],
			
			tbar: [{
				text: '配置事件通知',
				//iconCls: 'x-btn-download-icon',
				handler: function () {
					document.location.href='/configure_event_notifications.php?farmid='+loadParams['farmId'];
				}
			}],
			
			scalrReconfigure: function (loadParams) {
				Ext.applyIf(loadParams, { farmId: '' });
				Ext.apply(this.store.baseParams, loadParams);
				this.store.load();
			},
			store: store,
			stateId: 'listview-farm-events-view',

			listViewOptions: {
				emptyText: "未发现事件",
				columns: [
					{header: "日期", width: 80, dataIndex: 'dtadded', sortable: false},
					{header: "事件", width: 50, dataIndex: 'type', sortable: false},
					{header: "描述", width: 300, dataIndex: 'message', sortable: false}
			]}
		});
	}
}
