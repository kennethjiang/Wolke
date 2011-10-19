{include file="inc/header.tpl" upload_files=1}
	<style>
	{literal}
	.tr_with_padding
	{
		padding:5px;
	}
	{/literal}
	</style>
	{include file="inc/table_header.tpl"}
        {include file="inc/intable_header.tpl" header="Instance information" color="Gray"}
        <tr>
    		<td width="20%" class="tr_with_padding">Instance Id:</td>
    		<td>{$instance->instanceId}</td>
    	</tr>
    	<tr>
    		<td width="20%" class="tr_with_padding">Image Id:</td>
    		<td>{$instance->imageId}</td>
    	</tr>
    	<tr>
    		<td width="20%" class="tr_with_padding">Instance state</td>
    		<td>{$instance->instanceState->name}</td>
    	</tr>
    	<tr>
    		<td width="20%" class="tr_with_padding">Instance type</td>
    		<td>{$instance->instanceType}</td>
    	</tr>
    	<tr>
    		<td width="20%" class="tr_with_padding">Instance lifecycle</td>
    		<td>{$instance->instanceLifecycle}</td>
    	</tr>
    	<tr>
    		<td width="20%" class="tr_with_padding">Launch time</td>
    		<td>{$instance->launchTime}</td>
    	</tr>
    	<tr>
    		<td width="20%" class="tr_with_padding">Availability zone</td>
    		<td>{$instance->placement->availabilityZone}</td>
    	</tr>
    	<tr>
    		<td width="20%" class="tr_with_padding">Private IP address</td>
    		<td>{$instance->privateIpAddress}</td>
    	</tr>
    	<tr>
    		<td width="20%" class="tr_with_padding">IP address</td>
    		<td>{$instance->ipAddress}</td>
    	</tr>
    	<tr>
    		<td width="20%" class="tr_with_padding">Architecture</td>
    		<td>{$instance->architecture}</td>
    	</tr>
    	
        {include file="inc/intable_footer.tpl" color="Gray"}
	{include file="inc/table_footer.tpl" disable_footer_line=1}
{include file="inc/footer.tpl"}