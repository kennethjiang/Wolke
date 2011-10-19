<?php /* Smarty version 2.6.26, created on 2011-10-16 21:14:55
         compiled from farm_usage_stats.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'string_format', 'farm_usage_stats.tpl', 29, false),)), $this); ?>
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
	<table class="Webta_Items" rules="groups" frame="box" width="100%" cellpadding="2" id="Webta_Items">
	<thead>
		<tr>
			<th></th>
			<th colspan="3" align="center" style="text-align:center;">带宽使用</th>
			<th colspan="5" align="center" style="text-align:center;">服务器小时数</th>
		</tr>
		<tr>
			<th>日期</th>
			<th>Inbound</th>
			<th>Outbound</th>
			<th>总计</th>
			<th>m1.small</th>
			<th>m1.large</th>
			<th>m1.xlarge</th>
			<th>c1.medium</th>
			<th>c1.xlarge</th>
		</tr>
	</thead>
	<tbody>
	<?php unset($this->_sections['id']);
$this->_sections['id']['name'] = 'id';
$this->_sections['id']['loop'] = is_array($_loop=$this->_tpl_vars['rows']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
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
	<tr bgcolor="#F9F9F9">
		<td class="Item" valign="top"><a href="farm_usage_ext.php?farmid=<?php echo $this->_tpl_vars['rows'][$this->_sections['id']['index']]['farmid']; ?>
&month=<?php echo $this->_tpl_vars['rows'][$this->_sections['id']['index']]['month']; ?>
&year=<?php echo $this->_tpl_vars['rows'][$this->_sections['id']['index']]['year']; ?>
"><?php echo $this->_tpl_vars['rows'][$this->_sections['id']['index']]['date']; ?>
</a></td>
		<td class="Item" valign="top"><?php echo $this->_tpl_vars['rows'][$this->_sections['id']['index']]['bw_in']; ?>
</td>
		<td class="Item" valign="top"><?php echo $this->_tpl_vars['rows'][$this->_sections['id']['index']]['bw_out']; ?>
</td>
		<td class="Item" valign="top"><?php echo $this->_tpl_vars['rows'][$this->_sections['id']['index']]['bw_total']; ?>
</td>
		<td class="Item" valign="top"><?php echo ((is_array($_tmp=$this->_tpl_vars['rows'][$this->_sections['id']['index']]['m1_small'])) ? $this->_run_mod_handler('string_format', true, $_tmp, "%.1f") : smarty_modifier_string_format($_tmp, "%.1f")); ?>
</td>
		<td class="Item" valign="top"><?php echo ((is_array($_tmp=$this->_tpl_vars['rows'][$this->_sections['id']['index']]['m1_large'])) ? $this->_run_mod_handler('string_format', true, $_tmp, "%.1f") : smarty_modifier_string_format($_tmp, "%.1f")); ?>
</td>
		<td class="Item" valign="top"><?php echo ((is_array($_tmp=$this->_tpl_vars['rows'][$this->_sections['id']['index']]['m1_xlarge'])) ? $this->_run_mod_handler('string_format', true, $_tmp, "%.1f") : smarty_modifier_string_format($_tmp, "%.1f")); ?>
</td>
		<td class="Item" valign="top"><?php echo ((is_array($_tmp=$this->_tpl_vars['rows'][$this->_sections['id']['index']]['c1_medium'])) ? $this->_run_mod_handler('string_format', true, $_tmp, "%.1f") : smarty_modifier_string_format($_tmp, "%.1f")); ?>
</td>
		<td class="Item" valign="top"><?php echo ((is_array($_tmp=$this->_tpl_vars['rows'][$this->_sections['id']['index']]['c1_xlarge'])) ? $this->_run_mod_handler('string_format', true, $_tmp, "%.1f") : smarty_modifier_string_format($_tmp, "%.1f")); ?>
</td>
	</tr>
	<?php endfor; else: ?>
	<tr>
		<td colspan="9" align="center">尚未获得统计数据</td>
	</tr>
	<?php endif; ?>
	<tr>
		<td colspan="9" align="center">&nbsp;</td>
	</tr>
	</tbody>
	</table>
	<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "inc/table_footer.tpl", 'smarty_include_vars' => array('colspan' => 9,'disable_footer_line' => 1)));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "inc/footer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>