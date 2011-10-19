{include file="inc/header.tpl"}
    <script language="Javascript">
    
    {literal}
        
    function SaveParams()
    {    	 	
		var elems = Ext.get('footer_button_table').select('.btn');
		elems.each(function(item){
			item.disabled = true;
		});
		
		Ext.get('btn_hidden_field').dom.name = this.name;
		Ext.get('btn_hidden_field').dom.value = this.value;
		
		document.forms[2].submit();
    }
    
    {/literal}
    </script>
		{include file="inc/table_header.tpl"}  
{*------------ Parameters Group ------------------------*} 
		{include file="inc/intable_header.tpl" header="Parameters Group " color="Gray"}
    	<tr>
    		<td colspan="2">
    		  <table cellpadding="5" cellspacing="15" width="100%" border="0" >    		      
    		      <tr>
    		      	  <td style="font-weight:bold; width:30%;">Parameter Group Name</td>
    		      	  <td>{$group_name}</td>    		          
    		      </tr>    		      
    		      <tr>
    					<td style="font-weight:bold;">Engine</td>    	
    					<td> 
    						<select class="text"><option>MySQL5.1</option></select>
    					</td>
    		      </tr>
    		      <tr>
    		      	  <td style="font-weight:bold;vertical-align:text-top;">Description</td>
    		      	  <td style="vertical-align:text-top;">{$groupDescription}</td>  
    		      </tr> 
    		  </table>
       		</td>
    	</tr>
		{include file="inc/intable_footer.tpl" color="Gray"}	

{*------------ system parameters -------------------------*} 			
		{include file="inc/intable_header.tpl" header="system parameters" color="Gray"}
    	<tr>
    		<td colspan="2">    				
    			<table cellpadding="5" cellspacing="15" width="100%" border="0" > 	    			   
		      		 <tr>
		      			 <td  style="font-weight:bold; width:15%;">Name</td>
		      			 <td  style="font-weight:bold; width:25%;">Value</td>		      			 	      			 
		      			 <td  style="font-weight:bold;">Description</td>
		      		 </tr>
		      		 {foreach from = $parameters item = param}
    					{if $param.Source == 'system'}
		      			<tr id="{$param.ParameterName}">			
		      				<td  style="vertical-align:text-top;">{$param.ParameterName}</td>
		      				<td  style="vertical-align:text-top; text-align:left;">		      			
		      					{if $param.IsModifiable == 'false' } 																	      							
		      							{$param.ParameterValue}	
		      					{else}
	      						
      								{if $param.DataType == 'string' && $param.AllowedValues}
											<select class="text" style="width:80%" name="{$param.ParameterName}" id="{$param.ParameterName}">
											{* if the ParameterValue is set, the selected option is ParameterValue, else - ""*}
											<option class="text" selected value="{$param.ParameterValue}">{if $param.ParameterValue}{$param.ParameterValue}{else}{/if}</option>
											{foreach from = $param.AllowedValues  item = value }
												<option class="text"  value="{$value}">{$value}</option>
											{/foreach}    										
											</select>
									{else}
										{if $param.DataType == 'boolean'}																							
											<input type="checkbox"  name="{$param.ParameterName}" id="{$param.ParameterName}" {if $param.ParameterValue == '1'} checked {/if} value="1">
										{else}																				
											<input type="text" class="text" style="width:80%" id="{$param.ParameterName}" name="{$param.ParameterName}" value="{$param.ParameterValue}">    									
										{/if}
									{/if} 
								{/if}
		      				</td>		      				
							<td  style="font-style:italic;vertical-align:text-top;font-size:9pt; ">
								{$param.Description}
								{if $param.AllowedValues && $param.DataType != 'string' }<br/>Allowed values: {$param.AllowedValues}{/if}  
							</td>						  
						</tr>
						{/if}
					 {/foreach}    				          		      
    			</table>
    		</td>
    	</tr>    		
		{include file="inc/intable_footer.tpl" color="Gray"}

{*------------ engine-default parameter ------------------*} 	
		{include file="inc/intable_header.tpl" header="engine-default parameters" color="Gray"}
    	<tr>
    		<td colspan="2">    				
    			<table cellpadding="5" cellspacing="15" width="100%" border="0" > 	    			   
		      		 <tr>
		      			 <td  style="font-weight:bold; width:15%;">Name</td>
		      			 <td  style="font-weight:bold; width:25%;">Value</td>		      			 	      			 
		      			 <td  style="font-weight:bold;">Description</td>
		      		 </tr>
		      		 {foreach from = $parameters item = param}
    					{if $param.Source == 'engine-default'}
		      			<tr id="{$param.ParameterName}">			
		      				<td  style="vertical-align:text-top;">{$param.ParameterName}</td>
		      				<td  style="vertical-align:text-top; text-align:left;">		      			
		      					{if $param.IsModifiable == 'false' } 																	      							
		      							{$param.ParameterValue}	
		      					{else}
	      						
      								{if $param.DataType == 'string' && $param.AllowedValues}
											<select class="text" style="width:80%" name="{$param.ParameterName}" id="{$param.ParameterName}">
											{* if the ParameterValue is set, the selected option is ParameterValue, else - ""*}
											<option class="text" selected value="{$param.ParameterValue}">{if $param.ParameterValue}{$param.ParameterValue}{else}{/if}</option>
											{foreach from = $param.AllowedValues  item = value }
												<option class="text"  value="{$value}">{$value}</option>
											{/foreach}    										
											</select>
									{else}
										{if $param.DataType == 'boolean'}																							
											<input type="checkbox"  name="{$param.ParameterName}" id="{$param.ParameterName}" {if $param.ParameterValue == '1'} checked {/if} value="1">
										{else}																				
											<input type="text" class="text" style="width:80%" id="{$param.ParameterName}" name="{$param.ParameterName}" value="{$param.ParameterValue}">    									
										{/if}
									{/if} 
								{/if}
		      				</td>		      				
							<td  style="font-style:italic;vertical-align:text-top;font-size:9pt; ">
								{$param.Description}
								{if $param.AllowedValues && $param.DataType != 'string' }<br/>Allowed values: {$param.AllowedValues}{/if} 
							</td>						  
						</tr>
						{/if}
					 {/foreach}    				          		      
    			</table>
    		</td>
    	</tr>    		
		{include file="inc/intable_footer.tpl" color="Gray"}   
{*------------ user parameters ---------------------------*} 	
		{include file="inc/intable_header.tpl" header="user parameters" color="Gray"}
    	<tr>
    		<td colspan="2">    				
    			<table cellpadding="5" cellspacing="15" width="100%" border="0" > 	    			   
		      		 <tr>
		      			 <td  style="font-weight:bold; width:15%;">Name</td>
		      			 <td  style="font-weight:bold; width:25%;">Value</td>		      			 	      			 
		      			 <td  style="font-weight:bold;">Description</td>
		      		 </tr>
		      		 {foreach from = $parameters item = param}
    					{if $param.Source == 'user'}
		      			<tr id="{$param.ParameterName}">			
		      				<td  style="vertical-align:text-top;">{$param.ParameterName}</td>
		      				<td  style="vertical-align:text-top; text-align:left;">		      			
		      					{if $param.IsModifiable == 'false' } 																	      							
		      							{$param.ParameterValue}	
		      					{else}
	      						
      								{if $param.DataType == 'string' && $param.AllowedValues}
											<select class="text" style="width:80%" name="{$param.ParameterName}" id="{$param.ParameterName}">
											{* if the ParameterValue is set, the selected option is ParameterValue, else - ""*}
											<option class="text" selected value="{$param.ParameterValue}">{if $param.ParameterValue}{$param.ParameterValue}{else}{/if}</option>
											{foreach from = $param.AllowedValues  item = value }
												<option class="text"  value="{$value}">{$value}</option>
											{/foreach}    										
											</select>
									{else}
										{if $param.DataType == 'boolean'}																							
											<input type="checkbox"  name="{$param.ParameterName}" id="{$param.ParameterName}" {if $param.ParameterValue == '1'} checked {/if} value="1">
										{else}																				
											<input type="text" class="text" style="width:80%" id="{$param.ParameterName}" name="{$param.ParameterName}" value="{$param.ParameterValue}">    									
										{/if}
									{/if} 
								{/if}
		      				</td>		      				
							<td  style="font-style:italic;vertical-align:text-top;font-size:9pt; ">
								{$param.Description}
								{if $param.AllowedValues && $param.DataType != 'string' }<br/>Allowed values: {$param.AllowedValues}{/if} 
							</td>						  
						</tr>
						{/if}
					 {/foreach}
					 <tr id="user1">			
	      				<td  style="vertical-align:text-top;"><input type="text" class="text" value="" name="UserParamName[]" /></td>
	      				<td  style="vertical-align:text-top; text-align:left;">		      			
							<input type="text" class="text" style="width:80%" name="UserParamValue[]" value="">    									
	      				</td>		      				
						<td  style="font-style:italic;vertical-align:text-top;font-size:9pt; ">
							Apply method: <select name="UserParamMethod[]" class="text">
								<option value="immediate">immediate</option>
								<option value="pending-reboot">pending-reboot</option>
							</select>
						</td>						  
					</tr>    				          		      
    			</table>
    		</td>
    	</tr>    		
		{include file="inc/intable_footer.tpl" color="Gray"} 
{*---------------------------------------------------------------*}  
        
        <input type="hidden" name="name" value="{$group_name}">       
	{include file="inc/table_footer.tpl" button_js=1 button_js_name="Save" show_js_button=1 button_js_action="SaveParams();" button3=1 button3_name = "Set to default"}
		
{include file="inc/footer.tpl"}