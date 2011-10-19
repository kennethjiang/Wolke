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
		
		document.forms[2].submit();
    }
    
    {/literal}
</script>
{include file="inc/table_header.tpl"}
		{include file="inc/intable_header.tpl" header="Datafeed" color="Gray"}						
    	<tr>
    		<td style="padding: 3px;" colspan="2">    		
    		  <table style=" border-collapse: separate; border-spacing:3px; width:100%;" border="0" >    		      
    				<tr>
    					<td style="font-weight:bold; padding: 2px; width:10%;">Bucket</td>
    					<td>
    						<select style="width:180px;" id="buckets" name="buckets" class="text">
    						{if $buckets}
    						{foreach from=$buckets item=bucketName}	
    							<option  value="{$bucketName}"> {$bucketName}</option>	             						             				
	             			{/foreach}            				
	             			{else}
								<option value="">no buckets</option>
	             			{/if}
	             			</select>
    					</td>
    					<td></td>
    				</tr>    				
    		  </table>    	
       		</td>
    	</tr>
		{include file="inc/intable_footer.tpl" color="Gray"}  	
    	
    	{include file="inc/table_footer.tpl" button_js=1 button_js_name="Create datafeed" show_js_button=1 button_js_action="SaveParams();"}	   	
{include file="inc/footer.tpl"}


