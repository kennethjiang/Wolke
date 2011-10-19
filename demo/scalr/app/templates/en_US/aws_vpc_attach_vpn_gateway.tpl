{include file="inc/header.tpl"}
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
		{include file="inc/intable_header.tpl" header="VPN gateway " color="Gray"}
		
    	<tr>
    		<td colspan="2">
    		  <table cellpadding="5" cellspacing="15" width="100%" border="0" > 
    		      <tr>
					<td style="font-weight:bold; width:20%">VPN gateway ID</td>    	
					<td> 
						<select style="width:180px;" id="aws_vpc_vpn_gateways" name="aws_vpc_vpn_gateways" class="text">
             				{section name=gateway_id loop=$vpnGatewayId}	             					
             					<option  value="{$vpnGatewayId[gateway_id]}"> {$vpnGatewayId[gateway_id]}</option>	             					
             				{/section}
             		</select>
					</td>
    		      </tr>    		     
    		  </table>
       		</td>
    	</tr>
    	
    	
		{include file="inc/intable_footer.tpl" color="Gray"} 
	{include file="inc/table_footer.tpl" button_js=1 button_js_name="Attach VPN gateway" show_js_button=1 button_js_action="SaveParams();"}	
{include file="inc/footer.tpl"}