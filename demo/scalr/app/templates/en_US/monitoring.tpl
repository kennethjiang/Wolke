{include file="inc/header.tpl"}
	<script language="Javascript" src="/js/stat_img_loader.js"></script>
	<script language="Javascript">
	{literal}

		var MONITORING_VERSION = "2";
	
		Ext.onReady(function(){
		{/literal}
						
		{section name=id loop=$roles}
			{foreach key=watchername item=image from=$roles[id].images}
				LoadStatsImage('{$image.params.farmid}', '{$image.params.watcher}', '{$image.params.type}', '{$image.params.role_id}', '{$image.hash}');
			{/foreach}
		{/section}
			
		{literal}
		}); 
	{/literal}
	</script>
	{include file="inc/table_header.tpl" nofilter=1 tabs=1}
		{section name=id loop=$roles}
			{assign var=name value=$roles[id].name}
			{assign var=tid value=$roles[id].id}
			{if $selected_tab == $tid}
				{assign var=visible value=""}
			{else}
				{assign var=visible value="none"}
			{/if}
			{if $name != 'FARM'}
				{if $roles[id].t == 'instance'}
					{include intable_classname="tab_contents" intableid="tab_contents_$tid" visible="$visible" file="inc/intable_header.tpl" header="Instance statistics" color="Gray"}
				{else}
	        		{include intable_classname="tab_contents" intableid="tab_contents_$tid" visible="$visible" file="inc/intable_header.tpl" header="Statistics for role: $name" color="Gray"}
	        	{/if}
	        {else}
	        	{include intable_classname="tab_contents" intableid="tab_contents_$tid" visible="$visible" file="inc/intable_header.tpl" header="Farm statistics" color="Gray"}
	        {/if}
			<tr>
	    		<td colspan="2" align="center">
	    			<div style="width:1120px;" align="left">
	    				{foreach key=watchername item=image from=$roles[id].images}
	    					<div style="float:left;margin-right:15px;height:340px;width:535px;margin-bottom:10px;" align="center">
	    						<div id="loader_{$image.hash}" style="background-color:#dddddd;width:535px;height:340px;position:relative;top:0px;left:0px;">
	    							<div id="loader_content_{$image.hash}" style="position:relative;top:48%;">
	    								<img src="/images/snake-loader.gif"> Loading graphic. Please wait...
	    							</div>
	    						</div>
	    						<div id="image_div_{$image.hash}" style="display:none;">
	    							<a href="monitoring.php?farmid={$farmid}&role={$tid}&watcher={$watchername}"><img id="image_{$image.hash}" src=""></a>
	    						</div>
	    					</div>
	    				{/foreach}
	    			</div>
	    		</td>
	    	</tr>
        	{include file="inc/intable_footer.tpl" color="Gray"}
        {/section}
	{include file="inc/table_footer.tpl" disable_footer_line=1}				
{include file="inc/footer.tpl"}