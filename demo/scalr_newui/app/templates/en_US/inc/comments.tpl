		{include file="inc/intable_header.tpl" header="Comments" color="Gray"}
    	<tr>
    		<td colspan="2">
    		<a id="comments"></a>
    		{foreach item=comment from=$comments}
    			{if $comment.isprivate == 0 || ($smarty.session.uid == 0 || $smarty.session.uid == $comment.object_owner)}
    			<div class="comment_base {$comment.color_schema}">
    				<div>
    					<div style="float:left;">
    						{if $comment.isprivate}
    						<img src="/images/private.png" title="Private message" style="vertical-align:middle;" />
    						{/if}
    						From: {if $smarty.session.uid == 0 && $comment.clientid != 0}<a href="clients_view.php?clientid={$comment.clientid}">{/if}{$comment.client.fullname}{if $smarty.session.uid == 0}</a>{/if}
    					</div>
    					<div style="float:right;">
    						Date: {$comment.dtcreated}
    						{if $smarty.session.uid == 0}
    							<img onClick="if (window.confirm('Are you sure?')) document.location = document.location+'&task=delete&commentid={$comment.id}'" style="cursor:pointer;vertical-align:middle;" title="Remove this comment" src="/images/cross_circle.png">
    						{/if}
    					</div>
    					<div style="clear:both;"></div>
    				</div>
    				<div style="margin-top:15px;">
						{$comment.comment|nl2br}
					</div>
    			</div>
    			{/if}
    		{/foreach}
    		</td>
    	</tr>
    	<tr>
    		<td colspan="2">
    			<table>
    				<tr valign="top">
    					<td width="150">{t}Add comment:{/t}<a id="addcomment"></a></td>
    					<td>
    						<textarea name="comment" rows="10" cols="50" class="text"></textarea>
    					</td>
    				</tr>
    				{if $smarty.session.uid == 0}
	    				{if $allow_moderation}
	    				<tr valign="top">
	    					<td width="150">{t}Moderation phase:{/t}</td>
	    					<td>
	    						<select name="approval_state" class="text">
	    							{if $approval_state == 'Pending'}
	    								<option {if $approval_state == 'Pending'}selected{/if} value="Pending">{t}Pending{/t}</option>
	    							{/if}
	    							<option {if $approval_state == 'Approved'}selected{/if} value="Approved">{t}Approved{/t}</option>
	    							<option {if $approval_state == 'Declined'}selected{/if} value="Declined">{t}Declined{/t}</option>
	    						</select>
	    					</td>
	    				</tr>
	    				{/if}
    				{/if}
    				<tr valign="top">
    					<td></td>
    					<td>
    						<br />
    						<input type="checkbox" name="isprivate" value="1" style="vertical-align:middle;"> {t}Visible to {/t}{if $smarty.session.uid == 0}{t}contributor{/t}{else}{t}Scalr Team{/t}{/if} {t}only{/t}
    					</td>
    				</tr>
    			</table>
    		</td>
    	</tr>
        {include file="inc/intable_footer.tpl" color="Gray"}
       	<input type="hidden" name="task" value="post_comment">
       	<input type="hidden" name="id" value="{$id}">
		{include file="inc/table_footer.tpl" button2=1 button2_name="Post new comment"}