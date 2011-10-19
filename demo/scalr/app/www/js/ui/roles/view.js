{
	create: function (loadParams, moduleParams) {
		var store = new Scalr.data.Store({
			baseParams: loadParams,
			reader: new Scalr.data.JsonReader({
				id: 'id',
				fields: [
					{name: 'id', type: 'int'},
					{name: 'client_id', type: 'int'},
					'name', 'tags', 'origin', 'architecture', 'client_name', 'behaviors', 'os', 'platforms','generation','used_servers','status'
				]
			}),
			remoteSort: true,
			url: '/roles/xListViewRoles/'
		});

		return new Scalr.Viewers.ListView({
			title: '服务角色 &raquo; 查看',
			scalrOptions: {
				'reload': false,
				'maximize': 'all'
			},
			scalrReconfigure: function (loadParams) {
				Ext.applyIf(loadParams, { roleId: '', client_id: '' });
				Ext.apply(this.store.baseParams, loadParams);
				this.store.load();
			},
			store: store,
			stateId: 'listview-roles-view',

			tbar: [
				' ',
				'位置:',
				new Ext.form.ComboBox({
					itemId: 'cloudLocation',
					editable: false,
					store: Scalr.data.createStore(moduleParams.locations, { idProperty: 'id', fields: [ 'id', 'name' ]}),
					typeAhead: false,
					displayField: 'name',
					valueField: 'id',
					value: '',
					mode: 'local',
					triggerAction: 'all',
					selectOnFocus: false,
					width: 150,
					listeners: {
						select: function(combo, record, index) {
							store.baseParams.cloudLocation = combo.getValue();
							store.load();
						}
					}
				}),
				'-', ' ',
				'来源:',
				new Ext.form.ComboBox({
					itemId: 'origin',
					allowBlank: true,
					editable: false,
					store: [ [ '', 'All' ], [ 'Shared', '系统' ], [ 'Custom', 'Private' ] ],
					value: '',
					typeAhead: false,
					mode: 'local',
					triggerAction: 'all',
					selectOnFocus: false,
					emptyText: ' ',
					width: 150,
					listeners: {
						select: function(combo, record, index) {
							store.baseParams.origin = combo.getValue();
							store.load();
						}
					}
				})
			],

			listViewOptions: {
				emptyText: "未发现任何服务角色",
				columns: [
					{ header: "服务角色名", width: 50, dataIndex: 'name', sortable: true, hidden: 'no'},
					{ header: "操作系统", width: 25, dataIndex: 'os', sortable: true, hidden: 'no'},
					{ header: "所有人", width: 30, dataIndex: 'client_id', sortable: false, hidden: 'no', tpl: new Ext.XTemplate(
						'<tpl if="this.isScalrAdmin && client_id != &quot;&quot;"><a href="clients_view.php?client_id={client_id}"></tpl>{client_name}<tpl if="this.isScalrAdmin && client_id != &quot;&quot;"></a></tpl>',
						{ isScalrAdmin: moduleParams.isScalrAdmin }
					)},
					{ header: "用途", width: '150px', dataIndex: 'behaviors', sortable: false, hidden: 'no'},
					{ header: "可用云平台", width: '240px', dataIndex: 'platforms', sortable: false, hidden: 'no'},
					{ header: "标签", width: '140px', dataIndex: 'tags', sortable: false, hidden: 'no'},
					{ header: "架构", width: '65px', dataIndex: 'architecture', sortable: true, hidden: 'no'},
					{ header: "状态", width: '100px', dataIndex: 'status', sortable: false, hidden: 'no'},
					{ header: "系统代理", width: '100px', dataIndex: 'generation', sortable: false, hidden: 'no'},
					{ header: "服务器", width: '60px', dataIndex: 'used_servers', sortable: false, hidden: 'yes'}
				]
			},
			// Row menu
			rowOptionsMenu: [
				{ itemId: "option.view", iconCls: 'scalr-menu-icon-info', text:'详情', href: "#/roles/{id}/info" },
				{ itemId: "option.edit", iconCls: 'scalr-menu-icon-edit', text:'编辑', href: "#/roles/{id}/edit" }
			],

			getRowOptionVisibility: function (item, record) {
				if (item.itemId == 'option.view')
					return true;

				if (record.data.origin == 'CUSTOM') {
					if (item.itemId == 'option.edit') {
						if (! moduleParams.isScalrAdmin)
							return true;
						else
							return false;
					}

					return true;
				}
				else {
					return moduleParams.isScalrAdmin;
				}
			},
			
			getRowMenuVisibility: function (data) {
				return (data.status.indexOf('Deleting') == -1);
			},

			withSelected: {
				menu: [{
					text: '删除',
					iconCls: 'scalr-menu-icon-delete',
					request: {
						confirmBox: {
							msg: '删除所选的服务角色?',
							type: 'delete'
						},
						processBox: {
							msg: '正在删除服务角色... 请稍候.',
							type: 'delete'
						},
						url: '/roles/xRemove',
						dataHandler: function (records) {
							var roles = [];
							for (var i = 0, len = records.length; i < len; i++) {
								roles[roles.length] = records[i].get('id');
							}

							return { roles: Ext.encode(roles) };
						},
						success: function (data) {
							Scalr.Message.Success('已成功删除所选服务角色');
						}
					}
				}]
			}
		});
	}
}
