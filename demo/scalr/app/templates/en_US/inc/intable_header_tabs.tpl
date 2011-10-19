
<div {if $intableid}id="{$intableid}"{/if} {if $intable_classname}class="{$intable_classname}"{/if} style="display:{$visible};padding: {if $intablepadding}{$intablepadding}{else}7{/if}px;">
	
	{if $intable_tabs}
	{literal}
	<script language="Javascript">
		function SetActiveTab_i(id)
		{
			//
			// Unselect current active tab
			//
			var container = Ext.get('itabs_container');
			
			container.select('.TableHeaderLeft_Gray').each(function(el){ 
			   el.addClass('TableHeaderLeft_LGray');
			});  

			container.select('.SettingsHeader_Gray').each(function(el){ 
			   el.addClass('SettingsHeader_LGray');
			}); 
			
			container.select('.TableHeaderRight_Gray').each(function(el){ 
			   el.addClass('TableHeaderRight_LGray');
			}); 
			
			var ctab = Ext.get('itab_'+id);
			
			ctab.select('.TableHeaderLeft_LGray').each(function(el){ 
			   el.addClass('TableHeaderLeft_Gray');
			});  

			ctab.select('.SettingsHeader_LGray').each(function(el){ 
			   el.addClass('SettingsHeader_Gray');
			}); 
			
			ctab.select('.TableHeaderRight_LGray').each(function(el){ 
			   el.addClass('TableHeaderRight_Gray');
			}); 
			
			Ext.select('tbody.itab_contents').each(function(el){ 
			   el.dom.style.display = 'none';
			}); 
			
			var e = Ext.get('itab_contents_'+id);
			if (e)
				e.dom.style.display = '';
			
			try
			{
				OnTabChanged_i(id);
			}
			catch(e){}
		}
	</script>
	{/literal}
	<div id="itabs_container">
		{section name=id loop=$intable_tabs}
			{if $intable_selected_tab == $intable_tabs[id].id}
				{assign var="tab_color" value="Gray"}
			{else}
				{assign var="tab_color" value="LGray"}
			{/if}
			<div class="InTableTab" onClick="SetActiveTab_i('{$intable_tabs[id].id}');" id="itab_{$intable_tabs[id].id}" style="margin-left:7px;display:{$intable_tabs[id].display};float:left;">
				<table style="" cellpadding="0" cellspacing="0">
					<tr>
						<td width="7"><div class="TableHeaderLeft_{$tab_color}" style="height:25px;"></div></td>
						<td>
						<div id="itab_name_{$intable_tabs[id].id}" style="padding-top:2px;line-height:20px;" class="SettingsHeader_{$tab_color}" align="center">
							{$intable_tabs[id].name}
						</div>
						</td>
						<td width="7"><div class="TableHeaderRight_{$tab_color}" style="height:25px;"></div></td>
					</tr>
				</table>
			</div>
		{/section}
	<div style="clear:both;"></div>
	</div>
	{/if}
	
	<table border="0" cellpadding="0" cellspacing="0" width="100%" style="border-top:3px solid #dcdcdc;">
	<tr>
		<td width="7" class="TableHeaderCenter_{$color}"></td>
		<td class="Inner_{$color}">
			<table width="100%" cellspacing="0" cellpadding="2" id="Webta_InnerTable_{$header}" {if $section_closed}style="display: none;"{/if}>
			<tbody id="intable_top_empty_tr">
			<tr>
				<td width="{if $intable_first_column_width}{$intable_first_column_width}{else}20%{/if}"></td>
				<td colspan="{if $intable_colspan}{$intable_colspan}{else}1{/if}" width="80%" style="height:15px;"></td>
			</tr>
			</tbody>
			