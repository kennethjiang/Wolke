{include file="inc/header.tpl"}
	{include file="inc/table_header.tpl"}
		<script language="Javascript">
		{literal}
		var checked_items = 0;
		
		function SetSyncChecked(ami_id, checked)
		{
			Ext.get('i_'+ami_id).dom.style.display = checked ? '' : 'none';
			if (checked)
				checked_items++;
			else
				checked_items--;
				
			if (checked_items > 0)
				Ext.get('sync_opts').dom.style.display = '';
			else
				Ext.get('sync_opts').dom.style.display = 'none';
		}
		{/literal}
		</script>
		<input type="hidden" name="action" value="{$action}" />
		{if $action == 'Launch'}
			{include file="farms_control_launch.tpl"}
			
			{if $iswiz}
				{include file="inc/table_footer.tpl" button2=1 button2_name="Yes, launch the farm now" button3=1 button3_name="Configure scaling settings" cancel_btn=1}
			{else}
				{include file="inc/table_footer.tpl" button2=1 button2_name="Yes, launch the farm now" cancel_btn=1}
			{/if}
    	{elseif $action == 'Terminate'}
    		<input type="hidden" name="term_step" value="{$term_step+1}" />
    		{if $term_step == 1}
    			{include file="farms_control_terminate_s1.tpl"}
    			{if $farminfo.status == 1}
	    			{include file="inc/table_footer.tpl" 
						button2=1 
						button2_name="同步选中的服务器内容，并关闭服务器组"
						button3=1 
						button3_name="忽略同步，直接关闭服务器" 
						cancel_btn=1
	 				}
 				{else}
 					{include file="inc/table_footer.tpl" 
						button3=1 
						button3_name="直接关闭服务器" 
						cancel_btn=1
	 				}
 				{/if}
    		{elseif $term_step == 2}
    			{include file="farms_control_terminate_s2.tpl"}
    			{include file="inc/table_footer.tpl" button2=1 button2_name="是的，直接关闭服务器" cancel_btn=1}
    		{/if}
    	{/if}		
{include file="inc/footer.tpl"}