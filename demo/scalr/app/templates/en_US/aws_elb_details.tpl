{include file="inc/header.tpl" noheader=1}
	{include file="inc/table_header.tpl"}
        {include file="inc/intable_header.tpl" header="General" color="Gray"}
        <tr>
    		<td width="20%">Name:</td>
    		<td>{$elb->LoadBalancerName}</td>
    	</tr>
    	<tr>
    		<td width="20%">DNS Name:</td>
    		<td>{$elb->DNSName}</td>
    	</tr>
    	<tr>
    		<td width="20%">Created at:</td>
    		<td>{$elb->CreatedTime}</td>
    	</tr>
    	<tr>
    		<td colspan="2">&nbsp;</td>
    	</tr>
    	<tr>
    		<td width="20%">Availability Zones:</td>
    		<td>
    			{foreach name=az item=i from=$elb->AvailabilityZones->member}
    				{$i}{if !$smarty.foreach.az.last},{/if}
    			{/foreach}
    		</td>
    	</tr>
    	<tr>
    		<td colspan="2">&nbsp;</td>
    	</tr>
    	<tr>
    		<td width="20%">Instances:</td>
    		<td>
    			{foreach name=ai item=i from=$elb->Instances->member}
    				<a title="View instance health status" href="aws_elb_instance_health.php?lb={$elb->LoadBalancerName}&iid={$i->InstanceId}">{$i->InstanceId}</a>{if !$smarty.foreach.ai.last},{/if}
    			{foreachelse}
    				There are no instances registered on this load balancer
    			{/foreach}
    		</td>
    	</tr>
        {include file="inc/intable_footer.tpl" color="Gray"}
        
        {include file="inc/intable_header.tpl" header="HealthCheck settings" color="Gray"}
        <tr>
    		<td width="20%">Interval:</td>
    		<td>{$elb->HealthCheck->Interval} seconds</td>
    	</tr>
    	<tr>
    		<td width="20%">Target:</td>
    		<td>{$elb->HealthCheck->Target}</td>
    	</tr>
    	<tr>
    		<td width="20%">Healthy Threshold:</td>
    		<td>{$elb->HealthCheck->HealthyThreshold}</td>
    	</tr>
    	<tr>
    		<td width="20%">Timeout:</td>
    		<td>{$elb->HealthCheck->Timeout} seconds</td>
    	</tr>
    	<tr>
    		<td width="20%">UnHealthy Threshold:</td>
    		<td>{$elb->HealthCheck->UnhealthyThreshold}</td>
    	</tr>
        {include file="inc/intable_footer.tpl" color="Gray"}
        
        {include file="inc/intable_header.tpl" header="Listeners &amp; Stickiness Policy" color="Gray"}
        <tr>
    		<td colspan="2">
	    		<div style="width:550px;float:left;">
	    			<div style="width:497px;background-color:#cccccc;padding:5px;">Listeners</div>
	    			<div style="width:505px;border:1px solid #cccccc;border-bottom:0px;line-height:30px;height:30px;font-size:11px;font-weight:bold;">
	    				<div style="float:left;width:90px;border-right:1px solid #cccccc;text-align:center;">Protocol</div>
	    				<div style="float:left;width:125px;border-right:1px solid #cccccc;text-align:center;">LoadBalancer Port</div>
	    				<div style="float:left;width:125px;border-right:1px solid #cccccc;text-align:center;">Instance Port</div>
	    				<div style="float:left;width:160px;text-align:center;">Stickiness Policy</div>
	    			</div>
	    			<div style="clear:both;"></div>
	    			{foreach name=al item=i from=$elb->ListenerDescriptions->member}
	    			<div style="width:505px;border:1px solid #cccccc;border-bottom:0px;line-height:30px;height:30px;font-size:11px;">
		    			<div style="float:left;width:90px;border-right:1px solid #cccccc;text-align:center;">{$i->Listener->Protocol}</div>
		    			<div style="float:left;width:125px;border-right:1px solid #cccccc;text-align:center;">{$i->Listener->LoadBalancerPort}</div>
		    			<div style="float:left;width:125px;border-right:1px solid #cccccc;text-align:center;">{$i->Listener->InstancePort}</div>
		    			<div style="float:left;width:160px;text-align:center;">&nbsp;{$i->PolicyNames->member}</div>
		    		</div>
		    		{/foreach}
		    		<div style="width:507px;border-bottom:1px solid #cccccc;line-height:30px;height:0px;font-size:11px;">
		    			
		    		</div>
		    	</div>
		    	<div style="width:600px;float:left;margin-left:50px;">
		    	<form style="margin:0px;padding:0px;" action="" method="POST">
		    	<input type="hidden" name="action" value="associate_sp" />
		    	<input type="hidden" name="name" value="{$smarty.get.name}" />
	    			<div style="width:547px;background-color:#cccccc;padding:5px;">Stickiness Policies</div>
	    			<div style="width:555px;border:1px solid #cccccc;line-height:30px;height:30px;border-bottom:0px;font-size:11px;font-weight:bold;">
	    				<div style="float:left;width:120px;border-right:1px solid #cccccc;text-align:center;">Type</div>
	    				<div style="float:left;width:125px;border-right:1px solid #cccccc;text-align:center;">Name</div>
	    				<div style="float:left;width:200px;border-right:1px solid #cccccc;text-align:center;">Cookie name / Exp. period</div>
	    				<div style="float:left;width:105px;text-align:center;"></div>
	    			</div>
	    			<div style="clear:both;"></div>
	    			{foreach name=al item=i from=$elb->Policies->AppCookieStickinessPolicies->member}
	    			<div style="width:555px;border:1px solid #cccccc;line-height:30px;height:30px;border-bottom:0px;font-size:11px;">
		    			<div style="float:left;width:120px;border-right:1px solid #cccccc;text-align:center;">App cookie</div>
		    			<div style="float:left;width:125px;border-right:1px solid #cccccc;text-align:center;">{$i->PolicyName}</div>
		    			<div style="float:left;width:200px;border-right:1px solid #cccccc;text-align:center;">{$i->CookieName}</div>
		    			<div style="float:left;width:105px;text-align:center;">
							<input type="radio" name="p" value="{$i->PolicyName}" style="vertical-align:middle;margin-top:8px;" />		    			
		    			</div>
		    		</div>
		    		{/foreach}
		    		{foreach name=al item=i from=$elb->Policies->LBCookieStickinessPolicies->member}
	    			<div style="width:555px;border:1px solid #cccccc;line-height:30px;height:30px;border-bottom:0px;font-size:11px;">
		    			<div style="float:left;width:120px;border-right:1px solid #cccccc;text-align:center;">LB cookie</div>
		    			<div style="float:left;width:125px;border-right:1px solid #cccccc;text-align:center;">{$i->PolicyName}</div>
		    			<div style="float:left;width:200px;border-right:1px solid #cccccc;text-align:center;">{$i->CookieExpirationPeriod}</div>
		    			<div style="float:left;width:105px;text-align:center;">
		    				<input type="radio" name="p" value="{$i->PolicyName}" style="vertical-align:middle;margin-top:8px;" />
		    			</div>
		    		</div>
		    		{/foreach}
		    		<div style="width:555px;border:1px solid #cccccc;line-height:32px;height:32px;font-size:11px;border-bottom:0px;">
		    			<div style="float:left;width:550px;text-align:center;height:32px;">
		    				<input type="submit" name="submit" class="btn" value="Associate" /> 
		    				selected policy with 
		    				<select name="sp_p" class="text" style="text-align:center;">
		    					{foreach name=al item=i from=$elb->ListenerDescriptions->member}
		    						{if $i->Listener->Protocol == 'HTTP'}
		    						<option value="{$i->Listener->LoadBalancerPort}">HTTP: {$i->Listener->LoadBalancerPort} -> {$i->Listener->InstancePort}</option>
		    						{/if}
		    					{/foreach}
		    				</select>
		    				listener
		    			</div>
		    		</div>
		    		</form>
		    		<form style="margin:0px;padding:0px;" action="" method="POST">
			    	<input type="hidden" name="action" value="create_sp" />
			    	<input type="hidden" name="name" value="{$smarty.get.name}" />
		    		<div style="width:555px;border:1px solid #cccccc;line-height:32px;height:32px;font-size:11px;">
		    			<div style="float:left;width:120px;border-right:1px solid #cccccc;text-align:center;height:32px;">
			    			<select class="text" name="sp_type" style="vertical-align:middle;" onchange="UpdateSPForm(this.value);">
			    				<option value="AppCookieStickinessPolicies">App cookie</option>
			    				<option value="LBCookieStickinessPolicies">Lb cookie</option>
			    			</select>
		    			</div>
		    			<div style="float:left;width:125px;border-right:1px solid #cccccc;text-align:center;height:32px;">
		    				<input type="text" class="text" name="sp_name" style="vertical-align:middle;width:100px;" />
		    			</div>
		    			<div style="float:left;width:200px;border-right:1px solid #cccccc;text-align:center;height:32px;">
		    				<span id="sp_cname_n" style="display:;padding-left:2px;vertical-align:middle;">Cookie name:</span>
		    				<span id="sp_cname_p" style="display:none;padding-left:2px;vertical-align:middle;">Exp. period:</span>
		    				<input type="text" class="text" name="sp_cname" style="vertical-align:middle;width:95px;margin-top:1px;" />
		    			</div>
		    			<div style="float:left;width:105px;text-align:center;height:32px;">
		    				<input type="submit" name="sp_submit" value="Create" style="vertical-align:middle;margin-top:4px;" class="btn" />
		    			</div>
		    		</div>
		    	</form>
		    	</div>
		    	<div style="clear:both;"></div>
    		</td>
    	</tr>
        {include file="inc/intable_footer.tpl" color="Gray"}
	{include file="inc/table_footer.tpl" disable_footer_line=1}
	<script language="javascript">
	{literal}
	function UpdateSPForm(value)
	{
		Ext.get('sp_cname_n').dom.style.display = (value == 'AppCookieStickinessPolicies') ? '' : 'none';
		Ext.get('sp_cname_p').dom.style.display = (value != 'AppCookieStickinessPolicies') ? '' : 'none';
	}
	{/literal}
	</script>
{include file="inc/footer.tpl"}