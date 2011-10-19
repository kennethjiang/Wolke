{
	create: function (loadParams, moduleParams) {
		var store = new Scalr.data.Store({
			baseParams: loadParams,
			reader: new Scalr.data.JsonReader({
				id: 'id',
				fields: [
					{name: 'id', type: 'int'},{name: 'clientid', type: 'int'},
		            'server_id','prototype_role_id','replace_type','status','platform','rolename','failure_reason','bundle_type','dtadded',
		            'dtstarted','dtfinished','snapshot_id','platform_status','server_exists'
				]
			}),
			remoteSort: true,
			url: '/bundletasks/xListViewTasks/'
		});

		return new Scalr.Viewers.ListView({
			title: 'Bundle tasks &raquo; View',
			scalrOptions: {
				'reload': false,
				'maximize': 'all'
			},
			scalrReconfigure: function (loadParams) {
				Ext.applyIf(loadParams, { bundleTaskId: ''});
				Ext.apply(this.store.baseParams, loadParams);
				this.store.load();
			},
			store: store,
			stateId: 'listview-bundletasks-view',

			listViewOptions: {
				emptyText: 'No bundle tasks found',
				columns: [
					{ header: "ID", width: 20, dataIndex: 'id', sortable: true, hidden: 'no' },
					{ header: "Server ID", width: '335px', dataIndex: 'server_id', sortable: true, hidden: 'no', tpl: new Ext.XTemplate(
						'<tpl if="server_exists"><a href="#/servers/{server_id}/extendedInfo">{server_id}</a></tpl>' +
						'<tpl if="!server_exists">{server_id}</tpl>'
					)},
					{ header: "Role name", width: 100, dataIndex: 'rolename', sortable: true, hidden: 'no' },
					{ header: "Status", width: '180px', dataIndex: 'status', sortable: true, hidden: 'no', tpl:
						'<tpl if="status == &quot;failed&quot;">{status} (<a href="#/bundletasks/{id}/failureDetails">Why?</a>)</tpl>' +
						'<tpl if="status != &quot;failed&quot;">{status}</tpl>'
					},
					{ header: "Type", width: '135px', dataIndex: 'platform', sortable: false, hidden: 'no', tpl: '{platform}/{bundle_type}' },
					{ header: "Added", width: '165px', dataIndex: 'dtadded', sortable: true, hidden: 'yes' },
					{ header: "Started", width: '165px', dataIndex: 'dtstarted', sortable: true, hidden: 'no', tpl:
						'<tpl if="dtstarted">{dtstarted}</tpl>'
					},
					{ header: "Finished", width: '165px', dataIndex: 'dtfinished', sortable: true, hidden: 'no', tpl:
						'<tpl if="dtfinished">{dtfinished}</tpl>'
					}
			]},

			rowOptionsMenu: [{
				text:'View log',
				href: '#/bundletasks/{id}/logs'
			}, {
				itemId: 'option.cancel',
				text: 'Cancel',
				request: {
					processBox: {
						type: 'action',
						title: 'Canceling',
						msg: 'Please wait...'
					},
					url: '/bundletasks/xCancel/',
					dataHandler: function (record) {
						return { bundleTaskId: record.get('id') };
					},
					success: function(data) {
						store.reload();
						Scalr.Message.Success(data.message);
					}
				}
			}],
			getRowOptionVisibility: function (item, record) {
				if (item.itemId == 'option.cancel') {
					if (record.get('status') != 'success' && record.get('status') != 'failed')
						return true;
					else
						return false;
				}

				return true;
			}
		});
	}
}
