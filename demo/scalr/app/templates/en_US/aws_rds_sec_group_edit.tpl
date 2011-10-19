{include file="inc/header.tpl"}
    <script language="Javascript">
    
    {literal}
    
    function AddRule(no_alerts)
    {        
        var type = Ext.get('add_protocol').dom.value;
        
        if (type != 'user')
        {
	        if (Ext.get('add_ipranges').dom.value == "")
	        {
	            if (!no_alerts)
	            	alert("'IP ranges' required");
	            	
	            return false;
	        }
	    }
	    else
	    {
	    	if (Ext.get('add_portfrom').dom.value.length != 12)
	    	{
	    		alert("User ID must be 12 digits length");
	    		return false;
	    	}
	    }
        
        container = document.createElement("TR");
        container.id = "rule_"+parseInt(Math.random()*100000);
        
        //first row
        td1 = document.createElement("TD");
        
        if (type != 'user')
        {
        	td1.innerHTML = 'IP range';
        }
        else
        {
        	td1.innerHTML = 'User &amp; Group';
        }
        
        container.appendChild(td1);
        
                
        //forth row
       	td4 = document.createElement("TD");
        	
        if (type != 'user')
        {
        	td4.innerHTML = Ext.get('add_ipranges').dom.value;
        	td4.colSpan = '3';

        	rule = Ext.get('add_protocol').dom.value+':'+Ext.get('add_ipranges').dom.value;
        }
        else
        {
        	//second row
            td2 = document.createElement("TD");
            td2.innerHTML = Ext.get('add_portfrom').dom.value;
            container.appendChild(td2);
            
            //third row
            td3 = document.createElement("TD");
            td3.innerHTML = Ext.get('add_portto').dom.value;
            container.appendChild(td3);

            rule = Ext.get('add_protocol').dom.value+':'+Ext.get('add_portfrom').dom.value+":"+Ext.get('add_portto').dom.value;
        }

        
        container.appendChild(td4);

        tdX = document.createElement("TD");
        tdX.innerHTML = "new";
        container.appendChild(tdX);
        
        // fifth row
        td5 = document.createElement("TD");
        td5.innerHTML = "<input type='button' class='btn' name='dleterule' value='Delete' onclick='DeleteRule(\""+container.id+"\")'>";
        container.appendChild(td5);
        
        if (type != 'user')
        	Ext.get('rules_container').dom.appendChild(container);
        else
        {
        	Ext.get('ug_rules').dom.style.display = '';
        	Ext.get('ug_rules').dom.appendChild(container);
        }
        
        var rule_input = document.createElement('INPUT');
        rule_input.type = 'hidden';
        rule_input.name = 'rules[]';
        rule_input.id = container.id+"_input";
        rule_input.value = rule;
        
        document.forms[2].appendChild(rule_input);
        
        Ext.get('add_portfrom').dom.value = "";
        Ext.get('add_portto').dom.value = "";
        Ext.get('add_ipranges').dom.value = "0.0.0.0/0";
        Ext.get('add_protocol').dom.value = 'iprange';

        SetType('iprange');
    }
    
    function DeleteRule(id)
    {
        try
        {	
        	Ext.get(id+"_input").dom.parentNode.removeChild(Ext.get(id+"_input").dom);
        }
        catch(e){}	
        
        try
        {
        	Ext.get(id).dom.parentNode.removeChild(Ext.get(id).dom);
        }
        catch(e){}
    }
    
    function SaveRules()
    {
    	//AddRule(true);
		var elems = Ext.get('footer_button_table').select('.btn');
		elems.each(function(item){
			item.disabled = true;
		});
		
		Ext.get('btn_hidden_field').dom.name = this.name;
		Ext.get('btn_hidden_field').dom.value = this.value;
		
		document.forms[2].submit();
    }
    
    function SetType(type)
    {
    	if (type == 'user')
    	{
    		Ext.get('add_ipranges').dom.style.display = 'none';
    		Ext.get('add_portfrom').dom.style.display = '';
    		Ext.get('add_portto').dom.style.display = '';
    	}
    	else
    	{
    		Ext.get('add_ipranges').dom.style.display = '';
    		Ext.get('add_portfrom').dom.style.display = 'none';
    		Ext.get('add_portto').dom.style.display = 'none';
    	}
    }
    {/literal}
    </script>
	{include file="inc/table_header.tpl"}     
		{if $add}
	 	{include file="inc/intable_header.tpl" header="General" intable_first_column_width="15%" color="Gray"}
    	<tr>
    		<td>Name:</td>
    		<td><input type="text" name="name" value="" class="text" /></td>
    	</tr>
    	<tr valign="top">
    		<td>Description:</td>
    		<td>
    			<textarea rows="4" name="description" class="text" cols="20"></textarea>
    		</td>
    	</tr>
    	{include file="inc/intable_footer.tpl" color="Gray"}
	  	{else}
	  		<input type="hidden" name="name" value="{$group_name}">
	  	{/if}
        {include file="inc/intable_header.tpl" header="Security group rules" color="Gray"}
    	<tr>
    		<td colspan="2">
    		  <table cellpadding="5" cellspacing="15" width="700" border="0">
    		      <thead>
    		      <tr>
    		      	  <th width="10%" style="font-weight:bold;">Type</th>
    		          <th width="" colspan="3" style="font-weight:bold;">IP Ranges</th>
    		          <th width="10%" style="font-weight:bold;">Status</th>
    		          <th width="10%"></th>
    		      </tr>
    		      </thead>
    		      <tbody id="rules_container">
    		      {foreach from=$rules item=rule}
    		      <tr id="{$rule->id}">
    		      	  <td>IP range</td>
    		          <td colspan="3">{$rule->ip}</td>
    		          <td>{$rule->status}</td>
    		          <td><input type='button' {if $rule->status == 'revoking' || $rule->status == 'authorizing'}disabled{/if} class='btn' name='dleterule' value='Delete' onclick='DeleteRule("{$rule->id}")'><input type='hidden' name='rules[]' value='{$rule->rule}'></td>
    		      </tr>
    		      {/foreach}
    		      </tbody>
    		      <tbody id="ug_rules" style="display:{if $ug_rules|@count == 0}none{/if};">
    		      	<tr>
    		      	  <th width="20%" style="font-weight:bold;">Type</th>
    		          <th width="30%" style="font-weight:bold;">UserID</th>
    		          <th width="30%" style="font-weight:bold;">Group</th>
    		          <th width="10%" style="font-weight:bold;"></th>
    		          <th width="10%" style="font-weight:bold;">Status</th>
    		          <th width="10%"></th>
    		        </tr>
    		        {foreach from=$ug_rules item=rule}
	    		      <tr id="{$rule->id}">
	    		          <td>User &amp; Group</td>
	    		          <td>{$rule->userId}</td>
	    		          <td>{$rule->groupname}</td>
	    		          <td></td>
	    		          <td>{$rule->status}</td>
	    		          <td><input type='button' {if $rule->status == 'revoking' || $rule->status == 'authorizing'}disabled{/if} class='btn' name='dleterule' value='Delete' onclick='DeleteRule("{$rule->id}")'><input type='hidden' id="{$rule->id}_input" name='rules[]' value='{$rule->rule}'></td>
	    		      </tr>
	    		      {/foreach}
    		      </tbody>
    		      <tr>
    		      	<td colspan="10">&nbsp;</td>
    		      </tr>
    		      <tr>
    		          <td><select class="text" name="add_protocol" id="add_protocol" onchange="SetType(this.value);">
    		                  <option value="iprange">IP range</option>
    		                  <option value="user">User and Group</option>
    		              </select>
    		          </td>
    		          <td colspan="3">
    		          	  <input type="text" class="text" size="15" id="add_ipranges" name="add_ipranges" value="0.0.0.0/0">
    		              <input type="text" class="text" size="15" id="add_portfrom" name="add_portfrom" value=""> 
    		              <input type="text" class="text" size="15" id="add_portto" name="add_portto" value="">
    		          </td>
    		          <td></td>
    		          <td>
    		              <input type="button" class="btn" onclick="AddRule()" value="Add">
    		          </td>
    		      </tr>
    		  </table>
       		</td>
    	</tr>
        {include file="inc/intable_footer.tpl" color="Gray"}
	{include file="inc/table_footer.tpl" button_js=1 button_js_name="Save" show_js_button=1 button_js_action="SaveRules();"}
	<script language="Javascript">
		SetType('iprange');
	</script>
	<input type="hidden" name="region" value="{$region}"/>
	<input type="hidden" name="step" value="2"/>
{include file="inc/footer.tpl"}