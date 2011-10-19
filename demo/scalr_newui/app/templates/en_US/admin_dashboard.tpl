{include file="inc/header.tpl"}
    {if $Scalr_Session->getClientId() == 0}
	<table width="100%" border="0">
		<tr valign="top">
			<td>
				{include file="inc/table_header.tpl" nofilter=1}
					{include file="inc/intable_header.tpl" header="Clients" color="Gray"}
						<tr>
							<td>Total:</td>
							<td>{$clients.total} [<a href="clients_view.php">View</a>]</td>
						</tr>
						<tr>
							<td>Active:</td>
							<td>{$clients.active} [<a href="clients_view.php?isactive=1">View</a>]</td>
						</tr>
						<tr>
							<td>Inactive:</td>
							<td>{$clients.inactive} [<a href="clients_view.php?isactive=0">View</a>]</td>
						</tr>
					{include file="inc/intable_footer.tpl" color="Gray"}
				{include file="inc/table_footer.tpl" disable_footer_line=1}
			</td>
		</tr>
	</table>
	{/if}
{include file="inc/footer.tpl"}
