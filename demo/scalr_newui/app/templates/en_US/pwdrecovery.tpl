{include file="inc/login_header.tpl"}
{include file="inc/splash.tpl"}
<div id="content">
	{include file="inc/main_contents.tpl"}
	<div id="forms-col">
		<div>
			<div style="background: url(/images/rc-gray-middle.png) repeat-y;" id="login-form">
				<div style="background: url(/images/rc-gray-top.png) no-repeat; height:13px;"></div>
				<div>
				<form action="" method="POST">
					<h2>Password recovery</h2>
					{if $err.0 != ''}
					<span style="color:red;font-weight:bold;">{$err.0}</span>
					{/if}
					<p><label>E-mail:</label><input type="text" name="email" class="textfield" value="" /></p>
					<input type="hidden" name="action" value="pwdrecovery" />
					<p><button type="submit" style="cursor:pointer;">Reset password</button></p>
					<p class="links"><a href="/faq.html">FAQ</a> | <a href="/login.php">Log in</a></p>
				</form>
				</div>
				<div style="background: url(/images/rc-gray-bottom.png) no-repeat; height:13px;"></div>
			</div>
		</div>
	</div>
</div>
{include file="inc/login_footer.tpl"}