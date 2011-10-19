{include file="inc/header.tpl"}
<script type="text/javascript" src="/js/ui-ng/data.js"></script>
<script type="text/javascript" src="/js/ui-ng/viewers/ListView.js"></script>

<div id="listview-apache-vhosts-view"></div>

<script type="text/javascript">
{literal}
Ext.onReady(function() {
	var store = new Scalr.data.Store({
		reader: new Scalr.data.JsonReader({
			root: 'data',
			successProperty: 'success',
			errorProperty: 'error',
			totalProperty: 'total',
			id: 'id',
			fields: [ 'id','domain_name','farmid','farm_name','farm_roleid','role_name','isSslEnabled','last_modified' ]
		}),
		remoteSort: true,
		url: '/server/grids/apache_vhosts_list.php?s=1{/literal}{$grid_query_string}{literal}'
	});

	var panel = new Scalr.Viewers.ListView({
		renderTo: "listview-apache-vhosts-view",
		autoRender: true,
		store: store,
		enablePaging: false,
		enableFilter: false,
		stateId: 'listview-apache-vhosts-view',
		stateful: true,
		title: 'Apache vhosts',

		tbar: [{
			icon: '/images/add.png',
			cls: 'x-btn-icon',
			tooltip: 'Add new virtual host',
			handler: function() {
				document.location.href = '/apache_vhost_add.php';
			}
		}],

		// Row menu
		rowOptionsMenu: [
			{ itemId: "option.edit", 	text: 'Edit', 		href: "/apache_vhost_add.php?vhost_id={id}" } 
		],

		withSelected: {
			menu: [
				{
					text: 'Delete',
					params: {
						action: 'delete',
						with_selected: 1
					},
					confirmationMessage: 'Remove selected virtual vhost(s)?'
				}
			]
		},

		listViewOptions: {
			emptyText: "No vhosts found",
			columns: [
				{ header: "ID", width: 15, dataIndex: 'id', sortable:false, hidden: 'no' },
				{ header: "Virtual host name", width: 40, dataIndex: 'domain_name', sortable:false, hidden: 'no' },
				{ header: "Role", width: 40, dataIndex: 'role_name', sortable: false, hidden: 'no', tpl:
					'Farm: <a href="#/farms/{values.farmid}/view" title="Farm {values.farm_name}">{values.farm_name}</a>' +
					'<tpl if="role_name">&nbsp;&rarr;&nbsp;Role: <a href="#/farms/{values.farmid}/roles" title="Role {values.role_name}">{values.role_name}</a></tpl>'
				},
				{ header: "Last time modified", width: 50, dataIndex: 'last_modified', sortable: false, hidden: 'no' },
				{ header: "SSL", width: '50px', dataIndex: 'isSslEnabled', sortable: false, align:'center', hidden: 'no', tpl:
					'<tpl if="isSslEnabled == 1"><img src="/images/true.gif"></tpl><tpl if="isSslEnabled != 1"><img src="/images/false.gif"></tpl>'
				}
			]
		}
	});
});
{/literal}
</script>
{include file="inc/footer.tpl"}