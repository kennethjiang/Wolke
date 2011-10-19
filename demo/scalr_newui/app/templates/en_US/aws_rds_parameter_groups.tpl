{include file="inc/header.tpl"}
<script type="text/javascript" src="/js/ui-ng/data.js"></script>
<script type="text/javascript" src="/js/ui-ng/viewers/ListView.js"></script>

<div id="listview-rds-parameter-groups-view"></div>
<script type="text/javascript">
var regions = [
{foreach from=$regions name=id key=key item=item}
	['{$key}','{$item}']{if !$smarty.foreach.id.last},{/if}
{/foreach}
];

var region = '{$smarty.session.aws_region}';

{literal}
Ext.onReady(function () {
	var panel = new Scalr.Viewers.ListView({
		renderTo: 'listview-rds-parameter-groups-view',
		autoRender: true,
		store: new Scalr.data.Store({
			reader: new Scalr.data.JsonReader({
				id: 'id',
				fields: [ 'id','engine','name','description' ]
			}),
			remoteSort: true,
			url: '/server/grids/aws_rds_param_groups_list.php?a=1{/literal}{$grid_query_string}{literal}'
		}),
		savePagingSize: true,
		enableFilter: false,
		stateId: 'listview-rds-parameter-groups-view',
		stateful: true,
		title: 'Parameter groups',

		listViewOptions: {
			emptyText: 'No db parameter groups found',
			columns: [
				{ header: "Name", width: 70, dataIndex: 'name', sortable: true, hidden: 'no' },
				{ header: "Description", width: 50, dataIndex: 'description', sortable: false, hidden: 'no' }
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
				tooltip: 'Add new parameter group',
				handler: function() {
					document.location.href = '/aws_rds_param_group_add.php';
				}
			 }
		],

		rowOptionsMenu: [
			{ itemId: "option.edit", 		text:'Edit', 			  	href: "/aws_rds_param_group_edit.php?name={name}"},
			new Ext.menu.Separator({ itemId: "option.editSep"}),
			{ itemId: "option.events",       text: 'Events log', href: "/aws_rds_events_log.php?type=db-parameter-group&name={name}"}
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
					confirmationMessage: 'Delete selected parameter group(s)?',
					url: '/aws_rds_parameter_groups.php'
				}
			],
		}
    });
});
{/literal}
</script>
{include file="inc/footer.tpl"}
