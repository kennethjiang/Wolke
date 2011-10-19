{include file="inc/header.tpl"}
	<style>
	{literal}
		.s_field_container
		{
			margin-bottom:10px;
			width:400px;
		}
		
		.s_field_name
		{
			width:150px;
			float:left;
			vertical-align:middle;
		}
		
		.td_with_padding
		{	height:18px; 
			padding-bottom: 2px;
			padding-top: 2px;
			margin: 0px;
		}
		.ScriptArgItem label
		{	
			width: 20%;
			font-size:10pt;
			padding-right:0;
			padding-bottom: 2px;
			padding-top: 2px;
			margin:0px;	
		}
		.ScriptArgItem input
		{	
			padding-left:0%;
		}
		.hideIfTerminate
		{
			display='none';
		}
		.hideFarm
		{
			display='';
		}
		.hideRole
		{
			display='none';
		}
		.hideServer
		{
			display='none';
		}
		
		input#issync_1 
		{
		 margin: 5px;
		}
		input#issync_0
		{
		 margin: 3px;
		}
		.loadmask
		{		
			float:left;
			padding-left: 20px;	
			background-image:url("../images/extjs-default/grid/loading.gif");
			display='';
			background-repeat: no-repeat;
		}
		  				
	{/literal}
	</style>
	 <script src="/js/farm_role_server_loader.js"></script> 
	<script language="javascript">

	var serverCombo 		= null;	
	var typeCombo 			= null;
	var farmsCombo 			= null;
	var roleCombo 			= null;
	var scrtipCombo 		= null;
	var scriptRevisionCombo = null;
	var scriptFieldsPanel 	= null;
	var farmsStore			= null;
	var roleStore			= null;
	var serverStore			= null;
	var scriptStore 		= null;
	var scriptRevisionStore	= null;	
	var loadingMask 		= null;
	{literal}
	
	var create_form = Boolean("{$create_form}");  		// true - create form, false - edit form
	var formData 	= {/literal}{$formData}{literal}; 	// data array from php script
	
	if(formData.task == 'edit' && formData.task_type == 'script_exec')
	{
		var scriptSettings 		= {/literal}{$scriptSettings}{literal};
		var loadedScriptId 		= scriptSettings.scriptid;
		var loadedScriptRevision 	= scriptSettings.revision;
		var loadedScriptArgs 	= {/literal}{$script_args}{literal};
	}    
	
	Ext.onReady(function () 
	{
			HideAllSettings();
			loadingMask = new Ext.LoadMask(Ext.getBody(), {msg:"Processing task. Please wait..."});
							
//// Script Revision
		
			scriptRevisionCombo = FarmRoleServerHelper.newScriptRevisionCombo(
			{				
				listeners:
				{
					select:function(combo, record, index)
					{	 						
						la = Ext.get("loadArguments");

						if (la)
							la.dom.style.display='';
						
		       			var fields = Ext.decode(record.data.fields);

			       		// if script asks for arguments
		       			if(Ext.isEmpty(fields) == false)
		       			{	
			       			LoadRevisionArguments(combo,fields,loadedScriptArgs);			       			
			    		}
			       		else
			       		{	     				
			       			if(scriptFieldsPanel) 									
								scriptFieldsPanel.removeAll();
		       			}
		       			if(la)	
		       				la.dom.style.display='none';
		   			}	
				}					
			},
			// storeConfig
			{
				listeners:
				{
					load:function(params, reader, callback, scope, arg)
					{				
						if((scriptCombo.value == loadedScriptId) && formData.task == 'edit')
						{							 
							SelectComboByValue(scriptRevisionCombo,scriptRevisionStore,loadedScriptRevision,"revision");										
						}
						if(Ext.get("loadVersion"))
							Ext.get("loadVersion").dom.style.display='none';
					}
				}
			}

			);		
				
			scriptRevisionStore = scriptRevisionCombo.store;			
			
			
///// scripts
			scriptCombo = FarmRoleServerHelper.newScriptCombo(
				{
					listeners:
					{
						select:function(combo, record, index)
						{								
							// load scipt's versions of the selected script by its id
							if(scriptRevisionCombo)
							{
			   					scriptRevisionStore.setBaseParam('scriptId',record.data.id);			   					
			   					scriptRevisionStore.load();			   					
							}
			   				
			   				if(typeCombo.value == "script_exec" && Ext.get("loadVersion"))				   				
			   						Ext.get("loadVersion").dom.style.display='';
								  							
							//  reset selected text in scriptRevisionCombo
			   				if(scriptRevisionCombo)
			   					scriptRevisionCombo.clearValue();
		   					
			   				if(scriptFieldsPanel) 								
								scriptFieldsPanel.removeAll();								
						}
					}	
				},
				{
					listeners:
					{
						load:function(params, reader, callback, scope, arg)
						{ 															
							if(formData.task_type == 'script_exec' && formData.task == 'edit')
							{
								// SetScript(loadedScriptId);
								 SelectComboByValue(scriptCombo,scriptStore,loadedScriptId,"id");
								
							}
							if(Ext.get("loadVersion"))
		   						Ext.get("loadVersion").dom.style.display='none';
						}	
					}
				}
			);
			
			scriptStore = scriptCombo.store;
			
////// task type
			typeCombo = FarmRoleServerHelper.newTaskTypeCombo(
			{
				listeners: 
				{
					select:function(combo, record, index)
						{
							HideAllSettings();
							
							// select the options' set which would be displayed
							if(record.data.id == 'script_exec')
							{
								//  load scripts of current user
								if(scriptStore)
									scriptStore.load();
								
								if( Ext.get('script_options'))
								 	Ext.get('script_options').dom.style.display = '';						
							}
							
							if(record.data.id  == 'terminate_farm')
							{
								// show farm_term options
								if(Ext.get('terminate_options'))
									Ext.get('terminate_options').dom.style.display = '';
							}
							
							if(record.data.id == 'launch_farm')
							{				
								// Everything has been hidden
							}
							
						},
					afterrender: function(combo)
						{						
							var task = null;
							if(formData.task == 'edit')
							{		
								task = formData.task_type;
								combo.disable();					
							}
							else
							{
								task = "script_exec";
							}
							
							combo.setValue(task);
							index = combo.store.find("id", task);
							combo.fireEvent("select", combo, combo.store.getAt(index), index);
						}
					}
			},
			null
			);
			taskTypeStore = typeCombo.store;			
						
			if(formData.task == 'create')
			{

//// Servers
				serverCombo = FarmRoleServerHelper.newServerCombo(
					{
						listeners:
						{
							select: function(combo,record,index)
							{
								if(Ext.get('server_radio'))
									Ext.get('server_radio').dom.checked = true;		
							}							
						}
					},
					// serverStore
					{
						listeners:
						{							
							load:function(params, reader, callback, scope, arg)
							 { 		
						 		if(Ext.get("loadServer"))
									Ext.get("loadServer").dom.style.display='none';
								
								if(params.data.length > 0)
								{ 
									// display servers if it's not empty									
									HideComboByClassName('Server',true,false);
								}
								else
								{ // hide servers
									HideComboByClassName('Server',false,true);							

								}
							 }							
						}
					}
				);
				serverStore = serverCombo.store;

//// Roles 
				roleCombo = FarmRoleServerHelper.newRoleCombo(
					{
						listeners:
						{
							select:function(combo, record, index)
							{		
								if(serverStore)
								{									
					    			serverStore.baseParams.farm_roleId = record.data.id;
					    			serverStore.baseParams.farmId = farmsCombo.value;						    		
					    			serverStore.load();
								}
					    		if(typeCombo.value == "script_exec" && Ext.get("loadServer"))
					    			Ext.get("loadServer").dom.style.display='';
					    										
					    		HideComboByClassName('Server',false,false);	
														    		
					    		if(Ext.get('role_radio'))
					    			Ext.get('role_radio').dom.checked = true;
					    		//  reset selected text in instanceCombo
				   				if(serverCombo)
				   					serverCombo.clearValue();
							}
						}
					},
					
					// roleStore:
					{	
						listeners:
						{
							load:function(params, reader, callback, scope, arg)
							 {
								 // show roles field only if it's not empty	
								 if(Ext.get("loadRole"))							
									Ext.get("loadRole").dom.style.display='none'; 	
								if(params.data.length > 0)
			       				{
			       					if(Ext.get('role_target_combo'))			       					
			       						HideComboByClassName('Role',true,false);	
			       					
			       				}
			       				else
			       				{
				       				if(Ext.get('role_target_combo'))		       						
			       						HideComboByClassName('Role',false,true);
		       							
			       				}
							  }
						}
					}
				);
				roleStore = roleCombo.store;
//// Farms
				farmsCombo = FarmRoleServerHelper.newFarmsCombo(
					{
						listeners:
						{
							select:function(combo, record, index)
							{									
								HideComboByClassName('Role',false,false);
								HideComboByClassName('Server',false,false);										
								
								// load roles and show them only for "script_exec" task type
								if(typeCombo.value == "script_exec")
								{
									if(Ext.get("loadRole"))
										Ext.get("loadRole").dom.style.display='';	
									
									// load farm roles of selected farm by farmId ( from farmsStore comboBox)			
					       			roleStore.baseParams.farmId = record.data.id; 
					       				roleStore.load();	
				    						
									//  reset selected text in roleCombo
					   				if(roleCombo)
					   					roleCombo.clearValue();	
							    	
					   				if(Ext.get('farm_radio'))
										Ext.get('farm_radio').dom.checked = true;											
								}		       				
				   			} 
						}
					},
					{
						listeners:
						{
							load:function(params, reader, callback, scope, arg)
							 {
						 		if(Ext.get("loadFarm"))
									Ext.get("loadFarm").dom.style.display='none';
								 // show farms field only if it's not empty
								if(params.data.length > 0)
			       				{
			       					if(Ext.get('farm_target_combo'))			       					
			       						HideComboByClassName('Farm',true,false);
			       					
			       				}
			       				else
			       				{
			       					if(Ext.get('farm_target_combo'))
			       						HideComboByClassName('Farm',false,true);
			       				}
							 }
						}
					}
				);
				farmsStore = farmsCombo.store;

				
			} // END OF  "if(formData.task == 'create')"

				
		if(formData.task == 'create')
		{
			farmsStore.load();
			if(Ext.get("loadFarm"))
				Ext.get("loadFarm").dom.style.display='';
		}		
		
	});

	function SubmitTask(value)
	{			
		loadingMask.show();
        
		ajaxUrl = '/scheduler_task_add.php?action=SaveTask';
		
		if(value == "create")
			ajaxUrl += '&task=create';
		else
		{
			if(value == "edit")
				ajaxUrl += '&task=edit&task_id='+formData.task_id;
			else
				return 0;
		}
		typeCombo.enable();
		
		Ext.Ajax.request({
			url: ajaxUrl, 
			method: 'POST',
			form: 'frm',
			failure: function()
			{			
				if(formData.task == 'edit')			
					typeCombo.disable();
				
				Scalr.Viewers.ErrorMessage("Some connection problems occurred");						
			},	
					
			success: function(transport) 
			{ 						
				try
				{
					if(formData.task == 'edit')
						typeCombo.disable();
							
					loadingMask.hide();
							
					if (transport.responseText)
					{
								var data = Ext.decode(transport.responseText);	
                            
								if(data.result == "error")
									Scalr.Viewers.ErrorMessage(data.msg);
								
								if(data.result == "ok")
								{
									Scalr.Viewers.SuccessMessage(data.msg);
													
									loadedScriptId 		 = scriptCombo.value;														 
									loadedScriptRevision = scriptRevisionCombo.value;
									loadedScriptArgs 	 = Ext.decode(data.args);

									document.location = '/scheduler.php';
								}	
							}							
						}
						catch(e)
						{
							 // alert(e.message);
						}		
						if(Ext.get('script_loader'))				
							Ext.get('script_loader').dom.style.display = 'none';					 
					}
		}) 		
				
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
		
<!--  Task info -->
		{include file="inc/intable_header.tpl" header="Task" color="Gray"}
			<tr style="height:18px; ">
				<td class="td_with_padding" style="width:200px;">Task name:</td>
				<td class="td_with_padding">
				 <div><input type="text" name="task_name" id="task_name" class="text" value="{$taskinfo.task_name}" style="vertical-align:middle;width:186px;margin:0px; height:18px;"></div>							
				</td>
			</tr>
			<tr style="height:18px; ">
				<td class="td_with_padding">Task type:</td>
				<td class="td_with_padding">
					<div id="task_type_combo" style="float:left; width:200px;"></div><div class="loadmask" id="loadFarm" style="display:none;">Loading farms...</div>									
				</td>
			</tr>
		{include file="inc/intable_footer.tpl" color="Gray"}
		
<!--  Target  -->	
		{include file="inc/intable_header.tpl" header="Target" color="Gray"}			

			{if $farminfo && $task == 'edit'} 
				<tr style="height:18px; ">
					<td class="td_with_padding" style="width:200px;">On all servers of this farm:</td>					
					<td class="td_with_padding"><div id="farm_target_combo">{$farminfo.name}</div></td>
				</tr>
			{/if}
			{if !$farminfo || $task != 'edit'}
			<tr class="hideFarm" style="height:1px; display:">			
					<td class="td_with_padding" style="width:200px;">		
						<input type="radio" id="farm_radio" name="target_type" value="farm" style="vertical-align:middle;" checked>
						On all servers of this farm : 
					</td>				
					<td class="td_with_padding">
						<div id="farm_target_combo" style="float:left; width:200px;"></div><div class="loadmask" id="loadRole" style="display:none;">Loading roles...</div>
					</td>
				</tr>
				<tr class="emptyFarm" style="height:1px; display:none">						
					<td class="td_with_padding" style="width:200px;">
						On all servers of this farm : 
					</td>				
					<td class="td_with_padding">
						<div id="farm_target_combo" style="float:left; width:200px;">Farms are not available</div>
					</td>
				</tr>
			{/if}				
			
			
			{if $roleinfo && $task == 'edit'}		
				<tr  class="roleInfo" style="height:1px; display:">	
					<!-- farm_role -->
					<td class="td_with_padding" style="width:200px;">On all servers of this role: </td>
					<td class="td_with_padding"><div id="role_target_combo">{$roleinfo.name}</div></td>
				</tr>
			{/if}
			{if  !$roleinfo || $task != 'edit'}			
				<tr class="hideRole" style="height:1px; display:none;">	
					<td class="td_with_padding">
						<input type="radio" id="role_radio" name="target_type" value="role" style="vertical-align:middle; display='none'"> 
						 On all servers of this role:
					 </td>
					 <td class="td_with_padding">
						<div id="role_target_combo"  style="float:left; width:200px;"></div><div class="loadmask" id="loadServer" style="display:none;">Loading servers...</div>											
					 </td>
				</tr>
				<tr class="emptyRole" style="height:1px; display:none;">
					<td class="td_with_padding">
					On all servers of this role:
					 </td>
					 <td class="td_with_padding">
						<div id="role_target_combo" style="float:left; width:400px;">There is no roles assigned to selected farm</div><div class="loadmask" id="loadServer" style="display:none;">Loading data...</div>
					 </td>
				</tr>
			{/if}	


			{if $serverInfo && $task == 'edit'}
		    	<tr class="serverInfo" style="height:1px; display:">
		    		<td class="td_with_padding" >
		    			On server:    			
		    		</td>
		    		<td class="td_with_padding">
		    			{$serverInfo.remoteIp} 
		    		 </td>
		    	</tr>
	    	{/if}							
			{if !$serverInfo || $task != 'edit'}
		    	<tr class="hideServer" style="height:1px; display:none;">      		
		    		<td class="td_with_padding">
		    			<input type="radio"  id="server_radio" name="target_type" value="instance" style="vertical-align:middle;">
		    			On server:
		    		</td>
		    		<td class="td_with_padding">
		    			<div id="server_target_combo" style="float:left; width:200px;"></div>
					</td>
		    	</tr>
		    	<tr class="emptyServer" style="height:1px; display:none;">
					<td class="td_with_padding">
						On server:
					 </td>
					 <td class="td_with_padding">
						<div id="role_target_combo" style="float:left; width:400px;">There is no running servers on selected role</div>
					 </td>
				</tr>		
	    	{/if}	
			{include file="inc/intable_footer.tpl" color="Gray"}
			
<!--  Settings  -->	
		{include intableid="task_settings" file="inc/intable_header.tpl" header="Task settings" color="Gray"}		
		<tr>
			<td class="td_with_padding">Start time</td>
			<td class="td_with_padding">
				<div id="StartDate" class="td_with_padding">   							
				</div>    						
				<script type="text/javascript">
				{literal}	
				ds = new Ext.form.DateField(
				{
					renderTo: 'StartDate',
					format: "Y-m-d H:i:s",	
					width: 194,							
					name: 'startDateTime',
					value: '{/literal}{$taskinfo.start_time_date}{literal}'
				})
				ds.render();
				{/literal}
				</script>    
			</td>
			
		</tr>
		<tr>
			<td class="td_with_padding">End time</td>
			<td class="td_with_padding">
				<div id="EndDate" class="td_with_padding">   							
				</div>    						
				<script type="text/javascript">
				{literal}	
				df = new Ext.form.DateField(
				{
					renderTo: 'EndDate',
					format: "Y-m-d H:i:s",	
					width: 194,							
					name: 'endDateTime',
					value: '{/literal}{$taskinfo.end_time_date}{literal}'
				})
				df.render();
				{/literal}
				</script>    
			</td>
			
		</tr>    				
		<tr>
			<td class="td_with_padding">Restart every:</td>
			<td class="td_with_padding">
				<div><input class="text"  style='vertical-align:middle;  margin:0px; width:186px;' type='text' name='restart_timeout' id='restart_timeout' value="{if $taskinfo.restart_every}{$taskinfo.restart_every}{else}1440{/if}" size="5" > minutes.
			0 - task will be executed only once.</div>
			</td>
		</tr>
		<tr>
			<td class="td_with_padding">Priority:</td>
			<td class="td_with_padding">
				<div><input type='text' class='text'  id="order_index" name="order_index" value="{if $taskinfo.order_index}{$taskinfo.order_index}{else}0{/if}" style="width:186px; margin:0px;" >  0 - the highest priority</div>
			</td>
		</tr>
		<tr>
			<td class="td_with_padding">Timezone:</td>
			<td class="td_with_padding">
				<select name="timezone" id="timezone" class="text" style="margin:0px;padding:0px;">
				{section name=id loop=$timezones}
					<option value="{$timezones[id]}" {if $timezones[id] == $timezone}selected="selected"{/if}>{$timezones[id]}</option>
				{/section}
				</select>
			</td>
		</tr>
		{include file="inc/intable_footer.tpl" color="Gray"}
		
		
<!-- Script settings -->
		{include intableid="script_options" file="inc/intable_header.tpl" header="Script options" color="Gray"}
			<tr>
				<td class="td_with_padding" style="width:200px;">Script:</td>
				<td class="td_with_padding">
					<div id="script_target_combo" style="float:left; width:200px;"></div><div class="loadmask" id="loadVersion" style="display:none;">Loading versions...</div>
			    </td>
			</tr>
	        <tr>
				<td class="td_with_padding">Execution mode:</td>
				<td class="td_with_padding">
					<input type="radio" name="issync" value="1" id="issync_1" {if $issync == '1'}checked{/if} style="vertical-align:middle;"> {t}Synchronous{/t} &nbsp;&nbsp;
					<input type="radio" name="issync" value="0" id="issync_0" {if $issync != '1'}checked{/if} style="vertical-align:middle;"> {t}Asynchronous{/t} 
				</td>
				<td></td>
			</tr>		
			<tr>
				<td class="td_with_padding">Version:</td>
				<td class="td_with_padding">
					<div id="version_target_combo" style="float:left; width:200px;"></div><div class="loadmask" id="loadArguments" style="display:none;">Loading arguments...</div>
			    </td>
			</tr>	
			<tr>
				<td class="td_with_padding">Timeout:</td>
				<td class="td_with_padding">				
				<div><input type='text' class='text' id="timeout" name="timeout" value="{if $timeout}{$timeout}{else}1000{/if}" style="margin:0px; width:188px;" > seconds</div>
				</td>
				<td></td>
			</tr>
			<tr>
			<td colspan="2" >
				<div id="event_script_config_container">				
				</div>
			</td>	
			</tr>	
		{include file="inc/intable_footer.tpl" color="Gray"}

<!--  Terminate settings -->
		{include intableid="terminate_options" file="inc/intable_header.tpl" visible="none" header="Terminate settings" color="Gray"}		
		<tr>
			<td class="td_with_padding" colspan="2">
				<input type="checkbox"  style="vertical-align:middle;" {if $deleteDNS}checked="checked"{/if} name="deleteDNS" id="deleteDNS" value="1">
				<span style="vertical-align:middle;">Delete DNS zone from nameservers. It will be recreated when the farm is launched.</span>
				<br>
				<br>
			</td>			
		</tr>		
		<tr>	
			<td class="td_with_padding" colspan="2">
				<input type="radio" id="keep_elastic_ips_0" style="vertical-align:middle;" {if !$keep_elastic_ips}checked="checked"{/if} name="keep_elastic_ips" value="0">				
				<span style="vertical-align:middle;">Release the static IP adresses that are allocated for this farm. When you start the farm again, new IPs will be allocated.</span>
			</td>
		</tr>
		<tr>	
			<td class="td_with_padding" colspan="2"> 
				<input type="radio" id="keep_elastic_ips_1" style="vertical-align:middle;" {if $keep_elastic_ips}checked="checked"{/if} name="keep_elastic_ips" value="1">				
				<span style="vertical-align:middle;">Keep the static IP adresses that are allocated for this farm. Amazon will keep billing you for them even when the farm is stopped.</span>
				{$keep_elastic_ips}
				<br>
				<br>
			</td>			
		</tr>
		<tr>			
			<td class="td_with_padding" colspan="2">
				<input type="radio" style="vertical-align:middle;" {if !$keep_ebs}checked="checked"{/if}  name="keep_ebs" id="keep_ebs_0" value="0">				
				<span style="vertical-align:middle;">Release the EBS volumes created for this farm. When you start the farm again, new EBS volumes will be created.</span>
				{$keep_ebs}
			</td>				
		</tr>
		<tr>
			<td class="td_with_padding" colspan="2">
				<input type="radio" style="vertical-align:middle;" {if $keep_ebs}checked="checked"{/if} name="keep_ebs" id="keep_ebs_1" value="1">				
				<span style="vertical-align:middle;">Keep the EBS volumes that are created for this farm. Amazon will keep billing you for them even when the farm is stopped.</span>
			</td>			
		</tr>
		{include file="inc/intable_footer.tpl" color="Gray"}
			
<!-- "Saves changes" button or "Add task" button -->			
			{if $task != 'edit'} 
				{include file="inc/table_footer.tpl" button_js=1 show_js_button=1 button_js_name="Add task" button_js_action="SubmitTask('create');"}
			{else}							
				{include file="inc/table_footer.tpl" button_js=1 show_js_button=1 button_js_name="Save changes" button_js_action="SubmitTask('edit');"}
			{/if}	
	</div>
{include file="inc/footer.tpl"}
	
	