{include file="inc/header.tpl" upload_files=1}
	<style>
	{literal}

	.td_with_padding
	{	
		height:18px; 
		padding:5px;
	}	
	a.notSelectedMode
	{
		cursor: pointer;
		color:#112ABB;
	}
	a.notSelectedMode:active
	{
		cursor: pointer;
		color:#112ABB;
	}
		
	{/literal}
	</style>
	<script src="/js/farm_role_server_loader.js"></script> 
	<script language="javascript">

	var farmsCombo 			= null;
	var roleCombo 			= null;
	var farmsStore			= null;
	var roleStore			= null;
	
	var loadedFarmId		= {if $loadedFarmId}	{$loadedFarmId}		{else}0{/if};
	var loadedFarmRoleId	= {if $loadedFarmRoleId}{$loadedFarmRoleId}	{else}0{/if};
	var host_ssl_enabled	= {if $host_ssl_enabled}{$host_ssl_enabled}	{else}0{/if};
	var host_ca_enabled		= {if $host_ca_enabled}	{$host_ca_enabled}	{else}0{/if};
	
	{literal}
	Ext.onReady(function () 
	{
		if (Ext.isIE) 
		{
			(function () { setUserModeSettings() }).defer(50)
		} 
		else 
		{
			setUserModeSettings();
		}
			
		//
		// Roles
		//
		roleCombo = FarmRoleServerHelper.newRoleCombo(
				null,			
				// roleStore:
				{	baseParams: {action: "LoadFarmRoles", behavior: "app"},
					listeners:
					{
						load:function(params, reader, callback, scope, arg)
						{							 		
							 if(Ext.get("loadRole"))						
								Ext.get("loadRole").dom.style.display = 'none';
								 
							// show roles field only if it's not empty	
							if(params.data.length > 0)
		       				{
		       					if(Ext.get('role_target_combo'))			       					
		       						HideComboByClassName('Role', true, false);
	       						
		       					if(loadedFarmRoleId && farmsCombo.value == loadedFarmId)
			   						SelectComboByValue(roleCombo, roleStore, loadedFarmRoleId, "id");
		   						
		       					if(Ext.get('button_js'))
			       					Ext.get('button_js').dom.disabled = false;					       					
		       				}
		       				else
		       				{
			       				if(Ext.get('role_target_combo'))		       						
		       						HideComboByClassName('Role', false, true);
	       						
								if(Ext.get('button_js'))
			       					Ext.get('button_js').dom.disabled = true;		       							       							
		       				}
						 }
					}
				});
		
		roleStore = roleCombo.store;

		//
		// Farms
		//
		farmsCombo = FarmRoleServerHelper.newFarmsCombo(
			{					
				listeners:
				{
					select:function(combo, record, index)
					{									
						HideComboByClassName('Role', false, false);
						
						// load roles and show them only for "script_exec" task type	
						if(Ext.get("loadRole"))							
							Ext.get("loadRole").dom.style.display = '';		
													
						// load farm roles of selected farm by farmId ( from farmsStore comboBox)
						if(roleStore)			
						{
		       				roleStore.baseParams.farmId = record.data.id; 
		       				roleStore.load();
						}
	    						
						//  reset selected text in roleCombo
		   				if(roleCombo)
		   					roleCombo.clearValue();	
		   			} 
				}
			},
			// store config
			{
				listeners:
				{
					load:function(params, reader, callback, scope, arg)
					 {
						if(Ext.get("loadFarm"))
							Ext.get("loadFarm").dom.style.display = 'none';
						
						 // show farms field only if it's not empty
						if(params.data.length > 0)
	       				{
	       					if(Ext.get('farm_target_combo'))			       					
	       						HideComboByClassName('Farm', true, false);	

	       					if(loadedFarmId)
	       						SelectComboByValue(farmsCombo, farmsStore, loadedFarmId, "id");	 
	       				}
	       				else
	       				{
	       					if(Ext.get('farm_target_combo'))
	       						HideComboByClassName('Farm', false, true);
	       				}
					 }
				}
			}
		);
		
		farmsStore = farmsCombo.store;			
		farmsStore.load();
		
		if(Ext.get("loadFarm"))			
			 Ext.get('loadFarm').dom.style.display = '';			
		
	});
	
	//
	// SSL and other options
	//
	
	// shows SSL  cerificate options
	function ShowCertificateFields(cb,fieldsId)
	{ 
		
		var strShow = 'none';
		
		if(cb.checked)
		{
			strShow = 'table-row';
			cb.value = "1";
		}
		else		
			cb.value = "0";
				
		if(Ext.get(fieldsId))
			Ext.get(fieldsId).dom.style.display = strShow;

		if(Ext.get('userTemplateBodySSL'))
			Ext.get('userTemplateBodySSL').dom.style.display = strShow;
	}

	function setUserModeSettings()
	{ 
		ShowCertificateFields(Ext.get('isSslEnabled').dom, 'SSLFields');		
	}

	function submitTask(value)
	{			
		if(Ext.get('button_js'))
			Ext.get('button_js').dom.disabled = true;		
		
		document.forms[2].submit();		
	}

	{/literal}
	</script>
	<br />				 
	<div style="position:relative;width:auto;">
	
	<div id="script_loader" align="center" style="display:none;z-index:1000;position:absolute;top:7px;left:7px;background-color:#F0F0F0;right:7px;bottom:7px;vertical-align: middle;">
		<div align="center" style="position: absolute;left:50%; top: 50%;display: table-cell; vertical-align: middle;">
			<img style="vertical-align:middle;" src="/images/snake-loader.gif"> {t}Loading...{/t}
		</div>
	</div>			
	{include file="inc/table_header.tpl" nofilter=1}
	
<!-- General -->	
	{include file="inc/intable_header.tpl" header="General information" color="Gray"}
		<tr style="height:18px; ">
			<td class="td_with_padding" style="width:200px;">Domain name:</td>
			<td class="td_with_padding">
				<div><input type="text" name="domain_name" id="domain_name" class="text" value="{$domain_name}" style="vertical-align:middle; width:186px; margin:0px; height:18px;"></div>							
			</td>
		</tr>
		<!-- Farms -->
		<tr class="hideFarm" style=" display:none;width:200px;">	
			<td class="td_with_padding">
				Farm:
			 </td>
			 <td class="td_with_padding">
				<div id="farm_target_combo"  style="float:left; width:200px;"></div><div class="loadmask" id="loadFarm" style="display:none;">Loading farms...</div>											
			 </td>
		</tr>
		<tr class="emptyFarm" style=" display:none;width:200px;">
			<td class="td_with_padding">
				Farm:
			 </td>
			 <td class="td_with_padding">
				<div id="farm_target_combo" style="float:left; width:400px;">You have no available farms</div><div class="loadmask" id="loadRoles" style="display:none;">Loading data...</div>
			 </td>
		</tr>
		<!-- Roles  -->
		<tr class="hideRole" style=" display:none;width:200px;">	
			<td class="td_with_padding">
				Role:
			 </td>
			 <td class="td_with_padding">
				<div id="role_target_combo"  style="float:left; width:200px;"></div><div class="loadmask" id="loadRole" style="display:none;">Loading roles...</div>											
			 </td>
		</tr>
		<tr class="emptyRole" style=" display:none;width:200px;">
			<td class="td_with_padding">
				Role:
			 </td>
			 <td class="td_with_padding">
				<div id="role_target_combo" style="float:left; width:400px;">There are no apache roles assigned to selected farm</div><div class="loadmask" id="loadRoles" style="display:none;">Loading data...</div>
			 </td>
		</tr>
	{include file="inc/intable_footer.tpl" color="Gray"}
<!-- End General -->

<!-- SSL -->	
	{include file="inc/intable_header.tpl" header="SSL" color="Gray"}
		<tr style="height:18px;">
			<td class="td_with_padding" colspan="10"><input type="checkbox" id="isSslEnabled" name="isSslEnabled" value="{if $host_ssl_enabled}{$host_ssl_enabled}{else}0{/if}" onClick="ShowCertificateFields(this,'SSLFields')" {if $host_ssl_enabled}checked="checked"{/if}> Enable SSL</td>
		</tr>
		<tbody id="SSLFields" style="display:none;">
			<tr style="width:200px;">
				<td class="td_with_padding" style="width:200px;">
					SSL certificate:
				 </td>
				 <td class="td_with_padding" >
					<input type="file" name="ssl_cert"  id="ssl_cert" class="text">	
					{if $ssl_cert_name}(Current: {$ssl_cert_name}){/if}			
				 </td>
			</tr>
			<tr style="width:200px;">
				<td class="td_with_padding" style="width:200px;">
					SSL key:
				</td>
				<td class="td_with_padding" >
					<input type="file" name="ssl_key"  id="ssl_key" class="text">
				</td>
			</tr>
			<!-- CA cerificate -->
			<tr style="width:200px;">
				<td class="td_with_padding" style="width:200px;">
					CA certificate:
				</td>
				<td class="td_with_padding" >
					<input type="file" name="ca_cert"  id="ca_cert" class="text">
					{if $ca_cert_name}(Current: {$ca_cert_name}){/if}
				</td>
			</tr>
		</tbody>

	{include file="inc/intable_footer.tpl" color="Gray"}
<!-- End SSL -->

<!-- Options -->	
	{include file="inc/intable_header.tpl" header="Options" color="Gray"}
		<tr>
			<td width="width:200px;">{t}Document root{/t}:</td>
			<td colspan="6"><input type="text" class="text" style="width:390px;" size="50" id="document_root_dir" name="document_root_dir" value="{$document_root_dir}"></td>
		</tr>
		<tr>
			<td width="width:200px;">{t}Logs directory{/t}:</td>
			<td colspan="6"><input type="text" class="text" style="width:390px;" size="50" id="logs_dir" name="logs_dir" value="{$logs_dir}"></td>
		</tr>
		<tr>
			<td width="width:200px;">{t}Server admin's email{/t}:</td>
			<td colspan="6"><input type="text" class="text" size="25" id="server_admin" name="server_admin" value="{$server_admin}"></td>
		</tr>
		<tr>
			<td width="width:200px;">{t}Server alias (space separated){/t}:</td>
			<td colspan="6"><input type="text" class="text" size="25" id="aliases" name="aliases" value="{$aliases}"> {if $domain_name} ({t}Exclude{/t}: {$domain_name}, www.{$domain_name}){/if}</td>
		</tr>		
		<tr>			 
			<td width="width:200px;" style="vertical-align:top;">{t}Server non-SSL template{/t}:</td>
			<td colspan="6" class="td_with_padding">
				<textarea  id="user_template" name="user_template"  class="text" style="vertical-align:top; height:300px; width:690px; margin-left:0;" size="25" >{$user_template}</textarea>
			</td>				
		</tr>
			<tr id="userTemplateBodySSL" style="display:none;">			 
				<td width="width:200px;" style="vertical-align:top;">{t}Server SSL template{/t}:</td>
				<td colspan="6" class="td_with_padding">
					<textarea  id="user_template_ssl" name="user_template_ssl"  class="text" style="vertical-align:top; height:300px; width:690px; margin-left:0;" size="25" >{$user_template_ssl}</textarea>
				</td>				
			</tr>
	{include file="inc/intable_footer.tpl" color="Gray"}
<!-- End Options -->

	<input type="hidden" name="set_to_default" id="set_to_default_id" value="0">
	<input type="hidden" name="vhost_id" id="vhost_id_id" value="{$vhost_id}">
	<input type="hidden" name="task" id="task" value="{if $task == 'edit'}edit{else}create{/if}">
{if $task == 'edit'}
	{include file="inc/table_footer.tpl"  button_js=1 show_js_button=1 button_js_name="Save changes" button_js_action="submitTask('edit');" button_js=2 show_js_button=2}
		
{else}
	{include file="inc/table_footer.tpl" button_js=1 show_js_button=1 button_js_name="Add new virtual host" button_js_action="submitTask('create');"}
{/if}
{include file="inc/footer.tpl"}

	