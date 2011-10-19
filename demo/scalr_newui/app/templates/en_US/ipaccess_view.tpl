{include file="inc/header.tpl"}
	{include file="inc/table_header.tpl"}
	<table class="Webta_Items" rules="groups" frame="box" width="100%" cellpadding="2" id="Webta_Items">
	<thead>
	<tr>
		<th>Allowed host</th>
		<th>Comments</th>
		<th nowrap width="1%">Edit</th>
		<th width="1%" nowrap><input type="checkbox" name="checkbox" value="checkbox" onClick="checkall()"></th>
	</tr>
	</thead>
	<tbody>
    	{section name=id loop=$rows}
    	<tr id='tr_{$smarty.section.id.iteration}'>
    		<td class="Item" valign="top">{$rows[id].ipaddress}</td>
    		<td class="Item" valign="top">{$rows[id].comment}</td>
    		<td class="ItemEdit" valign="top"><a href="ipaccess_add.php?id={$rows[id].id}">Edit</a></td>
    		<td class="ItemDelete">
    			<span>
    				<input type="checkbox" id="delete[]" name="delete[]" value="{$rows[id].id}">
    			</span>
    		</td>
    	</tr>
    	{sectionelse}
    	<tr>
    		<td colspan="7" align="center">No Alowed IPs found</td>
    	</tr>
    	{/section}
	<tr>
		<td colspan="2" align="center">&nbsp;</td>
		<td class="ItemEdit" valign="top">&nbsp;</td>
		<td class="ItemDelete" valign="top">&nbsp;</td>
	</tr>
	</tbody>
	</table>
	{include file="inc/table_footer.tpl" colspan=9 page_data_options_add=1}
{include file="inc/footer.tpl"}