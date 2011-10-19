<?php /* Smarty version 2.6.26, created on 2011-10-18 00:57:45
         compiled from farms_control_terminate_s1.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'count', 'farms_control_terminate_s1.tpl', 4, false),)), $this); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "inc/intable_header.tpl", 'smarty_include_vars' => array('header' => "确认关闭云平台",'color' => 'Gray')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<tr>
	<td colspan="2">
	<?php if (count($this->_tpl_vars['outdated_farm_roles']) == 0): ?>
		确认关闭云平台 '<?php echo $this->_tpl_vars['farminfo']['name']; ?>
'? <?php if ($this->_tpl_vars['num'] > 0): ?>所有 <b><?php echo $this->_tpl_vars['num']; ?>
台</b> 服务器将一同关闭。<?php endif; ?>
	<?php else: ?>
	    自从上次云平台启动以来你还没有保存你的服务器内容。
	    如果你曾经对这些服务器进行过任何的设置，我们建议你先保存服务器内容。
		否则，所有的相关设置都会在云平台关闭后丢失。 
		<br />
		<br />
		<div style="background-color:#F9F9F9;padding:10px;">
		<div style="font-weight:bold;">保存我如下服务器的设置:</div>
		<br />
			<?php unset($this->_sections['id']);
$this->_sections['id']['name'] = 'id';
$this->_sections['id']['loop'] = is_array($_loop=$this->_tpl_vars['outdated_farm_roles']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
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
			<div style="margin-bottom:10px;">
				<div style="width:100%;">
					<div style="float:left;line-height:40px;">
					<input <?php if ($this->_tpl_vars['outdated_farm_roles'][$this->_sections['id']['index']]->IsBundleRunning): ?>checked disabled<?php endif; ?> onclick="SetSyncChecked('<?php echo $this->_tpl_vars['outdated_farm_roles'][$this->_sections['id']['index']]->ID; ?>
', this.checked);" type="checkbox" name="sync[]" value="<?php echo $this->_tpl_vars['outdated_farm_roles'][$this->_sections['id']['index']]->ID; ?>
" style="vertical-align:middle;"> 
					
					<?php $this->assign('frole', $this->_tpl_vars['outdated_farm_roles'][$this->_sections['id']['index']]); ?>
					<?php $this->assign('role', $this->_tpl_vars['frole']->GetRoleObject()); ?>
					<?php echo $this->_tpl_vars['role']->name; ?>
 (<?php echo $this->_tpl_vars['role']->imageId; ?>
) &nbsp;&nbsp;Last synchronization: <?php if ($this->_tpl_vars['outdated_farm_roles'][$this->_sections['id']['index']]->dtLastSync): ?><?php echo $this->_tpl_vars['outdated_farm_roles'][$this->_sections['id']['index']]->dtLastSync; ?>
<?php else: ?>Never<?php endif; ?>
					</div>
					<?php if ($this->_tpl_vars['role']->hasBehavior('mysql')): ?>
					<div class="Webta_ExperimentalMsg" style="float:left;margin-left:15px;padding-right:15px;font-size:12px;">
						绑定将不包括MySQL数据. <a href='farm_mysql_info.php?farmid=<?php echo $this->_tpl_vars['farminfo']['id']; ?>
'>如果你想包括MySQL数据，请点击这里</a>.
					</div> 
					<?php endif; ?>
					<div style="clear:both;font-size:1px;"></div>
				</div>
				<?php if (! $this->_tpl_vars['outdated_farm_roles'][$this->_sections['id']['index']]->IsBundleRunning): ?>
				<div id="i_<?php echo $this->_tpl_vars['outdated_farm_roles'][$this->_sections['id']['index']]->ID; ?>
" style="margin-left:20px;display:none;">
					<?php $this->assign('servers', $this->_tpl_vars['outdated_farm_roles'][$this->_sections['id']['index']]->RunningServers); ?>
					<?php unset($this->_sections['iid']);
$this->_sections['iid']['name'] = 'iid';
$this->_sections['iid']['loop'] = is_array($_loop=$this->_tpl_vars['servers']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['iid']['show'] = true;
$this->_sections['iid']['max'] = $this->_sections['iid']['loop'];
$this->_sections['iid']['step'] = 1;
$this->_sections['iid']['start'] = $this->_sections['iid']['step'] > 0 ? 0 : $this->_sections['iid']['loop']-1;
if ($this->_sections['iid']['show']) {
    $this->_sections['iid']['total'] = $this->_sections['iid']['loop'];
    if ($this->_sections['iid']['total'] == 0)
        $this->_sections['iid']['show'] = false;
} else
    $this->_sections['iid']['total'] = 0;
if ($this->_sections['iid']['show']):

            for ($this->_sections['iid']['index'] = $this->_sections['iid']['start'], $this->_sections['iid']['iteration'] = 1;
                 $this->_sections['iid']['iteration'] <= $this->_sections['iid']['total'];
                 $this->_sections['iid']['index'] += $this->_sections['iid']['step'], $this->_sections['iid']['iteration']++):
$this->_sections['iid']['rownum'] = $this->_sections['iid']['iteration'];
$this->_sections['iid']['index_prev'] = $this->_sections['iid']['index'] - $this->_sections['iid']['step'];
$this->_sections['iid']['index_next'] = $this->_sections['iid']['index'] + $this->_sections['iid']['step'];
$this->_sections['iid']['first']      = ($this->_sections['iid']['iteration'] == 1);
$this->_sections['iid']['last']       = ($this->_sections['iid']['iteration'] == $this->_sections['iid']['total']);
?>
						<input <?php if ($this->_sections['iid']['first']): ?>checked<?php endif; ?> style="vertical-align:middle;" type="radio" name="sync_i[<?php echo $this->_tpl_vars['outdated_farm_roles'][$this->_sections['id']['index']]->ID; ?>
]" value="<?php echo $this->_tpl_vars['servers'][$this->_sections['iid']['index']]->serverId; ?>
"> <?php echo $this->_tpl_vars['servers'][$this->_sections['iid']['index']]->serverId; ?>
 (<?php echo $this->_tpl_vars['servers'][$this->_sections['iid']['index']]->remoteIp; ?>
)
						<br />
					<?php endfor; else: ?>
						当前服务角色未发现任何服务器。
					<?php endif; ?>
				</div>
				<?php else: ?>
				<div id="i_<?php echo $this->_tpl_vars['outdated_farm_roles'][$this->_sections['id']['index']]->ID; ?>
" style="margin-left:20px;">
					对服务角色的同步已经在进行中... 
				</div>
				<?php endif; ?>
			</div>
			<?php endfor; endif; ?>
			<div id="sync_opts" style="display:none;">
			<br />
				<input style="vertical-align:middle;" type="checkbox" name="untermonfail" value="1"> 当有任何一个服务角色的同步失败时，不要关闭云平台
			</div>
		</div>
	<?php endif; ?>
	</td>
</tr>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "inc/intable_footer.tpl", 'smarty_include_vars' => array('color' => 'Gray')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>