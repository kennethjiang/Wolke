{include file="inc/header.tpl"}
	<style>
	{literal}
	.stats_row
	{
		padding:3px;
	}
	{/literal}
	</style>
	{assign var=start_date value=$stats.start_date}
	{assign var=end_date value=$stats.end_date}
	{include file="inc/table_header.tpl" table_header_text="$start_date - $end_date" nofilter=1}
	{include file="inc/intable_header.tpl" header="Bandwidth usage" color="Gray"}
	<tr>
		<td colspan="2">
			<div class="stats_row stats_cell1" style="">
				<div style="width:150px;float:left;">Inbound:</div>
				<div style="width:150px;float:left;">{$stats.bw_in}</div>
			</div>
			<div style="clear:both;"></div>
			<div class="stats_row stats_cell2">
				<div style="width:150px;float:left;">Outbound:</div>
				<div style="width:150px;float:left;">{$stats.bw_out}</div>
			</div>
			<div style="clear:both;"></div>
			<div class="stats_row stats_cell1">
				<div style="width:150px;float:left;">Total:</div>
				<div style="width:150px;float:left;">{$stats.bw_total}</div>
			</div>
		</td>
	</tr>
	{include file="inc/intable_footer.tpl" color="Gray"}
	
	{include file="inc/intable_header.tpl" header="Instances usage" color="Gray"}
	<tr>
		<td colspan="2">
			<table cellpadding="4" width="400">
				<tr>
					<td>m1.small:</td>
					<td align="left">{$stats.m1_small} hours</td>
					<td align="right">${$stats.m1_small_cost|string_format:"%.2f"}</td>
				</tr>
				<tr>
					<td>m1.large:</td>
					<td align="left">{$stats.m1_large} hours</td>
					<td align="right">${$stats.m1_large_cost|string_format:"%.2f"}</td>
				</tr>
				<tr>
					<td>m1.xlarge:</td>
					<td align="left">{$stats.m1_xlarge} hours</td>
					<td align="right">${$stats.m1_xlarge_cost|string_format:"%.2f"}</td>
				</tr>
				<tr>
					<td>c1.medium:</td>
					<td align="left">{$stats.c1_medium} hours</td>
					<td align="right">${$stats.c1_medium_cost|string_format:"%.2f"}</td>
				</tr>
				<tr>
					<td>m1.xlarge:</td>
					<td align="left">{$stats.c1_xlarge} hours</td>
					<td align="right">${$stats.c1_xlarge_cost|string_format:"%.2f"}</td>
				</tr>
				<tr>
					<td colspan="3"><hr size="1" /></td>
				</tr>
				<tr>
					<td>Total:</td>
					<td align="left">{$stats.total} hours</td>
					<td align="right">${$stats.total_cost|string_format:"%.2f"}</td>
				</tr>
			</table>
		</td>
	</tr>
	{include file="inc/intable_footer.tpl" color="Gray"}
	{include file="inc/table_footer.tpl" colspan=9 disable_footer_line=1}
{include file="inc/footer.tpl"}