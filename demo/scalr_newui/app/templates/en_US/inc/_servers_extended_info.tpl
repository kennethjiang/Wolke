{include file="inc/table_header.tpl"}
	{include file="inc/intable_header.tpl" header="General" color="Gray"}
	<tr>
		<td width="20%">Server ID:</td>
		<td>{$server->serverId}</td>
	</tr>
	<tr>
		<td width="20%">Platform:</td>
		<td>{$server->platform}</td>
	</tr>
	<tr>
		<td width="20%">Remote IP:</td>
		<td>{$server->remoteIp}</td>
	</tr>
	<tr>
		<td width="20%">Local IP:</td>
		<td>{$server->localIp}</td>
	</tr>
	<tr>
		<td width="20%">Status:</td>
		<td>{$server->status}</td>
	</tr>
	<tr>
		<td width="20%">Index:</td>
		<td>{$server->index}</td>
	</tr>
	<tr>
		<td width="20%">Added at:</td>
		<td>{$server->dateAdded}</td>
	</tr>
	{include file="inc/intable_footer.tpl" color="Gray"}

	{include file="inc/intable_header.tpl" header="Platform specific details" color="Gray"}
	{if $info}
		{foreach key=name item=value from=$info}
		<tr>
			<td width="20%">{$name}:</td>
			<td>{$value}</td>
		</tr>
		{/foreach}
	{else}
	<tr>
		<td colspan='2'>Platform specific details not available for this server.</td>
	</tr>
	{/if}

	<!--
	<tr>
		<td width="20%">CloudWatch monitoring:</td>
		<td>{if $info->instancesSet->item->monitoring->state == 'enabled'}
				<a href="/aws_cw_monitor.php?ObjectId={$info->instancesSet->item->instanceId}&Object=InstanceId&NameSpace=AWS/EC2">{$info->instancesSet->item->monitoring->state}</a>
				&nbsp;(<a href="aws_ec2_cw_manage.php?action=Disable&iid={$info->instancesSet->item->instanceId}&region={$smarty.request.region}">Disable</a>)
			{else}
				{$info->instancesSet->item->monitoring->state}
				&nbsp;(<a href="aws_ec2_cw_manage.php?action=Enable&iid={$info->instancesSet->item->instanceId}&region={$smarty.request.region}">Enable</a>)
			{/if}
		</td>
	</tr>
	-->
	{include file="inc/intable_footer.tpl" color="Gray"}

	{include file="inc/intable_header.tpl" header="Scalr internal server properties" color="Gray"}
	{foreach key=name item=value from=$props}
		<tr>
			<td width="20%">{$name}:</td>
			<td>{$value}</td>
		</tr>
	{/foreach}
	{include file="inc/intable_footer.tpl" color="Gray"}
{include file="inc/table_footer.tpl" disable_footer_line=1}
