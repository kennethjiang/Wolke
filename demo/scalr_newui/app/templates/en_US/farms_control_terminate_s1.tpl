{include file="inc/intable_header.tpl" header="确认关闭云平台" color="Gray"}
<tr>
	<td colspan="2">
	{if $outdated_farm_roles|@count == 0}
		确认关闭云平台 '{$farminfo.name}'? {if $num > 0}所有 <b>{$num}台</b> 服务器将一同关闭。{/if}
	{else}
	    自从上次云平台启动以来你还没有保存你的服务器内容。
	    如果你曾经对这些服务器进行过任何的设置，我们建议你先保存服务器内容。
		否则，所有的相关设置都会在云平台关闭后丢失。 
		<br />
		<br />
		<div style="background-color:#F9F9F9;padding:10px;">
		<div style="font-weight:bold;">保存我如下服务器的设置:</div>
		<br />
			{section name=id loop=$outdated_farm_roles}
			<div style="margin-bottom:10px;">
				<div style="width:100%;">
					<div style="float:left;line-height:40px;">
					<input {if $outdated_farm_roles[id]->IsBundleRunning}checked disabled{/if} onclick="SetSyncChecked('{$outdated_farm_roles[id]->ID}', this.checked);" type="checkbox" name="sync[]" value="{$outdated_farm_roles[id]->ID}" style="vertical-align:middle;"> 
					
					{assign var=frole value=$outdated_farm_roles[id]}
					{assign var=role value=$frole->GetRoleObject()}
					{$role->name} ({$role->imageId}) &nbsp;&nbsp;Last synchronization: {if $outdated_farm_roles[id]->dtLastSync}{$outdated_farm_roles[id]->dtLastSync}{else}Never{/if}
					</div>
					{if $role->hasBehavior('mysql')}
					<div class="Webta_ExperimentalMsg" style="float:left;margin-left:15px;padding-right:15px;font-size:12px;">
						绑定将不包括MySQL数据. <a href='farm_mysql_info.php?farmid={$farminfo.id}'>如果你想包括MySQL数据，请点击这里</a>.
					</div> 
					{/if}
					<div style="clear:both;font-size:1px;"></div>
				</div>
				{if !$outdated_farm_roles[id]->IsBundleRunning}
				<div id="i_{$outdated_farm_roles[id]->ID}" style="margin-left:20px;display:none;">
					{assign var=servers value=$outdated_farm_roles[id]->RunningServers}
					{section name=iid loop=$servers}
						<input {if $smarty.section.iid.first}checked{/if} style="vertical-align:middle;" type="radio" name="sync_i[{$outdated_farm_roles[id]->ID}]" value="{$servers[iid]->serverId}"> {$servers[iid]->serverId} ({$servers[iid]->remoteIp})
						<br />
					{sectionelse}
						当前服务角色未发现任何服务器。
					{/section}
				</div>
				{else}
				<div id="i_{$outdated_farm_roles[id]->ID}" style="margin-left:20px;">
					对服务角色的同步已经在进行中... 
				</div>
				{/if}
			</div>
			{/section}
			<div id="sync_opts" style="display:none;">
			<br />
				<input style="vertical-align:middle;" type="checkbox" name="untermonfail" value="1"> 当有任何一个服务角色的同步失败时，不要关闭云平台
			</div>
		</div>
	{/if}
	</td>
</tr>
{include file="inc/intable_footer.tpl" color="Gray"}
