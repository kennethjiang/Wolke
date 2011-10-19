{include file="inc/header.tpl"}
<script type="text/javascript" src="/js/ui-ng/data.js"></script>
<script type="text/javascript" src="/js/ui-ng/viewers/ListView.js"></script>

<div id="listview-ec2-spotrequest-view"></div>
<script type="text/javascript">

var FarmID = '{$smarty.get.farmid}';

var regions = [
{foreach from=$regions name=id key=key item=item}
	['{$key}','{$item}']{if !$smarty.foreach.id.last},{/if}
{/foreach}
];

var region = '{$smarty.session.aws_region}';

{literal}
Ext.onReady(function () {
	var store = new Scalr.data.Store({
		reader: new Scalr.data.JsonReader({
			id: 'spotInstanceRequestId',
			fields: [ 'spotInstanceRequestId' , 'spotPrice', 'type', 'state', 'createTime','instanceId','productDescription', 'imageId','instanceType','validFrom','validUntil' ]
		}),
		remoteSort: true,
		url: '/server/grids/aws_ec2_spotrequest_list.php?a=1{/literal}{$grid_query_string}{literal}'
	});

	new Scalr.Viewers.ListView({
		renderTo: "listview-ec2-spotrequest-view",
		autoRender: true,
		store: store,
		savePagingSize: true,
		savePagingNumber: true,
		enableFilter: false,
		stateId: 'listview-ec2-spotrequest-view',
		stateful: true,
		title: 'Spot requests',

	    tbar: [
			{ text: 'Location:' },
			new Ext.form.ComboBox({
				allowBlank: false,
				editable: false,
				store: regions,
				value: region,
				displayField: 'state',
				typeAhead: false,
				mode: 'local',
				triggerAction: 'all',
				selectOnFocus: false,
				width: 100,
				listeners: {
					select: function (combo, record, index) {
						store.baseParams.region = combo.getValue();
						store.load();
					}
				}
			}), {
				icon: '/images/add.png', // icons can also be specified inline
				cls: 'x-btn-icon',
				tooltip: 'Add new request',
				handler: function () {
					document.location.href = '/aws_ec2_amis_view.php';
				}
			 }
	    ],

		listViewOptions: {
			emptyText: "No requests were found",
			columns: [
				{ header: "Request ID",		width: 40, dataIndex: 'spotInstanceRequestId',	sortable: false, hidden: 'no' },
				{ header: "Instance ID",		width: 40, dataIndex: 'instanceId',				sortable: false, hidden: 'no' },
				{ header: "Instance Type",	width: 40, dataIndex: 'instanceType',			sortable: false, hidden: 'no' },
				{ header: "Image ID",		width: 40, dataIndex: 'imageId',				sortable: false, hidden: 'no' },
				{ header: "Spot price",		width: 40, dataIndex: 'spotPrice',				sortable: false, hidden: 'no' },
				{ header: "Type",			width: 40, dataIndex: 'type',					sortable: false, hidden: 'no' },
				{ header: "Create time",		width: 40, dataIndex: 'createTime',				sortable: false, hidden: 'no' },
				{ header: "Valid from",		width: 40, dataIndex: 'validFrom',				sortable: false, hidden: 'no' },
				{ header: "Valid until",		width: 40, dataIndex: 'validUntil',			sortable: false, hidden: 'no' },
				{ header: "Description",		width: 40, dataIndex: 'productDescription',		sortable: false, hidden: 'no' },
				{ header: "State",			width: 40, dataIndex: 'state',					sortable: false, hidden: 'no' }
			]
		},

		withSelected: {
			menu: [
				{
					text: "Cancel request",
					method: 'post',
					params: {
						action: 'delete',
						with_selected: 1
					},
					confirmationMessage: 'Cancel selected request(s)?',
					url: '/aws_ec2_spotrequest_view.php'
				}
			],
		}
	});
});
{/literal}
</script>
{include file="inc/footer.tpl"}
