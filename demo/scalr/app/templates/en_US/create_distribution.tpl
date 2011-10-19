{include file="inc/header.tpl"}
{include file="inc/table_header.tpl"}
    {include file="inc/intable_header.tpl" header="Distribution information" color="Gray"}
	<tr>
		<td width="15%">S3 Bucket:</td>
		<td colspan="6">
			{$bucket_name}
			<input type="hidden" name="bucket_name" value="{$bucket_name}" />
		</td>
	</tr>		
	<tr valign="top" rowspan="6">
		<td width="15%">Comment:</td>
		<td colspan="6">
			<textarea class="text" cols="40" rows="4" name="comment"></textarea>
		</td>
	</tr>
{include file="inc/intable_footer.tpl" color="Gray"}
{literal}
<script type="text/javascript">

// disable remote/local domain name inputs depending from customer's choice.

window.onload = function() 
	{
		var localDomain = document.getElementById('domainname');
			localDomain.disabled = false;
		var localZone = document.getElementById('zone');
			localZone.disabled = false;
			
		var remoute = document.getElementById('remotedomainname');
		 remoute.disabled = true;
	}

function hideLocalDomain()
{	
	var localDomain = document.getElementById('domainname');
		localDomain.disabled = true;
	var localZone = document.getElementById('zone');
		localZone.disabled = true;
		
	var remoute = document.getElementById('remotedomainname');
	 remoute.disabled = false;
}
function hideRemoteDomain()
{
	var localDomain = document.getElementById('domainname');
		localDomain.disabled = false;
	var localZone	= document.getElementById('zone');
		localZone.disabled = false;	
	var remoute		= document.getElementById('remotedomainname');
		remoute.disabled = true;
}
</script>
{/literal}
{include file="inc/intable_header.tpl" header="Domain name" color="Gray"}		
	<tr>
		<td width="20%" ><input type="radio" onClick="hideRemoteDomain();" style="vertical-align: middle;" value="1" checked="checked" id="localDomainRadio" name="domainNameType"> Local domain name</td>
		<td colspan="6">
			<input type="text" class="text" id="domainname" name="domainname" value="{$domainname}" style="vertical-align: middle;" />.
			<select id="zone" name="zone" style="vertical-align: middle;">
				{section name=id loop=$zones}
					<option value="{$zones[id].zone_name}">{$zones[id].zone_name}</option>
				{/section}
			</select>
		</td>
	</tr>
	<tr>
		<td><input type="radio" onClick="hideLocalDomain();" style="vertical-align: middle;" value="2" id="remoteDomainRadio" name="domainNameType"> Remote domain name</td>
		<td colspan="6"><input type="text" class="text" id="remotedomainname" name="remotedomainname" value="{$domainname}" style="vertical-align: middle; width:410px;" />
		</td>
	</tr>
	
{include file="inc/intable_footer.tpl" color="Gray"}
<input type="hidden" name="task" value="create_dist" />
<input type="hidden" name="confirm" value="1" />
{include file="inc/table_footer.tpl" button2=1 button2_name='Create distribution' cancel_btn=1}
{include file="inc/footer.tpl"}