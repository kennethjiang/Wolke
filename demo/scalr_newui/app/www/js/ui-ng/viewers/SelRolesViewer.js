Ext.ns("Scalr.Viewers");

Scalr.Viewers.SelRolesViewer = Ext.extend(Ext.BoxComponent, {

	height: 130,
	mouseDrag: false,

	initComponent: function() {
		Scalr.Viewers.SelRolesViewer.superclass.initComponent.call(this);

		this.addEvents(
			"addrole",
			"deleterole"
		);

		this.dataView = new Ext.DataView({
			onAdd: Ext.emptyFn,
			fixWidth: function(obj) {
				if (this.rendered) {
					if (this.store.getCount()) {
						var newWidth = this.store.getCount() * 130; // width + margin (fix)
						var el = this.getEl().child("ul")
						if (el) el.setWidth(newWidth);
					}

					var scrollOffset = parseInt(obj.dataViewEl.dom.scrollLeft) || 0;
					//console.log(1);
					obj.dataViewEl.dom.scrollLeft = scrollOffset; // browser will clean scrollLeft if needed
					//console.log(2);

					var el = this.getEl().child("ul");

					if (el && el.getWidth() > this.getEl().getWidth()) {
						obj.buttonMoveLeftEl.removeClass('viewers-selrolesviewer-scroll-disabled');
						obj.buttonMoveRightEl.removeClass('viewers-selrolesviewer-scroll-disabled');
						obj.buttonMoveLeftEl.child('img').dom.src = '/images/ui-ng/viewers/selrolesviewer/previous.png';
						obj.buttonMoveRightEl.child('img').dom.src = '/images/ui-ng/viewers/selrolesviewer/next.png';
					} else {
						obj.buttonMoveLeftEl.addClass('viewers-selrolesviewer-scroll-disabled');
						obj.buttonMoveRightEl.addClass('viewers-selrolesviewer-scroll-disabled');
						obj.buttonMoveLeftEl.child('img').dom.src = '/images/ui-ng/viewers/selrolesviewer/previous_disable.png';
						obj.buttonMoveRightEl.child('img').dom.src = '/images/ui-ng/viewers/selrolesviewer/next_disable.png';
					}
				}
			},

			createLinks: function(obj) {
				Ext.select("#viewers-selrolesviewer ul li a").each(function(el) {
					var item = el.parent("li");
					if (item) {
						var record = this.dataView.getStore().getAt(this.dataView.indexOf(item));
						if (record) {
							handler = function(e) {
								Ext.Msg.confirm("Delete", "从当前云平台中删除服务角色  \"" + this.record.get("name") + "\"?", function(btn) {
									if (btn == 'yes') {
										this.obj.fireEvent("deleterole", this.record);
									}
								}, this);

								e.stopEvent();
							};

							el.on('click', handler, {obj: this, record: record});
						}
					}
				}, obj);

				Ext.select('#viewers-selrolesviewer ul li div.short').each(function(el) {
					el.on('mouseover', function(e) {
						var el = e.getTarget("", 10, true).findParent("div.short", 10, true);
						var sib = el.next('div.full');
						if (sib) sib.addClass('full-show');
					}, el);

					el.on('mouseout', function(e) {
						var el = e.getTarget("", 10, true).findParent("div.short", 10, true);
						var sib = el.next('div.full');
						if (sib) sib.removeClass('full-show');
					}, el);
				});
			},

			id: 'viewers-selrolesviewer',
			store: this.store,
			height: this.height,
			emptyText: '<div class="viewers-selrolesviewer-empty-text">当前云平台中没有服务角色</div>',
			deferEmptyText: false,
			tpl: new Ext.XTemplate(
				'<ul>',
					'<tpl for=".">',
						'<li>',
							'<img src="/images/ui-ng/icons/{[this.getLocationIcon(values)]}.png" class="icon" />',
							'<img src="/images/ui-ng/icons/platform/{platform}.png" class="platform" />',
							'<img src="/images/ui-ng/icons/arch/{arch}.png" class="arch" />',
							'<div class="short">',
								'<tpl if="name.length &gt; 12">',
									'<span class="short">{[this.getName(values.name)]}</span>',
									'</div><div class="full">{name}',
								'</tpl>',
								'<tpl if="name.length &lt; 13">',
									'<span class="short">{name}</span>',
								'</tpl>',
							'</div>',
							'<div class="location">{cloud_location}</div>',
							'<a>&nbsp;</a>',
						'</li>',
					'</tpl>',
				'</ul>', {
				getLocationIcon: function (values) {
					var groups = [ "base", "database", "app", "lb", "cache", "mixed" ];
					var behaviors = [ "app_app", "app_tomcat", "cache_memcached", "database_cassandra", "database_mysql" ];

					var b = (values['behaviors1'] || '').split(','), key;
					for (var i = 0, len = b.length; i < len; i++) {
						key = values['group'] + '_' + b[i];

						for (var k = 0; k < behaviors.length; k++ ) {
							if (behaviors[k] == key)
								return 'behaviors/' + key;
						}
					}

					for (var i = 0; i < groups.length; i++ ) {
						if (groups[i] == values['group'])
							return 'groups/' + groups[i];
					}
				},
				getName: function (n) {
					return n.substr(0, 6) + '...' + n.substr(n.length - 5, 5);
				}
			}),
			singleSelect: true,
			itemSelector: 'li',
			selectedClass: 'selrolesviewer-selected'
		});

		this.dataView.on('selectionchange', function(comp, selections) {
			var records = [];
			Ext.each(selections, function (item) {
				records[records.length] = this.getStore().getAt(this.indexOf(item));
			}, this.dataView);

			this.fireEvent("selectionchange", records);
		}, this);

		this.dataView.on('afterrender', function() {
			this.dataViewEl.on('mousedown', function(e) {
				this.dataView.mouseDrag = true; // drag element's list
				this.dataView.mouseCancelClickAfterDrag = false; // cancel click when drag mouse
				this.dataView.lastXY = e.getXY();
			}, this);

			this.dataViewEl.on('mouseup', function(e, t) {
				this.dataView.mouseDrag = false;
			}, this);

			this.dataView.on('beforeclick', function(e) {
				return !this.mouseCancelClickAfterDrag;
			}, this.dataView);

			this.dataViewEl.on('mousemove', function(e) {
				var xy = e.getXY();
				if (this.dataView.lastXY && (xy[0] != this.dataView.lastXY[0] || xy[1] != this.dataView.lastXY[1])) {
					this.dataView.mouseCancelClickAfterDrag = true;
				}

				if (this.dataView.mouseDrag) {
					var xy = e.getXY(), s = this.dataView.lastXY;
					this.dataView.lastXY = xy;

					var scrollOffset = parseInt(this.dataViewEl.dom.scrollLeft) || 0;
					this.dataViewEl.scrollTo('left', scrollOffset + s[0] - xy[0]);
				}
			}, this);

			this.dataViewEl.on('mousewheel', function(e) {
				var scrollOffset = parseInt(this.dataViewEl.dom.scrollLeft) || 0;
				this.dataViewEl.scrollTo('left', scrollOffset - e.getWheelDelta() * 130, true);
				e.preventDefault();
			}, this);

			this.dataView.refresh.call(this.dataView);
		}, this);

		this.dataView.on('containerclick', function () {
			return false;
		});

		/*handler = function() {
			this.dataView.fixWidth(this);
			//this.dataView.createLinks(this);
		};*/

		Ext.apply(this.dataView, { refresh: this.dataView.refresh.createSequence(function() {
			this.dataView.fixWidth(this);
		}, this) });

		Ext.apply(this.dataView, { updateIndexes: this.dataView.updateIndexes.createSequence(function(startIndex, endIndex) {
			this.dataView.createLinks(this, startIndex, endIndex);
		}, this) });

		Ext.apply(this.dataView, { onRemove: this.dataView.refresh });

		this.dataView.getStore().on('add', this.dataView.refresh, this.dataView);
		this.dataView.getStore().on('remove', this.dataView.refresh, this.dataView);
		//this.dataView.getStore().on('save', this.dataView.createLinks.createCallback(this), this.dataView);
		//this.dataView.getStore().un('remove', this.dataView.onRemove);
		//this.dataView.getStore().on('remove', this.dataView.fixWidth, this);

		// TODO: check all variants (include browser window/parent window resize)
		this.on('resize', function() {
			// set width of dataView (indent from left and right)
			this.dataViewEl.setWidth(this.getEl().getWidth() - 80 - 40); // 80 (left), 40 (right)
			this.dataView.fixWidth.call(this.dataView, this);
		});
	},

	clearFilter: function () {
		this.dataView.store.clearFilter();
		if (this.rendered)
			this.filterInputEl.child("input").dom.value = '';
	},

	onRender: function (ct, position) {
		Scalr.Viewers.SelRolesViewer.superclass.onRender.call(this, ct, position);

		this.el.setStyle('overflow', 'hidden');
		this.el.setStyle('position', 'relative');
		this.el.setStyle('background-color', '#FFF');
		this.el.setHeight(this.height);

		this.dataViewEl = this.el.createChild({
			tag: 'div', html: '', cls: 'viewers-selrolesviewer-blocks viewers-selrolesviewer-dataview'
		});

		this.buttonAddEl = this.el.createChild({
			tag: 'div', html: '&nbsp;', cls: 'viewers-selrolesviewer-blocks viewers-selrolesviewer-add'
		});

		this.buttonMoveLeftEl = this.el.createChild({
			tag: 'div', html: '<img src="/images/ui-ng/viewers/selrolesviewer/previous.png">', cls: 'viewers-selrolesviewer-blocks viewers-selrolesviewer-left viewers-selrolesviewer-scroll'
		});

		this.buttonMoveRightEl = this.el.createChild({
			tag: 'div', html: '<img src="/images/ui-ng/viewers/selrolesviewer/next.png">', cls: 'viewers-selrolesviewer-blocks viewers-selrolesviewer-right viewers-selrolesviewer-scroll'
		});

		this.filterInputEl = this.el.createChild({
			tag: 'div', html: '<input type="text">', cls: 'viewers-selrolesviewer-blocks viewers-selrolesviewer-filter-input'
		});
		this.filterInputEl.hide();

		this.filterButtonEl = this.el.createChild({
			tag: 'div', html: '&nbsp;', cls: 'viewers-selrolesviewer-blocks viewers-selrolesviewer-filter-button'
		});

		this.buttonAddEl.on('click', function(e) {
			e.stopEvent();
			this.fireEvent('addrole', this);
		}, this);

		this.filterButtonEl.on('click', function() {
			if (this.filterButtonEl.is("div.viewers-selrolesviewer-filter-button-click")) {
				this.filterInputEl.hide();
				this.filterButtonEl.removeClass("viewers-selrolesviewer-filter-button-click");
			} else {
				this.filterInputEl.show();
				this.filterButtonEl.addClass("viewers-selrolesviewer-filter-button-click");
			}
		}, this);

		this.buttonMoveLeftEl.on('click', function() {
			var scrollOffset = parseInt(this.dataViewEl.dom.scrollLeft) || 0;
			this.dataViewEl.scrollTo('left', scrollOffset + 130, true);
		}, this);

		this.buttonMoveRightEl.on('click', function() {
			var scrollOffset = parseInt(this.dataViewEl.dom.scrollLeft) || 0;
			this.dataViewEl.scrollTo('left', scrollOffset - 130, true);
		}, this);

		this.dataViewEl.on('scroll', function () {
			var scrollOffset = parseInt(this.dataViewEl.dom.scrollLeft) || 0, el = this.dataViewEl.child('ul');
			//console.log(scrollOffset);

			//if (this.)

/*

					if (el && el.getWidth() > this.getEl().getWidth()) {
						obj.buttonMoveLeftEl.removeClass('viewers-selrolesviewer-scroll-disabled');
						obj.buttonMoveRightEl.removeClass('viewers-selrolesviewer-scroll-disabled');
						obj.buttonMoveLeftEl.child('img').dom.src = '/images/ui-ng/viewers/selrolesviewer/previous.png';
						obj.buttonMoveRightEl.child('img').dom.src = '/images/ui-ng/viewers/selrolesviewer/next.png';
					} else {
						obj.buttonMoveLeftEl.addClass('viewers-selrolesviewer-scroll-disabled');
						obj.buttonMoveRightEl.addClass('viewers-selrolesviewer-scroll-disabled');
						obj.buttonMoveLeftEl.child('img').dom.src = '/images/ui-ng/viewers/selrolesviewer/previous_disable.png';
						obj.buttonMoveRightEl.child('img').dom.src = '/images/ui-ng/viewers/selrolesviewer/next_disable.png';
					}

*/

			//console.log('scroll');
		}, this);

		this.dataView.render(this.dataViewEl);

		this.dataViewEl.unselectable();
		this.buttonMoveLeftEl.unselectable();
		this.buttonMoveRightEl.unselectable();

		var el = this.filterInputEl.child("input");
		Scalr.fireOnInputChange(el, this, function() {
			this.dataView.getStore().filterBy(function(record) {
				return (record.get('name').toLowerCase().search(this.filterInputEl.child("input").getValue().toLowerCase()) != -1) ? true : false;
			}, this);
		});
	}
});
