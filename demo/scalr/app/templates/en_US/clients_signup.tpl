{include file="inc/signup_header.tpl"}
    <div id="signupForm" style="width:480px; margin-left:auto; margin-right:auto;">
	{include file="inc/table_header.tpl"}
		{include file="inc/intable_header.tpl" header="Account information" color="Gray"}
		{if $err != ''}
		  <tr>
    		<td colspan=2 class="error">
    			{section name="id" loop=$err}
					{$err[id]}<br/>
				{/section}
    		</td>
    	  </tr>
		{/if}
    	<tr>
    		<td width="20%">E-mail:</td>
    		<td><input type="text" class="text" name="email" value="{$email}" /></td>
    	</tr>
    	<tr>
    		<td width="20%">Password:</td>
    		<td><input type="password" class="text" name="password" value="{$password}" /></td>
    	</tr>
    	<tr>
    		<td width="20%">Confirm password:</td>
    		<td><input type="password" class="text" name="password2" value="{$password}" /></td>
    	</tr>
    	<tr>
    		<td width="20%">Full name:</td>
    		<td><input type="text" class="text" name="name" value="{$name}" /></td>
    	</tr>
    	<tr>
    		<td width="20%">Organization:</td>
    		<td><input type="text" class="text" name="org" value="{$org}" /></td>
    	</tr>
    	<tr>
    		<td width="20%">Country:</td>
    		<td>
    			<select  id="country" name="country" class="text">
				{section name="id" loop=$countries}
					<option value="{$countries[id].code}" {if $country|strtolower == $countries[id].code|strtolower || (!$country|strtolower && $countries[id].code|strtolower == 'us')}selected{/if}>{$countries[id].name}</option>
				{/section}
				</select>
    		</td>
    	</tr>
    	<tr>
    		<td width="20%">State / Region:</td>
    		<td><input type="text" class="text" name="state" value="{$state}" /></td>
    	</tr>
    	<tr>
    		<td width="20%">City:</td>
    		<td><input type="text" class="text" name="city" value="{$city}" /></td>
    	</tr>
    	<tr>
    		<td width="20%">Postal code:</td>
    		<td><input type="text" class="text" name="zipcode" value="{$zipcode}" /></td>
    	</tr>
    	<tr>
    		<td width="20%">Address 1:</td>
    		<td><input type="text" class="text" name="address1" value="{$address1}" /></td>
    	</tr>
    	<tr>
    		<td width="20%">Address 2:</td>
    		<td><input type="text" class="text" name="address2" value="{$address2}" /></td>
    	</tr>
    	<tr>
    		<td width="20%">Phone:</td>
    		<td><input type="text" class="text" name="phone" value="{$phone}" /></td>
    	</tr>
    	<tr>
    		<td width="20%">Fax:</td>
    		<td><input type="text" class="text" name="fax" value="{$fax}" /></td>
    	</tr>
        {include file="inc/intable_footer.tpl" color="Gray"}
	{include file="inc/table_footer.tpl" edit_page=1}
	</div>
{include file="inc/signup_footer.tpl"}