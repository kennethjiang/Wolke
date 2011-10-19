{include file="inc/header.tpl"}
<script type="text/javascript" src="/js/ui-ng/data.js"></script>
<script type="text/javascript" src="/js/ui-ng/viewers/ListView.js"></script>

<div id="listview-scheduler-view"></div>

<script type="text/javascript">
{literal}
Ext.onReady(function () {
	var store = new Scalr.data.Store({
		reader: new Scalr.data.JsonReader({
			id: 'id',
			fields: [
				'id', 'task_name', 'task_type', 'target_name', 'target_type', 'start_time_date',
				'end_time_date', 'last_start_time', 'restart_every','order_index', 'farmid','farm_name','status'
			]
		}),
		remoteSort: true,
		url: '/server/grids/scheduler_tasks_list.php?a=1{/literal}{$grid_query_string}{literal}'
	});

	var panel = new Scalr.Viewers.ListView({
		renderTo: "listview-scheduler-view",
		autoRender: true,
		store: store,
		savePagingSize: true,
		saveFilter: true,
		stateId: 'listview-scheduler-view',
		stateful: true,
		title: 'Script tasks',

		tbar: [{
			icon: '/images/add.png', // icons can also be specified inline
			cls: 'x-btn-icon',
			tooltip: 'Add new request',
			handler: function () {
				document.location.href = '/scheduler_task_add.php?task=create';
			}
		}],

		rowOptionsMenu: [
			{itemId: "option.activate", text: 'Activate',	href: "/scheduler.php?&action=activate&id={id}" },
			{itemId: "option.suspend", text: 'Suspend',   	href: "/scheduler.php?&action=suspend&id={id}"},
			new Ext.menu.Separator({itemId: "option.editSep"}),
			{itemId: "option.edit", text: 'Edit', 			href: "/scheduler_task_add.php?task=edit&id={id}"}
		],

		getRowOptionVisibility: function (item, record) {
			if (item.itemId == "option.activate" || item.itemId == "option.suspend" || item.itemId == "option.editSep") {
				var reg =/Finished/i
				if(reg.test(record.data.status))
					return false;
			}
			var reg =/Active/i
			if (item.itemId == "option.activate" && reg.test(record.data.status))
				return false;

			var reg =/Suspended/i
			if (item.itemId == "option.suspend"  && reg.test(record.data.status))
				return false;

			return true;
		},

		withSelected: {
			menu: [
				{
					text: "Delete",
					method: 'post',
					params: {
						action: 'delete'
					},
					confirmationMessage: 'Delete selected task(s)?',
					url: '/scheduler.php'
				}, {
					text: "Activate",
					method: 'post',
					params: {
						action: 'activate'
					},
					confirmationMessage: 'Activate selected task(s)?',
					url: '/scheduler.php'
				}, {
					text: "Suspend",
					method: 'post',
					params: {
						action: 'suspend'
					},
					confirmationMessage: 'Suspend selected task(s)?',
					url: '/scheduler.php'
				}
			],
		},

		listViewOptions: {
			emptyText: "No tasks found",
			columns: [
				{ header: "ID", width: 15, dataIndex: 'id', sortable: true, hidden: 'no' },
				{ header: "Task name", width: 40, dataIndex: 'task_name', sortable: true, hidden: 'no' },
				{ header: "Task type", width: 40, dataIndex: 'task_type', sortable: false, hidden: 'no' },
				{ header: "Target name", width: 80, dataIndex: 'target_name', sortable: true, hidden: 'no', tpl:
					'<tpl if="target_type == &quot;farm&quot;">Farm: <a href="#/farms/{farmid}/view" title="Farm {target_name}">{target_name}</a></tpl>' +
					'<tpl if="target_type == &quot;role&quot;">Farm: <a href="#/farms/{farmid}/view" title="Farm {farm_name}">{farm_name}</a>' +
						'&nbsp;&rarr;&nbsp;Role: <a href="#/farms/{farmid}/roles" title="Role {target_name}">{target_name}</a>' +
					'</tpl>' +
					'<tpl if="target_type == &quot;instance&quot;">Farm: <a href="#/farms/{farmid}/view" title="Farm {farm_name}">{farm_name}</a>' +
						'&nbsp;&rarr;&nbsp;Server: <a href="#/servers/view?farmId={farmid}" title="Server {target_name}">{target_name}</a>' +
					'</tpl>'
				},
				{ header: "Start date", width: 50, dataIndex: 'start_time_date', sortable: true, hidden: 'no' },
				{ header: "End date", width: 50, dataIndex: 'end_time_date', sortable: true, hidden: 'no' },
				{ header: "Last time executed", width: 50, dataIndex: 'last_start_time', sortable: true, hidden: 'no', tpl:
					'<tpl if="last_start_time">{last_start_time}</tpl>'
				},
				{ header: "Priority", width: 20, dataIndex: 'order_index', sortable: true, hidden: 'no' },
				{ header: "Status", width: 20, dataIndex: 'status', sortable: true, hidden: 'no', tpl:
					'<tpl if="status == &quot;Active&quot;"><span style="color: green;">{status}</span></tpl>' +
					'<tpl if="status == &quot;Suspended&quot;"><span style="color: blue;">{status}</span></tpl>' +
					'<tpl if="status == &quot;Finished&quot;"><span style="color: red;">{status}</span></tpl>'
				}
			]
		}
	});
});
{/literal}
</script>
{include file="inc/footer.tpl"}
