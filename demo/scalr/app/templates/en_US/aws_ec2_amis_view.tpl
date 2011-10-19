{include file="inc/header.tpl"}
<link rel="stylesheet" href="css/grids.css" type="text/css" />
<div id="maingrid-ct" class="ux-gridviewer"></div>
<script type="text/javascript">

var FarmID = '{$smarty.get.farmid}';

/*
var regions = [
{section name=id loop=$regions}
	['{$regions[id]}','{$regions[id]}']{if !$smarty.section.id.last},{/if}
{/section}
];
*/
var region = '{$smarty.session.aws_region}';

var regions = [
{foreach from=$regions name=id key=key item=item}
	['{$key}','{$item}']{if !$smarty.foreach.id.last},{/if}
{/foreach}
];

var region = '{$smarty.session.aws_region}';

{literal}
Ext.onReady(function () {

Ext.QuickTips.init();
	// create the Data Store
    var store = new Ext.ux.scalr.Store({
    	reader: new Ext.ux.scalr.JsonReader({
	        root: 'data',
	        successProperty: 'success',
	        errorProperty: 'error',
	        totalProperty: 'total',
	        id: 'imageId',	   
	        fields: [
				'imageId', 'imageState', 'imageOwnerId', 'isPublic', 'architecture', 'imageType', 'rootDeviceType'
	        ]
    	}),
    	remoteSort: false,
		url: '/server/grids/aws_ec2_amis_list.php?a=1{/literal}{$grid_query_string}{literal}&ownerFilter=my',
		listeners: { dataexception: Ext.ux.dataExceptionReporter }
    });

		
    var renderers = Ext.ux.scalr.GridViewer.columnRenderers;
	var grid = new Ext.ux.scalr.GridViewer(
	{
        renderTo: "maingrid-ct",
        height: 500,
        title: "AMIs for spot instances",
        id: 'ec2_amis_view_'+GRID_VERSION,
        store: store,
        maximize: true,
        viewConfig: 
		{ 
        	emptyText: "No amies were found"
		},

        enableFilter: true,		
        tbar: [{text: 'Location:'}, new Ext.form.ComboBox({
					allowBlank: false,
					editable: false, 
			        store: regions,
			        value: region,
			        displayField:'state',
			        typeAhead: false,
			        mode: 'local',
			        triggerAction: 'all',
			        selectOnFocus:false,
			        width:100,
			        listeners:
				        {	select:function(combo, record, index){
			        		store.baseParams.region = combo.getValue(); 
			        		store.load();
			        	}}
			    	}),
			    'Owner:', 
				new Ext.form.ComboBox
				({
					allowBlank: false,
					editable: false, 
					 store: new Ext.data.ArrayStore
						({
							id: 0,
							fields: ['id','title'],
							data: [['all','All'],['my', 'My AMIs'],['amazon', 'Amazon\'s']]
						}),
					value: 'My AMIs',
					valueField: 'id',
					displayField: 'title',				
					typeAhead: false,
					mode: 'local',
					triggerAction: 'all',
					selectOnFocus:false,
					width:100,	
					listeners:
						{select:function(combo, record, index)
							{
			        			store.baseParams.ownerFilter = combo.getValue();	
			        			store.load();
			        		}
        				}
				})
				],
				
	    
                
        // Columns
        columns:
        [
			{header: "AMI ID",		 width: 50, dataIndex: 'imageId',		sortable: false},
			{header: "State",		 width: 50, dataIndex: 'imageState',	sortable: false},
			{header: "Owner",		 width: 50, dataIndex: 'imageOwnerId',	sortable: false},			
			{header: "Architecture", width: 50, dataIndex: 'architecture',	sortable: false},
			{header: "Type",		 width: 50, dataIndex: 'imageType',		sortable: false},			
			{header: "Device Type",	 width: 50, dataIndex: 'rootDeviceType',sortable: false}
			
		],

	
		// Row menu
    	rowOptionsMenu: 
        [      	             	
			{id: "option.SpotRequest",       text: 'Create spot instance request', 		href: "/aws_ec2_spotrequest_add.php?id={imageId}&arch={architecture}"}
			
     	],
     	getRowOptionVisibility: function (item, record) {

			return true;
		}
		
    });
    
    grid.render();
    store.load();

	return;
});
{/literal}
</script>
{include file="inc/footer.tpl"}