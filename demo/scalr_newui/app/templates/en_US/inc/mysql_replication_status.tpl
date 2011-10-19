{include file="inc/intable_header.tpl" header="同步状态" intable_first_column_width="150" color="Gray"}
{foreach from=$replication_status key=server_id item=status}
<tr>
	<td colspan="2">
		<table width="500" cellpadding="4">
			<tr>
				<td colspan="2">
					{if $status.IsMaster == 1}{t}主服务器{/t}{else}{t}从服务器 #{/t}{$status.SlaveNumber}{/if} (<a href="#/servers/{$server_id}/extendedInfo">{$server_id}</a>):
				</td>
			</tr>
			<tr>
				<td colspan="2"><div style="vertical-align:middle;font-size:1px;border-bottom:1px dotted black;width:100%">&nbsp;</div></td>
			</tr>
			{if !$status.error}
				{if !$status.IsMaster}
					{if $status.data.Slave_IO_Running && $status.data.Slave_IO_Running == 'Yes'}
	                <tr>
						<td width="240"><b>{t}从服务器状态:{/t}</b></td>
						<td style="color:green;"><img src="images/true.gif"> OK</td>
					</tr>
					{else}
					<tr>
						<td width="240"><b>{t}从服务器状态:{/t}</b></td>
						<td style="color:red;"><img src="images/del.gif"></td>
					</tr>
					{/if}
					<tr>
						<td width="240"><b>{t}数据日志位置:{/t}</b></td>
						<td style="color:red;">
							{if $status.MasterPosition-$status.SlavePosition > 0 || $status.Seconds_Behind_Master != 0 || !$status.SlavePosition}
								<span style="color:red;"><img src="images/del.gif"> {$status.SlavePosition}</span>
							{else}
								<span style="color:green;"><img src="images/true.gif"> {$status.SlavePosition}</span>
							{/if}
						</td>
					</tr>
				{else}
					<tr>
						<td width="240"><b>{t}数据日志位置:{/t}</b></td>
						<td style="color:red;">
							<span style="color:green;"><img src="images/true.gif"> {$status.MasterPosition}</span>
						</td>
					</tr>
				{/if}
				{foreach from=$status.data key=key item=item}
				<tr>
					<td width="240">{$key}:</td>
					<td>{$item}</td>
				</tr>
				{/foreach}
				<tr>
					<td>&nbsp;</td>
				</tr>
			{else}
				<tr>
					<td colspan="2">
						<div class="Webta_ErrMsg" id="Webta_ErrMsg">
						{t}未获得MySQL同步信息{/t} {$status.error}
						</div>
					</td>
				</tr>
			{/if}
		</table>
	</td>
</tr>
{foreachelse}
<tr>
	<td colspan="2">{t}未获得MySQL同步信息{/t}</td>
</tr>
{/foreach}
{include file="inc/intable_footer.tpl" color="Gray"}
