{include file="inc/header.tpl"}
	{include file="inc/table_header.tpl"}
	<table class="Webta_Items" rules="groups" frame="box" width="100%" cellpadding="2" id="Webta_Items">
	<thead>
		<tr>
			<th></th>
			<th colspan="3" align="center" style="text-align:center;">带宽使用</th>
			<th colspan="5" align="center" style="text-align:center;">服务器小时数</th>
		</tr>
		<tr>
			<th>日期</th>
			<th>Inbound</th>
			<th>Outbound</th>
			<th>总计</th>
			<th>m1.small</th>
			<th>m1.large</th>
			<th>m1.xlarge</th>
			<th>c1.medium</th>
			<th>c1.xlarge</th>
		</tr>
	</thead>
	<tbody>
	{section name=id loop=$rows}
	<tr bgcolor="#F9F9F9">
		<td class="Item" valign="top"><a href="farm_usage_ext.php?farmid={$rows[id].farmid}&month={$rows[id].month}&year={$rows[id].year}">{$rows[id].date}</a></td>
		<td class="Item" valign="top">{$rows[id].bw_in}</td>
		<td class="Item" valign="top">{$rows[id].bw_out}</td>
		<td class="Item" valign="top">{$rows[id].bw_total}</td>
		<td class="Item" valign="top">{$rows[id].m1_small|string_format:"%.1f"}</td>
		<td class="Item" valign="top">{$rows[id].m1_large|string_format:"%.1f"}</td>
		<td class="Item" valign="top">{$rows[id].m1_xlarge|string_format:"%.1f"}</td>
		<td class="Item" valign="top">{$rows[id].c1_medium|string_format:"%.1f"}</td>
		<td class="Item" valign="top">{$rows[id].c1_xlarge|string_format:"%.1f"}</td>
	</tr>
	{sectionelse}
	<tr>
		<td colspan="9" align="center">尚未获得统计数据</td>
	</tr>
	{/section}
	<tr>
		<td colspan="9" align="center">&nbsp;</td>
	</tr>
	</tbody>
	</table>
	{include file="inc/table_footer.tpl" colspan=9 disable_footer_line=1}
{include file="inc/footer.tpl"}