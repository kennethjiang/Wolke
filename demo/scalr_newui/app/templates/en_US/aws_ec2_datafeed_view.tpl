{include file="inc/header.tpl"}
{include file="inc/table_header.tpl"}
		{include file="inc/intable_header.tpl" header="Datafeed" color="Gray"}
						
    	<tr>
    		<td style="padding: 3px;" colspan="2">
    		{if $createDatefeed === true}
			<table style=" border-collapse: separate; border-spacing:3px; width:100%;" border="0" >			   
				<tr>
					<td style="padding: 2px; width:10%;">Datafeed was not created. Please, <b>create</b> datafeed first.</td>
				</tr>
			</table>
    		{else}
    		  <table style=" border-collapse: separate; border-spacing:3px; width:100%;" border="0" >    		      
    				<tr>
    					<td style="font-weight:bold; padding: 2px; width:10%;">Bucket</td>
    					<td>{$bucket}</td>
    				</tr>
    				<tr>
    					<td style="font-weight:bold; padding: 2px; width:10%;">Prefix</td>
    					<td>{$prefix}</td>
    				</tr>
    				<tr>
    					<td style="font-weight:bold; padding: 2px; width:10%;">State</td>
    					<td>{$state}</td>
    				</tr>
    		  </table>
    		  {/if}
       		</td>
    	</tr>
		{include file="inc/intable_footer.tpl" color="Gray"} 
    	{if $createDatefeed == true}
    		{include file="inc/table_footer.tpl" button2=1 button2_name="Create new datafeed"}	
    	{else}
			{include file="inc/table_footer.tpl" button2=1 button2_name="Delete datafeed"}	
		{/if}
{include file="inc/footer.tpl"}