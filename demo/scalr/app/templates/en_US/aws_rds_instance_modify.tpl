
{include file="inc/header.tpl"}
 
	{include file="inc/table_header.tpl"}
    	{include file="inc/intable_header.tpl" intable_first_column_width="15%" header="General information" color="Gray"}
        <tr>
    		<td>Identifier:</td>
    		<td>
    			<input type="text" class="text" name="DBInstanceIdentifier" disabled value="{$instance->DBInstanceIdentifier}" />
    			<input type="hidden" name="name" value="{$instance->DBInstanceIdentifier}" />
    		</td>
    	</tr>
    	<tr>
    		<td>Allocated Storage:</td>
    		<td>
    			<input type="text" class="text" name="AllocatedStorage" value="{$instance->AllocatedStorage}" /> GB
    		</td>
    	</tr>
    	<tr>
    		<td>Type:</td>
    		<td>
    			<select name="DBInstanceClass" class="text">
    				<option {if $instance->DBInstanceClass == 'db.m1.small'}selected{/if} value="db.m1.small">db.m1.small</option>
    				<option {if $instance->DBInstanceClass == 'db.m1.large'}selected{/if} value="db.m1.large">db.m1.large</option>
    				<option {if $instance->DBInstanceClass == 'db.m1.xlarge'}selected{/if} value="db.m1.xlarge">db.m1.xlarge</option>
    				<option {if $instance->DBInstanceClass == 'db.m2.2xlarge'}selected{/if} value="db.m2.2xlarge">db.m2.2xlarge</option>
    				<option {if $instance->DBInstanceClass == 'db.m1.4xlarge'}selected{/if} value="db.m2.4xlarge">db.m2.4xlarge</option>
    			</select>
    		</td>
    	</tr>
    	<tr>
    		<td>Master Password:</td>
    		<td>
    			<input type="text" class="text" name="MasterUserPassword" value="" />
    			<span style="font-size:10px;font-style:italic;">(Leave this field blank if you don't want to change password.)</span>
    		</td>
    	</tr>
    	<tr>
    		<td>Enable Multi Availability Zones</td>
    		<td><input type="checkbox" class="text" id="MultiAZId" name="MultiAZ" {if $instance->MultiAZ == 'true' }checked="checked"{/if} {if $instance->PendingModifiedValues->MultiAZ}disabled{/if} value="true" />{if $instance->PendingModifiedValues->MultiAZ} (state: pending){/if}</td>
    	</tr>
    	<tr>
    		<td>DB Parameter Group:</td>
    		<td>
    	    	<select name="DBParameterGroupName" class="text">
    			{foreach from=$DBParameterGroups key=id item=groupName} 
    				<option {if $DBParameterGroupName == $groupName.DBParameterGroupName}selected{/if} value="{$groupName.DBParameterGroupName}">{$groupName.DBParameterGroupName}</option>
    			{/foreach}  
    			</select> 		
    			
    		</td>
    	</tr>
    	<tr>
    		<td>DB Security Groups:</td>
    		<td>
    			<select name="DBSecurityGroups[]" multiple="multiple" class="text" style="min-width:250px;">
    				{section name=id loop=$DBSecurityGroups}
    				<option {if $DBSecurityGroups[id]->DBSecurityGroupName|in_array:$sec_groups}selected{/if} value="{$DBSecurityGroups[id]->DBSecurityGroupName}">{$DBSecurityGroups[id]->DBSecurityGroupName}</option>
    				{/section}
    			</select>
    		</td>
    	</tr>
    	<tr>
    		<td>Preferred Maintenance Window:<br />
    			<span style="font-size:10px;font-style:italic;">Format: ddd:hh24:mi - ddd:hh24:mi</span>
    		</td>
    		<td>
    			<select name="pmw1[ddd]" class="text">
    				<option value="Mon">Mon</option>
    				<option value="Tue">Tue</option>
    				<option value="Wed">Wed</option>
    				<option value="Thu">Thu</option>
    				<option value="Fri">Fri</option>
    				<option value="Sat">Sat</option>
    				<option selected value="Sun">Sun</option>
    			</select>:<input type="text" class="text" size="3" name="pmw1[hh]" value="05" />:<input class="text" type="text" size="3" name="pmw1[mm]" value="00" />
    			-
    			<select name="pmw2[ddd]" class="text">
    				<option value="Mon">Mon</option>
    				<option value="Tue">Tue</option>
    				<option value="Wed">Wed</option>
    				<option value="Thu">Thu</option>
    				<option value="Fri">Fri</option>
    				<option value="Sat">Sat</option>
    				<option selected value="Sun">Sun</option>
    			</select>:<input class="text" type="text" size="3" name="pmw2[hh]" value="09" />:<input class="text" type="text" size="3" name="pmw2[mm]" value="00" />
    		</td>
    	</tr>
    	<tr>
    		<td>Backup Retention Period:</td>
    		<td>
    			<input type="text" class="text" size="2" name="BackupRetentionPeriod" value="{$instance->BackupRetentionPeriod}" />
    		</td>
    	</tr>
    	<tr>
    		<td>Preferred Backup Window:<br />
    			<span style="font-size:10px;font-style:italic;">Format: hh24:mi - hh24:mi</span>
    		</td>
    		<td>
    			<input type="text" class="text" size="3" name="pbw1[hh]" value="05" />:<input class="text" type="text" size="3" name="pbw1[mm]" value="00" />
    			-
    			<input class="text" type="text" size="3" name="pbw2[hh]" value="09" />:<input class="text" type="text" size="3" name="pbw2[mm]" value="00" />
    		</td>
    	</tr>
    	{include file="inc/intable_footer.tpl" color="Gray"}
	{include file="inc/table_footer.tpl" button2=1 button2_name="Save changes" cancel_btn=1}
{include file="inc/footer.tpl"}