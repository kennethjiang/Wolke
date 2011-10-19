{include file="inc/header.tpl"}
    
	<link rel="stylesheet" type="text/css" href="/js/highlight/styles/sunburst.css" />

	<style type="text/css">
		{literal}
		pre code
		{
			width:800px;
			height:290px;
		}
		pre .xml .tag .title 
		{
		
			color:#84CDe4;				
		}				
		{/literal}
	</style>

    <script language="Javascript">
    {literal}
        
    function SaveParams()
    {    	 	
		var elems = Ext.get('footer_button_table').select('.btn');
		elems.each(function(item){
			item.disabled = true;
		});
		
		Ext.get('btn_hidden_field').dom.name = this.name;
		Ext.get('btn_hidden_field').dom.value = this.value;
		
		document.forms[1].submit();
    }
    
    {/literal}
    </script>
{include file="inc/table_header.tpl"}
		{include file="inc/intable_header.tpl" header="VPN connection configuration" color="Gray"}
		
    	<tr>
    		<td colspan="2">
    		  <table cellpadding="5" cellspacing="15" width="100%" border="0" >    		      
    		      <tr>
    		      	  <td style="font-weight:bold; width:20%;">VPN connection ID</td>
    		      	  <td>{$vpnConnection.vpnConnectionId}
    		      	  </td>    		          
    		      </tr>   
    		      <tr>
    		      	  <td style="font-weight:bold;">State</td>
    		      	  <td>{$vpnConnection.state}
    		      	  </td>    		      	  
    		      </tr>  		      
    		     <tr>
    		      	  <td style="font-weight:bold;">Type</td>
    		      	  <td>{$vpnConnection.type}
    		      	  </td>    		      	  
    		      </tr> 
    		      <tr>
    		      	  <td style="font-weight:bold;">Customer gateway ID</td>
    		      	  <td>{$vpnConnection.customerGatewayId}
    		      	  </td>    		      	  
    		      </tr> 
    		      <tr>
    		      	  <td style="font-weight:bold;">VPN gateway ID</td>
    		      	  <td>{$vpnConnection.vpnGatewayId}
    		      	  </td>    		      	  
    		      </tr>    		     
    		  </table>
       		</td>
    	</tr>
    	<tr>
    		<td>
    			<div id="script_source_div" style="width:100%; height:300px;">    			
					<div id="script_source_container" style="height:300px;overflow:hidden;">
						<pre style="margin:0px;"><code>{$customerGatewayConfiguration}</code></pre>
					</div>
				</div>
    		</td>
    	</tr>
    	
    	
		{include file="inc/intable_footer.tpl" color="Gray"} 	
		{include file="inc/table_footer.tpl" }	
{include file="inc/footer.tpl"}