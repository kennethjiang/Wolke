	var DataFormField = function(){
		this.initialize.apply(this, arguments);
	};
	
	DataFormField.prototype = {
		name:"",
		type:"text",
		required:false,
		defval:"",
		allow_multiple_choise:false,
		options: new Array(),
		
		initialize: function(name, type, required, defval, allow_multiple_choise, options) 
		{
			this.name = name;
			this.type = type;
			this.required = required;
			this.defval = defval;
			this.allow_multiple_choise = allow_multiple_choise;
			this.options = options;
	  	}
	};

	var DataForm = function(){
		this.initialize.apply(this, arguments);
	};
	
	DataForm.prototype = {
		fields: new Array(),
		container: null,
		count:0,
		
		initialize: function(container) 
		{
			this.container = container;			
	  	},
	  	
	  	Load: function(fields)
	  	{
	  		Ext.each(fields,function(item){
	  			window.df.AddField(item);
			});
	  	},
	  	
	  	AddField: function (DataFromField)
	  	{
	  		if (!this.fields[DataFromField.name])
	  		{
	  			this.fields[DataFromField.name] = DataFromField;
	  			this.count++;
	  			
	  			this.AddFieldToContainer(DataFromField);
	  			
	  			Ext.get('no_fields').dom.style.display = 'none';
	  			
	  			return true;
	  		}
	  		else
	  			alert("Field with same name already exists");
	  			
	  		return false;
	  	},
	  	
	  	AddFieldToContainer: function(DataFromField)
	  	{
	  		var color = (this.count % 2 == 0) ? 'background-color:#f0f0f0;' : '';
	  		
	  		var isreq = DataFromField.required ? 'true' : 'false';
	  		var content = '<tr id="field_container_'+DataFromField.name+'" style="'+color+'"><td></td>'+
 							'<td style="padding:2px;">'+DataFromField.name+'</td>'+
 							'<td style="padding:2px;">'+DataFromField.type+'</td>'+
 							'<td style="padding:2px;" width="1%" nowrap="nowrap" align="center"><img src="images/'+isreq+'.gif"></td>'+
 							'<td style="padding:2px;" width="1%" nowrap="nowrap" align="right"><img onClick="window.df.EditField(\''+DataFromField.name+'\');" style="cursor:pointer;" title="Edit" src="/images/edit.png">&nbsp;<img onClick="window.df.DeleteField(\''+DataFromField.name+'\');" title="Delete" style="cursor:pointer;" src="/images/del.gif"></td>'+
 						   '<td></td></tr>';
 						   
 			this.container.insertHtml('beforeEnd', content);
	  	},
	  	
	  	GetFieldByName: function(name)
	  	{
	  		return this.fields[name];
	  	},
	  	
	  	DeleteField: function(name)
	  	{
	  		this.fields[name] = null;
	  		this.count--;
	  		
	  		if (this.count == 0)
	  			Ext.get('no_fields').dom.style.display = '';
	  		
	  		var f = Ext.get('field_container_'+name).dom;
	  		f.parentNode.removeChild(f);
	  		
	  		ResetFieldForm();
	  	},
	  	
	  	EditField: function(name)
	  	{
	  		ResetFieldForm();
	  		
	  		Ext.get('fieldname').dom.disabled = true;
	  		
	  		Ext.get('field_buttons_add').dom.style.display = 'none';
	  		Ext.get('field_buttons_edit').dom.style.display = '';
	  		
	  		field = this.GetFieldByName(name);
	  		
	  		Ext.get('fieldname').dom.value = field.name;
	  		Ext.get('fieldtype').dom.value = field.type;
			var items = Ext.get('fieldtype').dom.options;
			for(var i = 0; i < items.length;i++)
			{
				if (items[i].value == field.type)
					items[i].selected = true;
			}
			
			Ext.get('fieldrequired').dom.checked = field.required;
			
			Ext.get('tab_contents_options').select('[name="fielddefval"]').each(function(item){
				item.dom.value = field.defval;
			});
			
			Ext.get('allow_multiplechoise').dom.checked = field.allow_multiple_choise;
					
			for (var k in field.options)
			{
				window.AddItem(field.options[k][0], field.options[k][1], field.options[k][3]);
			}		
			
			AllowMultipleChoice(Ext.get('allow_multiplechoise').dom.checked);			
			SetFieldForm();
	  	},
	  	
	  	UpdateField: function (DataFromField)
	  	{
	  		if (this.fields[DataFromField.name])
	  		{
	  			this.fields[DataFromField.name] = DataFromField;
	  			
	  			var field_cont = Ext.get('field_container_'+DataFromField.name);
	  			var cols = field_cont.select('td');
	  			cols.elements[1].innerHTML = DataFromField.name;
	  			cols.elements[2].innerHTML = DataFromField.type;
	  			
	  			var isreq = DataFromField.required ? 'true' : 'false';
	  			cols.elements[3].innerHTML = '<img src="images/'+isreq+'.gif">';
	  			
	  			return true;
	  		}
	  		else
	  			alert("Field with does not exists");
	  			
	  		return false;
	  	}
	};
	
	
	function CheckType(type)
	{
	    if (type == 'SELECT')
	    {
	        Ext.get('selectinfo').dom.style.display = '';
	    }
	    else
	    {
	        Ext.get('selectinfo').dom.style.display = 'none';
	    }
	}
	
	var Items = new Array();
	var Num = 0;
	
	function AddItem(item_key, item_name, item_isdef)
	{
	    if (!item_key)
	    	var item_key = Ext.get('ikey').dom.value;
	    	
	    if (!item_name)
	    	var item_name = Ext.get('iname').dom.value;
	    	
	    if (!item_isdef)
	    	var item_isdef = Ext.get('idef_add').dom.checked
	    	
	    
	    if (item_key == '' || item_name == '')
	        return "";
	    
	    var uniqid = parseInt((Math.random()*100000))+"."+parseInt((Math.random()*100000));
	    
	    var index = Items.length;
	    
	    Items[index] = [item_key, item_name, uniqid];
	    Num++;
	    
	    cont = document.createElement("DIV");
	    cont.style.width = '490px';
	    cont.style.clear = 'both';
	    cont.className = "select_item";
	       
	    
	    dv_key = document.createElement("DIV");
	    dv_key.className = 'item_key';
	    dv_key.innerHTML = item_key;
	    cont.appendChild(dv_key);
	    
	    dv_name = document.createElement("DIV");
	    dv_name.className = 'item_value';
	    dv_name.innerHTML = item_name;
	    cont.appendChild(dv_name);
	    
	    dv_def = document.createElement("DIV");
	    dv_def.className = 'item_def';
	    dv_def.align = 'center';
	    
	    var ischecked = item_isdef;
	    
	    if (ischecked)
	    {
	    	checked = 'checked';
	    	Ext.get('select_properties').select('.select_one').each(function(item){
				tmp = item.select('[name="idef"]')
				tmp.elements[0].checked = false;
			});
	    }
	    else
	    	checked = '';
	    
	    if (Ext.get('allow_multiplechoise').dom.checked)
	    {
	    	visible_one = 'none';
	    	visible_many = '';
	    }
	    else
	    {
	    	visible_one = '';
	    	visible_many = 'none';
	    }
	    
	    dv_def.innerHTML = ""+
	    		"<span class=\"select_one\" id=\"select_one_"+uniqid+"\" style=\"display:"+visible_one+";\">"+
	         	"	<input type=\"radio\" name=\"idef\" "+checked+" id=\"idef_rdo_"+index+"\" value=\"1\">"+
	         	"</span>"+
	         	"<span class=\"select_many\" id=\"select_many_"+uniqid+"\" style=\"display:"+visible_many+";\">"+
	         	"	<input type=\"checkbox\" name=\"idef\" "+checked+" id=\"idef_chk_"+index+"\" value=\"1\">"+
	         	"</span>"+
	    "";
	    
	    cont.appendChild(dv_def);
	    Ext.get('idef_add').dom.checked = false;
	    
	    img = document.createElement("IMG");
	    img.style.verticalAlign = 'middle';
	    img.src = "images/delete.png";
	    img.id = index;
	    
	    dv_img = document.createElement("DIV");
	    dv_img.className = 'item_delete';
	    dv_img.align = 'center';
	    dv_img.appendChild(img);
	    cont.appendChild(dv_img);
	    
	    img.onclick = function()
	    {
	         Num--;
	         Items[this.id] = false;
	         this.parentNode.parentNode.parentNode.removeChild(this.parentNode.parentNode);
	         
	         if (Num == 0)
	         {
	            Ext.get('no_items').dom.style.display = '';    
	         }
	    }
	    
	    Ext.get('Items').dom.appendChild(cont);
	    Ext.get('no_items').dom.style.display = 'none';
	    
	    Ext.get('iname').dom.value = "";
	    Ext.get('ikey').dom.value = "";
	    
	    Ext.get('idef_add').dom.checked = false;
	}
	
	function PrepareSubmit()
	{
	    var df_inp = document.createElement("INPUT");
	    df_inp.type = 'hidden';
	    df_inp.name = 'role_options_dataform';
	    
	    var fields = window.df.fields;
	    var submit_fields = new Array();
	    
	    for (k in fields)
	    {
	    	if (typeof(fields[k]) == 'object')
	    		submit_fields[submit_fields.length] = fields[k]; 
	    }
	    
	    df_inp.value = Ext.encode(submit_fields);
	    document.forms[1].appendChild(df_inp);
	    
	    Ext.get('button_js').dom.disabled = true;
	    
	    document.forms[1].submit();
	}
	
	function SetFieldForm()
	{
		var items = Ext.get('fieldtype').dom.options;
		for(var i = 0; i < items.length;i++)
		{
			if (Ext.get('fieldtype').dom.options[i].selected == true)
			{
				Ext.get(Ext.get('fieldtype').dom.options[i].value+'_properties').dom.style.display = '';
				if (Ext.get('fieldtype').dom.options[i].value == 'select')
				{
					Ext.get('list_options').dom.style.display = '';
				}
				else
				{
					Ext.get('list_options').dom.style.display = 'none';
				}
			}
			else
			{
				Ext.get(Ext.get('fieldtype').dom.options[i].value+'_properties').dom.style.display = 'none';
			}
		}
	}
	
	function ResetFieldForm()
	{
		Ext.get('fieldname').dom.value = "";
		Ext.get('fieldtype').dom.value = "text";
		Ext.get('fieldrequired').dom.checked = false;
		Ext.get('fieldname').dom.disabled = false;
		
		Ext.get('tab_contents_options').select('[name="fielddefval"]').each(function(item){
			item.dom.value = "";
		});
		Ext.get('allow_multiplechoise').dom.checked = false;
		
		Items = new Array();
		
		/*
		var items = Ext.get('Items').select('.select_item');
		for (var i = 0; i < items.length; i++)
		{
			Ext.get('Items').dom.removeChild(items[i]);
		}
		*/
		Ext.get('Items').dom.innerHTML = '<div id="no_items" align="center" style="display:;">No items defined</div>';
		
		Ext.get('no_items').dom.style.display = '';
		
		AllowMultipleChoice(false);
		
		SetFieldForm();
		
		Ext.get('field_buttons_add').dom.style.display = '';
		Ext.get('field_buttons_edit').dom.style.display = 'none';
	}
	
	function AllowMultipleChoice(value)
	{
		if (value == true)
		{
			Ext.get('list_options').select('.select_one').each(function(item){
				item.dom.style.display = 'none';
				
				if (item.select("[name='idef']").elements[0].checked == true)
					Ext.get(item.select("[name='idef']").elements[0].id.replace('rdo', 'chk')).dom.checked = true;
				else
					Ext.get(item.select("[name='idef']").elements[0].id.replace('rdo', 'chk')).dom.checked = false;
			});
			
			Ext.get('list_options').select('.select_many').each(function(item){
				item.dom.style.display = '';
			});
		}
		else
		{
			Ext.get('list_options').select('.select_one').each(function(item){
				item.dom.style.display = '';
				
				var chk = false;
			
				if (item.select("[name='idef']").elements[0].checked == true && !chk)
				{
					Ext.get(item.select("[name='idef']").elements[0].id.replace('chk', 'rdo')).dom.checked = true;
					chk = true;
				}
				else
					Ext.get(item.select("[name='idef']").elements[0].id.replace('rdo', 'chk')).dom.checked = false;
				
			});
					
			Ext.get('list_options').select('.select_many').each(function(item){
				item.dom.style.display = 'none';
			});
		}
	}
	
	function SetField()
	{
		name = Ext.get('fieldname').dom.value;
		type = Ext.get('fieldtype').dom.value;
		required = Ext.get('fieldrequired').dom.checked;
		
		if (name == '')
		{
			alert("Field name required");
			return;
		}
		
		if (name == 'all')
		{
			alert("Field name cannot be 'all'. Please select another name.");
			return;
		}
		
		if (type == 'text' || type == 'textarea')
		{
			tmp = Ext.get(type+'_properties').select('[name=fielddefval]');
			def_val = tmp.elements[0].value; 
		}
		else
			def_val = false;
			
		allow_multiplechoise = Ext.get('allow_multiplechoise').dom.checked;
			
		if (type == 'select')
		{
			options = Items;
			Ext.each(options,function(item){
			
				if (!item || !item[2])
					return;
				
				if (allow_multiplechoise)
					var container = Ext.get('select_many_'+item[2]);
				else
					var container = Ext.get('select_one_'+item[2]);
				
				var isdef = container.select('[name=idef]').elements[0].checked;
				
				item[3] = isdef; 			
			});
			
			if (options.length == 0)
			{
				alert("For select field you should add at least one option");
				return;
			}			
		}
		else
			options = null;
		
		var field = new DataFormField(name, type, required, def_val, allow_multiplechoise, options);
		
		var result = false;
		if (window.df_action == 'edit')
		{
			result = window.df.UpdateField(field);
			
		}
		else if (window.df_action == 'create')
		{
			result = window.df.AddField(field);
		}
		
		if (result)
		{
			ResetFieldForm();
		}
	}