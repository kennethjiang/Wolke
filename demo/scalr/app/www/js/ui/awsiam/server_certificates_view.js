{
	create: function (loadParams, moduleParams) {
		return new Scalr.Viewers.ListView({
			title: 'Server certificates',
			scalrOptions: {
				'reload': false,
				'maximize': 'all'
			},
			store: new Scalr.data.Store({
				reader: new Scalr.data.JsonReader({
					id: 'id',
					fields: [
						'name','path','arn','id','upload_date'
					]
				}),
				remoteSort: true,
				url: '/awsiam/xListViewServerCertificates/'
			}),
			enableFilter: false,
			enablePaging: false,
			stateId: 'listview-servercerts-view',

			listViewOptions: {
				emptyText: 'No server certificates found',
				columns: [
					{ header: "ID", width: '250px', dataIndex: 'id', sortable: false, hidden: 'no' },
					{ header: "Name", width: '200px', dataIndex: 'name', sortable: false, hidden: 'no' },
					{ header: "Path", width: '200px', dataIndex: 'path', sortable: false, hidden: 'no' },
					{ header: "Arn", width: 2, dataIndex: 'arn', sortable: false, hidden: 'no' },
					{ header: "Upload date", width: '200px', dataIndex: 'upload_date', sortable: false, hidden: 'no' }
				]
			}
			/*rowOptionsMenu: [
				{ itemId: "option.delete", text:'Delete', href: "#/awsIam/{id}/deleteServerCertificate" }
			]*/
		});
	}
}
