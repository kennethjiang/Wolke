{include file="inc/header.tpl"}	
	<script language="javasript" type="text/javascript">

		var Measures = {$measures};
		var Dimensions = {$dimensions};
		var DimensionValues = {$dimension_values}
		
		{literal}
		function SetNamespace(namespace)
		{
			var el = Ext.get('Measure').dom; 
			while(el.firstChild) { 
				el.removeChild(el.firstChild); 
			}
														
			for (var i = 0; i < Measures[namespace].length; i ++)
			{
				var opt = document.createElement("OPTION");
				opt.value = Measures[namespace][i];
				opt.innerHTML = Measures[namespace][i];
				Ext.get('Measure').dom.appendChild(opt); 
			}

			SetMeasure(namespace, Ext.get('Measure').dom.value);
		}

		function SetMeasure(namespace, measure)
		{
			var el = Ext.get('DimensionType').dom; 
			while(el.firstChild) { 
				el.removeChild(el.firstChild); 
			}

			for (var i = 0; i < Dimensions[namespace+":"+measure].length; i ++)
			{
				var opt = document.createElement("OPTION");
				opt.value = Dimensions[namespace+":"+measure][i];
				opt.innerHTML = Dimensions[namespace+":"+measure][i];
				Ext.get('DimensionType').dom.appendChild(opt); 
			}

			SetDimensionType(namespace, measure, Ext.get('DimensionType').dom.value);
		}

		function SetDimensionType(namespace, measure, dimension_type)
		{
			var el = Ext.get('DimensionValue').dom; 
			while(el.firstChild) { 
				el.removeChild(el.firstChild); 
			}

			for (var i = 0; i < DimensionValues[namespace+":"+measure+":"+dimension_type].length; i ++)
			{
				var opt = document.createElement("OPTION");
				opt.value = DimensionValues[namespace+":"+measure+":"+dimension_type][i];
				opt.innerHTML = DimensionValues[namespace+":"+measure+":"+dimension_type][i];
				Ext.get('DimensionValue').dom.appendChild(opt); 
			}
		}

		Ext.onReady(function(){
			SetNamespace(Ext.get('Namespace').dom.value);
		});
		{/literal}
	</script>
	{include file="inc/table_header.tpl"}
    	{include file="inc/intable_header.tpl" intable_first_column_width="15%" header="Request information" color="Gray"}
    	<tr>
    		<td>Namespace:</td>
    		<td>
    			<select name="Namespace" id="Namespace" class="text" onChange="SetNamespace(this.value);">
    				{foreach from=$namespaces key=key item=item}
    				<option value="{$item}">{$item}</option>
    				{/foreach}
    			</select>
    		</td>
    	</tr>
    	<tr>
    		<td>Metric:</td>
    		<td>
    			<select name="Measure" id="Measure" class="text" onChange="SetMeasure(Ext.get('Namespace').dom.value, this.value);">
    				
    			</select>
    		</td>
    	</tr>
    	<tr>
    		<td>Dimension:</td>
    		<td>
    			<select name="DimensionType" id="DimensionType" class="text" onChange="SetDimensionType(Ext.get('Namespace').dom.value, Ext.get('Measure').dom.value, this.value);">
    				
    			</select>
    			
    			<select name="DimensionValue" id="DimensionValue" class="text">
    				
    			</select>
    		</td>
    	</tr>
    	{include file="inc/intable_footer.tpl" color="Gray"}
	{include file="inc/table_footer.tpl" button2=1 button2_name="Continue" cancel_btn=1}
{include file="inc/footer.tpl"}