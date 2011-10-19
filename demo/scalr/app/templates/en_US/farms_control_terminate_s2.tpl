{include file="inc/intable_header.tpl" header="DNS Zone" color="Gray"}
<tr>
	<td colspan="2">
		<input style="vertical-align:middle;" checked type="checkbox" name="deleteDNS" value="1"> 从域名服务器上删除DNS区域. 该区域会在下次服务器组启动时重新生成.
	</td>
</tr>
{include file="inc/intable_footer.tpl" color="Gray"}
    	
{if $elastic_ips > 0}
{include file="inc/intable_header.tpl" header="Elastic IPs" color="Gray"}
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
{include file="inc/intable_footer.tpl" color="Gray"}
{/if}

{if $ebs > 0}
{include file="inc/intable_header.tpl" header="EBS (Elastic Block Storage)" color="Gray"}
<tr>
	<td colspan="2">
		<div style="margin-top:10px;margin-left:-2px;">
			<input type="radio" style="vertical-align:middle;" name="keep_ebs" value="0">
			<span style="vertical-align:middle;">释放此服务器组的EBS卷，在下次服务器组启动时会生成新的EBS卷。</span>
		</div>
		<div style="margin-top:10px;margin-left:-2px;">
			<input type="radio" style="vertical-align:middle;" checked="checked" name="keep_ebs" value="1">
			<span style="vertical-align:middle;">保留此服务器组的EBS卷。你需要为此继续支付费用给Amazon。</span>
		</div>
	</td>
</tr>
{include file="inc/intable_footer.tpl" color="Gray"}
{/if}