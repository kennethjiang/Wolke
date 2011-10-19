var AWSCWMonitor = function(){
	this.initialize.apply(this, arguments);
};

AWSCWMonitor.prototype = {
	initialize:function(metric_name, time, type, NameSpace, DType, DValue)
	{
		this.props = new Array();
		
		this.props['MetricName'] = '';
		this.props['StartTime'] = '';
		this.props['EndTime'] = '';
		this.props['Type'] = 'Average';
		this.props['Period'] = 60;
		this.props['NameSpace'] = NameSpace;
		this.props['DType'] = DType;
		this.props['DValue'] = DValue;
		this.props['DateFormat'] = 'H:i';
		
		this.setMetricName(metric_name);
		
		this.setTimeRange(time, true);
		this.setType(type, true);
		
		this.store = new Ext.ux.scalr.Store({
			reader: new Ext.ux.scalr.JsonReader({
				root: 'data',
				successProperty: 'success',
				errorProperty: 'error',
				totalProperty: 'total',
				id: 'time',
					
				fields: [
					'time', 'value'
				]
			}),
			url: '/',
			listeners: { dataexception: Ext.ux.dataExceptionReporter }
		});
	},
	
	setMetricName:function(metric)
	{
		this.props['MetricName'] = metric;
	},
	
	load:function()
	{
		this.buildURL();
		this.store.load();
	},
	
	buildURL:function()
	{
		var url = "/server/ajax-ui-server-aws-cw.php?a=1&action=GetMetric";
		for (k in this.props)
		{
			if (typeof(this.props[k]) != 'function')
				url +="&"+k+"="+encodeURIComponent(this.props[k]);
		}
		
		this.store.proxy.setUrl(url);
	},
	
	setType:function(type, noload)
	{
		this.props['Type'] = type;
		
		if (!noload)
			this.load();
	},
	
	setTimeRange:function(time_range, noload)
	{
		var current_date = new Date();
		
		switch(time_range)
		{
			case "1hour":
				
				this.props['Period'] = 60;
				
				this.props['DateFormat'] = 'H:i';

				var d = new Date(
					current_date.getFullYear(),
					current_date.getMonth(),
					current_date.getDate(),
					(current_date.getHours()-1),
					current_date.getMinutes()
				); 
				
				this.props['StartTime'] = d.toString();
				this.props['EndTime'] = current_date.toString();
				
				break;
				
			case "1day":
				
				this.props['Period'] = 3600;
				
				this.props['DateFormat'] = 'D d, H:i';
				
				var d = new Date(
						current_date.getFullYear(),
						current_date.getMonth(),
						current_date.getDate()-1,
						current_date.getHours(),
						current_date.getMinutes()
					); 
					
				this.props['StartTime'] = d.toString();
				this.props['EndTime'] = current_date.toString();
				
				break;
				
			case "1week":
				
				this.props['Period'] = 21600;
				
				this.props['DateFormat'] = 'D d, H:i';
				
				var d = new Date(
						current_date.getFullYear(),
						current_date.getMonth(),
						current_date.getDate()-7,
						current_date.getHours(),
						current_date.getMinutes()
					); 
					
				this.props['StartTime'] = d.toString;
				this.props['EndTime'] = current_date.toString;
				
				break;
				
			case "1month":
				
				this.props['Period'] = 86400;
				
				this.props['DateFormat'] = 'D, d M';
				
				var d = new Date(
						current_date.getFullYear(),
						current_date.getMonth()-1,
						current_date.getDate(),
						current_date.getHours(),
						current_date.getMinutes()
					); 
					
				this.props['StartTime'] = d.toString;   	   				
				this.props['EndTime'] = current_date.toString;
				
				break;
				
			case "1year":
				
				this.props['Period'] = 864000;
				
				this.props['DateFormat'] = 'd M Y';
				
				var d = new Date(
						current_date.getFullYear()-1,
						current_date.getMonth(),
						current_date.getDate(),
						current_date.getHours(),
						current_date.getMinutes()
					); 
					
				this.props['StartTime'] = d.toString;   	   				
				this.props['EndTime'] = current_date.toString;
				
				break;
		}
		
		if (!noload)
			this.load();
	}
};
