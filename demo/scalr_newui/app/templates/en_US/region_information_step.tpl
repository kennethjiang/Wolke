{include file="inc/header.tpl"}
{include file="inc/table_header.tpl"}
    {include file="inc/intable_header.tpl" header="Step 1 - Location information" color="Gray"}
	<tr>
		<td width="15%">Location:</td>
		<td colspan="6">
			<select name="region" id="region" style="vertical-align:middle;">
				{foreach from=$regions name=id key=key item=item}
					<option {if $region == $key}selected{/if} value="{$key}">{$item}</option>
				{/foreach}
			</select>
		</td>
	</tr>
{include file="inc/intable_footer.tpl" color="Gray"}
{include file="inc/table_footer.tpl" button2=1 button2_name='Next'}
{include file="inc/footer.tpl"}