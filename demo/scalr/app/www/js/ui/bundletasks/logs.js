{
	create: function (loadParams, moduleParams) {
		var store = new Scalr.data.Store({
			baseParams: loadParams,
			reader: new Scalr.data.JsonReader({
				id: 'id',
				fields: [
					{name: 'id', type: 'int'},
		            'dtadded','message'
				]
			}),
			remoteSort: true,
			url: '/bundletasks/xListViewLogs/'
		});

		return new Scalr.Viewers.ListView({
			title: 'Bundle task &raquo; Log',
			scalrOptions: {
				'maximize': 'all',
				'reload':false
			},
			tools: [{
				id: 'close',
				handler: function () {
					Scalr.Viewers.EventMessager.fireEvent('close');
				}
			}],
			scalrReconfigure: function (loadParams) {
				Ext.applyIf(loadParams, { bundleTaskId: ''});
				Ext.apply(this.store.baseParams, loadParams);
				this.store.load();
			},
			store: store,
			stateId: 'listview-bundletask-logs-view',

			listViewOptions: {
				emptyText: 'Log is empty for selected bundle task',
				columns: [
					{ header: "Date", width: '165px', dataIndex: 'dtadded', sortable: true, hidden: 'no' },
					{ header: "Message", width: 80, dataIndex: 'message', sortable: true, hidden: 'no'}
			]}
		});
	}
}
