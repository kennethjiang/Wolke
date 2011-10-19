{if $DataForm}
	{assign var=fields value=$DataForm->ListFields()}
	{foreach from=$fields key=key item=field}
		    {if ($field->FieldType == 'text')}
			<tr valign="top">
				<td style="padding-left:20px;">{$field->Title}: {if $field->IsRequired}*{/if}</td>
				<td>
					<input id="{$elem_id}" type="text" size="10" class="text" name="{$field_prefix}{$field->Name}{$field_suffix}" value="{$field->Value}"/>
					{if $field->Hint}
						<span class="Webta_Ihelp">{$field->Hint}</span>
					{/if}				
				</td>
			</tr>
			{elseif ($field->FieldType == 'textarea')}
			<tr valign="top">
				<td style="padding-left:20px;">{$field->Title}: {if $field->IsRequired}*{/if}</td>
				<td>
					<textarea id="{$elem_id}" cols="40" rows="8" class="text" name="{$field_prefix}{$field->Name}{$field_suffix}">{$field->Value}</textarea>
					{if $field->Hint}
						<span class="Webta_Ihelp">{$field->Hint}</span>
					{/if}
				</td>
			</tr>
			{elseif ($field->FieldType == 'checkbox')}
			<tr valign="top">
				<td style="padding-left:20px;">{$field->Title}: {if $field->IsRequired}*{/if}</td>
				<td>
					<input id="{$elem_id}" type="checkbox" {if $field->Value == 1}checked{/if} name="{$field_prefix}{$field->Name}{$field_suffix}" value="1"/>
					{if $field->Hint}
						<span class="Webta_Ihelp">{$field->Hint}</span>
					{/if}
				</td>
			</tr>
			{elseif ($field->FieldType == 'separator')}
			<tr valign="top">
				<td colspan="2"><br />{$field->Title}<br /><br /></td>
			</tr>
			{elseif $field->FieldType == 'select'}
			<tr valign="top">
				<td style="padding-left:20px;">{$field->Title}: {if $field->IsRequired}*{/if}</td>
				<td>
					{assign var=values value=$field->Options}
					{if $field->AllowMultipleChoice}
						{foreach from=$values key=vkey item=vfield}
						<div style="float:left;padding-right:5px;">
							<input id="{$elem_id}" {if $vkey|@in_array:$field->Value}checked{/if} style="vertical-align:middle;" type="checkbox" name="{$field_prefix}{$field->Name}[{$vkey}]{$field_suffix}" value="1"> {$vfield}
						</div> 
						{/foreach}
					{else}
					<select id="{$elem_id}" name="{$field_prefix}{$field->Name}{$field_suffix}">
						{foreach from=$values key=vkey item=vfield}
							<option {if $field->Value == $vkey}selected{/if} value="{$vkey}">{$vfield}</option>
						{/foreach}
					</select>
					{/if}
				</td>
			</tr>
			{/if}
	{/foreach}
{/if}