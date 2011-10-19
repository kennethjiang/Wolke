{include file="inc/header.tpl"}
<script type="text/javascript" src="/js/ui-ng/data.js"></script>
<script type="text/javascript" src="/js/ui-ng/viewers/ListView.js"></script>

<div id="listview-search-view"></div>
<script type="text/javascript">

var gridData = {$grid_data};

{literal}
Ext.onReady(function () {
	var panel = new Scalr.Viewers.ListView({
		renderTo: 'listview-search-view',
		autoRender: true,
		store: new Scalr.data.Store({
			reader: new Scalr.data.JsonReader({
				id: 'id',
				fields: ['title', 'href', 'count']
			}),
			remoteSort: false,
			data: gridData
			//url: '/server/grids/farms_list.php?a=1{/literal}{$grid_query_string}{literal}'
		}),
		savePagingSize: true,
		enableFilter: false,
		enableAutoLoad: false,
		enablePaging: false,
		stateId: 'listview-search-view',
		stateful: true,
		title: 'Search results',

		listViewOptions: {
			emptyText: 'Not found',
			columns: [
				{ header: "Page", width: 75, dataIndex: 'title', hidden: 'no', tpl: '<a href="{href}">{title}</a>'},
				{ header: "Total results", width: 25, dataIndex: 'count', hidden: 'no' }
			]
		}
    });
});
{/literal}
</script>
{include file="inc/footer.tpl"}
