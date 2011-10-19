<?php /* Smarty version 2.6.26, created on 2011-09-29 04:24:03
         compiled from exception.tpl */ ?>
<?php echo '
<style type="text/css">
.backtrace {
	list-style:decimal;
	margin:10px 0px 0px 35px;
	padding:0px;
}
</style>
'; ?>

<div id="content" class="inner-content">
		<div class="text-page">
		<div align="center" style="width: 600px; padding:30px;">
			<div style="font-size:24px; background-color:red;padding:10px; color:white;">Oops... Something went wrong.</div>
		    <div style="background-color: #f0f0f0; text-align:center;font-size:14px; color:black; padding:20px;">
		    	We apologize for the inconvenience. An email has been sent to us, please try again later.
		    </div>
		    <?php if ($this->_tpl_vars['message']): ?>
		    	<div style="overflow: auto; height:200px; word-wrap: break-word; text-align:left; padding: 20px; background-color:fcfcfc;">
			 	   <span style="text-decoration:underline;">Call stack</span> <?php echo $this->_tpl_vars['backtrace']; ?>

			 	   <?php echo $this->_tpl_vars['message']; ?>

			 	</div>
			<?php else: ?>
				<!-- Put something here -->
			<?php endif; ?>
		    <div style="height:2px; background-color:#CCCCCC; font-size:1px;"></div>
		</div>
	</div>
</div>