{include file="inc/header.tpl"}
<script type="text/javascript" src="/js/ui-ng/data.js"></script>
<script type="text/javascript" src="/js/ui-ng/viewers/ListView.js"></script>

<div id="listview-elastic-ips-view"></div>

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
			fields: [ 'ipaddress','instance_id', 'farm_id', 'farm_name', 'role_name', 'indb', 'farm_roleid', 'server_id', 'server_index' ]
		}),
		remoteSort: true,
		url: '/server/grids/eips_list.php?a=1{/literal}{$grid_query_string}{literal}'
	});

	var panel = new Scalr.Viewers.ListView({
		renderTo: "listview-elastic-ips-view",
		autoRender: true,
		store: store,
		savePagingSize: true,
		savePagingNumber: true,
		enableFilter: false,
		stateId: 'listview-elastic-ips-view',
		stateful: true,
		title: 'Elastic IPs',

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
				selectOnFocus:false,
				width: 100,
				listeners: {
					select: function(combo, record, index) {
						store.baseParams.region = combo.getValue(); 
						store.load();
					}
				}
			})
		],

		rowOptionsMenu: [
			{ itemId: "option.associate", text:'Associate', href: "/elastic_ips.php?task=associate&ip={ipaddress}"},
			{ itemId: "option.release", text:'Delete', href: "/elastic_ips.php?task=release&ip={ipaddress}", confirmationMessage: 'Release selected elastic ip?' }
		],

		listViewOptions: {
			emptyText: "No elastic IPs found",
			columns: [
				{ header: "Used By", width: 10, dataIndex: 'farm_name', sortable: true, hidden: 'no', tpl:
					'<tpl if="farm_id">Farm: <a href="#/farms/{values.farm_id}/view" title="Farm {values.farm_name}">{values.farm_name}</a>' +
						'<tpl if="role_name">&nbsp;&rarr;&nbsp;<a href="#/farms/{values.farm_id}/roles/{values.farm_roleid}/view"' + 
							'title="Role {values.role_name}">{values.role_name}</a> #{values.server_index}' + 
						'</tpl>' +
					'</tpl>' + 
					'<tpl if="! farm_id"><img src="/images/false.gif" /></tpl>'
				},
				{ header: "IP address", width: 4, dataIndex: 'ipaddress', sortable: false, hidden: 'no' },
				{ header: "Auto-assigned", width: 4, dataIndex: 'role_name', sortable: true, hidden: 'no', align:'center', tpl: 
					'<tpl if="indb"><img src="images/true.gif"></tpl>' +
					'<tpl if="!indb"><img src="images/false.gif"></tpl>'
				},
				{ header: "Server", width: 5, dataIndex: 'instance_id', sortable: true, hidden: 'no', tpl:
					'<tpl if="server_id"><a href="#/servers/{values.server_id}/view">{values.server_id}</a></tpl>' +
					'<tpl if="!server_id">{values.instance_id}</tpl>'
				}
			]
		}
	});
});
{/literal}
</script>
{include file="inc/footer.tpl"}