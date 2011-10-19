{include file="inc/header.tpl"}
<script type="text/javascript" src="/js/ui-ng/data.js"></script>
<script type="text/javascript" src="/js/ui-ng/viewers/ListView.js"></script>

<div id="listview-aws-elb-view"></div>

<script type="text/javascript">
var regions = [
{foreach from=$regions name=id key=key item=item}
	['{$key}','{$item}']{if !$smarty.foreach.id.last},{/if}
{/foreach}
];

var region = '{$smarty.session.aws_region}';

{literal}
Ext.onReady(function() {
	var store = new Scalr.data.Store({
		reader: new Scalr.data.JsonReader({
			root: 'data',
			successProperty: 'success',
			errorProperty: 'error',
			totalProperty: 'total',
			id: 'id',
			fields: [ 'name','dtcreated', 'dnsname', 'farmid', 'role_name', 'farm_name' ]
		}),
		remoteSort: true,
		url: '/server/grids/elbs_list.php?a=1{/literal}{$grid_query_string}{literal}'
	});

	var panel = new Scalr.Viewers.ListView({
		renderTo: "listview-aws-elb-view",
		autoRender: true,
		store: store,
		savePagingSize: true,
		savePagingNumber: true,
		enableFilter: false,
		stateId: 'listview-aws-elb-view',
		stateful: true,
		title: 'Elastic Load Balancers',

		tbar: [
			'Location:',
			new Ext.form.ComboBox({
				allowBlank: false,
				editable: false, 
				store: regions,
				value: region,
				typeAhead: false,
				mode: 'local',
				triggerAction: 'all',
				selectOnFocus: false,
				width: 100,
				listeners: {
					select: function(combo, record, index) {
						store.baseParams.region = combo.getValue(); 
						store.load();
					}
				}
			})
		],

		// Row menu
		rowOptionsMenu: [
			{ itemId: "option.details", text:'Details', href: "/aws_elb_details.php?name={name}"},
			new Ext.menu.Separator({itemId: "option.delSep"}),
			{ itemId: "option.delete", text:'Remove', href: "/aws_elb.php?name={name}&action=remove", confirmationMessage: 'Remove selected load balancer ?' }
		],

		listViewOptions: {
			emptyText: "No elastic load balancers found",
			columns: [
				{ header: "Name", width: 10, dataIndex: 'name', sortable: false, hidden: 'no' },
				{ header: "Used on", width: 7, dataIndex: 'name', sortable: false, hidden: 'no', tpl:
					'<tpl if="role_name && farmid">' +
						'Farm: <a href="#/farms/{values.farmid}/view" title="Farm {values.farm_name}">{values.farm_name}</a>' +
						'&nbsp;&rarr;&nbsp;<a href="#/farms/{values.farmid}/roles" title="Role {values.role_name}">{values.role_name}</a>' +
					'</tpl>' + 
					'<tpl if="! role_name && farmid">Not used by Scalr</tpl>'
				},
				{ header: "DNS name", width: 15, dataIndex: 'dnsname', sortable: false, hidden: 'no' },
				{ header: "Created at", width: 6, dataIndex: 'dtcreated', sortable: false, hidden: 'no' }
			]
		}
    });
});
{/literal}
</script>
{include file="inc/footer.tpl"}