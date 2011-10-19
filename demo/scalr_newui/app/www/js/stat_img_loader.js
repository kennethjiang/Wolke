	function LoadStatsImage(farmid, watchername, type, role, hash)
	{		
		var url = '/server/statistics_proxy.php?version='+MONITORING_VERSION+'&task=get_stats_image_url&farmid='+farmid+'&watchername='+watchername+'&graph_type='+type+'&role='+role; 
		
		Ext.Ajax.request({
    		url:url,
    		method:'GET',
    		hash: hash,
    		success:function(transport, options)
    		{
				var hash = options.hash;
				
				try
				{
					eval('var response = '+transport.responseText);
					if (response.type == 'ok')
					{
						var image1 = new Image();
						var suffix = new Date();
						image1.src = response.msg+"?"+suffix.getTime();
						image1.onload = function()
						{
							Ext.get('image_'+hash).dom.src = this.src;
							Ext.get('loader_'+hash).dom.style.display = 'none';
							Ext.get('image_div_'+hash).dom.style.display = '';
						}
						
						/*
						image1.onerror = function()
						{
							Ext.get('loader_content_'+hash).dom.innerHTML = '<img src="/images/cross_circle.png"> Graphic image is not available';
						}
						*/
						
					}
					else
					{
						Ext.get('loader_content_'+hash).dom.innerHTML = '<img src="/images/cross_circle.png"> '+response.msg;
					}
				}
				catch(e)
				{
					Ext.get('loader_content_'+hash).dom.innerHTML = '<img src="/images/cross_circle.png"> '+e.message;
				}	
    		}
    	});
	}