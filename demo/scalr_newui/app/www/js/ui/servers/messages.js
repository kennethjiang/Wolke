{
	create: function (loadParams, moduleParams) {
		var store = new Scalr.data.Store({
			baseParams: loadParams,
			reader: new Scalr.data.JsonReader({
				id: 'messageid',
				fields: [
				         "messageid", "server_id", "status", "handle_attempts", "dtlasthandleattempt","message_type","type","isszr"
				]
			}),
			remoteSort: true,
			url: '/servers/xListViewMessages/'
		});

		return new Scalr.Viewers.ListView({
			title: '服务器 &raquo; ' + loadParams['serverId'] + ' &raquo; 消息',
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
				Ext.applyIf(loadParams, { serverId: ''});
				Ext.apply(this.store.baseParams, loadParams);
				this.store.load();
			},
			store: store,
			stateId: 'listview-server-messages-view',
			
			listViewOptions: {
				viewConfig: {
					emptyText: "No messages found"
				},
				// Columns
				columns:[
					{header: "消息ID", width: 50, dataIndex: 'messageid', sortable: true},
					{header: "消息类型", width: 40, dataIndex: 'message_type', tpl:'{type} / {message_type}', sortable: false},
					{header: "服务器ID", width: 30, dataIndex: 'server_id', tpl:'<a href="#/servers/{server_id}/extendedInfo">{server_id}</a>', sortable: true},
					{header: "状态", width: 30, dataIndex: 'isdelivered', tpl:''+
					'<tpl if="status == 1"><span style="color:green;">Delivered</span></tpl>'+
					'<tpl if="status == 0"><span style="color:orange;">Delivering...</span></tpl>'+
					'<tpl if="status == 2 || status == 3"><span style="color:red;">Failed</span></tpl>'
					, sortable: true},
					{header: "发送次数", width: '100px', dataIndex: 'handle_attempts', sortable: true}, 
					{header: "上次发送时间", width: '200px', dataIndex: 'dtlasthandleattempt', sortable: true}
				]
			},
			rowOptionsMenu: [{
				text: 'Re-send message',
				request: {
					processBox: {
						type: 'action',
						msg: '正在重发消息。请稍候...'
					},
					dataHandler: function (record) {
						this.url = '/servers/' + record.get('server_id') + '/xResendMessage/';
						return { messageId: record.get('messageid') };
					},
					success: function () {
						Scalr.Message.Success("消息已成功重发至服务器");
						store.reload();
					}
				}
			}],
			getRowMenuVisibility: function (data) {
				return (data.status == 2 || data.status == 3);
			}
		});
	}
}
