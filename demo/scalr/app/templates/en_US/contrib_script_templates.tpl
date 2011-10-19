{include file="inc/header.tpl"}
<link rel="stylesheet" href="css/grids.css" type="text/css" />
<div id="maingrid-ct" class="ux-gridviewer"></div>
<script type="text/javascript">
{literal}
Ext.onReady(function () {
	// create the Data Store
    var store = new Ext.ux.scalr.Store({
    	reader: new Ext.ux.scalr.JsonReader({
	        root: 'data',
	        successProperty: 'success',
	        errorProperty: 'error',
	        totalProperty: 'total',
	        id: 'id',
	        	
	        fields: [
	            'name', 'description', 'revision', 'dtcreated', 'approval_state', 'id', 'clientid','client_email'
	        ]
    	}),
    	remoteSort: true,
		url: '/server/grids/admin/contrib_scripts_list.php?a=1{/literal}{$grid_query_string}{literal}',
		listeners: { dataexception: Ext.ux.dataExceptionReporter }
    });
	
    

	function clientRenderer(value, p, record)
	{
		if (record.data.client_email)
			return '<a href="clients_view.php?clientid='+record.data.clientid+'">'+record.data.client_email+'</a>';
		else
			'Scalr';
	}
	
    var renderers = Ext.ux.scalr.GridViewer.columnRenderers;
	var grid = new Ext.ux.scalr.GridViewer({
        renderTo: "maingrid-ct",
        height: 500,
        title: "Contributed scipts",
        id: 'contrib_scripts_list_'+GRID_VERSION,
        store: store,
        maximize: true,
        viewConfig: { 
        	emptyText: "No contributed scripts found"
        },

        // Columns
        columns:[
			{header: "Created by", width: 100, dataIndex: 'zone', renderer:clientRenderer, sortable: false},
			{header: "Name", width: 100, dataIndex: 'name', sortable: true},
			{header: "Description", width: 100, dataIndex: 'description',  sortable: true},
			{header: "Version", width: 25, dataIndex: 'revision', sortable: false},
			{header: "Created at", width: 75, dataIndex: 'dtcreated', sortable: true},
			{header: "Approval state", width: 50, dataIndex: 'approval_state', sortable: false}
		],

		// Row menu
    	rowOptionsMenu: [
			{id: "option.moderate", 	text:'Moderate', 		href: "/script_info.php?id={id}&revision={revision}"}
     	],

     	getRowOptionVisibility: function (item, record) {
			return true;
		},

		getRowMenuVisibility: function (record) {
			return true
		}
    });
    grid.render();
    store.load();

	return;
});
{/literal}
</script>
{include file="inc/footer.tpl"}