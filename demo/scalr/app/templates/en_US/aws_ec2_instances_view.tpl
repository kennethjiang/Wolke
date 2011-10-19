{include file="inc/header.tpl"}
<script type="text/javascript" src="/js/ui-ng/data.js"></script>
<script type="text/javascript" src="/js/ui-ng/viewers/ListView.js"></script>

<div id="listview-ec2-instances-view"></div>
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
		renderTo: 'listview-ec2-instances-view',
		autoRender: true,
		store: new Scalr.data.Store({
			reader: new Scalr.data.JsonReader({
		        id: 'iid',
		        fields: [
					'iid', 'imageId', 'instanceState','dnsName','keyName','instanceType','launchTime',
					'availabilityZone','monState','instanceLifecycle'
		        ]
			}),
			remoteSort: true,
			url: '/server/grids/aws_ec2_instances_list.php?a=1{/literal}{$grid_query_string}{literal}'
		}),
		savePagingSize: true,
		enableFilter: false,
		stateId: 'listview-ec2-instances-view',
		stateful: true,
		title: 'Running spot instances',

		listViewOptions: {
			emptyText: 'No running spot instances were found',
			columns: [
				{ header: "Instance ID",			width: 40, dataIndex: 'iid',				sortable: false, hidden: 'no' },
				{ header: "Image ID",			width: 40, dataIndex: 'imageId',			sortable: false, hidden: 'no' },
				{ header: "Instance state",		width: 40, dataIndex: 'instanceState',		sortable: false, hidden: 'no' },
				{ header: "instance type",		width: 40, dataIndex: 'instanceType',		sortable: false, hidden: 'no' },
				{ header: "DNS name",			width: 40, dataIndex: 'dnsName',			sortable: false, hidden: 'no' },
				{ header: "availability zone",	width: 40, dataIndex: 'availabilityZone',	sortable: false, hidden: 'no' },
				{ header: "Lifecycle",			width: 40, dataIndex: 'instanceLifecycle',	sortable: false, hidden: 'no' }
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
			}), {
				icon: '/images/add.png', // icons can also be specified inline
				cls: 'x-btn-icon',
				tooltip: 'Launch new DB instance',
				handler: function() {
					document.location.href = '/aws_ec2_amis_view.php';
				}
			 }
		],

		rowOptionsMenu: [
			{ itemId: "option.details",	text: 'Details', 	href: "/aws_ec2_instance_info.php?iid={iid}"}
		],

		withSelected: {
			menu: [
				{
					text: "Delete",
					method: 'post',
					params: {
						action: 'delete',
						with_selected: 1
					},
					confirmationMessage: 'Delete selected security group(s)?',
					url: '/aws_ec2_instances_view.php'
				}
			],
		}
    });
});
{/literal}
</script>
{include file="inc/footer.tpl"}
