{literal}
<style type="text/css">
.backtrace {
	list-style:decimal;
	margin:10px 0px 0px 35px;
	padding:0px;
}
</style>
{/literal}
<div id="content" class="inner-content">
		<div class="text-page">
		<div align="center" style="width: 600px; padding:30px;">
			<div style="font-size:24px; background-color:red;padding:10px; color:white;">Oops... Something went wrong.</div>
		    <div style="background-color: #f0f0f0; text-align:center;font-size:14px; color:black; padding:20px;">
		    	We apologize for the inconvenience. An email has been sent to us, please try again later.
		    </div>
		    {if $message}
		    	<div style="overflow: auto; height:200px; word-wrap: break-word; text-align:left; padding: 20px; background-color:fcfcfc;">
			 	   <span style="text-decoration:underline;">Call stack</span> {$backtrace}
			 	   {$message}
			 	</div>
			{else}
				<!-- Put something here -->
			{/if}
		    <div style="height:2px; background-color:#CCCCCC; font-size:1px;"></div>
		</div>
	</div>
</div>