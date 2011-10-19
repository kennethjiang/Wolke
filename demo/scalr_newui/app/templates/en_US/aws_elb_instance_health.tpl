{include file="inc/header.tpl" upload_files=1}
	{include file="inc/table_header.tpl"}
        {include file="inc/intable_header.tpl" header="General" color="Gray"}
        <tr>
    		<td width="20%">Load balancer:</td>
    		<td><a href="/aws_elb_details.php?name={$name}">{$name}</a></td>
    	</tr>
        <tr>
    		<td width="20%">Instance ID:</td>
    		<td>{$info->InstanceId}</td>
    	</tr>
    	<tr>
    		<td width="20%">State:</td>
    		<td>{$info->State}</td>
    	</tr>
    	<tr>
    		<td width="20%">Description:</td>
    		<td>{$info->Description}</td>
    	</tr>
        {include file="inc/intable_footer.tpl" color="Gray"}
	{include file="inc/table_footer.tpl" button2=1 button2_name='Deregister instance from the load balancer'}
{include file="inc/footer.tpl"}