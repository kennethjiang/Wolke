{include file="inc/header.tpl"}
	{include file="inc/table_header.tpl"}
		{include file="inc/intable_header.tpl" intable_colspan=2 header="PHPMyAdmin" intable_first_column_width="150" color="Gray"}
		<tr>
			<td colspan="3">
				{if $mysql_pma_credentials}
					<input class="btn" type="submit" name="pma_launch" value="启动PHPMyAdmin" />
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<input class="btn" type="submit" name="pma_reset" value="重置PHPMyAdmin认证" />
				{elseif $mysql_pma_processing_access_request}
					MySQL需要访问具体PMA认证信息。请稍候再刷新本页面...
				{else}
					<input class="btn" type="submit" name="pma_request_credentials" value="安装PHPMyAdmin Access" />
				{/if}
			</td>
		</tr>
		{include file="inc/intable_footer.tpl" color="Gray"} 
	
		{include file="inc/intable_header.tpl" intable_colspan=2 header="MySQL备份和数据绑定" intable_first_column_width="150" color="Gray"}
		<tr>
			<td colspan="3">
				<table>
				<tr>
					<td width="150">最新备份: </td>
					<td width="300">
						{if $mysql_bcp_running}
						正在<a href="#/servers/{$mysql_bcp_server_id}/extendedInfo">{$mysql_bcp_server_id}</a>处理...
						{else}
							{if $mysql_last_backup}{$mysql_last_backup}{else}无{/if}
						{/if}
					</td>
					<td>
						<input type="submit" name="run_bcp" class="btn" value="现在进行备份" />
					</td>
				</tr>
				<tr>
					<td>最新数据绑定: </td>
					<td>
						{if $mysql_bundle_running}
						正在<a href="#/servers/{$mysql_bundle_server_id}/extendedInfo">{$mysql_bundle_server_id}</a>处理...
						{else}
							{if $mysql_last_bundle}{$mysql_last_bundle}{else}无{/if}
						{/if}
					</td>
					<td>
						<input type="submit" {if $mysql_bundle_running}disabled{/if} name="run_bundle" class="btn" value="现在进行MySQL数据绑定" />
					</td>
				</tr>
				<!-- 
				{if $mysql_data_storage_engine == 'ebs'}
				<tr>
					<td colspan="3">&nbsp;</td>
				</tr>
				<tr>
					<td>MySQL EBS卷ID: </td>
					<td>
						<input type="text" name="mysql_master_ebs" class="text" value="{$mysql_master_ebs_volume_id}" />
					</td>
					<td>
						<input type="submit" name="update_volumeid" class="btn" value="修改" />
					</td>
				</tr>
				{/if}
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
		{include file="inc/intable_footer.tpl" color="Gray"}     
		
		<!-- 
		{include file="inc/intable_header.tpl" intable_colspan=2 header="MySQL storage information" intable_first_column_width="150" color="Gray"}
		<tr>
			<td>ID: </td>
			<td colspan="2">
				{$storage.id}
			</td>
		</tr>
		<tr>
			<td>Type: </td>
			<td colspan="2">
				{$storage.type}
			</td>
		</tr>
		<tr>
			<td>Version: </td>
			<td colspan="2">
				{$storage.version}
			</td>
		</tr>
		{include file="inc/intable_footer.tpl" color="Gray"} 
		 -->             		
		{include file="inc/mysql_replication_status.tpl"}
	{include file="inc/table_footer.tpl" disable_footer_line=1}
{include file="inc/footer.tpl"}