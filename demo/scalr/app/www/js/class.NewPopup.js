
var NewPopup = function(){
	this.initialize.apply(this, arguments);
};

NewPopup.prototype = {
	options: {},
	tap: null,
	content:null,
	showTap:false,
	tap_class_name:'pop_tap',
	
	initialize: function(popup) {
		var popup = this.byID(popup);
		var options = this.extend({
			target: null,
			width:	370,
			height: 150,
			popup: popup,
			selecters: null
		}, arguments[1] || {});
				
		this.options = options;
		this.setup();
	},
	
	positioningPopup: function(off) {
		var scrOffset = this.scrollOffset();
		var popup = this.options.popup;
		var TAP_HEIGHT = 100;
		var Y_OFFSET = 23;
		
		if (this.options.target)
			var targetOffset = this.cumulativeOffset(this.options.target);
		else
			var targetOffset = off;
			
		this.tap_class_name = "pop_tap";
		
		/* Calculate LEFT position */
		off[0] = off[0]-this.options.width/2;
		if (off[0] < 0)
		{
			off[0] = 5;
		}
		else if (off[0] > document.body.offsetWidth)
		{
			off[0] = document.body.offsetWidth-this.options.width-5;
		}
			
		/* Calculate TOP position */
		off[1] = off[1]-this.options.height;
		if (off[1] < 0)
		{
			off[1] = 5;
		}
						
		/* Calculate TAP position & TAP type */
		if (off[1]-this.options.height < 0)
		{
			this.tap_class_name = "pop_bat";
			off[1] = off[1]+this.options.height+TAP_HEIGHT-Y_OFFSET-10;
			var tapMarginTop = -1*(this.options.height+TAP_HEIGHT+Y_OFFSET); 
		}
		else
		{
			off[1] = off[1]-TAP_HEIGHT-Y_OFFSET;
			var tapMarginTop = -27;
		}
			
		Ext.get(this.tapid).dom.className = this.tap_class_name;
		Ext.get(this.tapid).dom.style.marginTop = tapMarginTop + 'px'; 
	
		popup.style.left = off[0]+70 + 'px';
		popup.style.top = off[1] + 'px';
	},
	
	ieTweak: function(command)
	{
		Ext.select(this.options.selecters).each(
			function (Item)
			{
				if ((typeof (Item[1]) == "object"))
				{
					try {
						Item[1].style.visibility = command;
					} catch (err) {}
				}
			}
		);
	},
	
	raisePopup: function(Offsets)
	{
		this.ieTweak('hidden');
		
		this.positioningPopup(Offsets);
		this.selecters('hidden');
		this.show(this.options.popup);
	},
	
	setup: function() {
		var options	= this.options;
		var popup	= this.options.popup;
		var content = popup.innerHTML;
		
		if (this.options.target != '')
		{
			var target	= this.byID(this.options.target);
			target.callerObj = this;
			target.onclick = function(event) {
				var event = event || window.event;
				var Offsets	= [event.clientX, event.clientY];
				this.callerObj.positioningPopup(Offsets);
				this.callerObj.selecters('hidden');
				this.callerObj.show(this.callerObj.options.popup);
				
				this.ieTweak('hidden');
			};
		}
		
		//alert(target.offsetTop);
//		target.style.border 	= '1px solid red';
		popup.innerHTML 		= '';
		popup.style.position	= 'absolute';
		popup.style.width 		= options.width + 50 + 'px';
		popup.style.left 		= 0 + 'px';
		popup.style.top			= 0 + 'px'; //TOffset[1] - PDims.height + 'px';
		popup.style.zIndex 		= 99999;
		
		var div1 = this.div("pop_popup pop_header");
		div1.style.left = options.width - 15 + "px";
		div1.style.width = "15px";
		var div2 = this.div("pop_sizex");
		div2.style.width = "15px";
				
		if (!NoCloseButton)
		{
			var img  = new Image();
				img.src = "/images/popup/close.gif";
				img.border = 0;
				img.callerObj = this;
				img.onclick = function() {
					this.callerObj.hide(this.callerObj.options.popup);
					//this.callerObj.hide(this.callerObj.tap);
					this.callerObj.selecters('visible');
					this.callerObj.ieTweak('visible');
				};
				
			div2.appendChild(img);
		}
		
		div1.appendChild(div2);
		
		var div3 = this.div("pop_popup pop_content");
		var div4 = this.div("pop_sizex pop_sizey pop_content_container", 1, 1, 1, 10, 15);
			div4.innerHTML = content;
		div3.appendChild(div4);
		
		this.content = div4;
		
		var div5 = this.div("pop_popup");
		var div7 = this.div("pop_nw pop_left");
		var div8 = this.div("pop_n pop_left pop_sizex", 1);
		var div9 = this.div("pop_ne pop_left");
		var div10 = this.div("pop_clear");
		var div11 = this.div("pop_w pop_left pop_sizey", 0, 1);
		var div12 = this.div("pop_left pop_sizey pop_sizex", 1, 1);
		var div13 = this.div("pop_e pop_left pop_sizey", 0, 1);
		var div14 = this.div("pop_clear");
		var div15 = this.div("pop_sw pop_left");
		var div16 = this.div("pop_s pop_left pop_sizex", 1);
		var div17 = this.div("pop_se pop_left");
		var div18 = this.div("pop_clear");
		var div19 = this.div(this.tap_class_name);
				
		this.tap  = div19;
				
		div5.appendChild(div7);
		div5.appendChild(div8);
		div5.appendChild(div9);
		div5.appendChild(div10);
		div5.appendChild(div11);
		div5.appendChild(div12);
		div5.appendChild(div13);
		div5.appendChild(div14);
		div5.appendChild(div15);
		div5.appendChild(div16);
		div5.appendChild(div17);
		div5.appendChild(div18);
		div5.appendChild(div19);
		
		popup.appendChild(div1);
		popup.appendChild(div3);
		popup.appendChild(div5);
		
		this.tapid  = div19.id;
		
		this.hide(popup);
	},
	
	div: function(className, setSizeX, setSizeY, disable_overflow, w_offset, h_offset) {
		var div = document.createElement("DIV");
		
		if (disable_overflow)
		{
			//div.style.overflow = 'hidden';
		}
			
		div.id = "popup_"+Math.random();
		if (className) div.className = className;

		if (!w_offset)
			w_offset = 0;

		if (!h_offset)
			h_offset = 0;

		if (setSizeX)	div.style.width = this.options.width + w_offset + 'px';
		if (setSizeY)	div.style.height = this.options.height + h_offset + 'px';
		
		return div;
	},
	
	hide: function(element) {
		var element = this.byID(element);
		try {
			element.style.display = 'none';
		} catch (err) {}
	},
	
	show: function(element) {
		var element = this.byID(element);

		try {
			element.style.display = '';
		} catch (err) {}
	},
	
	selecters: function(command)
	{
		if (this.options.target != '')
		{
			var frm = this.findElement(this.options.target, "form");
			if (!frm) return false;
			
			var selecters = frm.getElementsByTagName("select");
			for(var i=0; i < selecters.length; i++) {
				if ((typeof (selecters[i]) == "object"))
				{
					try {
						selecters[i].style.visibility = command;
					} catch (err) {}
				}
			}
		}
		else
			return false;
	},
		
	findElement: function(element, tagName) {
		var element = this.byID(element);
		while (element.parentNode && (!element.tagName ||
	    	(element.tagName.toUpperCase() != tagName.toUpperCase())))
	    element = element.parentNode;
		return element;
	},
	
	extend: function(destination, source) {
		for (var property in source) destination[property] = source[property];
		return destination;
	},
	
	byID: function(el) {
		if (typeof(el) == 'string') el = document.getElementById(el);
		return el;
	},
	
	cumulativeOffset: function(element) {
		var valueT = 0, valueL = 0;
		do {
			valueT += element.offsetTop  || 0;
			valueL += element.offsetLeft || 0;
			element = element.offsetParent;
		} while (element);
		return [valueL, valueT];
	},
	
	getDimensions: function(element) {
		element = this.byID(element);

		if (element.style.display != 'none')
			return {width: element.offsetWidth, height: element.offsetHeight};

		// All *Width and *Height properties give 0 on elements with display none,
		// so enable the element temporarily
		var els = element.style;
		var originalVisibility = els.visibility;
		var originalPosition = els.position;
			els.visibility = 'hidden';
			els.position = 'absolute';
			els.display = '';
		var originalWidth = element.clientWidth;
		var originalHeight = element.clientHeight;
			els.display = 'none';
			els.position = originalPosition;
			els.visibility = originalVisibility;
		return {width: originalWidth, height: originalHeight};
	},
	
	scrollOffset: function() {
		var deltaX =  window.pageXOffset
				|| document.documentElement.scrollLeft
				|| document.body.scrollLeft
				|| 0;
		var deltaY =  window.pageYOffset
				|| document.documentElement.scrollTop
				|| document.body.scrollTop
				|| 0;
		return [deltaX, deltaY];
	}

};
