{include file="inc/header.tpl"}
<script type="text/javascript" src="/js/ui-ng/data.js"></script>
<script type="text/javascript" src="/js/ui-ng/viewers/ListView.js"></script>

<div id="listview-s3browser-view"></div>

<script type="text/javascript">
{literal}
Ext.onReady(function () {
	var store = new Scalr.data.Store({
		reader: new Scalr.data.JsonReader({
			id: 'id',
			fields: [ 'name', 'cfid', 'cfurl', 'cname', 'status', 'enabled' ]
		}),
		remoteSort: true,
		url: '/server/grids/s3browser_list.php?a=1{/literal}{$grid_query_string}{literal}'
	});

	var panel = new Scalr.Viewers.ListView({
		renderTo: "listview-s3browser-view",
		autoRender: true,
		store: store,
		savePagingSize: true,
		saveFilter: true,
		stateId: 'listview-s3browser-view',
		stateful: true,
		title: 'S3 buckets',

		rowOptionsMenu: [
			{ itemId: "option.create_dist", 		text: 'Create distribution', 	href: "/s3browser.php?action=create_dist&name={name}"},
			{ itemId: "option.delete_dist", 		text: 'Remove distribution',   	href: "/s3browser.php?action=delete_dist&id={cfid}"},
			{ itemId: "option.disable_dist", 	text: 'Disable distribution',   href: "/s3browser.php?action=disable_dist&id={cfid}"},
			{ itemId: "option.enable_dist", 		text: 'Enable distribution',  	href: "/s3browser.php?action=enable_dist&id={cfid}"},
				new Ext.menu.Separator({itemId: "option.editSep"}),
			{ itemId: "option.delete_backet", 	text: 'Delete bucket', href: "/s3browser.php?action=delete_backet&name={name}", confirmationMessage: 'Delete bucket ?'}
		],

		getRowOptionVisibility: function (item, record) {
			switch (item.itemId) {
				case "option.disable_dist":
					return ((record.data.enabled == "true") && record.data.cfig); // returns true if distribution has enabled status. Shows disable button

				case  "option.enable_dist":
					return ((record.data.enabled == "false") && record.data.cfig);

				case "option.delete_dist":
					return (record.data.cfig);

				case "option.create_dist":
						return (!record.data.cfig);

				default:
					return true;
			}
		},

		listViewOptions: {
			emptyText: "No tasks defined",
			columns: [
				{ header: "Bucket name", width: 40, dataIndex: 'name', sortable: false, hidden: 'no' },
				{ header: "Cloudfront ID", width: 30, dataIndex: 'cfid', sortable: false, hidden: 'no' },
				{ header: "Cloudfront URL", width: 30, dataIndex: 'cfurl', sortable: false, hidden: 'no' },
				{ header: "CNAME", width: 40, dataIndex: 'cname', sortable: false, hidden: 'no' },
				{ header: "Status", width: 20, dataIndex: 'status', sortable: false, hidden: 'no' },
				{ header: "Enabled", width: 30, dataIndex: 'enabled',sortable: false, hidden: 'no', tpl:
					'<tpl if="enabled"><img src="/images/true.gif"></tpl>' +
					'<tpl if="! enabled"><img src="/images/false.gif"></tpl>'
				}
			]
		}
	});
});
{/literal}
</script>
{include file="inc/footer.tpl"}
