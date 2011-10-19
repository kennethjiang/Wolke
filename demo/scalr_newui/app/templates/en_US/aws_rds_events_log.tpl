{include file="inc/header.tpl"}
<script type="text/javascript" src="/js/ui-ng/data.js"></script>
<script type="text/javascript" src="/js/ui-ng/viewers/ListView.js"></script>

<div id="listview-rds-events-log-view"></div>
<script type="text/javascript">

var uid = '{$smarty.session.uid}';

var regions = [
{foreach from=$regions name=id key=key item=item}
	['{$key}','{$item}']{if !$smarty.foreach.id.last},{/if}
{/foreach}
];

var region = '{$smarty.session.aws_region}';

{literal}
Ext.onReady(function () {
	var panel = new Scalr.Viewers.ListView({
		renderTo: 'listview-rds-events-log-view',
		autoRender: true,
		store: new Scalr.data.Store({
			reader: new Scalr.data.JsonReader({
				id: 'message',
				fields: [ 'time', 'message', 'source', 'type' ]
			}),
			remoteSort: true,
			url: '/server/grids/aws_rds_event_log_list.php?a=1{/literal}{$grid_query_string}{literal}'
		}),
		savePagingSize: true,
		saveFilter: true,
		stateId: 'listview-rds-events-log-view',
		stateful: true,
		title: 'Events',

		listViewOptions: {
			emptyText: 'No events found',
			columns: [
				{ header: "Time", width: 100, dataIndex: 'time', sortable: false, hidden: 'no' },
				{ header: "Message", width: 100, dataIndex: 'message',  sortable: false, hidden: 'no' },
				{ header: "Source", width: 100, dataIndex: 'source',  sortable: false, hidden: 'no' },
				{ header: "Type", width: 50, dataIndex: 'type', sortable: false, hidden: 'no' }
			]
		}
    });
});
{/literal}
</script>
{include file="inc/footer.tpl"}
