{include file="inc/header.tpl"}
{include file="inc/table_header.tpl" nofilter=1}
	<input type="hidden" name="farmid" value="{$farminfo.id}" />
    {include file="inc/intable_header.tpl" header="RSS feed settings" color="Gray"}
    <tr>
    	<td colspan="2"><div style="display:inline;margin-right:25px;">RSS feed URL:</div>
    	<img src="/images/feed-icon-14x14.png"> <a href="https://{$smarty.server.HTTP_HOST}/storage/events/{$farminfo.id}/rss.xml">http://{$smarty.server.HTTP_HOST}/storage/events/{$farminfo.id}/rss.xml</a></td>
    </tr>
    <tr>
    	<td colspan="2" style="font-style:italic;font-size:12px;">
    		<br />
    		<img src="/images/warn.png"> Your RSS reader must support basic HTTP authentication. The login and password for RSS feeds can be found in <a href="system_settings.php">Settings -> System settings</a>
    	</td>
    </tr>
    {include file="inc/intable_footer.tpl" color="Gray"}
    
    {foreach from=$observers key=name item=form}
	    {include file="inc/intable_header.tpl" header="$name observer settings" color="Gray"}
	    	{include file="inc/dynamicform.tpl" DataForm=$form field_prefix="settings[$name][" field_suffix="]"}
	    {include file="inc/intable_footer.tpl" color="Gray"}
    {/foreach}
{include file="inc/table_footer.tpl" button2=1 button2_name="Save"}
{include file="inc/footer.tpl"}