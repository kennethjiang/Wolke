{include file="inc/header.tpl"}
<link rel="stylesheet" href="css/grids.css" type="text/css" />
<div id="maingrid-ct1" class="ux-gridviewer"></div>
	<br />
<div id="maingrid-ct2" class="ux-gridviewer"></div>
	<br/>
<div id="maingrid-ct3" class="ux-gridviewer"></div>
<script type="text/javascript">

var FarmID = '{$smarty.get.farmid}';

var regions = [
{section name=id loop=$regions}
	['{$regions[id]}','{$regions[id]}']{if !$smarty.section.id.last},{/if}
{/section}
];


//----------------------------------------------------------------- Data Stores
//-----------------------------------------------------------------------------
var region = '{$smarty.session.aws_region}';

{literal}
Ext.onReady(function () {

Ext.QuickTips.init();
	// create the Data Store for Customer gateways
    var customer_gateway_store = new Ext.ux.scalr.Store
    ({
    	reader: new Ext.ux.scalr.JsonReader(
    	{
	        root: 'data',
	        successProperty: 'success',
	        errorProperty: 'error',
	        totalProperty: 'total',
	        id: 'customer_id',	   
	        fields: 
	        [
				'customer_id', 'type', 'state', 'ipAddress', 'bgpAsn'
	        ]
    	}),
    	remoteSort: true,
		url: '/server/grids/aws_vpc_custom_gateways_list.php?a=1{/literal}{$grid_query_string}{literal}',
		listeners: { dataexception: Ext.ux.dataExceptionReporter }
    });

//------------  create the Data Store for VPN Gateways
    var vpn_gateway_store = new Ext.ux.scalr.Store
    ({
    	reader: new Ext.ux.scalr.JsonReader
    	({
	        root: 'data',
	        successProperty: 'success',
	        errorProperty: 'error',
	        totalProperty: 'total',
	        id: 'vpn_id',	   
	        fields: 
	        [
				'vpn_id', 'type', 'state', 'availabilityZone','attachmentState','vpcId'
	        ]
    	}),
    	remoteSort: true,
		url: '/server/grids/aws_vpc_vpn_gateways_list.php?a=1{/literal}{$grid_query_string}{literal}',
		listeners: { dataexception: Ext.ux.dataExceptionReporter }
    });



//------------  create the Data Store for VPN Connections 
    var vpn_connections_store = new Ext.ux.scalr.Store
    ({
    	reader: new Ext.ux.scalr.JsonReader
    	({
	        root: 'data',
	        successProperty: 'success',
	        errorProperty: 'error',
	        totalProperty: 'total',
	        id: 'conn_id',	   
	        fields: 
	        [
				'conn_id', 'type', 'state', 'vpnGatewayId', 'customerGatewayId'
	        ]
    	}),
    	remoteSort: true,
		url: '/server/grids/aws_vpc_vpn_connections_list.php?a=1{/literal}{$grid_query_string}{literal}',
		listeners: { dataexception: Ext.ux.dataExceptionReporter }
    });



//--------------------------------------------------------------- Grids Creation
//-------------------------------------------------------------------------------

    var renderers = Ext.ux.scalr.GridViewer.columnRenderers;
	var grid_customer_gateways = new Ext.ux.scalr.GridViewer(
	{
        renderTo: "maingrid-ct1",
        height: 250,
        width: '100%',
        title: "Customer gateways",
        id: 'vpc_customer_gateways_list_'+GRID_VERSION,
        store: customer_gateway_store,
        maximize: false,
        viewConfig: 
		{ 
        	emptyText: "No customer gateways were found"
		},

        enableFilter: false,
        
        tbar: 
        [ 
			{
				icon: '/images/add.png', // icons can also be specified inline
				cls: 'x-btn-icon',
				tooltip: 'Add customer gateway',
				handler: function()
				{
					document.location.href = '/aws_vpc_add_custom_gateway.php';
				}
			}
	    ],
                
        // Columns
        columns:
        [
			{header: "Customer gateway ID", width: 70, dataIndex: 'customer_id', sortable: false},
			{header: "Type", width: 70,			dataIndex: 'type', sortable: false},
			{header: "State", width: 70,		dataIndex: 'state', sortable: false},
			{header: "IP address", width: 80,	dataIndex: 'ipAddress', sortable: false},
			{header: "BGP ASN", width: 80,		dataIndex: 'bgpAsn', sortable: false}
			
		],

	
		// Row menu
    	/*rowOptionsMenu: 
        [    
			{id: "option.eventsCustomGateways",       text: 'Events log', href: ""}
     	],
     	getRowOptionVisibility: function (item, record) {

			return true;
		},
		*/
		withSelected: 
		{
			menu: 
			[
				{text: "Delete", value: "delete"}
			],
			//hiddens: {with_selected : 'c1'},
			actionName: "action_customer"
			//action: "act"
		}
		
		
		
    });
    
   grid_customer_gateways.render();
   customer_gateway_store.load();
   
//---------------------------------------------------------- VPN gateways grid 

    var grid_vpn_gateways = new Ext.ux.scalr.GridViewer(
	{
        renderTo: "maingrid-ct2",
        height: 250,
        width: '100%',
        title: "VPN gateways",
        id: 'vpc_vpn_gateways_list_'+GRID_VERSION,
        store: vpn_gateway_store ,
        maximize: false,
        viewConfig: 
		{ 
        	emptyText: "No vpn gateways were found"
		},

        enableFilter: false,
        
        tbar: 
        [ 
			 {
				icon: '/images/add.png', // icons can also be specified inline
				cls: 'x-btn-icon',
				tooltip: 'Add VPN gateway',
				handler: function()
				{
					document.location.href = '/aws_vpc_add_vpn_gateway.php';
				}
			 }
	    ],  
               
        // Columns
        columns:
        [
			{header: "VPN gateway ID", width: 5, dataIndex: 'vpn_id', sortable: false},
			{header: "Type", width: 5, dataIndex: 'type', sortable: false},
			{header: "State", width: 5, dataIndex: 'state', sortable: false},
			{header: "Availability zone", width: 5, dataIndex: 'availabilityZone', sortable: false},
			{header: "Attachment state", width: 5, dataIndex: 'attachmentState', sortable: false, hidden: true},
			{header: "VPC ID", width: 50, dataIndex: 'vpcId', sortable: false, hidden: true}
			
		],

	
		// Row menu
    	rowOptionsMenu: 
    	[   
    		{id: "option.detachVpnGateway",       text: 'Detach a VPN gateway', href: "/aws_vpc_gateways_view.php?vpcId={vpcId}&vpnId={vpn_id}&action=detach"}
			
     	],
     	getRowOptionVisibility: function (item, record) {

			return true;
		},
		withSelected: 
		{
			menu: 
			[
				{text: "Delete", value: "delete"}
			],
			//hiddens: {with_selected : 'c2'},
			actionName: "action_vpn"
			//action: "act"
		}	
		
		
    });
    
    
    grid_vpn_gateways.render();
    vpn_gateway_store.on("datachanged", function (store)
     {
		if (store.getCount() > 0) 
		{
			var cm = grid_vpn_gateways.getColumnModel();
			var isHidden = store.getAt(0).data.attachmentState == null;
			cm.setHidden(4, isHidden);
			cm.setHidden(5, isHidden);
		}
    });
    vpn_gateway_store.load();

//---------------------------------------------------------- VPN Connections grid 

    var grid_vpn_connections = new Ext.ux.scalr.GridViewer(
	{
        renderTo: "maingrid-ct3",
        height: 250,
        width: '100%',
        title: "VPN connections",
        id: 'vpc_vpn_connections_list_'+GRID_VERSION,
        store: vpn_connections_store,
        maximize: false,
        viewConfig: 
		{ 
        	emptyText: "No vpn connections were found"
		},

        enableFilter: false,
        
        tbar: 
        [ 
			 {
				icon: '/images/add.png', // icons can also be specified inline
				cls: 'x-btn-icon',
				tooltip: 'Add VPN connection',
				handler: function()
				{
					document.location.href = '/aws_vpc_add_vpn_connections.php';
				}
			 }
	    ],  
               
        // Columns
        columns:
        [
			{header: "VPN connection ID", width: 60, dataIndex: 'conn_id', sortable: false},
			{header: "Type", width: 60, dataIndex: 'type', sortable: false},
			{header: "State", width: 60, dataIndex: 'state', sortable: false},			
			{header: "VPN gateway ID", width: 60, dataIndex: 'vpnGatewayId', sortable: false},
			{header: "Customer gateway ID", width: 60, dataIndex: 'customerGatewayId', sortable: false}
			
		],
	
		// Row menu
    	rowOptionsMenu: 
    	[   
    		{id: "option.getConfig",       text: 'Get configuration', href: "/aws_vpc_vpn_connection_config.php?id={conn_id}"}	
			
     	],
     	getRowOptionVisibility: function (item, record) 
     	{

			return true;
		},
		withSelected: 
		{
			menu: 
			[
				{text: "Delete", value: "delete"}
			],
			actionName: "action_conn"
			//hiddens: {with_selected : 'c3'},
			//action: "act"
		}
		
		
		
    });
    
    
    grid_vpn_connections.render();
    vpn_connections_store.load();
    
    
	return;
});
{/literal}
</script>
{include file="inc/footer.tpl"}