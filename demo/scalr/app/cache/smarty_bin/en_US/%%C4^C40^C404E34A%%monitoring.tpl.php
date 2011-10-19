<?php /* Smarty version 2.6.26, created on 2011-09-22 17:53:49
         compiled from monitoring.tpl */ ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "inc/header.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	<script language="Javascript" src="/js/stat_img_loader.js"></script>
	<script language="Javascript">
	<?php echo '

		var MONITORING_VERSION = "2";
	
		Ext.onReady(function(){
		'; ?>

						
		<?php unset($this->_sections['id']);
$this->_sections['id']['name'] = 'id';
$this->_sections['id']['loop'] = is_array($_loop=$this->_tpl_vars['roles']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['id']['show'] = true;
$this->_sections['id']['max'] = $this->_sections['id']['loop'];
$this->_sections['id']['step'] = 1;
$this->_sections['id']['start'] = $this->_sections['id']['step'] > 0 ? 0 : $this->_sections['id']['loop']-1;
if ($this->_sections['id']['show']) {
    $this->_sections['id']['total'] = $this->_sections['id']['loop'];
    if ($this->_sections['id']['total'] == 0)
        $this->_sections['id']['show'] = false;
} else
    $this->_sections['id']['total'] = 0;
if ($this->_sections['id']['show']):

            for ($this->_sections['id']['index'] = $this->_sections['id']['start'], $this->_sections['id']['iteration'] = 1;
                 $this->_sections['id']['iteration'] <= $this->_sections['id']['total'];
                 $this->_sections['id']['index'] += $this->_sections['id']['step'], $this->_sections['id']['iteration']++):
$this->_sections['id']['rownum'] = $this->_sections['id']['iteration'];
$this->_sections['id']['index_prev'] = $this->_sections['id']['index'] - $this->_sections['id']['step'];
$this->_sections['id']['index_next'] = $this->_sections['id']['index'] + $this->_sections['id']['step'];
$this->_sections['id']['first']      = ($this->_sections['id']['iteration'] == 1);
$this->_sections['id']['last']       = ($this->_sections['id']['iteration'] == $this->_sections['id']['total']);
?>
			<?php $_from = $this->_tpl_vars['roles'][$this->_sections['id']['index']]['images']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['watchername'] => $this->_tpl_vars['image']):
?>
				LoadStatsImage('<?php echo $this->_tpl_vars['image']['params']['farmid']; ?>
', '<?php echo $this->_tpl_vars['image']['params']['watcher']; ?>
', '<?php echo $this->_tpl_vars['image']['params']['type']; ?>
', '<?php echo $this->_tpl_vars['image']['params']['role_id']; ?>
', '<?php echo $this->_tpl_vars['image']['hash']; ?>
');
			<?php endforeach; endif; unset($_from); ?>
		<?php endfor; endif; ?>
			
		<?php echo '
		}); 
	'; ?>

	</script>
	<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "inc/table_header.tpl", 'smarty_include_vars' => array('nofilter' => 1,'tabs' => 1)));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		<?php unset($this->_sections['id']);
$this->_sections['id']['name'] = 'id';
$this->_sections['id']['loop'] = is_array($_loop=$this->_tpl_vars['roles']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['id']['show'] = true;
$this->_sections['id']['max'] = $this->_sections['id']['loop'];
$this->_sections['id']['step'] = 1;
$this->_sections['id']['start'] = $this->_sections['id']['step'] > 0 ? 0 : $this->_sections['id']['loop']-1;
if ($this->_sections['id']['show']) {
    $this->_sections['id']['total'] = $this->_sections['id']['loop'];
    if ($this->_sections['id']['total'] == 0)
        $this->_sections['id']['show'] = false;
} else
    $this->_sections['id']['total'] = 0;
if ($this->_sections['id']['show']):

            for ($this->_sections['id']['index'] = $this->_sections['id']['start'], $this->_sections['id']['iteration'] = 1;
                 $this->_sections['id']['iteration'] <= $this->_sections['id']['total'];
                 $this->_sections['id']['index'] += $this->_sections['id']['step'], $this->_sections['id']['iteration']++):
$this->_sections['id']['rownum'] = $this->_sections['id']['iteration'];
$this->_sections['id']['index_prev'] = $this->_sections['id']['index'] - $this->_sections['id']['step'];
$this->_sections['id']['index_next'] = $this->_sections['id']['index'] + $this->_sections['id']['step'];
$this->_sections['id']['first']      = ($this->_sections['id']['iteration'] == 1);
$this->_sections['id']['last']       = ($this->_sections['id']['iteration'] == $this->_sections['id']['total']);
?>
			<?php $this->assign('name', $this->_tpl_vars['roles'][$this->_sections['id']['index']]['name']); ?>
			<?php $this->assign('tid', $this->_tpl_vars['roles'][$this->_sections['id']['index']]['id']); ?>
			<?php if ($this->_tpl_vars['selected_tab'] == $this->_tpl_vars['tid']): ?>
				<?php $this->assign('visible', ""); ?>
			<?php else: ?>
				<?php $this->assign('visible', 'none'); ?>
			<?php endif; ?>
			<?php if ($this->_tpl_vars['name'] != 'FARM'): ?>
				<?php if ($this->_tpl_vars['roles'][$this->_sections['id']['index']]['t'] == 'instance'): ?>
					<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "inc/intable_header.tpl", 'smarty_include_vars' => array('intable_classname' => 'tab_contents','intableid' => "tab_contents_".($this->_tpl_vars['tid']),'visible' => ($this->_tpl_vars['visible']),'header' => 'Instance statistics','color' => 'Gray')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
				<?php else: ?>
	        		<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "inc/intable_header.tpl", 'smarty_include_vars' => array('intable_classname' => 'tab_contents','intableid' => "tab_contents_".($this->_tpl_vars['tid']),'visible' => ($this->_tpl_vars['visible']),'header' => "Statistics for role: ".($this->_tpl_vars['name']),'color' => 'Gray')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	        	<?php endif; ?>
	        <?php else: ?>
	        	<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "inc/intable_header.tpl", 'smarty_include_vars' => array('intable_classname' => 'tab_contents','intableid' => "tab_contents_".($this->_tpl_vars['tid']),'visible' => ($this->_tpl_vars['visible']),'header' => 'Farm statistics','color' => 'Gray')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	        <?php endif; ?>
			<tr>
	    		<td colspan="2" align="center">
	    			<div style="width:1120px;" align="left">
	    				<?php $_from = $this->_tpl_vars['roles'][$this->_sections['id']['index']]['images']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['watchername'] => $this->_tpl_vars['image']):
?>
	    					<div style="float:left;margin-right:15px;height:340px;width:535px;margin-bottom:10px;" align="center">
	    						<div id="loader_<?php echo $this->_tpl_vars['image']['hash']; ?>
" style="background-color:#dddddd;width:535px;height:340px;position:relative;top:0px;left:0px;">
	    							<div id="loader_content_<?php echo $this->_tpl_vars['image']['hash']; ?>
" style="position:relative;top:48%;">
	    								<img src="/images/snake-loader.gif"> Loading graphic. Please wait...
	    							</div>
	    						</div>
	    						<div id="image_div_<?php echo $this->_tpl_vars['image']['hash']; ?>
" style="display:none;">
	    							<a href="monitoring.php?farmid=<?php echo $this->_tpl_vars['farmid']; ?>
&role=<?php echo $this->_tpl_vars['tid']; ?>
&watcher=<?php echo $this->_tpl_vars['watchername']; ?>
"><img id="image_<?php echo $this->_tpl_vars['image']['hash']; ?>
" src=""></a>
	    						</div>
	    					</div>
	    				<?php endforeach; endif; unset($_from); ?>
	    			</div>
	    		</td>
	    	</tr>
        	<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "inc/intable_footer.tpl", 'smarty_include_vars' => array('color' => 'Gray')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
        <?php endfor; endif; ?>
	<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "inc/table_footer.tpl", 'smarty_include_vars' => array('disable_footer_line' => 1)));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>				
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "inc/footer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>