<?php /* Smarty version 2.6.26, created on 2011-09-18 17:34:03
         compiled from farms_control.tpl */ ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "inc/header.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "inc/table_header.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		<script language="Javascript">
		<?php echo '
		var checked_items = 0;
		
		function SetSyncChecked(ami_id, checked)
		{
			Ext.get(\'i_\'+ami_id).dom.style.display = checked ? \'\' : \'none\';
			if (checked)
				checked_items++;
			else
				checked_items--;
				
			if (checked_items > 0)
				Ext.get(\'sync_opts\').dom.style.display = \'\';
			else
				Ext.get(\'sync_opts\').dom.style.display = \'none\';
		}
		'; ?>

		</script>
		<input type="hidden" name="action" value="<?php echo $this->_tpl_vars['action']; ?>
" />
		<?php if ($this->_tpl_vars['action'] == 'Launch'): ?>
			<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "farms_control_launch.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
			
			<?php if ($this->_tpl_vars['iswiz']): ?>
				<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "inc/table_footer.tpl", 'smarty_include_vars' => array('button2' => 1,'button2_name' => "Yes, launch the farm now",'button3' => 1,'button3_name' => 'Configure scaling settings','cancel_btn' => 1)));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
			<?php else: ?>
				<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "inc/table_footer.tpl", 'smarty_include_vars' => array('button2' => 1,'button2_name' => "Yes, launch the farm now",'cancel_btn' => 1)));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
			<?php endif; ?>
    	<?php elseif ($this->_tpl_vars['action'] == 'Terminate'): ?>
    		<input type="hidden" name="term_step" value="<?php echo $this->_tpl_vars['term_step']+1; ?>
" />
    		<?php if ($this->_tpl_vars['term_step'] == 1): ?>
    			<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "farms_control_terminate_s1.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
    			<?php if ($this->_tpl_vars['farminfo']['status'] == 1): ?>
	    			<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "inc/table_footer.tpl", 'smarty_include_vars' => array('button2' => 1,'button2_name' => "同步选中的服务器内容，并关闭服务器组",'button3' => 1,'button3_name' => "忽略同步，直接关闭服务器",'cancel_btn' => 1)));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
 				<?php else: ?>
 					<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "inc/table_footer.tpl", 'smarty_include_vars' => array('button3' => 1,'button3_name' => "直接关闭服务器",'cancel_btn' => 1)));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
 				<?php endif; ?>
    		<?php elseif ($this->_tpl_vars['term_step'] == 2): ?>
    			<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "farms_control_terminate_s2.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
    			<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "inc/table_footer.tpl", 'smarty_include_vars' => array('button2' => 1,'button2_name' => "是的，直接关闭服务器",'cancel_btn' => 1)));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
    		<?php endif; ?>
    	<?php endif; ?>		
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "inc/footer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>