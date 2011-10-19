{include file="inc/header.tpl"}
	{include file="inc/table_header.tpl"}
    	{include file="inc/intable_header.tpl" visible="" intable_first_column_width="30%" header="General" color="Gray"}
		<tr>
    		<td><input {if $auto_snap}checked{/if} onclick="if (this.checked) Ext.get('autosnaps_settings').dom.style.display = ''; else Ext.get('autosnaps_settings').dom.style.display = 'none';" type="checkbox" name="enable" value="1" style="vertical-align:middle;"> Enable auto-snapshots</td>
    		<td></td>
    	</tr>
    	<tr>
    		<td colspan="2">&nbsp;</td>
    	</tr>
    	{if $auto_snap.dtlastsnapshot}
    	<tr>
    		<td colspan="2">Latest snapshot created at: {$auto_snap.dtlastsnapshot}</td>
    	</tr>
    	{/if}
    	{if $auto_snap.last_snapshotid}
    	<tr>
    		<td colspan="2">Latest snapshot ID: {$auto_snap.last_snapshotid}</td>
    	</tr>
    	{/if}
    	{include file="inc/intable_footer.tpl" color="Gray"}
    	
    	{include intableid="autosnaps_settings" visible=$visible file="inc/intable_header.tpl" intable_first_column_width="10%" header="Auto-snapshots settings" color="Gray"}
		<tr>
    		<td colspan="2">
    			Create snapshot every <input name="period" type="text" class="text" id="period" value="{if $auto_snap.period}{$auto_snap.period}{else}24{/if}" size="3"> hour(s).
    		</td>
    	</tr>
    	<tr>
    		<td colspan="2">
    			Snapshots are rotated  <input name="rotate" type="text" class="text" id="rotate" value="{if $auto_snap.rotate}{$auto_snap.rotate}{else}10{/if}" size="3"> times before being removed. (0 - disable rotating)
    		</td>
    	</tr>
    	{include file="inc/intable_footer.tpl" color="Gray"}
    	<input type="hidden" name="volumeId" value="{$volumeId}">
    	<input type="hidden" name="array_id" value="{$array_id}">
    	<input type="hidden" name="region" value="{$region}">
	{include file="inc/table_footer.tpl" button2=1 button2_name="Save" cancel_btn=1}
{include file="inc/footer.tpl"}