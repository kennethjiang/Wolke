{include file="inc/header.tpl"}
<script language="javascript" type="text/javascript">
{literal}
	function CheckPrAdd(tp, id, val)
	{
		if (val == 'MX')
		{
			Ext.get(tp+"_"+id).dom.style.display = '';
			Ext.get(tp+"_"+id).dom.value = '10';
		}
		else
		{
			Ext.get(tp+"_"+id).dom.style.display = 'none';
			Ext.get(tp+"_"+id).dom.value = '';
		}
	}
{/literal}
</script>
{include file="inc/table_header.tpl"}
<table cellpadding="4" cellspacing="0" width="100%" border="0">
	<tr>
		<td class="th" width="300">Domain</td>
		<td class="th" width="150">TTL</td>
		<td class="th" width="50">&nbsp;</td>
		<td class="th" width="150">Record Type</td>
		<td class="th" colspan="2">Record value</td>
	</tr>
	{section name=id loop=$records}
	<tr>
		<td><input {if $records[id].issystem == 1}disabled{/if} type="text" class="text" name="records[{$records[id].id}][name]" size=30 value="{$records[id].name}"></td>
		<td><input {if $records[id].issystem == 1}disabled{/if} type="text" class="text" name="records[{$records[id].id}][ttl]" size=6 value="{$records[id].ttl}"></td>
		<td>IN</td>
		<td><select {if $records[id].issystem == 1}disabled{/if} class="text" name="records[{$records[id].id}][type]" onchange="CheckPrAdd('ed', '{$records[id].id}', this.value)">
				<option {if $records[id].type == "A"}selected{/if} value="A">A</option>
				<option {if $records[id].type == "CNAME"}selected{/if} value="CNAME">CNAME</option>
				<option {if $records[id].type == "MX"}selected{/if} value="MX">MX</option>
				<option {if $records[id].type == "NS"}selected{/if} value="NS">NS</option>
				<option {if $records[id].type == "TXT"}selected{/if} value="TXT">TXT</option>
			</select>
		</td>
		<td colspan="2"> <input {if $records[id].issystem == 1}disabled{/if} class="text" id="ed_{$records[id].id}" style="display:{if $records[id].type != "MX"}none{/if};" type=text name="records[{$records[id].id}][priority]" size=5 value="{$records[id].priority}"> <input {if $records[id].issystem == 1}disabled{/if} class="text" type=text name="records[{$records[id].id}][value]" size=30 value="{$records[id].value}"></td>
	</tr>
	{sectionelse}
	<tr>
		<td colspan=6 align="center">No default DNS records found</td>
	</tr>
	{/section}
	<tr>
		<td colspan=6>&nbsp;</td>
	</tr>
	<tr>
		<td colspan=6 class="th">Add New Entries Below this Line</td>
	</tr>
	{section name=id loop=$add}
	<tr>
		<td><input type="text" class="text" name="records[{$add[id]}][name]" size=30></td>
		<td><input type="text" class="text" name="records[{$add[id]}][ttl]" size=6 value="14400"></td>
		<td>IN</td>
		<td><select class="text" name="records[{$add[id]}][type]" onchange="CheckPrAdd('ad', '{$add[id]}', this.value)">
				<option selected value="A">A</option>
				<option value="CNAME">CNAME</option>
				<option value="MX">MX</option>
				<option value="NS">NS</option>
				<option value="TXT">TXT</option>
			</select>
		</td>
		<td colspan="2"> <input id="ad_{$add[id]}" size="5" style="display:none;" type="text" class="text" name="records[{$add[id]}][priority]" value="10" size=30> <input type="text" class="text" name="records[{$add[id]}][value]" size=30></td>
	</tr>
	{/section}
</table>
{include file="inc/table_footer.tpl" edit_page=1}
{include file="inc/footer.tpl"}