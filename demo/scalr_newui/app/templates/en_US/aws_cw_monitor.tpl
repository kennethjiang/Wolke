{include file="inc/header.tpl"}
<script language="Javascript" src="/js/extjs-3.2.1/ext-alone-charts.js"></script>
<script language="Javascript" src="/js/class.AWSCWMonitor.js"></script>
<div style="margin-top:10px;">
	{section name=id loop=$metrics}
	<div style="float:left;margin-right:10px;margin-bottom:10px;">
		<div id='{$metrics[id]}_chart'></div>
	</div>
	{/section}
</div>
<div style="clear:both;"></div>
<br />
<script>

Ext.chart.Chart.CHART_URL = '/js/extjs-3.2.1/resources/charts.swf';

/*!
 * Ext JS Library 3.0+
 * Copyright(c) 2006-2009 Ext JS, LLC
 * licensing@extjs.com
 * http://www.extjs.com/license
 */

 	var graphs = [
	    {section name=id loop=$metrics}
	    	{assign var="metric" value=$metrics[id]}
	    	['{$metrics[id]}', '{$units.$metric}']{if !$smarty.section.id.last},{/if} 
		{/section}
	];
	
	var monitors = new Array();
	var panels = new Array();

	var NameSpace = '{$NameSpace}';
	var DType = '{$Object}';
	var DValue = '{$ObjectId}';

	
	{literal}
Ext.onReady(function(){
	
	for(i = 0; i < graphs.length; i++)
	{
		monitors[graphs[i][0]] = new AWSCWMonitor(graphs[i][0], '1hour', 'Average', NameSpace, DType, DValue);  
		panels[i] = new Ext.Panel({
	        width: 700,
	        height: 400,
	        renderTo: graphs[i][0]+'_chart',
	        title: graphs[i][0],
	        tbar: ['&nbsp;&nbsp; Time Range:', new Ext.form.ComboBox({
				allowBlank: false,
				editable: false,
				id: 'cb_time_'+graphs[i][0], 
		        store: [['1hour', '1 Hour'], ['1day', '1 Day'], ['1week', '1 Week'], ['1month', '1 Month'], ['1year', '1 Year']],
		        value: '1hour',
		        displayField:'state',
		        typeAhead: false,
		        monitorType:graphs[i][0],
		        monitor: monitors[graphs[i][0]],
		        mode: 'local',
		        triggerAction: 'all',
		        selectOnFocus:false,
		        width:100,
		        listeners:{select:function(combo, record, index){
		        	combo.monitor.setTimeRange(combo.getValue());
		        }}
		    }),'&nbsp;&nbsp;Type:', new Ext.form.ComboBox({
				allowBlank: false,
				editable: false, 
		        store: [['Average', 'Average'], ['Minimum', 'Minimum'], ['Maximum', 'Maximum'], ['Sum', 'Sum']],
		        value: 'Average',
		        displayField:'state',
		        typeAhead: false,
		        mode: 'local',
		        monitorType:graphs[i][0],
		        monitor: monitors[graphs[i][0]],
		        triggerAction: 'all',
		        selectOnFocus:false,
		        width:100,
		        listeners:{select:function(combo, record, index){
		    		combo.monitor.setType(combo.getValue());
		        }}
		    }),'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;','-',
	        {
	            text: 'Refresh',
	            monitorType:graphs[i][0],
	            monitor: monitors[graphs[i][0]],
	            handler: function(menuItem){
		    		menuItem.monitor.load();
	            }
	        }],
	        items: {
	    		xtype: 'linechart',
	            store: monitors[graphs[i][0]].store,
	            yField: 'value',
	            xField: 'time',
	            
	            xAxis: new Ext.chart.CategoryAxis({
	                title: 'Time'
	            }),
	            yAxis: new Ext.chart.NumericAxis({
	            	displayName: graphs[i][1],
	            	title: graphs[i][1],
	            	snapToUnits: true
	            }),
	            extraStyle: {
	               xAxis: {
	                    labelRotation: -90
	                }
	            }
	        }
	    });

	    monitors[graphs[i][0]].load();
	}
});


</script>
{/literal}
{include file="inc/footer.tpl"}