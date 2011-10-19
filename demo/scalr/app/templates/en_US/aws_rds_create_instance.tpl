{include file="inc/header.tpl"}
    <script language="Javascript">
    {literal}        
    function AllowMultiAZ(cb)
    {    
    	if(cb.checked)
        	Ext.get('AvailabilityZoneId').dom.disabled = true;
    	else
    		Ext.get('AvailabilityZoneId').dom.disabled = false;        	
    }    
    {/literal}
    </script>
    
	{include file="inc/table_header.tpl"}
    	{include file="inc/intable_header.tpl" intable_first_column_width="15%" header="General information" color="Gray"}
    	{if $snapshot}
    	<tr>
    		<td>Snapshot identifier:</td>
    		<td>
    			&nbsp;{$snapshot}
    			<input type="hidden" name="snapshot" value="{$snapshot}" />
    		</td>
    	</tr>
    	<tr>
    		<td colspan="2">&nbsp;</td>
    	</tr>
    	{/if}
        <tr>
    		<td>Identifier:</td>
    		<td>
    			<input type="text" class="text" name="DBInstanceIdentifier" value="{$POST.DBInstanceIdentifier}" />
    		</td>
    	</tr>
    	{if !$snapshot}
    	<tr>
    		<td>Allocated Storage:</td>
    		<td>
    			<input type="text" class="text" name="AllocatedStorage" value="{if $POST.AllocatedStorage}{$POST.AllocatedStorage}{else}5{/if}" /> GB
    		</td>
    	</tr>
    	{/if}
    	<tr>
    		<td>Type:</td>
    		<td>
    			<select name="DBInstanceClass" class="text">
    				<option {if $POST.DBInstanceClass == 'db.m1.small'}selected{/if} value="db.m1.small">db.m1.small</option>
    				<option {if $POST.DBInstanceClass == 'db.m1.large'}selected{/if} value="db.m1.large">db.m1.large</option>
    				<option {if $POST.DBInstanceClass == 'db.m1.xlarge'}selected{/if} value="db.m1.xlarge">db.m1.xlarge</option>
    				<option {if $POST.DBInstanceClass == 'db.m2.2xlarge'}selected{/if} value="db.m2.2xlarge">db.m2.2xlarge</option>
    				<option {if $POST.DBInstanceClass == 'db.m2.4xlarge'}selected{/if} value="db.m2.4xlarge">db.m2.4xlarge</option>
    			</select>
    		</td>
    	</tr>
    	{if !$snapshot}
    	<tr>
    		<td>Engine:</td>
    		<td>
    			<select name="Engine" class="text">
    				<option {if $POST.Engine == 'MySQL5.1'}selected{/if} value="MySQL5.1">MySQL 5.1</option>
    			</select>
    		</td>
    	</tr>
    	<tr>
    		<td>Master Username:</td>
    		<td>
    			<input type="text" class="text" name="MasterUsername" value="{$POST.MasterUsername}" />
    		</td>
    	</tr>
    	<tr>
    		<td>Master Password:</td>
    		<td>
    			<input type="text" class="text" name="MasterUserPassword" value="{$POST.MasterUserPassword}" />
    		</td>
    	</tr>
    	{/if}
    	<tr>
    		<td>Port:</td>
    		<td>
    			<input type="text" class="text" name="Port" value="{if $POST.Port}{$POST.Port}{else}3306{/if}" />
    		</td>
    	</tr>
    	{if !$snapshot}
    	<tr>
    		<td>DB name:</td>
    		<td>
    			<input type="text" class="text" name="DBName" value="{$POST.DBName}" />
    		</td>
    	</tr>
    	<tr>
    		<td>DB Parameter Group:</td>
    		<td>
    			<select name="DBParameterGroup" class="text">
    				{section name=id loop=$DBParameterGroups}
    				<option value="{$DBParameterGroups[id]->DBParameterGroupName}">{$DBParameterGroups[id]->DBParameterGroupName}</option>
    				{/section}
    			</select>
    		</td>
    	</tr>
    	<tr>
    		<td>DB Security Groups:</td>
    		<td>
    			<select name="DBSecurityGroups[]" class="text" multiple="multiple" style="min-width:250px;">
    				{section name=id loop=$DBSecurityGroups}
    				<option {if $DBSecurityGroups[id]->DBSecurityGroupName|in_array:$POST.DBSecurityGroups}selected{/if} value="{$DBSecurityGroups[id]->DBSecurityGroupName}">{$DBSecurityGroups[id]->DBSecurityGroupName}</option>
    				{/section}
    			</select>
    		</td>
    	</tr>
    	{/if}
    	<tr>
    		<td>Availability Zone:</td>
    		<td>
    			<select name="AvailabilityZone"  id="AvailabilityZoneId">
    				{section name=id loop=$avail_zones}
    				<option {if $POST.AvailabilityZone == $avail_zones[id]}selected{/if} value="{if $avail_zones[id]}{$avail_zones[id]}{else}{$avail_zones[1]}{/if}">{if $avail_zones[id] == ""}Choose randomly{else}{$avail_zones[id]}{/if}</option>
    				{/section}
    			</select>
    			<input type="hidden" name="region" value="{$region}" />
    			<input type="hidden" name="step" value="2" />
    		</td>
    	</tr>
    	<tr>
    		<td>Enable Multi Availability Zones</td>
    		<td><input type="checkbox" class="text" id="MultiAZId" name="MultiAZ" onClick="AllowMultiAZ(this)" value="true"  /></td>
    	</tr>
    	{if !$snapshot}
    	<tr>
    		<td>Preferred Maintenance Window:<br />
    			<span style="font-size:10px;font-style:italic;">Format: ddd:hh24:mi - ddd:hh24:mi</span>
    		</td>
    		<td>
    			<select name="pmw1[ddd]" class="text">
    				<option {if $POST.pmw1.ddd == 'Mon'}selected{/if} value="Mon">Mon</option>
    				<option {if $POST.pmw1.ddd == 'Tue'}selected{/if} value="Tue">Tue</option>
    				<option {if $POST.pmw1.ddd == 'Wed'}selected{/if} value="Wed">Wed</option>
    				<option {if $POST.pmw1.ddd == 'Thu'}selected{/if} value="Thu">Thu</option>
    				<option {if $POST.pmw1.ddd == 'Fri'}selected{/if} value="Fri">Fri</option>
    				<option {if $POST.pmw1.ddd == 'Sat'}selected{/if} value="Sat">Sat</option>
    				<option {if $POST.pmw1.ddd == 'Sun' || !$POST.pmw1.ddd}selected{/if} value="Sun">Sun</option>
    			</select>:<input type="text" class="text" size="3" name="pmw1[hh]" value="{if $POST.pmw1.hh}{$POST.pmw1.hh}{else}05{/if}" />:<input class="text" type="text" size="3" name="pmw1[mm]" value="{if $POST.pmw1.mm}{$POST.pmw1.mm}{else}00{/if}" />
    			-
    			<select name="pmw2[ddd]" class="text">
    				<option {if $POST.pmw2.ddd == 'Mon'}selected{/if} value="Mon">Mon</option>
    				<option {if $POST.pmw2.ddd == 'Tue'}selected{/if} value="Tue">Tue</option>
    				<option {if $POST.pmw2.ddd == 'Wed'}selected{/if} value="Wed">Wed</option>
    				<option {if $POST.pmw2.ddd == 'Thu'}selected{/if} value="Thu">Thu</option>
    				<option {if $POST.pmw2.ddd == 'Fri'}selected{/if} value="Fri">Fri</option>
    				<option {if $POST.pmw2.ddd == 'Sat'}selected{/if} value="Sat">Sat</option>
    				<option {if $POST.pmw2.ddd == 'Sun' || !$POST.pmw2.ddd}selected{/if} value="Sun">Sun</option>
    			</select>:<input class="text" type="text" size="3" name="pmw2[hh]" value="{if $POST.pmw2.hh}{$POST.pmw2.hh}{else}09{/if}" />:<input class="text" type="text" size="3" name="pmw2[mm]" value="{if $POST.pmw2.mm}{$POST.pmw2.mm}{else}00{/if}" />
    		</td>
    	</tr>
    	<tr>
    		<td>Backup Retention Period:</td>
    		<td>
    			<input type="text" class="text" size="2" name="BackupRetentionPeriod" value="{if $POST.BackupRetentionPeriod}{$POST.BackupRetentionPeriod}{else}1{/if}" />
    		</td>
    	</tr>
    	<tr>
    		<td>Preferred Backup Window:<br />
    			<span style="font-size:10px;font-style:italic;">Format: hh24:mi - hh24:mi</span>
    		</td>
    		<td>
    			<input type="text" class="text" size="3" name="pbw1[hh]" value="{if $POST.pbw1.hh}{$POST.pbw1.hh}{else}05{/if}" />:<input class="text" type="text" size="3" name="pbw1[mm]" value="{if $POST.pbw1.mm}{$POST.pbw1.mm}{else}00{/if}" />
    			-
    			<input class="text" type="text" size="3" name="pbw2[hh]" value="{if $POST.pbw2.hh}{$POST.pbw2.hh}{else}09{/if}" />:<input class="text" type="text" size="3" name="pbw2[mm]" value="{if $POST.pbw2.mm}{$POST.pbw2.mm}{else}00{/if}" />
    		</td>
    	</tr>
    	
    	{/if}
    	{include file="inc/intable_footer.tpl" color="Gray"}
	{include file="inc/table_footer.tpl" button2=1 button2_name="Launch DB Instance" cancel_btn=1}
{include file="inc/footer.tpl"}