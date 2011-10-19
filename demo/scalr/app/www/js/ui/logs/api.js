{
	create: function (loadParams, moduleParams) {
		var store = new Scalr.data.Store({
			reader: new Scalr.data.JsonReader({
				id: 'id',
				fields: [ 'id','transaction_id','dtadded','action','ipaddress','request' ]
			}),
			remoteSort: true,
			url: '/logs/xGetApiLogs/'
		});

		var list = new Scalr.Viewers.ListView({
			title: 'Logs &raquo; API',
			scalrOptions: {
				'reload': false,
				'maximize': 'all'
			},
			store: store,
			stateId: 'listview-logs-api-view',

			bbar: [ '->', new Scalr.Toolbar.TimeItem({ time: moduleParams['time'], timeOffset: moduleParams['timeOffset'] })],

			rowOptionsMenu: [
				{ itemId: "option.details", 		text:'Details', 			  	href: "#/logs/apiLogEntryDetails?transactionId={transaction_id}" }
			],
			listViewOptions: {
				emptyText: 'No logs found',
				columns: [
					{ header: "Transaction ID", width: 35, dataIndex: 'transaction_id', sortable: false, hidden: 'no' },
					{ header: "Time", width: 35, dataIndex: 'dtadded', sortable: false, hidden: 'no' },
					{ header: "Action", width: 15, dataIndex: 'action', sortable: false, hidden: 'no' },
					{ header: "IP address", width: 25, dataIndex: 'ipaddress', sortable: false, hidden: 'no' }
				]
			}

		});

		return list;
	}
}
