<?php /* Smarty version 2.6.26, created on 2011-09-18 17:33:48
         compiled from inc/table_footer.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'replace', 'inc/table_footer.tpl', 13, false),)), $this); ?>
			<?php if (! $this->_tpl_vars['disable_footer_line']): ?>
			<div class="WebtaTable_Footer" id="footer_button_table" style="padding-left:6px;padding-top:2px; padding-bottom:2px;">
				<?php if ($this->_tpl_vars['prev_page']): ?>
					<input type="submit" class="btn" value="Prev" name="返回">&nbsp;
				<?php endif; ?>
	
				<?php if ($this->_tpl_vars['edit_page']): ?>
					<input style="vertical-align:middle;" name="Submit" type="submit" class="btn" value="保存">
					<input name="id" type="hidden" id="id" value="<?php echo $this->_tpl_vars['id']; ?>
">
				<?php elseif ($this->_tpl_vars['search_page']): ?>
					<input type="submit" class="btn" value="Search">
				<?php elseif ($this->_tpl_vars['page_data_options_add']): ?>
					<a href="<?php echo ((is_array($_tmp=$_SERVER['PHP_SELF'])) ? $this->_run_mod_handler('replace', true, $_tmp, 'view', 'add') : smarty_modifier_replace($_tmp, 'view', 'add')); ?>
<?php echo $this->_tpl_vars['page_data_options_add_querystring']; ?>
"><?php if ($this->_tpl_vars['page_data_options_add_text']): ?><?php echo $this->_tpl_vars['page_data_options_add_text']; ?>
<?php else: ?>Add new<?php endif; ?></a>
				<?php endif; ?>
				<?php if ($this->_tpl_vars['next_page']): ?>
						<input type="submit" style="margin-right:6px;vertical-align:middle;" class="btn" name="next" value="下一步" />	
				<?php endif; ?>
				<?php if ($this->_tpl_vars['button_js']): ?>
						<input id="button_js" style="margin-right:6px;display:<?php if (! $this->_tpl_vars['show_js_button']): ?>none<?php endif; ?>;vertical-align:middle;" type="button" onclick="<?php echo $this->_tpl_vars['button_js_action']; ?>
" class="btn" name="cbtn_2" value="<?php echo $this->_tpl_vars['button_js_name']; ?>
" />
				<?php endif; ?>
				<?php if ($this->_tpl_vars['button2']): ?>
						<input type="submit" style="margin-right:6px;vertical-align:middle;" class="btn" id="cbtn_2" name="cbtn_2" value="<?php echo $this->_tpl_vars['button2_name']; ?>
" />	
				<?php endif; ?>
				<?php if ($this->_tpl_vars['button3']): ?>
						<input type="submit" style="margin-right:6px;vertical-align:middle;" class="btn" id="cbtn_3" name="cbtn_3" value="<?php echo $this->_tpl_vars['button3_name']; ?>
" />	
				<?php endif; ?>
				<?php if ($this->_tpl_vars['cancel_btn']): ?>
						<input type="submit" class="btn" style="margin-right:6px;vertical-align:middle;" name="cancel" value="取消" />&nbsp;
				<?php endif; ?>
				<?php if ($this->_tpl_vars['retry_btn']): ?>
						<input type="button" style="margin-right:6px;vertical-align:middle;" class="btn" name="retrybtn" value="重试" onclick="window.location=get_url;return false;" />	
				<?php endif; ?>
	                     <?php if ($this->_tpl_vars['backbtn']): ?>
						<input type="submit" style="margin-right:6px;vertical-align:middle;" class="btn" name="cbtn_3" value="返回" onclick="history.back();return false;" />	
				<?php endif; ?>
				<?php if ($this->_tpl_vars['loader']): ?>
				    <span style="display:none;" id="btn_loader">
                        <img style="vertical-align:middle;" src="images/snake-loader.gif"> <?php echo $this->_tpl_vars['loader']; ?>

                    </span>
				<?php endif; ?>
				&nbsp;
				<input type="hidden" id="btn_hidden_field" name="" value="">
				<?php echo '
				<script language="Javascript">
					var footer_button_table = Ext.get(\'footer_button_table\');
					var elems = footer_button_table.select(\'.btn\');
					elems.each(function(item){
						if (item.id != \'button_js\')
						{    
							item.onclick = function()
							{
								var footer_button_table = Ext.get(\'footer_button_table\');
								var elems = footer_button_table.select(\'.btn\');
								elems.each(function(item){
									item.disabled = true;
								});
								
								Ext.get(\'btn_hidden_field\').dom.name = this.name;
								Ext.get(\'btn_hidden_field\').dom.value = this.value;
								
								document.forms[2].submit();
								
								return false;
							}
						}
					});
				</script>
				'; ?>

			</div>
			<?php endif; ?>
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
	