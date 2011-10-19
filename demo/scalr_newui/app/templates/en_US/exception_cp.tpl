{include file="inc/header.tpl"}
{literal}
<style type="text/css">
.backtrace {
	list-style:decimal;
	margin:10px 0px 0px 35px;
	padding:0px;
}
</style>
{/literal}
<center>
<div align="center" style="width: 80%; margin:50px;">
	<div style="font-size:24px; background-color:red;padding:10px; color:white;">Unrecoverable error</div>
    <div style="background-color: #f0f0f0; text-align:left;font-size:14px; color:black; padding:20px;">{$message}</div>
    {if $backtrace}
	    <div style="overflow: auto; height:200px; word-wrap: break-word; text-align:left; padding: 20px; background-color:fcfcfc;">
	    <span style="text-decoration:underline;">Call stack</span> 
		{$backtrace}
	    </div>
	{/if}
    <div style="height:2px; background-color:#CCCCCC; font-size:1px;"></div>
    <div class="WebtaTable_Footer" id="footer_button_table" style="padding-left:6px;padding-top:2px; padding-bottom:2px;">
    	</br>
    </div>
</div>
</center>
{include file="inc/footer.tpl"}
