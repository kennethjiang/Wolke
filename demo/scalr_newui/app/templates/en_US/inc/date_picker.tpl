<script type="text/javascript" language="Javascript">
{literal}
	function SetFilterType(type)
	{
		Ext.get('filter_E').dom.style.display = "none";
		Ext.get('filter_Q').dom.style.display = "none";
		
		Ext.get('filter_'+type).dom.style.display = "";
	}
{/literal}
</script>
<div style="padding:4px;margin-left:25px;height:30px;vertical-align:middle;line-height:30px;">
	<select name="date_type" style="vertical-align:middle;" class="text" onChange="SetFilterType(this.value);">
		<option {if $date_type == 'Q'}selected{/if} value="Q">{t}Quick Date Range:{/t}</option>
		<option {if $date_type == 'E'}selected{/if} value="E">{t}Exact Date Range:{/t}</option>
	</select>
	
	<span id="filter_Q" style="display:{if $date_type && $date_type != 'Q'}none{/if};">
		<select name="quick_date" style="vertical-align:middle;" class="text">
			<option {if $quick_date == 'today'}selected{/if} value="today">{t}Today{/t}</option>
			<option {if $quick_date == 'yesterday'}selected{/if} value="yesterday">{t}Yesterday{/t}</option>
			<option {if $quick_date == 'last7days'}selected{/if} value="last7days">{t}Last 7 days{/t}</option>
			<option {if $quick_date == 'lastweek'}selected{/if} value="lastweek">{t}Last week (Mon-Sun){/t}</option>
			<option {if $quick_date == 'lastbusinessweek'}selected{/if} value="lastbusinessweek">{t}Last business week (Mon-Fri){/t}</option>
			<option {if $quick_date == 'thismonth'}selected{/if} value="thismonth">{t}This month{/t}</option>
			<option {if $quick_date == 'lastmonth'}selected{/if} value="lastmonth">{t}Last month{/t}</option>
		</select>
	</span>
	<span id="filter_E" style="display:{if !$date_type || $date_type != 'E'}none{/if};">
		<div style="display:inline;">
			<input name="dt" style="vertical-align:middle;width:100px;" type="text" class="text" id="dt" value="{$dt}">
			<input name="reset" style="margin-left:-7px;height:22px;vertical-align:middle;" type="reset" class="btn" onclick="return showCalendar('dt', 'mm/dd/y');" value=" ... ">
		</div>
		<div style="display:inline;padding-left:15px;">
			<input style="vertical-align:middle;width:100px;" name="dt2" type="text" class="text" id="dt2" value="{$dt2}">
			<input name="reset" style="margin-left:-7px;height:22px;vertical-align:middle;" type="reset" class="btn" onclick="return showCalendar('dt2', 'mm/dd/y');" value=" ... ">
		</div>
	</span>	
	<input style="vertical-align:middle;" type="submit" name="dfilter" value="Filter" class="btn{if $dfilter}i{else}{/if}" />
</div>