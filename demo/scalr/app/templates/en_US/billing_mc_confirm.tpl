{include file="inc/header.tpl"}
	{include file="inc/table_header.tpl"}
		{include file="inc/intable_header.tpl" header="Confirmation" color="Gray"}
		<tr>
			<td colspan="2">Your current plan is {$package.name} ( ${$package.cost} / month). 
				Are you sure want to upgrade to Mission Critical edition ( $399.00 / month)?
				
				<input type="hidden" name="action" value="upgrade_mc" />
				<input type="hidden" name="confirm" value="1" />
			</td>
		</tr>
	{include file="inc/table_footer.tpl" button2=1 button2_name="Continue" cancel_btn=1}
{include file="inc/footer.tpl"}