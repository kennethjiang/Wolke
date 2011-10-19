<?php /* Smarty version 2.6.26, created on 2011-09-18 17:33:48
         compiled from inc/mysql_replication_status.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('block', 't', 'inc/mysql_replication_status.tpl', 8, false),)), $this); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "inc/intable_header.tpl", 'smarty_include_vars' => array('header' => "同步状态",'intable_first_column_width' => '150','color' => 'Gray')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php $_from = $this->_tpl_vars['replication_status']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['server_id'] => $this->_tpl_vars['status']):
?>
<tr>
	<td colspan="2">
		<table width="500" cellpadding="4">
			<tr>
				<td colspan="2">
					<?php if ($this->_tpl_vars['status']['IsMaster'] == 1): ?><?php $this->_tag_stack[] = array('t', array()); $_block_repeat=true;smarty_block_t($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>主服务器<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_t($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?><?php else: ?><?php $this->_tag_stack[] = array('t', array()); $_block_repeat=true;smarty_block_t($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>从服务器 #<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_t($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?><?php echo $this->_tpl_vars['status']['SlaveNumber']; ?>
<?php endif; ?> (<a href="#/servers/<?php echo $this->_tpl_vars['server_id']; ?>
/extendedInfo"><?php echo $this->_tpl_vars['server_id']; ?>
</a>):
				</td>
			</tr>
			<tr>
				<td colspan="2"><div style="vertical-align:middle;font-size:1px;border-bottom:1px dotted black;width:100%">&nbsp;</div></td>
			</tr>
			<?php if (! $this->_tpl_vars['status']['error']): ?>
				<?php if (! $this->_tpl_vars['status']['IsMaster']): ?>
					<?php if ($this->_tpl_vars['status']['data']['Slave_IO_Running'] && $this->_tpl_vars['status']['data']['Slave_IO_Running'] == 'Yes'): ?>
	                <tr>
						<td width="240"><b><?php $this->_tag_stack[] = array('t', array()); $_block_repeat=true;smarty_block_t($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>从服务器状态:<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_t($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?></b></td>
						<td style="color:green;"><img src="images/true.gif"> OK</td>
					</tr>
					<?php else: ?>
					<tr>
						<td width="240"><b><?php $this->_tag_stack[] = array('t', array()); $_block_repeat=true;smarty_block_t($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>从服务器状态:<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_t($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?></b></td>
						<td style="color:red;"><img src="images/del.gif"></td>
					</tr>
					<?php endif; ?>
					<tr>
						<td width="240"><b><?php $this->_tag_stack[] = array('t', array()); $_block_repeat=true;smarty_block_t($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>数据日志位置:<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_t($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?></b></td>
						<td style="color:red;">
							<?php if ($this->_tpl_vars['status']['MasterPosition']-$this->_tpl_vars['status']['SlavePosition'] > 0 || $this->_tpl_vars['status']['Seconds_Behind_Master'] != 0 || ! $this->_tpl_vars['status']['SlavePosition']): ?>
								<span style="color:red;"><img src="images/del.gif"> <?php echo $this->_tpl_vars['status']['SlavePosition']; ?>
</span>
							<?php else: ?>
								<span style="color:green;"><img src="images/true.gif"> <?php echo $this->_tpl_vars['status']['SlavePosition']; ?>
</span>
							<?php endif; ?>
						</td>
					</tr>
				<?php else: ?>
					<tr>
						<td width="240"><b><?php $this->_tag_stack[] = array('t', array()); $_block_repeat=true;smarty_block_t($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>数据日志位置:<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_t($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?></b></td>
						<td style="color:red;">
							<span style="color:green;"><img src="images/true.gif"> <?php echo $this->_tpl_vars['status']['MasterPosition']; ?>
</span>
						</td>
					</tr>
				<?php endif; ?>
				<?php $_from = $this->_tpl_vars['status']['data']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['item']):
?>
				<tr>
					<td width="240"><?php echo $this->_tpl_vars['key']; ?>
:</td>
					<td><?php echo $this->_tpl_vars['item']; ?>
</td>
				</tr>
				<?php endforeach; endif; unset($_from); ?>
				<tr>
					<td>&nbsp;</td>
				</tr>
			<?php else: ?>
				<tr>
					<td colspan="2">
						<div class="Webta_ErrMsg" id="Webta_ErrMsg">
						<?php $this->_tag_stack[] = array('t', array()); $_block_repeat=true;smarty_block_t($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>未获得MySQL同步信息<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_t($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?> <?php echo $this->_tpl_vars['status']['error']; ?>

						</div>
					</td>
				</tr>
			<?php endif; ?>
		</table>
	</td>
</tr>
<?php endforeach; else: ?>
<tr>
	<td colspan="2"><?php $this->_tag_stack[] = array('t', array()); $_block_repeat=true;smarty_block_t($this->_tag_stack[count($this->_tag_stack)-1][1], null, $this, $_block_repeat);while ($_block_repeat) { ob_start(); ?>未获得MySQL同步信息<?php $_block_content = ob_get_contents(); ob_end_clean(); $_block_repeat=false;echo smarty_block_t($this->_tag_stack[count($this->_tag_stack)-1][1], $_block_content, $this, $_block_repeat); }  array_pop($this->_tag_stack); ?></td>
</tr>
<?php endif; unset($_from); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "inc/intable_footer.tpl", 'smarty_include_vars' => array('color' => 'Gray')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>