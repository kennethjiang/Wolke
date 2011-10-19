<div style="float:left;">
	<table border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td width="7"><div class="TableHeaderLeft"></div></td>
			<td><div class="TableHeaderCenter"></div></td>
			<td><div class="TableHeaderCenter"></div></td>
			<td width="7"><div class="TableHeaderRight"></div></td>
		</tr>
		<tr bgcolor="#C3D9FF">
			<td width="7" class="TableHeaderCenter"></td>
			<td nowrap style="padding:0px;height:26px;">
				{if !$show_region_filter_title}{t}Region{/t}{else}{$show_region_filter_title}{/if}:
				<select name="region" id="region" style="vertical-align:middle;" onchange="document.location.href = '{$smarty.server.PHP_SELF}?region='+this.value;">
					{section name=id loop=$regions}
						<option {if $smarty.session.aws_region == $regions[id]}selected{/if} value="{$regions[id]}">{$regions[id]}</option>
					{/section}
				</select>
			</td>
			<td align="left" nowrap>&nbsp;</td>
			<td width="7" class="TableHeaderCenter"></td>
		</tr>
	</table>
</div>
<div style="float:left;">
	&nbsp;&nbsp;
</div>