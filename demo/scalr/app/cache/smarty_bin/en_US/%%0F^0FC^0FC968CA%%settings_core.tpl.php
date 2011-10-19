<?php /* Smarty version 2.6.26, created on 2011-09-20 00:17:29
         compiled from settings_core.tpl */ ?>
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
		<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "inc/intable_header.tpl", 'smarty_include_vars' => array('header' => 'Admin account','color' => 'Gray')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		<tr>
			<td width="18%">Login:</td>
			<td width="82%"><input name="admin_login" type="text" class="text" id="login" value="<?php echo $this->_tpl_vars['admin_login']; ?>
" size="30"></td>
		</tr>
		<tr>
			<td>Password:</td>
			<td><input name="pass" type="password" class="text" id="pass" value="******" size="30"></td>
		</tr>
		<tr>
			<td width="18%">E-mail:</td>
			<td width="82%"><input name="email_address" type="text" class="text" id="email_address" value="<?php echo $this->_tpl_vars['email_address']; ?>
" size="30"></td>
		</tr>
		<tr>
			<td>Name:</td>
			<td><input name="email_name" type="text" class="text" id="email_name" value="<?php echo $this->_tpl_vars['email_name']; ?>
" size="30">
		</td>
		</tr>
		<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "inc/intable_footer.tpl", 'smarty_include_vars' => array('color' => 'Gray')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		
		<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "inc/intable_header.tpl", 'smarty_include_vars' => array('header' => 'eMail settings','color' => 'Gray')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		<tr>
			<td width="18%">SMTP connection:</td>
			<td width="82%"><input name="email_dsn" type="text" class="text" id="email_dsn" value="<?php echo $this->_tpl_vars['email_dsn']; ?>
" size="30"> (user:password@host:port. Leave empty to use MTA)</td>
		</tr>
		<tr valign="top">
			<td width="18%">Scalr team emails (one per line):</td>
			<td width="82%"><textarea name="team_emails" class="text" id="team_emails" cols="60" rows="5"><?php echo $this->_tpl_vars['team_emails']; ?>
</textarea></td>
		</tr>
		<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "inc/intable_footer.tpl", 'smarty_include_vars' => array('color' => 'Gray')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		
		<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "inc/intable_header.tpl", 'smarty_include_vars' => array('header' => 'Log rotation settings','color' => 'Gray')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		<tr>
			<td width="18%">Keep logs for:</td>
			<td width="82%"><input name="log_days" type="text" class="text" id="log_days" value="<?php echo $this->_tpl_vars['log_days']; ?>
" size="5"> days</td>
		</tr>
		<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "inc/intable_footer.tpl", 'smarty_include_vars' => array('color' => 'Gray')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		
		<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "inc/intable_header.tpl", 'smarty_include_vars' => array('header' => 'DNS settings','color' => 'Gray')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		<tr>
			<td width="18%">Dynamic A record TTL:</td>
			<td width="82%"><input name="dynamic_a_rec_ttl" type="text" class="text" id="dynamic_a_rec_ttl" value="<?php echo $this->_tpl_vars['dynamic_a_rec_ttl']; ?>
" size="5"> seconds</td>
		</tr>
		<tr>
			<td width="18%">Default SOA owner:</td>
			<td width="82%"><input name="def_soa_owner" type="text" class="text" id="def_soa_owner" value="<?php echo $this->_tpl_vars['def_soa_owner']; ?>
" size="30"></td>
		</tr>
		<tr>
			<td width="18%">Default SOA parent:</td>
			<td width="82%"><input name="def_soa_parent" type="text" class="text" id="def_soa_parent" value="<?php echo $this->_tpl_vars['def_soa_parent']; ?>
" size="30"></td>
		</tr>
		<tr>
			<td width="18%">Default SOA TTL:</td>
			<td width="82%"><input name="def_soa_ttl" type="text" class="text" id="def_soa_ttl" value="<?php echo $this->_tpl_vars['def_soa_ttl']; ?>
" size="30"></td>
		</tr>
		<tr>
			<td width="18%">Default SOA Refresh:</td>
			<td width="82%"><input name="def_soa_refresh" type="text" class="text" id="def_soa_refresh" value="<?php echo $this->_tpl_vars['def_soa_refresh']; ?>
" size="30"></td>
		</tr>
		<tr>
			<td width="18%">Default SOA Retry:</td>
			<td width="82%"><input name="def_soa_retry" type="text" class="text" id="def_soa_retry" value="<?php echo $this->_tpl_vars['def_soa_retry']; ?>
" size="30"></td>
		</tr>
		<tr>
			<td width="18%">Default SOA Expire:</td>
			<td width="82%"><input name="def_soa_expire" type="text" class="text" id="def_soa_expire" value="<?php echo $this->_tpl_vars['def_soa_expire']; ?>
" size="30"></td>
		</tr>
		<tr>
			<td width="18%">Default SOA Minimum TTL:</td>
			<td width="82%"><input name="def_soa_minttl" type="text" class="text" id="def_soa_minttl" value="<?php echo $this->_tpl_vars['def_soa_minttl']; ?>
" size="30"></td>
		</tr>
		<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "inc/intable_footer.tpl", 'smarty_include_vars' => array('color' => 'Gray')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		
		<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "inc/intable_header.tpl", 'smarty_include_vars' => array('header' => 'AWS settings','color' => 'Gray')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		<tr>
			<td width="18%">Security groups prefix:</td>
			<td width="82%"><input name="secgroup_prefix" type="text" class="text" id="secgroup_prefix" value="<?php echo $this->_tpl_vars['secgroup_prefix']; ?>
" size="30"></td>
		</tr>
		<tr valign="top">
			<td width="18%">S3cfg template:</td>
			<td width="82%"><textarea name="s3cfg_template" class="text" id="s3cfg_template" cols="60" rows="10"><?php echo $this->_tpl_vars['s3cfg_template']; ?>
</textarea></td>
		</tr>
		<tr>
			<td width="18%">Instances limit:</td>
			<td width="82%"><input name="client_max_instances" type="text" class="text" id="client_max_instances" value="<?php echo $this->_tpl_vars['client_max_instances']; ?>
" size="10"></td>
		</tr>
		<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "inc/intable_footer.tpl", 'smarty_include_vars' => array('color' => 'Gray')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		
		<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "inc/intable_header.tpl", 'smarty_include_vars' => array('header' => 'RRD statistics settings','color' => 'Gray')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		<tr>
			<td width="18%">Path to rrdtool binary:</td>
			<td width="82%"><input name="rrdtool_path" type="text" class="text" id="rrdtool_path" value="<?php echo $this->_tpl_vars['rrdtool_path']; ?>
" size="30"></td>
		</tr>
		<tr>
			<td width="18%">Path to font (for rrdtool):</td>
			<td width="82%"><input name="rrd_default_font_path" type="text" class="text" id="rrd_default_font_path" value="<?php echo $this->_tpl_vars['rrd_default_font_path']; ?>
" size="30"></td>
		</tr>
		<tr>
			<td width="18%">Path to RRD database dir:</td>
			<td width="82%"><input name="rrd_db_dir" type="text" class="text" id="rrd_db_dir" value="<?php echo $this->_tpl_vars['rrd_db_dir']; ?>
" size="30"></td>
		</tr>
		<tr>
			<td width="18%">Statistics URL:</td>
			<td width="82%"><input name="rrd_stats_url" type="text" class="text" id="rrd_stats_url" value="<?php if ($this->_tpl_vars['rrd_stats_url']): ?><?php echo $this->_tpl_vars['rrd_stats_url']; ?>
<?php else: ?>http://<?php echo $_SERVER['SERVER_NAME']; ?>
<?php endif; ?>" size="30">
			<span class="Webta_Ihelp">Allowed tags: %fid% - Farm ID, %rn% - role name, %wn% - watcher name</span>
			</td>
		</tr>
		<tr>
			<td width="18%">Store graphics in:</td>
			<td width="82%">
				<select name="rrd_graph_storage_type">
					<option <?php if ($this->_tpl_vars['rrd_graph_storage_type'] == 'S3'): ?>selected<?php endif; ?> value="S3">Amazon S3</option>
					<option <?php if ($this->_tpl_vars['rrd_graph_storage_type'] == 'LOCAL'): ?>selected<?php endif; ?> value="LOCAL">Local filesystem</option>
				</select>
			</td>
		</tr>
		<tr>
			<td width="18%">Path to graphics:</td>
			<td width="82%"><input name="rrd_graph_storage_path" type="text" class="text" id="rrd_graph_storage_path" value="<?php echo $this->_tpl_vars['rrd_graph_storage_path']; ?>
" size="30">
			<span class="Webta_Ihelp">Bucket name for Amazon S3 or path to folder for Local filesystem</span>
			</td>
		</tr>
		<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "inc/intable_footer.tpl", 'smarty_include_vars' => array('color' => 'Gray')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		
		<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "inc/intable_header.tpl", 'smarty_include_vars' => array('header' => 'Application settings','color' => 'Gray')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		<tr>
			<td width="18%">Event handler URL:</td>
			<td width="82%"><select name="http_proto" class="text" style="vertical-align:middle;">
				<option <?php if ($this->_tpl_vars['http_proto'] == 'http'): ?>selected<?php endif; ?> value="http">http://</option>
				<option <?php if ($this->_tpl_vars['http_proto'] == 'https'): ?>selected<?php endif; ?> value="https">https://</option>
			</select><input name="eventhandler_url" type="text" class="text" id="eventhandler_url" value="<?php echo $this->_tpl_vars['eventhandler_url']; ?>
" size="30"></td>
		</tr>
		<tr>
			<td colspan="2">Terminate instance if it doesn't send 'rebootFinish' event after reboot in <input name="reboot_timeout" type="text" class="text" id="reboot_timeout" value="<?php echo $this->_tpl_vars['reboot_timeout']; ?>
" size="3"> seconds.</td>
		</tr>
		<tr>
			<td colspan="2">Terminate instance if it doesn't send 'hostUp' or 'hostInit' event after launch in <input name="launch_timeout" type="text" class="text" id="launch_timeout" value="<?php echo $this->_tpl_vars['launch_timeout']; ?>
" size="3"> seconds.</td>
		</tr>
		<tr>
			<td width="18%">Cron processes number:</td>
			<td width="82%"><input name="cron_processes_number" type="text" class="text" id="cron_processes_number" value="<?php echo $this->_tpl_vars['cron_processes_number']; ?>
" size="5"></td>
		</tr>
		<tr>
			<td width="18%">Server IP address:</td>
			<td width="82%"><input name="app_sys_ipaddress" type="text" class="text" id="app_sys_ipaddress" value="<?php echo $this->_tpl_vars['app_sys_ipaddress']; ?>
"></td>
		</tr>
		<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "inc/intable_footer.tpl", 'smarty_include_vars' => array('color' => 'Gray')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
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