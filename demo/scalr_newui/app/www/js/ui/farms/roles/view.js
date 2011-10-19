{
	create: function (loadParams, moduleParams) {
		var store = new Scalr.data.Store({
			baseParams: loadParams,
			reader: new Scalr.data.JsonReader({
				id: 'id',
				fields: [
					{name: 'id', type: 'int'}, 'platform', 'location',
					'name', 'min_count', 'max_count', 'min_LA', 'max_LA', 'servers', 'domains', 
					'image_id', 'farmid','shortcuts', 'role_id', 'scaling_algos', 'farm_status', 'location'
				]
			}),
			remoteSort: true,
			url: '/farms/roles/xListViewFarmRoles/'
		});

		return new Scalr.Viewers.ListView({
			title: '云平台 &raquo; ' + moduleParams['farmName'] + ' &raquo; 服务角色',
			scalrOptions: {
				'reload': false,
				'maximize': 'all'
			},
			scalrReconfigure: function (loadParams) {
				Ext.applyIf(loadParams, { roleId:'', farmRoleId: '', farmId: '', clientId: '', status: ''});
				Ext.apply(this.store.baseParams, loadParams);
				this.store.load();
			},
			store: store,
			stateId: 'listview-farmroles-view',

			rowOptionsMenu: [
     			{itemId: "option.ssh_key", 		text: '下载SSH private key', menuHandler: function (item){
     				Scalr.Viewers.userLoadFile('/farms/' + loadParams['farmId'] + '/roles/' + item.currentRecordData.id + '/xGetRoleSshPrivateKey');
     			}},
     			{itemId: "option.cfg", iconCls: 'scalr-menu-icon-configure', text:'配置', href: "#/farms/{farmid}/edit?roleId={role_id}"},
     			{itemId: "option.stat", iconCls: 'scalr-menu-icon-stats', text:'查看统计信息', href: "/monitoring.php?role={id}&farmid={farmid}"},
     			{itemId: "option.info", iconCls: 'scalr-menu-icon-info', text:'服务角色信息', href: "#/farms/" + loadParams['farmId'] + "/roles/{id}/extendedInfo"},
     			new Ext.menu.Separator({itemId: "option.mainSep"}),
     			{itemId: "option.exec", iconCls: 'scalr-menu-icon-execute', text: '执行脚本', href: "#/scripts/execute?farmRoleId={id}"},
     			new Ext.menu.Separator({itemId: "option.eSep"}),
     			{itemId: "option.sgEdit", 		text: '修改安全组', href: "/sec_group_edit.php?farm_roleid={id}&location={location}&platform={platform}"},
     			new Ext.menu.Separator({itemId: "option.sgSep"}),
			{
				itemId: 'option.launch',
				iconCls: 'scalr-menu-icon-launch',
				text: '启动新的服务器',
				request: {
					processBox: {
						type: 'launch',
						msg: '请等待 ...'
					},
					dataHandler: function (record) {
						this.url = '/farms/' + loadParams['farmId'] + '/roles/' + record.get('id') + '/xLaunchNewServer';
					},
					success: function (data) {
						store.reload();
						Scalr.Message.Success('服务器已成功启动');

						if (data.warnMsg)
							Scalr.Message.Warning(data.warnMsg);
					}
				}
			}, {
				xtype: 'menuseparator',
				itemId: 'option.scSep'
			}],

          	getRowOptionVisibility: function (item, record) {
     			var data = record.data;

     			if (item.itemId == "option.scSep")
     				return (data.shortcuts.length > 0);
     			
     			if (item.itemId == "option.sgEdit")
     				return (data.platform == 'euca' || data.platform == 'ec2');
     			
     			if (item.itemId == 'option.stat' || item.itemId == 'option.cfg' || item.itemId == 'option.ssh_key' || item.itemId == 'option.info')
     			{
     				return true;
     			}
     			else
     			{
     				if (data.farm_status == 1)
     					return true;
     				else
     					return false;
     			}
     			
     			return true;
     		},
			
			listViewOptions: {
				emptyText: "No roles assigned to selected farm",
				columns: [
					{ header: "平台", width: 15, dataIndex: 'platform', sortable: true, hidden: 'no' },
					{ header: "位置", width: 15, dataIndex: 'location', sortable: false, hidden: 'no' },
					{ header: "服务角色名称", width: 40, dataIndex: 'name', sortable: false, hidden: 'no', tpl:
						'<a href="#/roles/{role_id}/view">{name}</a>'
					},
					{header: "镜像ID", width: 30, dataIndex: 'image_id', sortable: false, hidden: 'no', tpl:
						'<a href="#/roles/{role_id}/view">{image_id}</a>'
					},
					{ header: "最小服务器数", width: 15, dataIndex: 'min_count', sortable: false, align:'center', hidden: 'no' },
					{ header: "最大服务器数", width: 15, dataIndex: 'max_count', sortable: false, align:'center', hidden: 'no' },
					{ header: "允许自适应算法", width: 70, dataIndex: 'scaling_algos', sortable: false, align:'center', hidden: 'no' },
					{ header: "服务器", width: 20, dataIndex: 'servers', sortable: false, hidden: 'no', tpl:
						'{servers} <a href="#/servers/view?farmId={farmid}&farmRoleId={id}" title="查看服务器"><img src="/images/39111513521.png"  alt="查看服务器"></a>'  
					},
					{ header: "域", width: 20, dataIndex: 'domains', sortable: false, hidden: 'no', tpl:
						'{domains} <a href="#/dnszones/view?farmRoleId={id}" title="查看域"><img src="/images/39111513521.png"  alt="查看域"></a>'
					}
			]},
			listeners:{
				'beforeshowoptions': {fn: function (grid, record, romenu, ev) {
					var data = record.data;

					var rows = romenu.items.items;
					for (k in rows)
					{
						if (rows[k].isshortcut == 1)
							romenu.remove(rows[k]);
					}

					if (data.shortcuts.length > 0)
					{
						for (i in data.shortcuts)
						{
							if (typeof(data.shortcuts[i]) != 'function')
							{
								romenu.add({
									id:'option.'+(Math.random()*100000),
									isshortcut:1,
									text:'Execute '+data.shortcuts[i].name,
									href:'#/scripts/execute?eventName='+data.shortcuts[i].event_name
								});
							}
						}
					}
					else
					{
						var rows = romenu.items.items;
						for (k in rows)
						{
							if (rows[k].isshortcut == 1)
								romenu.remove(rows[k]);
						}
					}
				}}
			}
		});
	}
}
