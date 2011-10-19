{include file="inc/header.tpl"}
    {include file="inc/table_header.tpl" table_header_text="S3 buckets" nofilter='1'}
    <table class="Webta_Items" rules="groups" frame="box" cellpadding="4" id="Webta_Items_">
	<thead>
		<tr>
			<th>Bucket name</th>
			<td width="1%" nowrap><input type="checkbox" name="checkbox" value="checkbox" onClick="webtacp.checkall('buckets[]')"></td>
		</tr>
	</thead>
	<tbody>
	{section name=id loop=$buckets}
	<tr id='tr_{$smarty.section.id.iteration}'>
		<td class="Item" width="400" valign="top">{$buckets[id].name}</td>
		<td class="ItemDelete" valign="top">
			<span>
				<input type="checkbox" id="buckets[]" name="buckets[]" value="{$buckets[id].name}">
			</span>
		</td>
	</tr>
	{sectionelse}
	<tr>
		<td colspan="2" align="center">No unused buckets found</td>
	</tr>
	{/section}
	<tr>
		<td colspan="1" align="center">&nbsp;</td>
		<td class="ItemDelete" valign="top">&nbsp;</td>
	</tr>
	</tbody>
	</table>
	{include file="inc/table_footer.tpl" colspan=9 disable_footer_line=1}
	
	{include file="inc/table_header.tpl" show_region_filter=1 show_region_filter_title="EC2 keypair in"}
    <table class="Webta_Items" rules="groups" frame="box" cellpadding="4" id="Webta_Items_">
	<thead>
		<tr>
			<th>KeyPair</th>
			<td width="1%" nowrap><input type="checkbox" name="checkbox" value="checkbox" onClick="webtacp.checkall('keypairs[]')"></td>
		</tr>
	</thead>
	<tbody>
	{section name=id loop=$keypairs}
	<tr id='tr_{$smarty.section.id.iteration}'>
		<td class="Item" width="400" valign="top">{$keypairs[id].name}</td>
		<td class="ItemDelete" valign="top">
			<span>
				<input type="checkbox" id="buckets[]" name="keypairs[]" value="{$keypairs[id].name}">
			</span>
		</td>
	</tr>
	{sectionelse}
	<tr>
		<td colspan="2" align="center">No unused keypairs found</td>
	</tr>
	{/section}
	<tr>
		<td colspan="1" align="center">&nbsp;</td>
		<td class="ItemDelete" valign="top">&nbsp;</td>
	</tr>
	</tbody>
	</table>
	{include file="inc/table_footer.tpl" colspan=9 disable_footer_line=1}	
	<br>
	{include file="inc/table_header.tpl" nofilter='1'}
	{include file="inc/table_footer.tpl" colspan=9 button2=1 button2_name="Remove selected items"}
{include file="inc/footer.tpl"}