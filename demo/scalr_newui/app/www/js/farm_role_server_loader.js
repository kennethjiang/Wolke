
	FarmRoleServerHelper = (function () 
	{
						
		pub = {			
//// TaskType 						
				newTaskTypeStore: function (config) 
				{	
					var taskTypeData = [ 
	                  	['script_exec','Execute script'],
	                  	['terminate_farm','Terminate farm'],
	                  	['launch_farm','Launch farm']
	               ];
					
					var defaults =
					{
					    autoDestroy: 	true,
					    storeId: 		'taskTypeStore',
					    fields: 		['id','name'],
					    data: 			taskTypeData			
					}
					
					return new Ext.data.ArrayStore(Ext.apply(defaults, config || {}));
				},
				
				newTaskTypeCombo: function (config,storeConfig)
				{
					var  defaults = 
					{		
							renderTo:		'task_type_combo',
							width:			192,				
							hiddenName:		'task_type',
							store:			null,
							displayField:	'name',
							valueField:		'id',
							typeAhead:		true,
							mode:			'local',
							triggerAction:	'all',
							selectOnFocus:	true,
							emptyText:		'Select task...',
							listeners:		{}
					}
					config = Ext.apply(defaults, config || {});
					
					if(!config.store)
					{
						config.store = this.newTaskTypeStore(storeConfig);
					}			
					
					return new Ext.form.ComboBox(config);					
				},
//// Scripts 				
				newScriptStore: function (config) 
				{					
					var defaults =
					{
						url: "/server/ajax-ui-server.php",
						baseParams: {action: "LoadScripts"},			
						reader: new Ext.ux.scalr.JsonReader(
						{
							root: 'data', // from php file: array("data" => $result);
							id: 'id',
							fields: 
							[
								'id','name'
							]
						})		
					}
					
					return new Ext.data.Store(Ext.apply(defaults, config || {}));
				},
				
				newScriptCombo: function (config,storeConfig)
				{
					var  defaults = 
					{		
							renderTo:		'script_target_combo',
							width:			192,							
							hiddenName:		'scriptid',
							valueField:		'id',						
							store:			null,
							displayField:	'name',
							typeAhead:		true,
							mode:			'local',
							triggerAction:	'all',
							emptyText:		'Select script...',
							selectOnFocus:	true,
							listeners:		{}
					}
					config = Ext.apply(defaults, config || {});
					
					if(!config.store)
					{
						config.store = this.newScriptStore(storeConfig);
					}			
					
					return new Ext.form.ComboBox(config);					
				},
				
///// ScriptRevision				
				newScriptRevisionStore: function (config) 
				{					
					var defaults =
					{
						url: "/server/ajax-ui-server.php",
						baseParams: {action: "GetScriptArgs"},			
						reader: new Ext.ux.scalr.JsonReader(
						{
							root: 'data', // from php file: array("data" => $result);
							id: 'revision',
							fields: 
							[
								'revision','fields'
							]
						})		
					}
					
					return new Ext.data.Store(Ext.apply(defaults, config || {}));
				},
				
				newScriptRevisionCombo: function (config,storeConfig)
				{
					var  defaults = 
					{		
						renderTo:		'version_target_combo',
						width:			192,							
						hiddenName:		'script_version',
						store:			null,
						displayField:	'revision',
						valueField:	    'revision',
						typeAhead:		true,
						mode:			'local',
						triggerAction:	'all',
						emptyText:		'Select version...',
						selectOnFocus:	true,
						listeners:		{}
					}
					config = Ext.apply(defaults, config || {});
					
					
					if(!config.store)
					{
						config.store = this.newScriptRevisionStore(storeConfig);
					}			
					
					return new Ext.form.ComboBox(config);					
				},
				
//////  Farm				
				newFarmsStore: function (config) 
				{					
					var defaults =
					{
						url: "/server/ajax-ui-server.php",
						baseParams: {action: "LoadFarms",scriptId:""},			
						reader: new Ext.ux.scalr.JsonReader(
						{
							root: 'data', // from php file: array("data" => $result);
							id: 'id',
							fields: 
							[
								'id','name'
							]
						})
					}
					
					return new Ext.data.Store(Ext.apply(defaults, config || {}));
				},
				
				newFarmsCombo: function (config,storeConfig)
				{
					var  defaults = 
					{		
						renderTo:		'farm_target_combo',
						width:			192,							
						hiddenName:		'farm_target',
						store:			null,
						displayField:	'name',
						valueField:	    'id',
						typeAhead:		true,
						mode:			'local',
						triggerAction:	'all',
						emptyText:		'Select farm...',
						selectOnFocus:	true,
						listeners:		{}
					}
					config = Ext.apply(defaults, config || {});					
					
					if(!config.store)
					{
						config.store = this.newFarmsStore(storeConfig);
					}			
					
					return new Ext.form.ComboBox(config);					
				},
//// Roles			
				newRoleStore: function (config) 
				{					
					var defaults =
					{
						url: "/server/ajax-ui-server.php",
						baseParams: {action: "LoadFarmRoles"},			
						reader: new Ext.ux.scalr.JsonReader(
						{
							root: 'data', // from php file: array("data" => $result);
							id: 'id',					        	
							fields: 
							[
								'id','name'
							]
						})	
					}
					
					return new Ext.data.Store(Ext.apply(defaults, config || {}));
				},
				
				newRoleCombo: function (config,storeConfig)
				{
					var  defaults = 
					{		
							renderTo:		'role_target_combo',
							width:			192,							
							hiddenName:		'role_target',
							valueField:		'id',
							store:			null,
							displayField:	'name',
							typeAhead:		true,
							farmsCombo:		farmsCombo,
							triggerAction:	'all',
							emptyText:		'Select role...',
							selectOnFocus:	true,
							listeners:{}
					}
					config = Ext.apply(defaults, config || {});					
					
					if(!config.store)
					{
						config.store = this.newRoleStore(storeConfig);
					}			
					
					return new Ext.form.ComboBox(config);					
				},
				
//// Server				
				newServerStore: function (config) 
				{					
					var defaults =
					{
						url: "/server/ajax-ui-server.php",
						baseParams: {action: "LoadServers"},			
						reader: new Ext.ux.scalr.JsonReader(
							{
								root: 'data', // from php file: array("data" => $result);
								id: 'server_id',					        	
								fields: 
								[
									'server_id','remote_ip'
								]
							}	
						)
					}
					
					return new Ext.data.Store(Ext.apply(defaults, config || {}));
				},
				
				newServerCombo: function (config,storeConfig)
				{
					var  defaults = 
					{		
							renderTo:		'server_target_combo',
							width:			192,
							hiddenName:		'server_target',
							valueField:		'server_id',
							store:			null,
							displayField:	'remote_ip',
							typeAhead:		true,
							mode:			'local',
							triggerAction:	'all',
							emptyText:		'Select server...',
							selectOnFocus:	true,
							listeners:		{}
					}
					config = Ext.apply(defaults, config || {});					
					
					if(!config.store)
					{
						config.store = this.newServerStore(storeConfig);
					}			
					
					return new Ext.form.ComboBox(config);					
				},
				
				newArgumentsPanel: function (config)
				{
					var  defaults = 
					{								
						labelWidth: '20%', // label settings here cascade unless overridden										
						renderTo: 'event_script_config_container',
						frame:false,
						header:false,
						title: '',
						items: null,
						bodyStyle:'padding:2px 0 0 0; border:none; background-color:#F4F4F4;',
						cls: "PanelItem",
						width: '100%',
						defaults: {width: 192},
						layout: 'form',
						defaultType: 'textfield'
					}
					config = Ext.apply(defaults, config || {});	
					
					return new Ext.Panel(config);					
				}		
				
				
		}	
		return pub;
	}
	)();
	
//////  End of FarmRoleServerHelper
 
	function HideAllSettings()
	{// function hides role and instance dd lists and script_options and terminate_options menues.
	// all menu items can be shown when it's necessary
			
		HideComboByClassName('Role',false,false);
		HideComboByClassName('Server',false,false);
				
		if(Ext.get('scripts_arg_section'))
			Ext.get('scripts_arg_section').dom.style.display = 'none';
		if(Ext.get('script_options'))
			Ext.get('script_options').dom.style.display = 'none';
		if(Ext.get('terminate_options'))
			Ext.get('terminate_options').dom.style.display = 'none';
	}
		
	// hides all .hide and .empty elements by className
	function HideComboByClassName(ClassName,hide,empty)
	{
		var strHide = 'none';
		var strEmpty = 'none';
		
		if(hide)		
			strHide = ''
		
		if(empty)		
			strEmpty = ''

		Ext.select('.hide'+ClassName).each(function (el) {
			el.dom.style.display = strHide;
		});	
		Ext.select('.empty'+ClassName).each(function (el) {
			el.dom.style.display = strEmpty;
		});	
	}
	
	function LoadRevisionArguments(combo,fields,scriptArgs)
	{
		// var loadedScriptArgs = {/literal} {$script_args} {literal};	
		// form a script_args arrey to be presented in $_POST
			var panelItems = new Array();
			
			if((scriptCombo.value == loadedScriptId) &&
					(combo.value == loadedScriptRevision)&&
					(scriptArgs != "") )				       				
			{
				for (var name in fields) 
				{
					panelItems.push({
						fieldLabel: fields[name],
						name: "script_args[" + name + "]",
						value: scriptArgs[name].value,
						itemCls: "ScriptArgItem" 
					});
				}
			}
			else
			{				
				for (var name in fields)
				{
					panelItems.push({
						fieldLabel: fields[name],
						name: "script_args[" + name + "]",
						itemCls: "ScriptArgItem" 										
					});
				}
			}
		// update (delete & reload) sctipt parameters. 
		if(scriptFieldsPanel) 
		{
			scriptFieldsPanel.removeAll();
		}
						 
		scriptFieldsPanel = FarmRoleServerHelper.newArgumentsPanel({ 
			items: panelItems
		});
		
		
	}
	function SelectComboByValue(combo,store,ID,findBy)
	{
		index =  store.find(findBy, ID);
		if(index >= 0)
		{
			combo.setValue(ID);
			combo.fireEvent("select", combo, store.getAt(index),index);
		}
	}
	
	function GetLatestRevision(items)
	{
		var max = 0;
		
		for(var i = 0; i<items.length; i++)	
		{
			if(items[i].data.revision > max)
			 max = items[i].data.revision;
		}
		return max;
	}
			

