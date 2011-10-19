{include file="inc/header.tpl"}
<script type="text/javascript" src="/js/ui-ng/data.js"></script>
<script type="text/javascript" src="/js/ui-ng/viewers/ListView.js"></script>

<div id="listview-ec2-pricehistory-view"></div>
<script type="text/javascript">

var FarmID = '{$smarty.get.farmid}';
var region = '{$smarty.session.aws_region}';
var regions = [
{foreach from=$regions name=id key=key item=item}
	['{$key}','{$item}']{if !$smarty.foreach.id.last},{/if}
{/foreach}
];

{literal}
Ext.onReady(function () {
	var panel = new Scalr.Viewers.ListView({
		renderTo: 'listview-ec2-pricehistory-view',
		autoRender: true,
		store: new Scalr.data.Store({
			reader: new Scalr.data.JsonReader({
				id: '',
				fields: [ 'type', 'price', 'description', 'timestamp' ]
			}),
			remoteSort: true,
			url: '/server/grids/aws_ec2_pricehistory_list.php?a=1{/literal}{$grid_query_string}{literal}'
		}),
		savePagingSize: true,
		saveFilter: true,
		stateId: 'listview-ec2-pricehistory-view',
		stateful: true,
		title: 'Price history',

		listViewOptions: {
			emptyText: 'No price history were found',
			columns: [
				{ header: "Instance type",	width: 70, dataIndex: 'type',		sortable: true, hidden: 'no' },
				{ header: "Spot price",		width: 70, dataIndex: 'price',		sortable: true, hidden: 'no' },
				{ header: "Timestamp",		width: 70, dataIndex: 'timestamp',	sortable: true, hidden: 'no' },
				{ header: "Description",	width: 80, dataIndex: 'description',sortable: false, hidden: 'no' }
			]
		},

		tbar: [
			'Region:',
			new Ext.form.ComboBox({
				allowBlank: false,
				editable: false,
				store: regions,
				value: region,
				typeAhead: false,
				mode: 'local',
				triggerAction: 'all',
				selectOnFocus:false,
				width: 100,
				listeners: {
					select: function (combo, record, index) {
						panel.store.baseParams.region = combo.getValue();
						panel.store.load();
					}
				}
			})
		]
    });
});
{/literal}
</script>
{include file="inc/footer.tpl"}
