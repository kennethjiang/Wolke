{
	create: function (loadParams, moduleParams) {
		var store = new Scalr.data.Store({
			baseParams: loadParams,
			reader: new Scalr.data.JsonReader({
				id: 'id',
				fields: [ 'id','type','fingerprint','cloud_location','farm_id','cloud_key_name' ]
			}),
			remoteSort: true,
			url: '/sshkeys/xListViewSshKeys/'
		});
		
		return new Scalr.Viewers.ListView({
			title: 'Tools &raquo; SSH Keys manager',
			scalrOptions: {
				'reload': false,
				'maximize': 'all'
			},
			scalrReconfigure: function (loadParams) {
				Ext.applyIf(loadParams, { sshKeyId: ''});
				Ext.apply(this.store.baseParams, loadParams);
				this.store.load();
			},
			enableFilter: true,
			store: store,
			stateId: 'listview-sshkeys-view',

			// Row menu
			rowOptionsMenu: [
				{itemId: "option.priv_ssh_key", 		text: 'Download Private key', 		menuHandler: function (item){
     				Scalr.Viewers.userLoadFile('/sshkeys/' + item.currentRecordData.id + '/downloadPrivate');
     			}},
     			{itemId: "option.pub_ssh_key", 		text: 'Download SSH public key', 		menuHandler: function (item){
     				Scalr.Viewers.userLoadFile('/sshkeys/' + item.currentRecordData.id + '/downloadPublic');
     			}}
				/*
				new Ext.menu.Separator({itemId: "option.download_sep"}),
				{ itemId: "option.regenerate", text:'Regenerate', handler: function(item) {

					Ext.Msg.wait('Please wait while generating keys');
					Ext.Ajax.request({
						url: '/sshkeys/regenerate',
						params:{id:item.currentRecordData.id},
						success: function(response, options) {
							Ext.MessageBox.hide();

							var result = Ext.decode(response.responseText);
							if (result.success == true) {
								Scalr.Viewers.SuccessMessage('Key successfully regenerated');
							} else {
								Scalr.Viewers.ErrorMessage(result.error);
							}
						}
					});
				}}
				*/
			],

			getRowMenuVisibility: function (data) {
				return true;
			},

			listViewOptions: {
				emptyText: 'No SSH keys found',
				columns: [
					{ header: "Key ID", width: '100px', dataIndex: 'id', sortable: true, hidden: 'no' },
					{ header: "Name", width: 2, dataIndex: 'cloud_key_name', sortable: true, hidden: 'no' },
					{ header: "Type", width: '200px', dataIndex: 'type', sortable: true, hidden: 'no' },
					{ header: "Cloud location", width: '150px', dataIndex: 'cloud_location', sortable: true, hidden: 'no' },
					{ header: "Farm ID", width: '80px', dataIndex: 'farm_id', sortable: true, hidden: 'no' }
				]
			}
		});
	}
}