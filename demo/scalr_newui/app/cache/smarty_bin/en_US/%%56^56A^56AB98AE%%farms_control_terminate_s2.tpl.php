<?php /* Smarty version 2.6.26, created on 2011-10-18 00:57:57
         compiled from farms_control_terminate_s2.tpl */ ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "inc/intable_header.tpl", 'smarty_include_vars' => array('header' => 'DNS Zone','color' => 'Gray')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<tr>
	<td colspan="2">
		<input style="vertical-align:middle;" checked type="checkbox" name="deleteDNS" value="1"> 从域名服务器上删除DNS区域. 该区域会在下次云平台启动时重新生成.
	</td>
</tr>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "inc/intable_footer.tpl", 'smarty_include_vars' => array('color' => 'Gray')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
    	
<?php if ($this->_tpl_vars['elastic_ips'] > 0): ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "inc/intable_header.tpl", 'smarty_include_vars' => array('header' => 'Elastic IPs','color' => 'Gray')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<tr>
	<td colspan="2">
		<div style="margin-top:10px;margin-left:-2px;">
			<input type="radio" style="vertical-align:middle;" name="keep_elastic_ips" value="0">
			<span style="vertical-align:middle;">Release the static IP adresses that are allocated for this farm. When you start the farm again, new IPs will be allocated.</span>
		</div>
		<div style="margin-top:10px;margin-left:-2px;">
			<input type="radio" style="vertical-align:middle;" checked="checked" name="keep_elastic_ips" value="1">
			<span style="vertical-align:middle;">Keep the static IP adresses that are allocated for this farm. Amazon will keep billing you for them even when the farm is stopped.</span>
		</div>
	</td>
</tr>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "inc/intable_footer.tpl", 'smarty_include_vars' => array('color' => 'Gray')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php endif; ?>

<?php if ($this->_tpl_vars['ebs'] > 0): ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "inc/intable_header.tpl", 'smarty_include_vars' => array('header' => "EBS (Elastic Block Storage)",'color' => 'Gray')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<tr>
	<td colspan="2">
		<div style="margin-top:10px;margin-left:-2px;">
			<input type="radio" style="vertical-align:middle;" name="keep_ebs" value="0">
			<span style="vertical-align:middle;">释放此云平台的EBS卷，在下次云平台启动时会生成新的EBS卷。</span>
		</div>
		<div style="margin-top:10px;margin-left:-2px;">
			<input type="radio" style="vertical-align:middle;" checked="checked" name="keep_ebs" value="1">
			<span style="vertical-align:middle;">保留此云平台的EBS卷。你需要为此继续支付费用给Amazon。</span>
		</div>
	</td>
</tr>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "inc/intable_footer.tpl", 'smarty_include_vars' => array('color' => 'Gray')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php endif; ?>