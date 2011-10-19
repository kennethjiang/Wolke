{
	create: function (loadParams, moduleParams) {
		var store = new Scalr.data.Store({
			baseParams: loadParams,
			reader: new Scalr.data.JsonReader({
				id: 'id',
				fields: [
					{ name: 'id', type: 'int' },
					'name', 'description', 'origin',
					{ name: 'clientid', type: 'int' },
					'approval_state', 'dtupdated', 'client_email', 'version', 'client_name'
				]
			}),
			remoteSort: true,
			url: '/scripts/xListViewScripts/'
		});

		return new Scalr.Viewers.ListView({
			title: 'Scripts &raquo; View',
			scalrOptions: {
				'reload': false,
				'maximize': 'all'
			},
			scalrReconfigure: function (loadParams) {
				Ext.applyIf(loadParams, { scriptId: '' });
				Ext.apply(this.store.baseParams, loadParams);
				this.store.load();
			},
			store: store,
			stateId: 'listview-scripts-view',

			tbar: [
				'&nbsp;&nbsp;Moderation phase:',
				new Ext.form.ComboBox({
					itemId: 'approvalState',
					allowBlank: true,
					editable: false, 
					store: [ ['','All'], ['Approved','Approved'], ['Declined','Declined'], ['Pending','Pending'] ],
					value: '',
					typeAhead: false,
					mode: 'local',
					triggerAction: 'all',
					selectOnFocus:false,
					width: 100,
					listeners: {
						select: function(combo, record, index) {
							store.baseParams.approvalState = combo.getValue(); 
							store.load();
						}
					}
				}),
				'-',
				'&nbsp;&nbsp;Origin:',
				new Ext.form.ComboBox({
					itemId: 'origin',
					allowBlank: true,
					editable: false, 
					store: [ ['','All'], ['Shared','Shared'], ['Custom','Custom'], ['User-contributed','User-contributed'] ],
					value: '',
					typeAhead: false,
					mode: 'local',
					triggerAction: 'all',
					selectOnFocus:false,
					width: 150,
					listeners:{
						select: function(combo, record, index) {
							store.baseParams.origin = combo.getValue(); 
							store.load();
						}
					}
				}),
				'-',
				{
					icon: '/images/add.png', // icons can also be specified inline
					cls: 'x-btn-icon',
					tooltip: 'Create new script template',
					handler: function() {
						document.location.href = '#/scripts/create';
					}
				}
			],

			getLocalState: function() {
				var it = {};
				it.filter_approval_state = this.getTopToolbar().getComponent('approval_state').getValue();
				it.filter_origin = this.getTopToolbar().getComponent('origin').getValue();
				return it;
			},

			// Row menu
			rowOptionsMenu: [
				{itemId: "option.execute", iconCls: 'scalr-menu-icon-execute', text:'Execute', href: "#/scripts/{id}/execute"},
				new Ext.menu.Separator({itemId: "option.execSep"}),
							
				{
					itemId: 'option.fork',
					text:'Fork',
					request: {
						processBox: {
							type: 'action'
						},
						dataHandler: function (record) {
							this.url = '/scripts/' + record.get('id') + '/xFork';
						},
						success: function () {
							store.reload();
							Scalr.Message.Success('Script successfully forked');
						}
					}
				},
				/*new Ext.menu.Separator({itemId: "option.forkSep"}),*/

				
				/*{itemId: "option.info", 		text: 'View', 	href: "/script_info.php?id={id}"},*/
				
				//new Ext.menu.Separator({itemId: "option.optSep"}),

				/*
				{itemId: "option.share", 		text: 'Share', 	href: "/script_templates.php?task=share&id={id}"},
				new Ext.menu.Separator({itemId: "option.shareSep"}),
				*/

				{itemId: "option.edit", iconCls: 'scalr-menu-icon-edit', text: 'Edit', href: "#/scripts/{id}/edit"},
				{
					itemId: 'option.delete',
					text: 'Delete',
					iconCls: 'scalr-menu-icon-delete',
					request: {
						confirmBox: {
							msg: 'Remove script "{name}"?',
							type: 'delete'
						},
						processBox: {
							type: 'delete',
							msg: 'Removing script. Please wait...'
						},
						dataHandler: function (record) {
							this.url = '/scripts/' + record.get('id') + '/xRemove';
						},
						success: function () {
							store.reload();
							Scalr.Message.Success('Script successfully removed');
						}
					}
				}
			],
			getRowOptionVisibility: function (item, record) {
				var data = record.data;

				if (item.itemId == 'option.fork' || item.itemId == 'option.forkSep')
				{
					if (!moduleParams['isScalrAdmin'] && (data.clientid == 0 || (data.clientid != 0 && data.clientid != moduleParams['clientId'])))
						return true;
					else
						return false;
				}
				else if (item.itemId != 'option.info')
				{
					if (item.itemId == 'option.execute' || item.itemId == 'option.execSep')
					{
						if (moduleParams['isScalrAdmin'])
							return false;
						else
							return true;
					}

					if ((data.clientid != 0 && data.clientid == moduleParams['clientId']) || moduleParams['isScalrAdmin'])
					{
						if (item.itemId == 'option.share' || item.itemId == 'option.shareSep')
						{
							if (data.origin == 'Custom' && !moduleParams['isScalrAdmin'])
								return true;
							else
								return false;
						}
						else 
							return true;
					}
					else
						return false;
				}
				else
					return true;
			},
			
			listViewOptions: {
				emptyText: "No scripts defined",
				columns: [
					{ header: "Author", width: 100, dataIndex: 'id', sortable: false, hidden: 'no', tpl: new Ext.XTemplate(
						'<tpl if="!this.isAdmin()">' +
							'<tpl if="clientid">' +
								'<tpl if="clientid == this.getClientId()">Me</tpl>' +
								'<tpl if="clientid != this.getClientId()">{client_name}</tpl>' +
							'</tpl>' +
							'<tpl if="!clientid">Scalr</tpl>' +
						'</tpl>' +
						'<tpl if="this.isAdmin()">' +
							'<tpl if="clientid"><a href="clients_view.php?clientid={clientid}">{client_name}</a></tpl>' +
							'<tpl if="!clientid">Scalr</tpl>' +
						'</tpl>', {getClientId:function(){ return moduleParams['clientId'] }, isAdmin:function(){ return moduleParams['isScalrAdmin'] }})
					},
					{ header: "Name", width: 100, dataIndex: 'name', sortable: true, hidden: 'no' },
					{ header: "Description", width: 120, dataIndex: 'description', sortable: true, hidden: 'no' },
					{ header: "Latest version", width: '120px', dataIndex: 'version', sortable: false, align:'center', hidden: 'no' },
					{ header: "Updated on", width: '120px', dataIndex: 'dtupdated', sortable: false, hidden: 'no' },
					{ header: "Origin", width: '80px', dataIndex: 'origin', sortable: false, align:'center', hidden: 'no', tpl:
						'<tpl if="origin == &quot;Shared&quot;"><img src="/images/ui-ng/icons/script/default.png" title="Contributed by Scalr"></tpl>' +
						'<tpl if="origin == &quot;Custom&quot;"><img src="/images/ui-ng/icons/script/custom.png" title="Custom"></tpl>' +
						'<tpl if="origin != &quot;Shared&quot; && origin != &quot;Custom&quot;"><img src="/images/ui-ng/icons/script/contributed.png" title="Contributed by {client_name}"></tpl>'
					},
					{ header: "Approved", width: '100px', dataIndex: 'approval_state', sortable: false, align:'center', hidden: 'no', tpl:
						'<tpl if="approval_state == &quot;Approved&quot; || !approval_state"><img src="/images/true.gif" title="Approved" />' +
						'<tpl if="approval_state == &quot;Pending&quot;"><img src="/images/pending.gif" title="Pending" /></tpl>' +
						'<tpl if="approval_state == &quot;Declined&quot;"><img src="/images/false.gif" title="Declined" /></tpl>'
					}
			]}
		});
	}
}
