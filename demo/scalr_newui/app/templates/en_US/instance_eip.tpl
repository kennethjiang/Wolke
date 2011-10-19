{include file="inc/header.tpl"}
{include file="inc/table_header.tpl"}
	{if $task == "assign"}
    {include file="inc/intable_header.tpl" header="Assign options" color="Gray" intable_first_column_width="25%"}
    <tr>
		<td width="500"><input style="vertical-align:middle;" {if $ips|@count == 0}disabled{else}checked{/if} type="radio" name="assigntype" value="1"> Assign already allocated elastic IP:</td>
		<td colspan="6">
		  <select name="eip" {if $ips|@count == 0}disabled{/if} class="text" style="vertical-align:middle;">
		  {section name=id loop=$ips}
		      <option value="{$ips[id]}">{$ips[id]}</option>
		  {sectionelse}
		  	  <option value="">No spare elastic IPs found</option>
		  {/section}
		  </select>
		</td>
	</tr>
	<tr>
		<td width="500"><input {if $ips|@count == 0}checked{/if} type="radio" name="assigntype" value="2"> Allocate and assign new elastic IP</td>
		<td colspan="6">
		</td>
	</tr>
    {include file="inc/intable_footer.tpl" color="Gray"}
    {else}
    {include file="inc/intable_header.tpl" header="Confirmation" color="Gray"}
    <tr>
		<td colspan="6">The instance will become totally unavailable for few minutes. Are you sure?</td>
	</tr>
    {include file="inc/intable_footer.tpl" color="Gray"}
    
    {include file="inc/intable_header.tpl" header="Options" color="Gray"}
    <tr>
		<td width="400"><input style="vertical-align:middle;" checked type="checkbox" name="releaseaddress" value="1"> Release (delete) {$ipaddr} after unassigning it</td>
		<td colspan="6"></td>
	</tr>
    {include file="inc/intable_footer.tpl" color="Gray"}
    {/if}
    <input type="hidden" name="iid" value="{$iid}">
    <input type="hidden" name="task" value="{$task}">
    
    {if $task == "assign"}
		{include file="inc/table_footer.tpl" button2=1 button2_name="Next"}
	{else}
		{include file="inc/table_footer.tpl" button2=1 button2_name="Yes, unassign elastic ip now" cancel_btn=1}
	{/if}
{include file="inc/footer.tpl"}