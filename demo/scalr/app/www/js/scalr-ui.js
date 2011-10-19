  	
	String.prototype.endsWith = function(pattern) {
  		var d = this.length - pattern.length;
  		return d >= 0 && this.lastIndexOf(pattern) === d;
  	};

	Ext.apply(Ext.Ajax.defaultHeaders = {"X-Requested-With": "XMLHttpRequest"});

/* // replaced with function in ui-ng/viewers.js
Ext.Ajax.on("requestexception", function (conn, response, options) {
		
		var msg = new Array();
		msg[0] = response.responseText; 
		
		ShowError(msg);
	});*/
	
	function ShowError(msg)
	{
		var ok_obj = Ext.get('Webta_OkMsg');
		if(ok_obj)			
			ok_obj.dom.style.display = 'none';
		
		var err_obj = Ext.get('Webta_ErrMsg').dom;
		
		err_obj.innerHTML = "";
		var i = 0;
		err_obj.innerHTML = err_obj.innerHTML += "<table style='margin-top:0px;' width='100%' cellpadding='5' cellspacing='1' bgcolor=''><tr><td bgcolor=''><span style='color: #CB3216'>";
		for (i; i<msg.length;i++)
		{
			err_obj.innerHTML = err_obj.innerHTML += "&bull;&nbsp;&nbsp;".concat(msg[i]);
			err_obj.innerHTML = err_obj.innerHTML += "<br>";
		}
		err_obj.innerHTML = err_obj.innerHTML += "</span></td></tr></table>";
		
		err_obj.style.display = '';
	}
	
	function ShowOk(msg)
	{
		var err_obj = Ext.get('Webta_ErrMsg');
		if(err_obj)			
			err_obj.dom.style.display = 'none';
		
		var ok_obj = Ext.get('Webta_OkMsg').dom;
		ok_obj.innerHTML = msg;
		ok_obj.style.display = '';	
		
	}
	
	
	function SendRequestWithConfirmation(RequestObject, ConfirmText, ProgressText, IconClass, OnError, OnSuccess, URL)
	{
		var REQ_URL = '/server/ajax-ui-server.php';
		if (URL)
				REQ_URL = URL;
		
		Ext.MessageBox.show({
			title:'Confirm',
			msg: ConfirmText,
			icon: 'ext-mb-info',
			buttons: Ext.Msg.YESNOCANCEL,
			fn: function(btn){
				
			if (arguments[0] != 'yes')
				return;

			RequestObject.r = Math.random();

			Ext.MessageBox.show({
			    msg: ProgressText,
			    progressText: 'Processing...',
			    width:450,
			    wait:true,
			    waitConfig: {interval:200},
			    icon:IconClass, //'ext-mb-info', //custom class in msg-box.html
			    animEl: 'mb7'
			});

			if (window.TID)
				RequestObject.decrease_mininstances_setting = 1;
			
			if (window.TIF)
				RequestObject.force_terminate = 1;
			
			//Ext.get('Webta_ErrMsg').dom.style.display = 'none';

			Ext.Ajax.request({
			   url: REQ_URL,
			   success: function(response,options){
			   	
			   		eval('var result = '+response.responseText+';');
			   		if (result.result == 'ok')
			   		{
						Ext.MessageBox.hide();
						
						if (typeof(OnSuccess) == 'function')	
							OnSuccess();
			   		}
			   		else
			   		{
			   			Ext.MessageBox.hide();
					   	//var err_obj = Ext.get('Webta_ErrMsg').dom;
						//err_obj.innerHTML = result.msg;
						//err_obj.style.display = '';
						
						if (typeof(OnError) == 'function')	
							OnError();
			   		}
					
			   },
			   failure: function(response,options) {
				   	Ext.MessageBox.hide();
				   	Scalr.Viewers.ErrorMessage('Cannot proceed your request at the moment. Please try again later.');
					
					if (typeof(OnError) == 'function')	
						OnError();
			   },
			   params: Ext.urlEncode(RequestObject)
			});
		}});
	}

	