{include file="inc/header.tpl"}
	<script language="Javascript" src="/js/stat_img_loader.js"></script>
	<script language="Javascript">

	var MONITORING_VERSION = "{if $mon_version}{$mon_version}{else}1{/if}";
	
	{literal}
		Ext.onReady(function(){
	{/literal}
		
		LoadStatsImage('{$farmid}', '{$watcher}', 'daily', '{$role_name}', 'daily');
		LoadStatsImage('{$farmid}', '{$watcher}', 'weekly', '{$role_name}', 'weekly');
		LoadStatsImage('{$farmid}', '{$watcher}', 'monthly', '{$role_name}', 'monthly');
		LoadStatsImage('{$farmid}', '{$watcher}', 'yearly', '{$role_name}', 'yearly');
			
	{literal}
		}); 
	{/literal}
	</script>
	{include file="inc/table_header.tpl"}
	        {include file="inc/intable_header.tpl" header="Daily graph (5 minutes average)" color="Gray"}
	    	<tr>
	    		<td colspan="2" align="center">
					<div id="loader_daily" style="background-color:#dddddd;width:535px;height:340px;position:relative;top:0px;left:0px;">
						<div id="loader_content_daily" style="position:relative;top:48%;">
							<img src="/images/snake-loader.gif"> Loading graphic. Please wait...
						</div>
					</div>
					<div id="image_div_daily" style="display:none;">
						<img id="image_daily" src="">
					</div>
	    		</td>
	    	</tr>
	    	{include file="inc/intable_footer.tpl" color="Gray"}
	    	
	    	{include file="inc/intable_header.tpl" header="Weekly graph (30 minutes average)" color="Gray"}
	    	<tr>
	    		<td colspan="2" align="center">
					<div id="loader_weekly" style="background-color:#dddddd;width:535px;height:340px;position:relative;top:0px;left:0px;">
						<div id="loader_content_weekly" style="position:relative;top:48%;">
							<img src="/images/snake-loader.gif"> Loading graphic. Please wait...
						</div>
					</div>
					<div id="image_div_weekly" style="display:none;">
						<img id="image_weekly" src="">
					</div>
	    		</td>
	    	</tr>
	    	{include file="inc/intable_footer.tpl" color="Gray"}
	    	
	    	{include file="inc/intable_header.tpl" header="Monthly graph (2 hours average)" color="Gray"}
	    	<tr>
	    		<td colspan="2" align="center">
					<div id="loader_monthly" style="background-color:#dddddd;width:535px;height:340px;position:relative;top:0px;left:0px;">
						<div id="loader_content_monthly" style="position:relative;top:48%;">
							<img src="/images/snake-loader.gif"> Loading graphic. Please wait...
						</div>
					</div>
					<div id="image_div_monthly" style="display:none;">
						<img id="image_monthly" src="">
					</div>
	    		</td>
	    	</tr>
	    	{include file="inc/intable_footer.tpl" color="Gray"}
	    	
	    	{include file="inc/intable_header.tpl" header="Yearly graph (1 day average)" color="Gray"}
	    	<tr>
	    		<td colspan="2" align="center">
					<div id="loader_yearly" style="background-color:#dddddd;width:535px;height:340px;position:relative;top:0px;left:0px;">
						<div id="loader_content_yearly" style="position:relative;top:48%;">
							<img src="/images/snake-loader.gif"> Loading graphic. Please wait...
						</div>
					</div>
					<div id="image_div_yearly" style="display:none;">
						<img id="image_yearly" src="">
					</div>
	    		</td>
	    	</tr>
	    	{include file="inc/intable_footer.tpl" color="Gray"}
	{include file="inc/table_footer.tpl" disable_footer_line=1}
{include file="inc/footer.tpl"}