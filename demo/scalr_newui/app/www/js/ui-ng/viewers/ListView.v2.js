Ext.ns("Scalr.Viewers");
Ext.ns("Scalr.Viewers.list");

Scalr.Viewers.list.ListView = Ext.extend(Ext.DataView, {
	/**
	 * @cfg {Boolean} hideHeaders
	 * <tt>true</tt> to hide the {@link #internalTpl header row} (defaults to <tt>false</tt> so
	 * the {@link #internalTpl header row} will be shown).
	 */
	hideHeaders: false,
	/**
	 * @cfg {String} itemSelector
	 * Defaults to <tt>'dl'</tt> to work with the preconfigured <b><tt>{@link Ext.DataView#tpl tpl}</tt></b>.
	 * This setting specifies the CSS selector (e.g. <tt>div.some-class</tt> or <tt>span:first-child</tt>)
	 * that will be used to determine what nodes the ListView will be working with.
	*/
	itemSelector: 'dl.viewers-listview-row',
	elementSelector: 'dt',

	/**
	 * @cfg {String} selectedClass The CSS class applied to a selected row (defaults to
	 * <tt>'x-list-selected'</tt>). An example overriding the default styling:
	 * @type String
	 */
	selectedClass: 'viewers-listview-row-selected',
	/**
	 * @cfg {String} overClass The CSS class applied when over a row (defaults to
	 * <tt>'x-list-over'</tt>). An example overriding the default styling:
	 * @type String
	*/
	overClass: 'viewers-listview-row-over',
	/**
	* @cfg {Boolean} reserveScrollOffset
	* By default will defer accounting for the configured <b><tt>{@link #scrollOffset}</tt></b>
	* for 10 milliseconds.  Specify <tt>true</tt> to account for the configured
	* <b><tt>{@link #scrollOffset}</tt></b> immediately.
	*/
	/**
	 * @cfg {Number} scrollOffset The amount of space to reserve for the scrollbar (defaults to
	 * <tt>undefined</tt>). If an explicit value isn't specified, this will be automatically
	 * calculated.
	 */
	scrollOffset : undefined,
	/**
	 * @cfg {Boolean/Object} columnResize
	 * Specify <tt>true</tt> or specify a configuration object for {@link Ext.list.ListView.ColumnResizer}
	 * to enable the columns to be resizable (defaults to <tt>true</tt>).
	 */
	columnResize: true,
	columnSort: true,
	columnHide: true,

	columnOrderPlugin: false,
	columnActionPlugin: true,

	initComponent: function() {
		if (this.columnResize) {
			this.columnResize = new Scalr.Viewers.list.ColumnResize();
			this.columnResize.init(this);
		}

		if (this.columnSort) {
			this.columnSort = new Scalr.Viewers.list.ColumnSort();
			this.columnSort.init(this);
		}

		if (this.columnHide) {
			this.columnHide = new Scalr.Viewers.list.ColumnHide();
			this.columnHide.init(this);
		}

		if (this.columnOrderPlugin) {
			this.columnOrderPlugin = new Scalr.Viewers.list.ColumnOrderPlugin();
			this.columnOrderPlugin.init(this);
		}

		if (this.columnActionPlugin) {
			this.columnActionPlugin = new Scalr.Viewers.list.ColumnActionPlugin();
			this.columnActionPlugin.init(this);
		}

		this.internalTpl = new Ext.XTemplate(
			'<div class="x-list-header">',
				'<div class="x-list-header-inner">',
					'<tpl for="columns">',
						'<div ',
							'<tpl if="typeof(values.hidden) == \'undefined\' || values.hidden == \'no\' || values.hidden == \'disabled\'">',
								'style="width:{values.widthPx}px; text-align:{align};"',
							'</tpl>',
							'<tpl if="values.hidden == \'yes\'">',
								'style="display: none; text-align: {align};"',
							'</tpl>',
						'><em unselectable="on">{header}</em></div>',
					'</tpl>',
					'<div class="x-clear"></div>',
				'</div>',
				'<div class="viewers-listview-columns-icon"><img src="/images/ui-ng/viewers/listview/popup_icon.gif"></div>',
			'</div>',
			'<div class="x-list-body"><div class="x-list-body-inner">',
			'</div></div>'
		);

		this.tpl = new Ext.XTemplate(
			'<tpl for="rows">',
				'<dl class="viewers-listview-row {[xindex % 2 === 0 ? "viewers-listview-row-alt" : ""]}">',
					'<tpl for="parent.columns">',
						'<dt dataindex="{dataIndex}" style="text-align: {align}; ',
							'<tpl if="typeof(values.hidden) == \'undefined\' || values.hidden == \'no\' || values.hidden == \'disabled\'">',
								'width:{values.widthPx}px;',
							'</tpl>',
							'<tpl if="values.hidden == \'yes\'">',
								'display: none;',
							'</tpl>',
						'">',
						'<em class="<tpl if="cls">{cls}</tpl> {[this.getRowClass(values)]}" <tpl if="style">style="{style}"</tpl> >',
							'{[values.tpl.apply(parent)]}',
						'</em></dt>',
					'</tpl>',
					'<div class="x-clear"></div>',
				'</dl>',
			'</tpl>', {
				getRowClass: this.getRowClass
			}
		);

		var cs = this.columns,
			len = cs.length,
			columns = [];

		for (var i = 0; i < len; i++) {
			var c = cs[i];
			if (! c.isColumn) {
				c.xtype = c.xtype ? (/^lv/.test(c.xtype) ? c.xtype : 'lv' + c.xtype) : 'lvcolumn';
				c = Ext.create(c);
			}
			columns.push(c);
		}

		this.columns = columns;
		this.emptyText = '<div class="viewers-listview-empty">' + this.emptyText + '</div>';

		if (! this.singleSelect)
			this.onClick = Ext.emptyFn;

		Scalr.Viewers.list.ListView.superclass.initComponent.call(this);

		this.addEvents('refresh');
		Ext.apply(this, {
			refresh: this.refresh.createSequence(function() {
				this.fireEvent('refresh');
			}, this)
		});
	},

	getRowClass: function (data) {
		return '';
	},

	onRender : function() {
		this.autoEl = {
			cls: 'x-list-wrap'
		};
		Scalr.Viewers.list.ListView.superclass.onRender.apply(this, arguments);

		this.internalTpl.overwrite(this.el, { columns: this.columns });

		this.innerBody = Ext.get(this.el.dom.childNodes[1].firstChild);
		this.innerHd = Ext.get(this.el.dom.firstChild.firstChild);

		this.updateColumnWidth();
		this.setHdWidths();

		if (this.hideHeaders) {
			this.el.dom.firstChild.style.display = 'none';
		}

		if (! this.columnHide)
			this.getEl().child('div.viewers-listview-columns-icon').hide();
	},

	collectData : function(){
		var rs = Scalr.Viewers.list.ListView.superclass.collectData.apply(this, arguments);
		return {
			columns: this.columns,
			rows: rs
		};
	},

	getTemplateTarget: function(){
		return this.innerBody;
	},

	// private
	onResize: function(w, h) {
		var bd = this.innerBody.dom;
		var hd = this.innerHd.dom;
		if (!bd) {
			return;
		}
		var bdp = bd.parentNode;

		if (Ext.isNumber(w)) {
			var sw = this.columnHide ? (w - 19) : w; // width of columns-icon
			bd.style.width = sw + 'px';
			hd.style.width = sw + 'px';
		}

		if (Ext.isNumber(h) && h > 0){
			bdp.style.height = (h - hd.parentNode.offsetHeight) + 'px';
		} else {
			bdp.style.height = 'auto';
		}

		this.updateColumnWidth();
		this.setHdWidths();
		this.setBodyWidths();
	},

	updateColumnWidth: function() {
		var columns = 0, fixedWidth = 0, allWidth = 0, availWidth = this.innerBody.getWidth(), averageWidth = 0;
		for (var i = 0, len = this.columns.length; i < len; i++) {
			if (this.columns[i].hidden && this.columns[i].hidden == 'yes')
				continue;

			if (Ext.isNumber(this.columns[i].width)) {
				columns++;
				allWidth += this.columns[i].width;
			} else {
				this.columns[i].widthPx = parseInt(this.columns[i].width);
				availWidth -= this.columns[i].widthPx;
			}
		}
		availWidth -= 2; // borders of viewers-listview-row

		if (columns > 0 && availWidth > 0) {
			averageWidth = Math.floor(availWidth / allWidth);
			for (var i = 0, len = this.columns.length; i < len; i++) {
				if (this.columns[i].hidden && this.columns[i].hidden == 'yes') {
					this.columns[i].widthPx = 0;
					continue;
				}

				if (Ext.isNumber(this.columns[i].width)) {
					var prepWidth = Math.floor(averageWidth * this.columns[i].width);
					if (columns == 1) {
						// last columns
						this.columns[i].widthPx = availWidth;
						break;
					} else {
						if ((availWidth - prepWidth) > 0) {
							this.columns[i].widthPx = prepWidth;
							availWidth -= prepWidth;
						} else {
							this.columns[i].widthPx = availWidth;
							availWidth = 0;
						}

						columns--;
					}
				}
			}
		}
	},

	setHdWidths: function() {
		var els = this.innerHd.dom.getElementsByTagName('div');
		for(var i = 0, cs = this.columns, len = cs.length; i < len; i++){
			els[i].style.width = (typeof(cs[i].widthPx) != "undefined" ? cs[i].widthPx : 0) + 'px';
			els[i].style.display = (cs[i].hidden != 'yes') ? 'block' : 'none';
		}
	},

	setBodyWidths: function() {
		var lines = this.innerBody.dom.getElementsByTagName('dl');
		for (var i = 0, len = lines.length; i < len; i++) {
			var dt = lines[i].getElementsByTagName('dt');
			for (var j = 0, cs = this.columns, lenCS = cs.length; j < lenCS; j++) {
				dt[j].style.width = (typeof(cs[j].widthPx) != "undefined" ? cs[j].widthPx : 0) + 'px';
				dt[j].style.display = (cs[j].hidden != 'yes') ? 'block' : 'none';
			}
		}
	},

	findHeaderIndex: function (header) {
		header = header.dom || header;
		var parentNode = header.parentNode,
			children = parentNode.parentNode.childNodes,
			i = 0,
			c;
		for (; c = children[i]; i++) {
			if (c == parentNode) {
				return i;
			}
		}
		return -1;
	}
});

Scalr.Viewers.list.Column = Ext.extend(Object, {
    /**
     * @private
     * @cfg {Boolean} isColumn
     * Used by ListView constructor method to avoid reprocessing a Column
     * if <code>isColumn</code> is not set ListView will recreate a new Ext.list.Column
     * Defaults to true.
     */
    isColumn: true,

    /**
     * @cfg {String} align
     * Set the CSS text-align property of the column. Defaults to <tt>'left'</tt>.
     */
    align: 'left',
    /**
     * @cfg {String} header Optional. The header text to be used as innerHTML
     * (html tags are accepted) to display in the ListView.  <b>Note</b>: to
     * have a clickable header with no text displayed use <tt>'&#160;'</tt>.
     */
    header: '',

    /**
     * @cfg {Number} width Optional. Percentage of the container width
     * this column should be allocated.  Columns that have no width specified will be
     * allocated with an equal percentage to fill 100% of the container width.  To easily take
     * advantage of the full container width, leave the width of at least one column undefined.
     * Note that if you do not want to take up the full width of the container, the width of
     * every column needs to be explicitly defined.
     */
    width: null,

    /**
     * @cfg {String} cls Optional. This option can be used to add a CSS class to the cell of each
     * row for this column.
     */
    cls: '',

	style: '',

    /**
     * @cfg {String} tpl Optional. Specify a string to pass as the
     * configuration string for {@link Ext.XTemplate}.  By default an {@link Ext.XTemplate}
     * will be implicitly created using the <tt>dataIndex</tt>.
     */

    /**
     * @cfg {String} dataIndex <p><b>Required</b>. The name of the field in the
     * ListViews's {@link Ext.data.Store}'s {@link Ext.data.Record} definition from
     * which to draw the column's value.</p>
     */

    constructor: function(c) {
        if (!c.tpl) {
            c.tpl = new Ext.XTemplate('{' + c.dataIndex + '}');
        }
        else if (Ext.isString(c.tpl)) {
            c.tpl = new Ext.XTemplate(c.tpl);
        }

        Ext.apply(this, c);
    }
});
Ext.reg('lvcolumn', Scalr.Viewers.list.Column);

Scalr.Viewers.list.ColumnResize = Ext.extend(Ext.util.Observable, {
	/**
	* @cfg {Number} minPct The minimum percentage to allot for any column (defaults to <tt>.05</tt>)
	*/
	minPct: .05,

	constructor: function(config) {
		Ext.apply(this, config);
		Scalr.Viewers.list.ColumnResize.superclass.constructor.call(this);
	},

	init : function(listView){
		this.view = listView;
		this.view.addEvents('columnresize');
		this.view.on('render', this.initEvents, this);
	},

	initEvents: function(view) {
		view.mon(view.innerHd, 'mousemove', this.handleHdMove, this);
		this.tracker = new Ext.dd.DragTracker({
			onBeforeStart: this.onBeforeStart.createDelegate(this),
			onStart: this.onStart.createDelegate(this),
			onDrag: this.onDrag.createDelegate(this),
			onEnd: this.onEnd.createDelegate(this),
			tolerance: 3,
			autoStart: 300
		});
		this.tracker.initEl(view.innerHd);
		view.on('beforedestroy', this.tracker.destroy, this.tracker);
	},

	handleHdMove : function(e, t){
		var handleWidth = 5,
			x = e.getPageX(),
			header = e.getTarget('em', 3, true);
		if(header){
			var region = header.getRegion(),
				style = header.dom.style,
				parentNode = header.dom.parentNode;

			if(x - region.left <= handleWidth && parentNode != parentNode.parentNode.firstChild){
				this.activeHd = Ext.get(parentNode.previousSibling.firstChild);
				style.cursor = Ext.isWebKit ? 'e-resize' : 'col-resize';
			} else if(region.right - x <= handleWidth && parentNode != parentNode.parentNode.lastChild.previousSibling){
				this.activeHd = header;
				style.cursor = Ext.isWebKit ? 'w-resize' : 'col-resize';
			} else{
				delete this.activeHd;
				style.cursor = '';
			}
		}
	},

	// Sets up the boundaries for the drag/drop operation
	setBoundaries: function(relativeX){
		var view = this.view,
			headerIndex = this.hdRealIndex,
			width = view.innerHd.getWidth(),
			relativeX = view.innerHd.getX(),
			minWidth = Math.ceil(width * this.minPct),
			maxWidth = width - minWidth,
			numColumns = view.columns.length,
			headers = view.innerHd.select('em', true),
			minX = minWidth + relativeX,
			maxX = maxWidth + relativeX,
			header;
		if (numColumns == 2) {
			this.minX = minX;
			this.maxX = maxX;
		}else{
			header = headers.item(this.hdRealNextIndex);
			this.minX = headers.item(headerIndex).getX() + minWidth;
			this.maxX = (header ? header.getX() - minWidth : maxX) + 2000; // HACK

			if (headerIndex == 0) {
				// First
				this.minX = minX;
			} else if (headerIndex == numColumns - 2) {
				// Last
				this.maxX = maxX;
			}
		}
	},

	onBeforeStart: function(e) {
		this.dragHd = this.activeHd;
		if (this.dragHd) {
			var hdIndex = this.view.findHeaderIndex(this.dragHd), index = hdIndex, len = this.view.columns.length;
			this.hdRealIndex = -1;
			this.hdRealNextIndex = -1;
			this.hdRealDiff = 0; // diff between first and next columns (columns with persist width)
			this.hdRealNextDiff = 0;

			while (index >= 0) {
				if (this.view.columns[index].hidden && this.view.columns[index].hidden == 'yes') {
					index--;
					continue;
				}

				if (Ext.isNumber(this.view.columns[index].width)) {
					this.hdRealIndex = index;
				} else {
					this.hdRealDiff += parseInt(this.view.columns[index].width);
					index--;
					continue;
				}
				break;
			}

			if (this.hdRealIndex == -1) {
				return false;
			}

			index = hdIndex + 1;
			while (index < len) {
				if (this.view.columns[index].hidden && this.view.columns[index].hidden == 'yes') {
					index++;
					continue;
				}

				if (Ext.isNumber(this.view.columns[index].width)) {
					this.hdRealNextIndex = index;
				} else {
					this.hdRealNextDiff += parseInt(this.view.columns[index].width);
					index++;
					continue;
				}
				break;
			}

			if (this.hdRealNextIndex == -1) {
				return false;
			}

			// replace dragHd and activeHd with hdRealIndex (error left size for hidden columns)
			this.dragHd = this.activeHd = Ext.get(this.view.innerHd.dom.childNodes[this.hdRealIndex]);
			return true;
		}
		return false;
	},

	onStart: function(e){
		var me = this,
			view = me.view,
			dragHeader = me.dragHd,
			x = me.tracker.getXY()[0];

		me.proxy = view.el.createChild({cls:'x-list-resizer'});
		me.dragX = dragHeader.getX();
		me.headerIndex = view.findHeaderIndex(dragHeader);

		me.headersDisabled = view.disableHeaders;
		view.disableHeaders = true;

		me.proxy.setHeight(view.el.getHeight());
		me.proxy.setX(me.dragX);
		me.proxy.setWidth(x - me.dragX);

		this.setBoundaries();
	},

	onDrag: function(e){
		var me = this,
			cursorX = me.tracker.getXY()[0].constrain(me.minX, me.maxX);

		me.proxy.setWidth(cursorX - this.dragX);
	},

	onEnd: function(e) {
		/* calculate desired width by measuring proxy and then remove it */
		var nw = this.proxy.getWidth();
		this.proxy.remove();

		var vw = this.view,
			cs = vw.columns,
			len = cs.length,
			w = this.view.innerHd.getWidth(),
			curw = cs[this.hdRealIndex].widthPx,
			nextw = cs[this.hdRealNextIndex].widthPx,
			allw = 0,
			wp = 0;

		cs[this.hdRealIndex].widthPx = Math.min(Math.max(20, nw - this.hdRealDiff), (curw + nextw - 20));
		cs[this.hdRealNextIndex].widthPx = nextw + curw - cs[this.hdRealIndex].widthPx;

		// calculate summary
		for (var i = 0; i < len; i++) {
			if (cs[i].hidden && cs[i].hidden == 'yes')
				continue;

			if (Ext.isNumber(cs[i].width)) {
				allw += cs[i].widthPx;
			}
		}

		// restore width percentages
		for (var i = 0; i < len; i++) {
			if (cs[i].hidden && cs[i].hidden == 'yes')
				continue;

			if (Ext.isNumber(cs[i].width)) {
				this.view.columns[i].width = cs[i].widthPx / allw * 100;
			}
		}

		delete this.dragHd;
		vw.setHdWidths();
		vw.setBodyWidths();
		vw.fireEvent('columnresize');

		setTimeout(function(){
			vw.disableHeaders = false;
		}, 100);
	}
});

Scalr.Viewers.list.ColumnSort = Ext.extend(Ext.util.Observable, {
	/**
	* @cfg {Array} sortClasses
	* The CSS classes applied to a header when it is sorted. (defaults to <tt>["sort-asc", "sort-desc"]</tt>)
	*/
	sortClasses : ["sort-asc", "sort-desc"],

	constructor: function(config){
		Ext.apply(this, config);
		Scalr.Viewers.list.ColumnSort.superclass.constructor.call(this);
	},

	init : function(listView){
		this.view = listView;
		this.view.addEvents('columnsort');
		this.view.on('render', this.initEvents, this);
	},

	initEvents : function(view){
		view.mon(view.innerHd, 'click', this.onHdClick, this);
		view.innerHd.setStyle('cursor', 'pointer');
		view.mon(view.store, 'datachanged', this.updateSortState, this);
		this.updateSortState.defer(10, this, [view.store]);
	},

	updateSortState : function(store){
		var state = store.getSortState();
		if(!state){
			return;
		}
		this.sortState = state;
		var cs = this.view.columns, sortColumn = -1;
		for(var i = 0, len = cs.length; i < len; i++){
			if(cs[i].dataIndex == state.field){
				sortColumn = i;
				break;
			}
		}
		if(sortColumn != -1){
			var sortDir = state.direction;
			this.updateSortIcon(sortColumn, sortDir);
		}
	},

	updateSortIcon : function(col, dir){
		var sc = this.sortClasses;
		var hds = this.view.innerHd.select('em').removeClass(sc);
		hds.item(col).addClass(sc[dir == "DESC" ? 1 : 0]);
	},


	onHdClick: function(e) {
		var hd = e.getTarget('em', 3);
		if (hd && !this.view.disableHeaders) {
			var index = this.view.findHeaderIndex(hd);
			if (this.view.columns[index].sortable && this.view.columns[index].sortable == true) {
				this.view.store.sort(this.view.columns[index].dataIndex);
				this.view.fireEvent('columnsort');
			}
		}
	}
});

Scalr.Viewers.list.ColumnOrderPlugin = Ext.extend(Ext.util.Observable, {
	constructor: function (config) {
		Ext.apply(this, config);
		Scalr.Viewers.list.ColumnOrderPlugin.superclass.constructor.call(this);
	},

	init: function(listView) {
		this.view = listView;

		this.view.columns.push({
			header: '&nbsp;',
			width: '50px',
			cls: 'viewers-listview-row-order-plugin',
			sortable: false,
			tpl: '<img src="/images/up_icon.png" class="up" style="cursor: pointer"> <img src="/images/down_icon.png" class="down" style="cursor: pointer">'
		});

		this.view.on('refresh', this.onRefresh, this);
	},

	onRefresh: function() {
		this.view.getTemplateTarget().select("img.up").each(function(el) {
			el.on('click', this.onClick, this.view);
		}, this);

		this.view.getTemplateTarget().select("img.down").each(function(el) {
			el.on('click', this.onClick, this.view);
		}, this);
	},

	onClick: function(e) {
		var item = e.getTarget(this.itemSelector, this.getTemplateTarget()), index = this.indexOf(item), el = e.getTarget(null, null, true);

		if (el.is('img.up') && index > 0) {
			var record = this.store.getAt(index);
			this.store.removeAt(index);
			this.store.insert(index - 1, record);
		} else if (el.is('img.down') && (index < this.store.getCount() - 1)) {
			var record = this.store.getAt(index);
			this.store.removeAt(index);
			this.store.insert(index + 1, record);
		}

		this.refresh();
	}
});

Scalr.Viewers.list.ColumnActionPlugin = Ext.extend(Ext.util.Observable, {
	constructor: function (config) {
		Ext.apply(this, config);
		Scalr.Viewers.list.ColumnActionPlugin.superclass.constructor.call(this);
	},

	init: function(listView) {
		listView.on('afterrender', function () {
			var cache = {};
			for (var i = 0; i < this.columns.length; i++) {
				if (this.columns[i].clickHandler)
					cache[this.columns[i].dataIndex] = this.columns[i].clickHandler;
			}

			this.getTemplateTarget().on('click', function (e) {
				var elem = e.getTarget(this.elementSelector, this.getTemplateTarget(), true), column = elem ? elem.getAttribute('dataindex') : '';
				if (column && cache[column]) {
					var item = e.getTarget(this.itemSelector, this.getTemplateTarget()), index = this.indexOf(item), record = this.store.getAt(index);
					cache[column].call(this, this, this.store, record);
				}
			}, this);
		}, listView);
	}
});

Scalr.Viewers.list.ColumnHide = Ext.extend(Ext.util.Observable, {
	constructor: function(config){
		Ext.apply(this, config);
		Scalr.Viewers.list.ColumnHide.superclass.constructor.call(this);
	},

	init: function(listView) {
		this.view = listView;
		this.view.addEvents('columnhide', 'columnshow');
		this.view.setHiddenColumn = this.setHiddenColumn;
		listView.on('afterrender', this.initEvents, this.view);
	},

	initEvents: function(view) {
		this.columnsMenu = new Ext.menu.Menu();

		for (var i = 0, len = this.columns.length; i < len; i++) {
			if (this.columns[i].hidden) {
				this.columnsMenu.addItem(
					new Ext.menu.CheckItem({
						text: this.columns[i].header,
						dataIndex: this.columns[i].dataIndex,
						checked: (this.columns[i].hidden == 'no') ? true : false,
						disabled: (this.columns[i].hidden == 'disabled') ? true : false,
						hideOnClick: false,
						listeners: {
							'checkchange': function(item, checked) {
								var column = null;
								for (var i = 0, len = this.columns.length; i < len; i++) {
									if (this.columns[i].dataIndex == item.dataIndex) {
										this.columns[i].hidden = this.columns[i].hidden == 'no' ? 'yes' : 'no';
										column = this.columns[i];
									}
								}
								this.updateColumnWidth();
								this.setHdWidths();
								this.setBodyWidths();

								if (column)
									this.fireEvent(column.hidden == 'no' ? 'columnshow' : 'columnhide', column);
							},
							scope: this
						}
					})
				);
			}
		}

		view.on('refresh', function() {
			this.getEl().child('div.viewers-listview-columns-icon').on('click', function(e) {
				this.columnsMenu.showAt(e.getXY());
				e.stopEvent();
			}, this);
		}, this);
	},

	setHiddenColumn: function (dataIndex, hidden) {
		for (var i = 0, len = this.columns.length; i < len; i++) {
			if (this.columns[i].dataIndex == dataIndex) {
				this.columns[i].hidden = hidden ? 'yes' : 'no';
				break;
			}
		}
		this.updateColumnWidth();
		this.setHdWidths();
		this.setBodyWidths();
	}
});

Scalr.Viewers.ListView = Ext.extend(Ext.Panel, {
	messages: {
		pageSize: "{0}记录每页",
		options: "选项",
		tickTrue: "是",
		tickFalse: "否",
		withSelected: "对所有选择的记录",
		blankSelection: "请选择至少一条记录",
		filter: "过滤器"
	},
	linkTplsCache: {},

	listViewOptions: {},

	defaultListViewOptions: {
		emptyText: '没有记录',
		autoScroll: true
	},

	enableFilter: true,
	enablePaging: true,
	enableAutoLoad: true, // store
	enableReloadOnShow: true, // store
	saveColumns: true,

	defaultPageSize: 10,
	pageSizes: [10, 15, 25, 50, 100],

	initComponent: function() {
		Ext.applyIf(this.listViewOptions, this.defaultListViewOptions);
		Ext.apply(this.listViewOptions, {
			store: this.store
		});

		this.store.on({
			scope: this,
			beforeload: function () {
				this.listView.getTemplateTarget().update();
				Scalr.Utils.CreateProcessBox({
					type: 'action',
					msg: '读取数据中，请稍候...'
				});
			},
			load: function () {
				Ext.MessageBox.hide();
			},
			exception: function () {
				Ext.MessageBox.hide();
				this.listView.getTemplateTarget().update('<div class="viewers-listview-empty" style="color: red">Unable to load data</div>');
			}
		});

		// create paging toolbar
		if (this.enablePaging) {
			this.pagingToolbar = new Ext.PagingToolbar({
				pageSize: this.defaultPageSize,
				store: this.store,
				items: this.bbar || []
			});
			this.bbar = this.pagingToolbar;
		}

		// create options menu
		if (this.rowOptionsMenu) {
			this.rowOptionsMenu = new Ext.menu.Menu({
				items: this.rowOptionsMenu,
				listeners: {
					itemclick: function(item, e) {
						if (Ext.isFunction(item.menuHandler)) {
							item.menuHandler(item);
							e.preventDefault();

						} else if (Ext.isObject(item.request)) {
							var r = Scalr.utils.CloneObject(item.request);
							r.params = r.params || {};

							if (Ext.isObject(r.confirmBox))
								r.confirmBox.msg = new Ext.Template(r.confirmBox.msg).applyTemplate(item.currentRecordData);

							if (Ext.isFunction(r.dataHandler)) {
								r.params = Ext.apply(r.params, r.dataHandler(item.currentRecord));
								delete r.dataHandler;
							}

							Scalr.Request(r);
							e.preventDefault();
						}
					}
				}
			});

			// Add options column
			this.listViewOptions.columns.push({
				header: '&nbsp;',
				width: '116px',
				resizable: false,
				align: 'center',
				cls: 'viewers-listview-row-options',
				tpl:
					new Ext.XTemplate(
						'<tpl if="this.getVisible(values)"><div class="viewers-listview-row-options-btn">选项<div class="viewers-listview-row-options-trigger"></div></div></tpl>', {
							getVisible: this.getRowMenuVisibility
						}
					)
			});
		}

		// with selected menu
		if (this.withSelected) {
			var withSelectedMenu = new Ext.menu.Menu(this.withSelected.menu);
			var items = [ '->', { itemId: 'withselectedmenu', text: this.messages.withSelected, menu: withSelectedMenu } ];
			if (this.pagingToolbar) {
				this.pagingToolbar.add(items);
			} else {
				this.bbar = items;
			}

			withSelectedMenu.on('click', this.withSelectedMenuHandler, this);

			this.listViewOptions.multiSelect = true;
			this.listViewOptions.columns.push({
				header: '<input type="checkbox" class="withselected" />',
				width: '25px',
				resizable: false,
				sortable: false,
				tpl:
					new Ext.XTemplate('<input type="checkbox" <tpl if="!this.getVisible(values)">disabled="true"</tpl> class="withselected" />', {
						getVisible: ((typeof(this.withSelected.renderer) == "function") ? this.withSelected.renderer : function(data) { return true; })
					})
			});
		}

		if (this.enableFilter) {
			this.filterField = new Scalr.Viewers.FilterField({
				store: this.store
			});

			var tbitems = [ this.messages.filter +': ', this.filterField ];
			if (this.tbar) {
				tbitems.push('-');
				this.tbar.unshift(tbitems);
			} else {
				this.tbar = tbitems;
			}
		}

		if (Ext.isArray(this.plugins)) {
			this.plugins[this.plugins.length] = new Scalr.Viewers.Plugins.localStorage();
		} else {
			this.plugins = [ new Scalr.Viewers.Plugins.localStorage() ];
		}

		Scalr.Viewers.ListView.superclass.initComponent.call(this);
	},

	withSelectedMenuHandler: function(menu, item, e) {
		if (this.listView.getSelectionCount()) {
			var store = this.store, records = this.listView.getSelectedRecords(), r = Ext.apply({}, item.request);
			r.params = r.params || {};
			r.params = Ext.apply(r.params, r.dataHandler(records));

			if (Ext.isFunction(r.success)) {
				r.success = r.success.createSequence(function() {
					store.reload();
				});
			} else {
				r.success = function () {
					store.reload();
				};
			}
			delete r.dataHandler;

			Scalr.Request(r);

		} else {
			Ext.Msg.alert('通知', this.messages.blankSelection);
		}
	},

	onRender: function(container, position) {
		// restore column's width
		if (this.saveColumns && this.stateId && this.localGet('columns')) {
			var columns = this.localGet('columns');
			for (var i = 0, len = this.listViewOptions.columns.length; i < len; i++) {
				if (this.listViewOptions.columns[i] && columns[i]) {
					if (this.listViewOptions.columns[i].hidden && columns[i].hidden)
						this.listViewOptions.columns[i].hidden = columns[i].hidden;

					if (! Ext.isNumber(this.listViewOptions.columns[i].width))
						continue; // don't overwrite fixed width

					if (this.listViewOptions.columns[i].width && columns[i].width)
						this.listViewOptions.columns[i].width = columns[i].width;
				}
			}
		}

		// create listview
		this.listView = new Scalr.Viewers.list.ListView(this.listViewOptions);

		if (this.rowOptionsMenu) {
			this.listView.on('refresh', function() {
				this.listView.innerBody.select('div.viewers-listview-row-options-btn').on('click', this.showOptions, this);
			}, this);
		}

		if (this.withSelected) {
			this.listView.on('refresh', function() {
				this.innerHd.child('input.withselected').dom.checked = false;
				this.innerBody.select('input.withselected').on('click', function(ev, el) {
					if (el.checked) {
						this.select(ev.getTarget("dl.viewers-listview-row"), true);
					} else {
						this.deselect(ev.getTarget("dl.viewers-listview-row"));
						this.innerHd.child('input.withselected').dom.checked = false;
					}
				}, this);
			}, this.listView);

			this.listView.on('afterrender', function() {
				this.innerHd.select('input.withselected').on('click', function(ev, el) {
					this.innerBody.select('input.withselected').each(function(elem) {
						if (el.checked) {
							if (! elem.dom.disabled) {
								elem.dom.checked = el.checked;
								this.select(elem.parent("dl.viewers-listview-row"), true);
							}
						} else {
							elem.dom.checked = el.checked;
						}
					}, this);

					if (! el.checked) {
						this.clearSelections();
					}
				}, this);
			}, this.listView);
		}

		// Render row options menu when needed
		if (this.rowOptionsMenu) {
			this.rowOptionsMenu.render();
		}

		// call super
		Scalr.Viewers.ListView.superclass.onRender.call(this, container, position);

		this.on('bodyresize', function(p, width, height) {
			width = width - p.body.getBorderWidth('lr');
			height = height - p.body.getBorderWidth('tb');

			this.listView.setSize(width, height);
		});

		if (this.enableReloadOnShow) {
			this.on('show', function () {
				this.store.reload();
			}, this);
		}

		// save columns width on change
		if (this.saveColumns && this.stateId) {
			this.saveColumnsState = function () {
				var result = [];
				for (var i = 0, len = this.listView.columns.length; i < len; i++) {
					result[i] = {};
					if (this.listView.columns[i].width)
						result[i].width = this.listView.columns[i].width;

					if (this.listView.columns[i].hidden)
						result[i].hidden = this.listView.columns[i].hidden;
				}

				this.localSet('columns', result);
			};

			this.listView.on('columnresize', this.saveColumnsState, this);
			this.listView.on('columnhide', this.saveColumnsState, this);
			this.listView.on('columnshow', this.saveColumnsState, this);
		}

		this.add(this.listView);
	},

	afterRender: function() {
		Scalr.Viewers.ListView.superclass.afterRender.call(this);

		if (this.pagingToolbar) {
			// try to discover optimal PageSize
			var height = this.body.getHeight() - 26; // header's height
			var num = Math.floor(height / 25); // row's height

			if (num > this.defaultPageSize) {
				var fl = true;
				for (var i = 0; i < this.pageSizes.length; i++)
					if (this.pageSizes[i] == num) {
						fl = false;
						break;
					}

				if (fl)
					this.pageSizes.push(num);

				this.pageSizes.sort(function (a, b) {
					if (a < b)
						return -1;
					else if (a > b)
						return 1;
					else
						return 0;
				});

				this.defaultPageSize = num;
				this.pagingToolbar.pageSize = num;
				this.store.setBaseParam("limit", num);
			}

			var menu = [];
			for (var i = 0; i < this.pageSizes.length; i++) {
				menu.push({
					group: 'pagesize',
					text: this.pageSizes[i].toString(),
					checked: this.pageSizes[i] == this.defaultPageSize,
					handler: this.changePageSize,
					scope: this
				});
			}

			this.pagingToolbar.insert(11, '-');
			this.pageSizeBtn = this.pagingToolbar.insert(12, {
				text: String.format(this.messages.pageSize, this.defaultPageSize),
				menu: menu
			});

			this.doLayout();
			this.pagingToolbar.doLoad(0);

		} else {
			if (this.enableAutoLoad) {
				this.store.load();
			}
		}
	},

	showOptions: function (ev) {
		var i = this.listView.indexOf(ev.getTarget("dl.viewers-listview-row"));
		var record = this.store.getAt(i), data = record.data;
    	this.fireEvent("beforeshowoptions", this, record, this.rowOptionsMenu, ev);

    	this.rowOptionsMenu.items.each(function (item) {
    		var display = this.getRowOptionVisibility(item, record);
			item.currentRecord = record; // save for future use
			item.currentRecordData = record.data; // save for future use
			item[display ? "show" : "hide"]();
    		if (display && item.href) { // Update item link
    			if (!this.linkTplsCache[item.id]) {
    				this.linkTplsCache[item.id] = new Ext.Template(item.href).compile();
    			}
    			var tpl = this.linkTplsCache[item.id];
    			item.el.dom.href = tpl.apply(record.data);
    		}
    	}, this);

     	var btnEl = Ext.get(ev.getTarget('div.viewers-listview-row-options-btn'));
    	var xy = btnEl.getXY();
    	this.rowOptionsMenu.showAt([xy[0] - (this.rowOptionsMenu.getEl().getWidth() - btnEl.getWidth()), xy[1] + btnEl.getHeight()]);
	},

	getRowMenuVisibility: function (data) {
		return true;
	},

	getRowOptionVisibility: function (menuItem, record) {
		return true;
	},

	changePageSize: function(cmp) {
		this.pagingToolbar.pageSize = Number(cmp.text);
		this.pageSizeBtn.setText(String.format(this.messages.pageSize, this.pagingToolbar.pageSize));
		this.store.setBaseParam("limit", this.pagingToolbar.pageSize);
		this.pagingToolbar.changePage(0);
	}
});
