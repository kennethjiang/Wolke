<?php /* Smarty version 2.6.26, created on 2011-09-20 00:17:58
         compiled from clients_view.tpl */ ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "inc/header.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<link rel="stylesheet" href="css/grids.css" type="text/css" />
<div id="maingrid-ct" class="ux-gridviewer"></div>
<script type="text/javascript">
<?php echo '
Ext.onReady(function () {

	Ext.QuickTips.init();
	
	// create the Data Store
    var store = new Ext.ux.scalr.Store({
    	reader: new Ext.ux.scalr.JsonReader({
	        root: \'data\',
	        successProperty: \'success\',
	        errorProperty: \'error\',
	        totalProperty: \'total\',
	        id: \'id\',
	        	
	        fields: [
				{name: \'id\', type: \'int\'},
				\'email\', \'dtadded\', \'farms\', \'roles\', \'apps\', \'instances\', \'isactive\', \'farms_limit\',\'fullname\', \'comments\'
	        ]
    	}),
    	remoteSort: true,
		url: \'/server/grids/clients_list.php?a=1'; ?>
<?php echo $this->_tpl_vars['grid_query_string']; ?>
<?php echo '\',
		listeners: { dataexception: Ext.ux.dataExceptionReporter }
    });
	
	function farmRenderer(value, p, record) {
		return record.data.farms+\' [<a href="#/farms/view?clientId=\'+record.data.id+\'">View</a>]\';
	}

	function commentRenderer(value, p, record)
	{
		if (value && value.length != 0)
			return \'<img ext:qtip="\'+value.replace(\'"\', \'\\"\')+\'" src=\\\'/images/comments.png\\\' />\';
		else
			return \'<img src=\\\'/images/false.gif\\\' />\';
	}
	
	function roleRenderer(value, p, record) {
		return record.data.roles;
	}

	function appRenderer(value, p, record) {
		return record.data.apps;
	}

	function isactiveRenderer(value, p, record) {
		return (record.data.isactive == 1) ? \'<img src=\\\'/images/true.gif\\\' />\' : \'<img src=\\\'/images/false.gif\\\' />\';
	}
	
	function limitRenderer(value, p, record) {
		return (record.data.farms_limit == 0) ? \'Unlimited\' : record.data.farms_limit;
	}
    	
    var renderers = Ext.ux.scalr.GridViewer.columnRenderers;
	var grid = new Ext.ux.scalr.GridViewer({
        renderTo: "maingrid-ct",
        height: 500,
        title: "Clients",
        id: \'clients_list1_\'+GRID_VERSION,
        store: store,
        maximize: true,
        viewConfig: { 
        	emptyText: "No clients defined"
        },

        // Columns
        columns:[
			{header: "E-mail", width: 120, dataIndex: \'email\', sortable: true},
			{header: "Name", width: 120, dataIndex: \'fullname\', sortable: true, hidden: true},
			{header: "Farms", width: 70, dataIndex: \'farms\', renderer:farmRenderer, sortable: false},
			{header: "Custom roles", width: 70, dataIndex: \'roles\', renderer:roleRenderer, sortable: false, hidden:true},
			{header: "Applications", width: 70, dataIndex: \'apps\', renderer:appRenderer, sortable: false},
			{header: "Running servers", width: 70, dataIndex: \'instances\', sortable: false},
			{header: "Farms limit", width: 70, dataIndex: \'farms_limit\', renderer: limitRenderer, sortable: false, hidden:true},
			{header: "Added at", width: 70, dataIndex: \'dtadded\', sortable: true, hidden:true},
			{header: "Comment", width: 50, dataIndex: \'comments\', renderer:commentRenderer, sortable: false, hidden:true, align:\'center\'},
			{header: "Active", width: 70, dataIndex: \'isactive\', renderer:isactiveRenderer, sortable: false, align:\'center\'}
		],

		//TODO: Hide option for non-active rows
		
    	// Row menu
    	rowOptionsMenu: [
			{id: "option.edit", 		text:\'Edit\', 			  	href: "/clients_add.php?id={id}"},
			\'-\',
			{id: "option.login", 		text: \'Log in to Client CP\', 	href: "/login.php?id={id}&isadmin=1"}
     	],

     	getRowOptionVisibility: function (item, record) {
			var data = record.data;

			return true;
		},

		getRowMenuVisibility: function (record) {
			return true;
		},
		// With selected options
		withSelected: {
			menu: [
				{text: "Activate", value: "activate"},
				{text: "Deactivate", value: "deactivate"},
				\'-\',
				{text: "Cleanup", value: "cleanup"},
				\'-\',
				{text: "Delete", value: "delete"}
			],
			hiddens: {with_selected : 1},
			action: "act"
		}
    });
    grid.render();
    store.load();

	return;
});
'; ?>

</script>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "inc/footer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>