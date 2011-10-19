{include file="inc/header.tpl"}
	{include file="inc/table_header.tpl"}
    	{include file="inc/intable_header.tpl" intable_first_column_width="10%" header="Associate Elastic IP with Instance" color="Gray"}
        <tr>
    		<td>IP address:</td>
    		<td>
				{$ip}
				<input type="hidden" name="ip" value="{$ip}" />
    		</td>
    	</tr>
        <tr>
    		<td>Server:</td>
    		<td>
    			{if !$server_id}
	    			<select name="server_id" id="server_id" class="text">
	    			{section name=iid loop=$servers}
						<option {if $server_id == $instances[iid].server_id}selected{/if} value="{$servers[iid].server_id}">{$servers[iid].server_id} ({$servers[iid].instance_id}) on '{$servers[iid].farm_name} / {$servers[iid].role_name}'</option>
					{/section}
					</select>
				{else}
					<input type="hidden" name="server_id" value="{$server_id}" />
				{/if}
    		</td>
    	</tr>
    	{include file="inc/intable_footer.tpl" color="Gray"}
    	<input type="hidden" name="task" value="associate">
	{include file="inc/table_footer.tpl" button2=1 button2_name="Continue" cancel_btn=1}
{include file="inc/footer.tpl"}