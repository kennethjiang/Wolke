{include file="inc/header.tpl"}
{include file="inc/table_header.tpl"}
		{include file="inc/intable_header.tpl" header="Request configuration" color="Gray"}
						
    	<tr>
    		<td style="padding: 3px;" colspan="2">    		
    		 <table style=" border-collapse: separate; border-spacing:3px; width:100%;" border="0" >    		      
    				<tr>
    					<td style="font-weight:bold; padding: 2px; width:20%;">AMI ID</td>
    					<td style="width:10%; padding: 2px;">{$amiId}</td>
    					<td style="width:70%;"></td>
    				</tr>       				
    				<tr>
    					<td style="font-weight:bold; padding: 2px; width:20%;">Max price</td>
    					<td style="padding: 2px;"><input name="spotPrice" type="text" class="text" style="width:196px;" value="0.1"></td>
    					<td></td>
    				</tr>    				
    				<tr>
    					<td style="font-weight:bold; padding: 2px; width:10%;">Request type</td>
    					<td style="padding: 2px;">
							<select style="width:200px;" id="isPersistanType" name="isPersistanType" class="role_settings text">							
								<option value="one-time">one-time</option>
								<option value="persistent">persistent</option>							
							</select>
    					</td>
    					<td></td>
    				</tr>
    				<tr>
    					<td style="font-weight:bold; padding: 2px; width:10%;">Number of instances</td>
    					<td><input name="count" type="text" class="text" style="width:196px;" value="1"></td>
    					<td></td>
    				</tr>
    				<tr>
    					<td style="font-weight:bold; padding: 2px; width:10%;">Instance type</td>
    					<td style="padding: 2px;">
    						<select style="width:200px;" id="aws_instance_type" name="aws_instance_type" class="role_settings text">
    						{section name=id loop=$instance_type}  							    						
    							<option value="{$instance_type[id]}">{$instance_type[id]}</option>    							
    						{/section}    							
    						</select>
    					</td>
    					<td></td>
    				</tr>
    				<tr>
    					<td style="font-weight:bold; padding: 2px; width:10%;">Availability zone</td>
    					<td style="padding: 2px;">
    						<select style="width:200px;" id="aws_availability_zone" name="aws_availability_zone" class="role_settings text">
	             				{section name=zid loop=$avail_zones}
	             					{if $avail_zones[zid] == ""}
	             					<option {if $servers[id].avail_zone == ""}selected{/if} value="">Choose randomly</option>
	             					<option {if $servers[id].avail_zone == "x-scalr-diff"}selected{/if} value="x-scalr-diff">Place in different zones</option>
	             					{else}
	             					<option {if $servers[id].avail_zone == $avail_zones[zid]}selected{/if} value="{$avail_zones[zid]}">{$avail_zones[zid]}</option>
	             					{/if}
	             				{/section}
	             			</select>
    					</td>
    					<td></td>
    				</tr>
    				<tr>
    					<td style="font-weight:bold; padding: 2px; width:10%;">Valid from</td>
    					<td style="padding: 2px;">
    						<div id="From">   							
    						</div>    						
    						<script type="text/javascript">
							{literal}	
							df = new Ext.form.DateField(
							{
								renderTo: 'From',
								format: "Y-m-d\\TH:i:s.\Z",
								width: 204,							
								name: 'validFrom'
							})
							df.render();
							{/literal}
    						</script>    
    					</td>
    					<td style="width:20%;"></td>
    				</tr>
    				<tr>
    					<td style="font-weight:bold; padding: 2px; width:10%;">Valid until</td>
    					<td style="padding: 2px;">
    						<div id="Until">   							
    						</div>    						
    						<script type="text/javascript">
							{literal}
							df = new Ext.form.DateField(
							{
								renderTo: 'Until',
								format: "Y-m-d\\TH:i:s.\Z",								
								width: 204,
								name: 'validUntil'
							})
							df.render();
							{/literal}
    						</script> 
    					</td>
    					<td style="width:20%;"></td>
    				</tr>
    				
    		  </table>
    		 
       		</td>
    	</tr>
		{include file="inc/intable_footer.tpl" color="Gray"}    	
    	{include file="inc/table_footer.tpl" button2=1 button2_name="Create spot request"}	
    	
{include file="inc/footer.tpl"}