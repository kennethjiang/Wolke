			{if !$disable_footer_line}
			<div class="WebtaTable_Footer" id="footer_button_table" style="padding-left:6px;padding-top:2px; padding-bottom:2px;">
				{if $prev_page}
					<input type="submit" class="btn" value="Prev" name="返回">&nbsp;
				{/if}
	
				{if $edit_page}
					<input style="vertical-align:middle;" name="Submit" type="submit" class="btn" value="保存">
					<input name="id" type="hidden" id="id" value="{$id}">
				{elseif $search_page}
					<input type="submit" class="btn" value="Search">
				{elseif $page_data_options_add}
					<a href="{$smarty.server.PHP_SELF|replace:"view":"add"}{$page_data_options_add_querystring}">{if $page_data_options_add_text}{$page_data_options_add_text}{else}Add new{/if}</a>
				{/if}
				{if $next_page}
						<input type="submit" style="margin-right:6px;vertical-align:middle;" class="btn" name="next" value="下一步" />	
				{/if}
				{if $button_js}
						<input id="button_js" style="margin-right:6px;display:{if !$show_js_button}none{/if};vertical-align:middle;" type="button" onclick="{$button_js_action}" class="btn" name="cbtn_2" value="{$button_js_name}" />
				{/if}
				{if $button2}
						<input type="submit" style="margin-right:6px;vertical-align:middle;" class="btn" id="cbtn_2" name="cbtn_2" value="{$button2_name}" />	
				{/if}
				{if $button3}
						<input type="submit" style="margin-right:6px;vertical-align:middle;" class="btn" id="cbtn_3" name="cbtn_3" value="{$button3_name}" />	
				{/if}
				{if $cancel_btn}
						<input type="submit" class="btn" style="margin-right:6px;vertical-align:middle;" name="cancel" value="取消" />&nbsp;
				{/if}
				{if $retry_btn}
						<input type="button" style="margin-right:6px;vertical-align:middle;" class="btn" name="retrybtn" value="重试" onclick="window.location=get_url;return false;" />	
				{/if}
	                     {if $backbtn}
						<input type="submit" style="margin-right:6px;vertical-align:middle;" class="btn" name="cbtn_3" value="返回" onclick="history.back();return false;" />	
				{/if}
				{if $loader}
				    <span style="display:none;" id="btn_loader">
                        <img style="vertical-align:middle;" src="images/snake-loader.gif"> {$loader}
                    </span>
				{/if}
				&nbsp;
				<input type="hidden" id="btn_hidden_field" name="" value="">
				{literal}
				<script language="Javascript">
					var footer_button_table = Ext.get('footer_button_table');
					var elems = footer_button_table.select('.btn');
					elems.each(function(item){
						if (item.id != 'button_js')
						{    
							item.onclick = function()
							{
								var footer_button_table = Ext.get('footer_button_table');
								var elems = footer_button_table.select('.btn');
								elems.each(function(item){
									item.disabled = true;
								});
								
								Ext.get('btn_hidden_field').dom.name = this.name;
								Ext.get('btn_hidden_field').dom.value = this.value;
								
								document.forms[2].submit();
								
								return false;
							}
						}
					});
				</script>
				{/literal}
			</div>
			{/if}
		</div>
	</div>	
	<div style="width:100%;">
		<div style="padding-left:7px; height:7px; background-image: url(/images/bl.gif); background-repeat: no-repeat;">
			<div style="padding-right:7px; height:7px; background-image: url(/images/br.gif); background-position: right top; background-repeat: no-repeat;">
				<div style="background-color: #C3D9FF; height:7px;"></div>
			</div>
		</div>
	</div>
</div>
<div style="clear:both;"></div>
	