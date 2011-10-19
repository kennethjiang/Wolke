{include file="inc/header.tpl"}
<link rel="stylesheet" href="css/grids.css" type="text/css" />
<!-- link rel="stylesheet" href="css/ext-ux-livegrid.css" type="text/css"/ -->
<!-- script type="text/javascript" src="js/extjs/livegrid-all-debug.js"></script -->
{literal}
<style>
	.x-grid3-cell-inner { white-space:normal !important; }
</style>
{/literal}
<div id="search-ct"></div>
<div id="maingrid-ct" class="ux-gridviewer"></div>
<script type="text/javascript">
{literal}
Ext.onReady(function () {

	// ---- Init search form
	var searchPanel = new Ext.FormPanel({
		style: 'margin:5px 5px 15px 5px',
		renderTo: document.body,
        labelWidth: 150,
        frame:true,
        title: 'Search',
        bodyStyle:'padding:5px 5px 0',
        defaultType: 'textfield',	
        
		items: [{
			width: 500,
			name: 'query',
			fieldLabel: 'Search string'
		}, {
			xtype: 'checkboxgroup',
			width: 500,
			fieldLabel: 'Severity',
			columns: 3,
            items: {/literal}{$severities}{literal},
			listeners: {
				render: {
					fn: function (cmp) {
						if (Ext.isIE) {
							cmp.el.select('.x-form-element').setStyle('width', '166px');
						}
					},
					delay: 20
				}
			}
		}, {
			name: 'category',
			width: 500,
			fieldLabel: "Category"
	    }],
		listeners: {
			render: {
				fn:	function () {
					// XXX: Direct renderTo: search-ct doesn't works with FormPanel
					Ext.get("search-ct").appendChild(this.el);
				},
				delay: Ext.isIE ? 20 : 0
			}
		},
		buttons: [
			{text: 'Filter', handler: doFilter}
		]
	});

	function doFilter () {
		Ext.apply(store.baseParams, searchPanel.getForm().getValues(false));
		store.load();
	}	

	/*
	var store = new Ext.ux.grid.livegrid.Store({
        autoLoad : true,
        url      : 'server/grids/system_log_trans_list.php?a=1{/literal}{$grid_query_string}{literal}',
        bufferSize : 50,
		reader: new Ext.ux.grid.livegrid.JsonReader({
            root: 'data',
            versionProperty: 'version',
            totalProperty: 'total',
            id: 'id'},
            //['id','dtadded','message','severity','transactionid','farmid','message', 'sub_transactionid']
            ['id']
		)
	});
	store.on("exception", function () {
		console.log(arguments);
	});
	Ext.apply(store.baseParams, Ext.ux.parseQueryString(window.location.href));

	function severityRenderer (value, p, record) {
		if (value == 'ERROR' || value == 'FATAL')
			return value+' (<a href="syslog_view_backtrace.php?logeventid='+record.data.id+'">View backtrace</a>)';
		else
			return value;
	}

	function messageRenderer (value, p, record) {
		if (record.data.transactionid != record.data.sub_transactionid && record.data.sub_transactionid)
			return '<a href="syslog_transaction_details.php?strnid='+record.data.sub_transactionid+'&trnid='+record.data.transactionid+'">'+value+'</a>';
		else
			return value;
	}

	var view = new Ext.ux.grid.livegrid.GridView({
        nearLimit : 30,
        loadMask  : {
            msg :  'Buffering. Please wait...'
        },
    	emptyText: "No logs found",
    	getRowClass: function (record, index) {
    		if (record.data.severity == 'FATAL' || record.data.severity == 'ERROR') {
    			return 'ux-row-red';
    		}

    		return '';
    	}
	});
	
    var renderers = Ext.ux.scalr.GridViewer.columnRenderers;
	var grid = new Ext.ux.grid.livegrid.GridPanel({
        id: 'logs_trans_list_'+GRID_VERSION,
        height: 550,
        title: "Transaction details",
        store: store,
        view: view,
        bbar: new Ext.ux.grid.livegrid.Toolbar({
            view        : view,
            displayInfo : true
        }),
        
	    // Columns
        columns:[
			{header: "ID", width: 40, dataIndex: 'id', sortable: false},
			//{header: "Severity", width: 40, dataIndex: 'severity', renderer:severityRenderer, sortable: false},
			//{header: "Date", width: 40, dataIndex: 'dtadded', sortable: false},
			//{header: "Message", width: 120, dataIndex: 'message', renderer:messageRenderer, sortable: false}
		]
    });

	grid.render("maingrid-ct");
	*/
	
	

	// ---- Init grid	
	// create the Data Store
    var store = new Ext.ux.scalr.Store({
        reader: new Ext.ux.scalr.JsonReader({
            root: 'data',
            successProperty: 'success',
            errorProperty: 'error',
            totalProperty: 'total',
            id: 'id',
            fields: [
                'id','dtadded','message','severity','transactionid','farmid','message', 'sub_transactionid', 'caller'
            ]
        }),
        baseParams: {
        	sort: 'id',
        	dir: 'DESC'
        },
    	remoteSort: true,
		url: 'server/grids/system_log_trans_list.php?a=1{/literal}{$grid_query_string}{literal}',
		listeners: { dataexception: Ext.ux.dataExceptionReporter }
    });
	Ext.apply(store.baseParams, Ext.ux.parseQueryString(window.location.href));

	function severityRenderer (value, p, record) {
		if (value == 'ERROR' || value == 'FATAL')
			return value+' (<a href="syslog_view_backtrace.php?logeventid='+record.data.id+'">View backtrace</a>)';
		else
			return value;
	}

	function messageRenderer (value, p, record) {
		if (record.data.transactionid != record.data.sub_transactionid && record.data.sub_transactionid)
			return '<a href="syslog_transaction_details.php?strnid='+record.data.sub_transactionid+'&trnid='+record.data.transactionid+'">'+value+'</a>';
		else
			return value;
	}
	
    var renderers = Ext.ux.scalr.GridViewer.columnRenderers;
	var grid = new Ext.ux.scalr.GridViewer({
        renderTo: "maingrid-ct",
        id: 'logs_trans_list_'+GRID_VERSION,
        height: 550,
        title: "Transaction details",
        store: store,
        maximize: true,
        enableFilter: false,
        defaultPageSize: 50,
        
        viewConfig: { 
        	emptyText: "No logs found",
        	getRowClass: function (record, index) {
        		if (record.data.severity == 'FATAL' || record.data.severity == 'ERROR') {
        			return 'ux-row-red';
        		}

        		return '';
        	}
        },
                     
	    // Columns
        columns:[
			{header: "ID", width: 20, dataIndex: 'id', sortable: false},
			{header: "Severity", width: 20, dataIndex: 'severity', renderer:severityRenderer, sortable: false},
			{header: "Category", width: 40, dataIndex: 'caller', sortable: false},
			{header: "Date", width: 40, dataIndex: 'dtadded', sortable: false},
			{header: "Message", width: 120, dataIndex: 'message', renderer:messageRenderer, sortable: false}
		]
    });
	
    store.load();
});
{/literal}
</script>
{include file="inc/footer.tpl"}

<!-- 
   	{include file="inc/table_header.tpl" filter=0}
		<table class="Webta_Items" rules="groups" frame="box" width="100%" cellpadding="2" id="Webta_Items">
		<thead>
			<tr>
				<th>{t}ID{/t}</th>
				<th>{t}Severity{/t}</th>
				<th>{t}Date{/t}</th>
				<th>{t}Action{/t}</th>
			</tr>
		</thead>
		<tbody>
		{section name=id loop=$rows}
		<tr id='tr_{$smarty.section.id.iteration}' {if $rows[id].severity == 'FATAL' || $rows[id].severity == 'ERROR'}style="background-color:pink;"{/if}>
			<td class="Item" nowrap="nowrap" valign="top">{$rows[id].id}</td>
			<td class="Item" nowrap="nowrap" valign="top">{$rows[id].severity} {if $rows[id].severity == 'FATAL' || $rows[id].severity == 'ERROR'}(<a href="syslog_view_backtrace.php?logeventid={$rows[id].id}">View backtrace</a>){/if}</td>
			<td class="Item" nowrap="nowrap" valign="top">{$rows[id].dtadded}</td>
			<td class="Item" valign="top">{if $rows[id].transactionid != $rows[id].sub_transactionid}<a href="syslog_transaction_details.php?strnid={$rows[id].sub_transactionid}&trnid={$rows[id].transactionid}">{$rows[id].message}</a>{else}{$rows[id].message}{/if}</td>
		</tr>
		{sectionelse}
		<tr>
			<td colspan="4" align="center">{t}No log entries found{/t}</td>
		</tr>
		{/section}
		<tr>
			<td colspan="4" align="center">&nbsp;</td>
		</tr>
		</tbody>
		</table>
	{include file="inc/table_footer.tpl" colspan=9 allow_delete=0 disable_footer_line=1 add_new=0}
	<br>
 -->