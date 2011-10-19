{include file="inc/header.tpl"}
	{include file="inc/table_header.tpl"}
		{include file="inc/intable_header.tpl" header="General" color="Gray"}
        <tr>
            <td nowrap="nowrap" width="20%">Host:</td>
            <td><input name="ipaddress" type="text" class="text" id="name" value="{$ip.ipaddress}" /> <span style="font-size:10px;">(Example: 192.168.*.* or 192.168.1.1 or mydomain.com)</span></td>
	    </tr>
	    <tr>
            <td nowrap="nowrap" width="20%">Comment:</td>
            <td><input name="comment" type="text" class="text" id="name" value="{$ip.comment}" /></td>
	    </tr>
		{include file="inc/intable_footer.tpl" color="Gray"}
	{include file="inc/table_footer.tpl" edit_page=1}
{include file="inc/footer.tpl"}