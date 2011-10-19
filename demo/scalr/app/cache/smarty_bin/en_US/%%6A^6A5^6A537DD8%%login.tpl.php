<?php /* Smarty version 2.6.26, created on 2011-09-19 02:50:29
         compiled from login.tpl */ ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "inc/login_header.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	<div class="middle" align="center" style="width:100%;">	
	
		<table border="0" cellpadding="0" cellspacing="0" class="Webta_Table">
		<tr>
			<td width="7"><div class="TableHeaderLeft"></div></td>
			<td><div class="TableHeaderCenter"></div></td>
			<td width="7"><div class="TableHeaderRight"></div></td>
		</tr>
		<tr>
			<td width="7" class="TableHeaderCenter"></td>
			<td align="center"><div id="loginform" style="width:450px;">
				<?php if ($this->_tpl_vars['err'] != ''): ?>
				<span class="error">Incorrect login or password</span>
				<?php endif; ?>
				<div id="loginform_inner" style="margin-left:40px;">
				  <table align="center" cellpadding="5" cellspacing="0">
				    <tr>	
				    	<td colspan="2">&nbsp;</td>
				    </tr>
				    <tr>
					    <td align="right">用户名:</td>
				    	<td align="left"><input name="login" type="text" class="text" id="login" value="<?php echo $this->_tpl_vars['login']; ?>
" size="15" /></td>
				    </tr>
				    <tr>
				    	<td align="right">密码:</td>
						<td align="left"><input name="pass" type="password" class="text" id="pass" size="15" /></td>
				    </tr>
				    <tr>
				    	<td><input name="s2" type="hidden" id="s2" value="<?php echo $this->_tpl_vars['s']; ?>
" /></td>
				    	<td align="left"><input name="Submit2" type="submit" class="btn" value="登录" /></td>
				    </tr>
				  </table>
				  </div>
				  </div>
				  </td>
			<td width="7" class="TableHeaderCenter"></td>
		</tr>
		<tr>
			<td width="7"><div class="TableFooterLeft"></div></td>
			<td><div class="TableFooterCenter"></div></td>
			<td width="7"><div class="TableFooterRight"></div></td>
		</tr>
		</table>
	</div>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "inc/login_footer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>