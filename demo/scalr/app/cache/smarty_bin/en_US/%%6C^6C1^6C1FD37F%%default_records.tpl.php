<?php /* Smarty version 2.6.26, created on 2011-09-21 14:14:51
         compiled from default_records.tpl */ ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "inc/header.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<script language="javascript" type="text/javascript">
<?php echo '
	function CheckPrAdd(tp, id, val)
	{
		if (val == \'MX\')
		{
			Ext.get(tp+"_"+id).dom.style.display = \'\';
			Ext.get(tp+"_"+id).dom.value = \'10\';
		}
		else
		{
			Ext.get(tp+"_"+id).dom.style.display = \'none\';
			Ext.get(tp+"_"+id).dom.value = \'\';
		}
	}
'; ?>

</script>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "inc/table_header.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<table cellpadding="4" cellspacing="0" width="100%" border="0">
	<tr>
		<td class="th" width="300">Domain</td>
		<td class="th" width="150">TTL</td>
		<td class="th" width="50">&nbsp;</td>
		<td class="th" width="150">Record Type</td>
		<td class="th" colspan="2">Record value</td>
	</tr>
	<?php unset($this->_sections['id']);
$this->_sections['id']['name'] = 'id';
$this->_sections['id']['loop'] = is_array($_loop=$this->_tpl_vars['records']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
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
	<tr>
		<td><input <?php if ($this->_tpl_vars['records'][$this->_sections['id']['index']]['issystem'] == 1): ?>disabled<?php endif; ?> type="text" class="text" name="records[<?php echo $this->_tpl_vars['records'][$this->_sections['id']['index']]['id']; ?>
][name]" size=30 value="<?php echo $this->_tpl_vars['records'][$this->_sections['id']['index']]['name']; ?>
"></td>
		<td><input <?php if ($this->_tpl_vars['records'][$this->_sections['id']['index']]['issystem'] == 1): ?>disabled<?php endif; ?> type="text" class="text" name="records[<?php echo $this->_tpl_vars['records'][$this->_sections['id']['index']]['id']; ?>
][ttl]" size=6 value="<?php echo $this->_tpl_vars['records'][$this->_sections['id']['index']]['ttl']; ?>
"></td>
		<td>IN</td>
		<td><select <?php if ($this->_tpl_vars['records'][$this->_sections['id']['index']]['issystem'] == 1): ?>disabled<?php endif; ?> class="text" name="records[<?php echo $this->_tpl_vars['records'][$this->_sections['id']['index']]['id']; ?>
][type]" onchange="CheckPrAdd('ed', '<?php echo $this->_tpl_vars['records'][$this->_sections['id']['index']]['id']; ?>
', this.value)">
				<option <?php if ($this->_tpl_vars['records'][$this->_sections['id']['index']]['type'] == 'A'): ?>selected<?php endif; ?> value="A">A</option>
				<option <?php if ($this->_tpl_vars['records'][$this->_sections['id']['index']]['type'] == 'CNAME'): ?>selected<?php endif; ?> value="CNAME">CNAME</option>
				<option <?php if ($this->_tpl_vars['records'][$this->_sections['id']['index']]['type'] == 'MX'): ?>selected<?php endif; ?> value="MX">MX</option>
				<option <?php if ($this->_tpl_vars['records'][$this->_sections['id']['index']]['type'] == 'NS'): ?>selected<?php endif; ?> value="NS">NS</option>
				<option <?php if ($this->_tpl_vars['records'][$this->_sections['id']['index']]['type'] == 'TXT'): ?>selected<?php endif; ?> value="TXT">TXT</option>
			</select>
		</td>
		<td colspan="2"> <input <?php if ($this->_tpl_vars['records'][$this->_sections['id']['index']]['issystem'] == 1): ?>disabled<?php endif; ?> class="text" id="ed_<?php echo $this->_tpl_vars['records'][$this->_sections['id']['index']]['id']; ?>
" style="display:<?php if ($this->_tpl_vars['records'][$this->_sections['id']['index']]['type'] != 'MX'): ?>none<?php endif; ?>;" type=text name="records[<?php echo $this->_tpl_vars['records'][$this->_sections['id']['index']]['id']; ?>
][priority]" size=5 value="<?php echo $this->_tpl_vars['records'][$this->_sections['id']['index']]['priority']; ?>
"> <input <?php if ($this->_tpl_vars['records'][$this->_sections['id']['index']]['issystem'] == 1): ?>disabled<?php endif; ?> class="text" type=text name="records[<?php echo $this->_tpl_vars['records'][$this->_sections['id']['index']]['id']; ?>
][value]" size=30 value="<?php echo $this->_tpl_vars['records'][$this->_sections['id']['index']]['value']; ?>
"></td>
	</tr>
	<?php endfor; else: ?>
	<tr>
		<td colspan=6 align="center">No default DNS records found</td>
	</tr>
	<?php endif; ?>
	<tr>
		<td colspan=6>&nbsp;</td>
	</tr>
	<tr>
		<td colspan=6 class="th">Add New Entries Below this Line</td>
	</tr>
	<?php unset($this->_sections['id']);
$this->_sections['id']['name'] = 'id';
$this->_sections['id']['loop'] = is_array($_loop=$this->_tpl_vars['add']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
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
	<tr>
		<td><input type="text" class="text" name="records[<?php echo $this->_tpl_vars['add'][$this->_sections['id']['index']]; ?>
][name]" size=30></td>
		<td><input type="text" class="text" name="records[<?php echo $this->_tpl_vars['add'][$this->_sections['id']['index']]; ?>
][ttl]" size=6 value="14400"></td>
		<td>IN</td>
		<td><select class="text" name="records[<?php echo $this->_tpl_vars['add'][$this->_sections['id']['index']]; ?>
][type]" onchange="CheckPrAdd('ad', '<?php echo $this->_tpl_vars['add'][$this->_sections['id']['index']]; ?>
', this.value)">
				<option selected value="A">A</option>
				<option value="CNAME">CNAME</option>
				<option value="MX">MX</option>
				<option value="NS">NS</option>
				<option value="TXT">TXT</option>
			</select>
		</td>
		<td colspan="2"> <input id="ad_<?php echo $this->_tpl_vars['add'][$this->_sections['id']['index']]; ?>
" size="5" style="display:none;" type="text" class="text" name="records[<?php echo $this->_tpl_vars['add'][$this->_sections['id']['index']]; ?>
][priority]" value="10" size=30> <input type="text" class="text" name="records[<?php echo $this->_tpl_vars['add'][$this->_sections['id']['index']]; ?>
][value]" size=30></td>
	</tr>
	<?php endfor; endif; ?>
</table>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "inc/table_footer.tpl", 'smarty_include_vars' => array('edit_page' => 1)));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "inc/footer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>