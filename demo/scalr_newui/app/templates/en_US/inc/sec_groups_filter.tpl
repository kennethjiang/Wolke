<div style="float:left;">
	<table border="0" cellpadding="0" cellspacing="0" style="height:26px;">
		<tr>
			<td width="7"><div class="TableHeaderLeft"></div></td>
			<td><div class="TableHeaderCenter"></div></td>
			<td><div class="TableHeaderCenter"></div></td>
			<td width="7"><div class="TableHeaderRight"></div></td>
		</tr>
		<tr bgcolor="#C3D9FF">
			<td width="7" class="TableHeaderCenter"></td>
			<td nowrap style="height:26px;" colspan="2">
				<input {if $smarty.session.sg_show_all}checked{/if} onclick="document.location='sec_groups_view.php?show_all='+this.checked;" style="vertical-align:middle;" type="checkbox" name="show_all_groups" value="1" /> Show all security groups
				&nbsp;&nbsp;
			</td>
			<td width="7" class="TableHeaderCenter"></td>
		</tr>
	</table>
</div>