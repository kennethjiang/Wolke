/* SlideShow version 1.0.0
 *
 * (c) 2006 Lyo Kato <lyo.kato@gmail.com>
 * SlideShow is freely distributable under the terms of MIT-style license.
 *
 * This library requires the JavaScript Framework "Prototype" (version 1.4 or later).
 * For details, see http://prototype.conio.net/
/*--------------------------------------------------------------------------------*/

var SlideShow = {
  Version: '1.0.0',
  create: function(options) {
    return new SlideShow.Window(options);
  }
};

SlideShow.Window = Class.create();
Object.extend(SlideShow.Window, {
  count: 0,
  getNextCount: function() {
    this.count++;
    return this.count;
  }
});

SlideShow.Window.prototype = {
  initialize: function(options) {
    this.objID     = "SlideShow.Window:" + SlideShow.Window.getNextCount();
    this.pictures  = [];
    this.slideBar  = null;
    this.container = null;
    this.index     = null;
    this.focusedPosition = { top: 0, left: 0 };
    this.mode = 'horizontal';
    this.containerStyle = {
      /*SCALR*/ //backgroundColor: '#FFFFFF'
    };
    this.slideBarStyle = {
      /*SCALR*/ //backgroundColor: '#FFFFFF'
    };
    this.callbacks = {
      beforeChange: [],
      afterChange:  []
    };
    if((!options) || (!options.pictureWidth) || (!options.pictureHeight) || (!options.capacity)) {
      throw "Set essential options.";
    }
    if (options.mode && !['horizontal', 'vertical'].include(options.mode))
      throw "wrong mode.";
    this.options = Object.extend({
      mode: 'horizontal'
    }, options);
    this.prepare();
  },
  registerCallback: function(type, callback) {
    var callbacks = this.callbacks[type];
    if(!callbacks)
      throw "Unknown callback type, " + type;
    callbacks.push(callback);
  },
  setContainerStyle: function(style) {
    Object.extend(this.containerStyle, style);
  },
  setSlideBarStyle: function(style) {
    Object.extend(this.slideBarStyle, style);
  },
  setCapacity: function(capacity) {
    this.options.capacity = capacity;
  },
  setPictureWidth: function(width) {
    this.options.pictureWidth = width;
  },
  setPictureHeight: function(height) {
    this.options.pictureHeight = height;
  },
  setMode: function(mode) {
    if (['horizontal', 'vertical'].include(mode))
      this.options.mode = mode;
  },
  setFocusedIndex: function(index) {
    var position = { left: 0, top: 0 };
    switch(this.options.mode) {
      case 'vertical':
        position.left = 0;
        position.top  = this.options.pictureHeight * index;
        break;
      case 'horizontal':
        //position.left = this.options.pictureWidth * index;
        /*SCALR*/ 
        position.left = 0;
        position.top  = 0;
        break;
    }
    this.setFocusedPosition(position.top, position.left);
  },
  setFocusedPosition: function(top, left) {
    this.focusedPosition = { top: top, left: left };
  },
  getContainerSize: function() {
    var size = { width: this.options.pictureWidth, height: this.options.pictureHeight };
    switch(this.options.mode) {
      case 'vertical':
        size.height *= this.options.capacity;
        break;
      case 'horizontal':
        size.width *= this.options.capacity;
        break;
    }
    return size;
  },
  prepare: function() {
    var container  = document.createElement('div');
    var slideBar   = document.createElement('div');
    this.slideBar  = slideBar;
    this.container = container;
    container.appendChild(slideBar);
  },
  add: function(src, options) {
    var picture = this.createPicture(src, this.options.pictureWidth, this.options.pictureHeight, options);
    this.addPicture(picture);
  },
  slideToNext: function() {
    if (this.index >= this.pictures.length - 1)
      this.index = -1;
      
    this.slideTo(this.index + 1);
  },
  slideToPrev: function() {
    if (this.index == 0)
      this.index = this.pictures.length;
      
    this.slideTo(this.index - 1);
  },
  slideTo: function(index, directly) {
    if (index == this.index)
      return;
    var picture  = this.getPicture(index);
    this.index   = index;
    this.slideToElement(picture.getElement(), directly);
  },
  forceSlideTo: function(index, directly) {
    var picture = this.getPicture(index);
    this.index = index;
    this.slideToElement(picture.getElement(), directly);
  },
  beforeChange: function() {
    var self = this;
    this.callbacks.beforeChange.each(function(c){c(self.pictures, self.index)});
  },
  afterChange: function() {
    var self = this;
    this.clearAction();
    this.callbacks.afterChange.each(function(c){c(self.pictures, self.index)});
  },
  slideToElement: function(element, directly) {
    if (!directly) {
      this.clearAction();
      var offset = this.getPictureOffset(element);
      var top    = this.focusedPosition.top  - offset.top;
      var left   = this.focusedPosition.left - offset.left;
      this.currentAction = new Effect.MoveBy(this.slideBar, top, left, {
        duration: 0.3,
        beforeStart: this.beforeChange.bind(this),
        afterFinish: this.afterChange.bind(this),
        queue: { position: 'end', scope: this.objID }
      });
    } else {
      this.beforeChange();
      var top    = parseFloat(Element.getStyle(element, 'top'));
      var left   = parseFloat(Element.getStyle(element, 'left'));
      var offset = this.getOffsetFromFocusedPosition(top, left);
      Element.setStyle(this.slideBar, {
        top:  offset.top   + 'px',
        left: offset.left  + 'px'
      });
      this.afterChange();
    }
  },
  getOffsetFromFocusedPosition: function(top, left) {
    var offset  = { top: 0, left: 0 };
    offset.top  = this.focusedPosition.top  - top;
    offset.left = this.focusedPosition.left - left;
    return offset;
  },
  clearAction: function() {
    if (this.currentAction) {
      this.currentAction.cancel();
      this.currentAction = null;
    }
  },
  addPicture: function(picture) {
    this.pictures.push(picture);
    var slideShow = this;
    var index     = this.pictures.length - 1;
    var element   = picture.getElement();
    Try.these(
      function(){ element.style.cursor = 'pointer' },
      function(){ element.style.cursor = 'hand'    }
    );
    
    //Event.observe(element, 'click', function(){slideShow.slideTo(index)});
    
    var offset = this.getPictureOffsetFromBar(index);
    Element.setStyle(element, {
      position: 'absolute',
      top:  offset.top  + 'px',
      left: offset.left + 'px'
    });
    this.slideBar.appendChild(element);
  },
  createPicture: function(src, width, height, options) {
    return new SlideShow.Picture(src, width, height, options);
  },
  getElement: function() {
    return this.container;
  },
  getPictureOffsetFromBar: function(index) {
    var offset = { left: 0, top: 0 };
    switch(this.options.mode) {
      case 'vertical':
        offset.top = this.options.pictureHeight * index;
        break;
      case 'horizontal':
        offset.left = this.options.pictureWidth * index;
        break;
    }
    return offset;
  },
  getPictureOffset: function(element) {
    var barOffset = this.getBarOffset();
    var top  = barOffset.top +  parseFloat(Element.getStyle(element, 'top'));
    var left = barOffset.left + parseFloat(Element.getStyle(element, 'left'));
    return { top: top, left: left };
  },
  getBarOffset: function() {
    var top  = parseFloat(Element.getStyle(this.slideBar, 'top'));
    var left = parseFloat(Element.getStyle(this.slideBar, 'left'));
    return { top: top, left: left };
  },
  getPicture: function(index) {
    if (index > this.pictures.length || index < 0)
      throw "wrong index.";
    return this.pictures[index];
  },
  setup: function(element, initialIndex) {
    if(!initialIndex)
      initialIndex = 0;
    if (this.pictures.length == 0)
      throw "set picture.";
    var size = this.getContainerSize();
    Element.setStyle(this.container, Object.extend(this.containerStyle, {
      width:    size.width +  'px',
      height:   size.height + 'px',
      position: 'relative',
      overflow: 'hidden'
    }));
    Element.setStyle(this.slideBar, Object.extend(this.slideBarStyle, {
      position: 'absolute',
      top:      0,
      left:     0
    }));
    element = $(element);
    this.slideTo(initialIndex, true);
    element.appendChild(this.getElement());
  },
  loadPlugin: function(pluginName) {
    var pluginClass = SlideShow.Plugin[pluginName];
    if (!pluginClass)
      throw "Unknown plugin, " + pluginName;
    var plugin = new pluginClass();
    plugin.load(this);
    return plugin;
  },
  wheel: function(event) {
    var delta;
    if (event.wheelDelta) {
      delta = event.wheelDelta / 120;
      if (window.opera)
        delta *= -1;
    } else if (event.detail) {
      delta = -event.detail / 3;
    }
    if (delta) {
      if (delta < 0)
        this.slideToNext();
      else
        this.slideToPrev();
    }
  },
  startWheelObserving: function() {
    this.wheelEvent = this.wheel.bind(this);
    if (window.addEventListener) {
      window.addEventListener('DOMMouseScroll', this.wheelEvent, false);
    } else {
      window.attachEvent('onmousewheel', this.wheelEvent);
      document.attachEvent('onmousewheel', this.wheelEvent);
    }
  }
}

SlideShow.Picture = Class.create();
SlideShow.Picture.prototype = {
  initialize: function(src, width, height, options) {
    this.options = options || {};
    this.setup(src, width, height);
  },
  setup: function(src, width, height) {
    var element = document.createElement('span');
    var imgElement = document.createElement('img');
    imgElement.src = src;
    imgElement.style.margin = '0px';
    imgElement.border = '0px';
    imgElement.setAttribute('width',  width);
    imgElement.setAttribute('height', height);
    element.appendChild(imgElement);
    this.element = element;
  },
  getElement: function() {
    return this.element;
  }
}

SlideShow.Plugin = {};
SlideShow.Plugin.Base = Class.create();
SlideShow.Plugin.Base.prototype = {
  initialize: function()          { },
  load:       function(slideShow) { }
}

SlideShow.Plugin.SpotLight = Class.create();
Object.extend(Object.extend(
  SlideShow.Plugin.SpotLight.prototype, SlideShow.Plugin.Base.prototype), {
  load: function(slideShow) {
    slideShow.registerCallback('beforeChange', function(pictures, index){
      //pictures.each(function(pic){Element.setOpacity(pic.getElement(), 0.3)});
      /*SCALR*/
      pictures.each(function(pic){Element.setOpacity(pic.getElement(), 1)});
    });
    slideShow.registerCallback('afterChange', function(pictures, index){
      var selected = pictures[index];
      //Element.setOpacity(selected.getElement(), 0.99);
      /*SCALR*/
      Element.setOpacity(selected.getElement(), 1);
    });
  }
});

