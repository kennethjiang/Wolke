{if !$nofilter}
<table border="0" width="100%" cellspacing="0" cellpadding="0" height="40">
	<tr>
		<td align="center" nowrap width="10">&nbsp;</td>
		<td align="left" valign="bottom" width="500px">
			<div style="padding:0px;">
			{if $show_region_filter}
				{include file="inc/region_filter.tpl"}
			{/if}
			{if $filter}{$filter}{/if}
			</div>
		</td>
		<td colspan="4" align="left" valign="bottom">{$paging}</td>
		<td align="center" nowrap>&nbsp;</td>
	</tr>
</table>
{/if}

{if $tabs}
{literal}
<script language="Javascript">
	function SetActiveTab(id, itable_tabs)
	{
		//
		// Unselect current active tab
		//
		var container = Ext.get('tabs_container');
		
		var elems = container.select('.TableHeaderLeft');
		elems.each(function(item){    
			item.dom.className = 'TableHeaderLeft_LGray';
		});
	
		var elems = container.select('.TableHeaderCenter');
		elems.each(function(item){    
			item.dom.className = 'TableHeaderCenter_LGray';
		});
		
		var elems = container.select('.TableHeaderRight');
		elems.each(function(item){    
			item.dom.className = 'TableHeaderRight_LGray';
		});
		
		var elems = container.select('.TableHeaderContent');
		elems.each(function(item){    
			item.dom.bgColor = '#f4f4f4';
			item.dom.className = 'TableHeaderContent_LGray';
		});
		
		//
		// Select active tab
		//
		var ctab = Ext.get('tab_'+id)
		
		var elems = ctab.select('[class="TableHeaderLeft_LGray"]');
		elems.each(function(item){    
			item.className = 'TableHeaderLeft';
		});
		
		var elems = ctab.select('[class="TableHeaderCenter_LGray"]');
		elems.each(function(item){    
			item.className = 'TableHeaderCenter';
		});
		
		var elems = ctab.select('[class="TableHeaderRight_LGray"]');
		elems.each(function(item){    
			item.className = 'TableHeaderRight';
		});
		
		var elems = ctab.select('[class="TableHeaderContent_LGray"]');
		elems.each(function(item){    
			item.bgColor = '#C3D9FF';
			item.className = 'TableHeaderContent';
		});
		
		
		var elems = Ext.select('div.tab_contents');
		elems.each(function(item){    
			item.dom.style.display = "none";
		});

		if (Ext.get('tab_contents_'+id))
		{
			Ext.get('tab_contents_'+id).dom.style.display = "";
		}
			
		try
		{
			OnTabChanged(id);
		}
		catch(e){}
	}
</script>
{/literal}
<div id="tabs_container">
	<div style="margin-left:10px;">
		{foreach from=$tabs_list key=id item=tab_name}
		{if $selected_tab == $id}
			{assign var="is_active_tab" value="1"}
		{else}
			{assign var="is_active_tab" value="0"}
		{/if}
	  	<div class="table_tab" id="tab_{$id}" onClick="SetActiveTab('{$id}');">
           	<table border="0" cellpadding="0" cellspacing="0" width="120">
           		<tr>
           			<td width="7"><div class="TableHeaderLeft{if !$is_active_tab}_LGray{/if}"></div></td>
           			<td><div class="TableHeaderCenter{if !$is_active_tab}_LGray{/if}"></div></td>
           			<td><div class="TableHeaderCenter{if !$is_active_tab}_LGray{/if}"></div></td>
           			<td width="7"><div class="TableHeaderRight{if !$is_active_tab}_LGray{/if}"></div></td>
           		</tr>
           		<tr id="tab_content_{$id}" class="TableHeaderContent{if !$is_active_tab}_LGray{/if}" bgcolor="{if $is_active_tab}#C3D9FF{else}#f4f4f4{/if}">
           			<td width="7" class="TableHeaderCenter{if !$is_active_tab}_LGray{/if}"></td>
           			<td id="tab_name_{$id}" nowrap style="padding-bottom:5px;" align="center">
						{$tab_name}
           			</td>
           			<td align="left" nowrap></td>
           			<td width="7" class="TableHeaderCenter{if !$is_active_tab}_LGray{/if}"></td>
           		</tr>
           	</table>
		</div>
		{/foreach}
		<div style="clear:both;"></div>
	</div>
</div>
{/if}

{if $table_header_text}
<div>
	<div style="margin-left:10px;">
      	<table border="0" cellpadding="0" cellspacing="0">
      		<tr>
      			<td width="7"><div class="TableHeaderLeft"></div></td>
      			<td><div class="TableHeaderCenter"></div></td>
      			<td><div class="TableHeaderCenter"></div></td>
      			<td width="7"><div class="TableHeaderRight"></div></td>
      		</tr>
      		<tr bgcolor="#C3D9FF">
      			<td width="7" class="TableHeaderCenter"></td>
      			<td nowrap style="padding-bottom:5px;">
      			 {$table_header_text}
      			</td>
      			<td align="left" nowrap></td>
      			<td width="7" class="TableHeaderCenter"></td>
      		</tr>
      	</table>
	</div>
{/if}
<div class="Webta_Table">
	<div style="width:100%;">
		<div style="padding-left:7px; height:7px; background-image: url(/images/tl.gif); background-repeat: no-repeat;">
			<div style="padding-right:7px; height:7px; background-image: url(/images/tr.gif); background-position: right top; background-repeat: no-repeat;">
				<div style="background-color: #C3D9FF; height:7px;"></div>
			</div>
		</div>
	</div>
	<div>
		<div style="border-left:7px solid #C3D9FF; border-right:7px solid #C3D9FF;">
		
		