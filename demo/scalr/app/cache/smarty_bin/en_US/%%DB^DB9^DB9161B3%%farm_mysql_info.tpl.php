<?php /* Smarty version 2.6.26, created on 2011-09-18 17:33:48
         compiled from farm_mysql_info.tpl */ ?>
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
$this->_smarty_include(array('smarty_include_tpl_file' => "inc/intable_header.tpl", 'smarty_include_vars' => array('intable_colspan' => 2,'header' => 'PHPMyAdmin','intable_first_column_width' => '150','color' => 'Gray')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		<tr>
			<td colspan="3">
				<?php if ($this->_tpl_vars['mysql_pma_credentials']): ?>
					<input class="btn" type="submit" name="pma_launch" value="启动PHPMyAdmin" />
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<input class="btn" type="submit" name="pma_reset" value="重置PHPMyAdmin认证" />
				<?php elseif ($this->_tpl_vars['mysql_pma_processing_access_request']): ?>
					MySQL需要访问具体PMA认证信息。请稍候再刷新本页面...
				<?php else: ?>
					<input class="btn" type="submit" name="pma_request_credentials" value="安装PHPMyAdmin Access" />
				<?php endif; ?>
			</td>
		</tr>
		<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "inc/intable_footer.tpl", 'smarty_include_vars' => array('color' => 'Gray')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?> 
	
		<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "inc/intable_header.tpl", 'smarty_include_vars' => array('intable_colspan' => 2,'header' => "MySQL备份和数据绑定",'intable_first_column_width' => '150','color' => 'Gray')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		<tr>
			<td colspan="3">
				<table>
				<tr>
					<td width="150">最新备份: </td>
					<td width="300">
						<?php if ($this->_tpl_vars['mysql_bcp_running']): ?>
						正在<a href="#/servers/<?php echo $this->_tpl_vars['mysql_bcp_server_id']; ?>
/extendedInfo"><?php echo $this->_tpl_vars['mysql_bcp_server_id']; ?>
</a>处理...
						<?php else: ?>
							<?php if ($this->_tpl_vars['mysql_last_backup']): ?><?php echo $this->_tpl_vars['mysql_last_backup']; ?>
<?php else: ?>无<?php endif; ?>
						<?php endif; ?>
					</td>
					<td>
						<input type="submit" name="run_bcp" class="btn" value="现在进行备份" />
					</td>
				</tr>
				<tr>
					<td>最新数据绑定: </td>
					<td>
						<?php if ($this->_tpl_vars['mysql_bundle_running']): ?>
						正在<a href="#/servers/<?php echo $this->_tpl_vars['mysql_bundle_server_id']; ?>
/extendedInfo"><?php echo $this->_tpl_vars['mysql_bundle_server_id']; ?>
</a>处理...
						<?php else: ?>
							<?php if ($this->_tpl_vars['mysql_last_bundle']): ?><?php echo $this->_tpl_vars['mysql_last_bundle']; ?>
<?php else: ?>无<?php endif; ?>
						<?php endif; ?>
					</td>
					<td>
						<input type="submit" <?php if ($this->_tpl_vars['mysql_bundle_running']): ?>disabled<?php endif; ?> name="run_bundle" class="btn" value="现在进行MySQL数据绑定" />
					</td>
				</tr>
				<!-- 
				<?php if ($this->_tpl_vars['mysql_data_storage_engine'] == 'ebs'): ?>
				<tr>
					<td colspan="3">&nbsp;</td>
				</tr>
				<tr>
					<td>MySQL EBS卷ID: </td>
					<td>
						<input type="text" name="mysql_master_ebs" class="text" value="<?php echo $this->_tpl_vars['mysql_master_ebs_volume_id']; ?>
" />
					</td>
					<td>
						<input type="submit" name="update_volumeid" class="btn" value="修改" />
					</td>
				</tr>
				<?php endif; ?>
				 -->
				<!-- 
				<tr>
					<td colspan="10">&nbsp;</td>
				</tr>
				<tr>
					<td colspan="10">&nbsp;</td>
				</tr>
				<tr>
					<td colspan="10">
						<input style="color:red;" type="submit" name="remove_mysql_data_bundle" class="btn" value="删除MySQL绑定" />
					</td>
				</tr>
				 -->
				</table>
			</td>
		</tr>
		<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "inc/intable_footer.tpl", 'smarty_include_vars' => array('color' => 'Gray')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>     
		
		<!-- 
		<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "inc/intable_header.tpl", 'smarty_include_vars' => array('intable_colspan' => 2,'header' => 'MySQL storage information','intable_first_column_width' => '150','color' => 'Gray')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		<tr>
			<td>ID: </td>
			<td colspan="2">
				<?php echo $this->_tpl_vars['storage']['id']; ?>

			</td>
		</tr>
		<tr>
			<td>Type: </td>
			<td colspan="2">
				<?php echo $this->_tpl_vars['storage']['type']; ?>

			</td>
		</tr>
		<tr>
			<td>Version: </td>
			<td colspan="2">
				<?php echo $this->_tpl_vars['storage']['version']; ?>

			</td>
		</tr>
		<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "inc/intable_footer.tpl", 'smarty_include_vars' => array('color' => 'Gray')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?> 
		 -->             		
		<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "inc/mysql_replication_status.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
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