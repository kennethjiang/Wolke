<div class="header-message warn-message">
	Amazon has the following issues that could affect your infrastructure:<br> 			
		{foreach from=$aws_problems_items item=problem}
	  		&bull; [{$problem.pub_date}] {$problem.title}. {$problem.description}<br/>
		{/foreach} 
		<a href='http://status.aws.amazon.com/' target='_blank'>Learn more...</a>
</div>