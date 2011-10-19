{include file="inc/header.tpl"}
<link rel="stylesheet" href="css/grids.css" type="text/css" />
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

	var farms_store = new Ext.data.SimpleStore({
	    fields: ['value', 'text'],
	    data : {/literal}{$farms}{literal}
	});

	
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
		}, new Ext.form.ComboBox({
			id: 'farmid',
			allowBlank: true,
			editable: false, 
			valueField:'value',
			displayField:'text',
	        store: farms_store,
	        fieldLabel: 'Farm',
	        typeAhead: true,
	        mode: 'local',
	        triggerAction: 'all',
	        selectOnFocus:false
	    }),{
			xtype: 'datefield',
			width: 230,
			name: 'dt',
			fieldLabel: 'Date'
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
		var farmid = searchPanel.getForm().findField('farmid').value;	
		store.baseParams.farmid = (farmid) ? farmid : '';
	
		store.load();
	}

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
                'id','dtadded','message','severity','transactionid','farmid','warns','errors','action'
            ]
        }),
        baseParams: {
        	sort: 'id',
        	dir: 'DESC'
        },
    	remoteSort: true,
		url: 'server/grids/system_log_list.php?a=1{/literal}{$grid_query_string}{literal}',
		listeners: { dataexception: Ext.ux.dataExceptionReporter }
    });
	Ext.apply(store.baseParams, Ext.ux.parseQueryString(window.location.href));

	function transRenderer (value, p, record) {
		return '<a href="syslog_transaction_details.php?trnid='+value+'">View log entries</a>';
	}
	
    var renderers = Ext.ux.scalr.GridViewer.columnRenderers;
	var grid = new Ext.ux.scalr.GridViewer({
        renderTo: "maingrid-ct",
        id: 'logs_list_'+GRID_VERSION,
        height: 550,
        title: "System Log {/literal}({$table_title_text}){literal}",
        store: store,
        maximize: true,
        enableFilter: false,
        viewConfig: { 
        	emptyText: "No logs found",
        	getRowClass: function (record, index) {
        		if (record.data.errors > 0) {
        			return 'ux-row-red';
        		}

        		return '';
        	}
        },
                     
	    // Columns
        columns:[
			{header: "Date", width: 40, dataIndex: 'dtadded', sortable: false},
			{header: "First log entry", width: 120, dataIndex: 'action', sortable: false},
			{header: "Warnings", width: 20, dataIndex: 'warns', sortable: false, align:'center'},
			{header: "Errors", width: 20, dataIndex: 'errors', sortable: false, align:'center'},
			{header: "Details", width: 30, dataIndex: 'transactionid', renderer:transRenderer, sortable: false}
		]
    });
	
    store.load();
});
{/literal}
</script>
{include file="inc/footer.tpl"}