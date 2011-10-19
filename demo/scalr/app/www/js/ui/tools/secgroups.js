Scalr.regPage('Scalr.ui.tools.secgroups', function (loadParams, moduleParams) {
	var store = new Scalr.data.Store({
		reader: new Scalr.data.JsonReader({
			id: 'id',
			fields: [ 'id','name','description' ]
		}),
		remoteSort: true,
		url: moduleParams['loadUrl']
	});

	return new Scalr.Viewers.ListView({
		title: moduleParams['title'],
		scalrOptions: {
			'reload': false,
			'maximize': 'all'
		},
		store: store,
		stateId: 'listview-sec-groups-view',
		stateful: true,

		tbar: [
			'Location:',
			new Ext.form.ComboBox({
				itemId: 'cloudLocation',
				editable: false,
				store: Scalr.data.createStore(moduleParams.locations, { idProperty: 'id', fields: [ 'id', 'name' ]}),
				typeAhead: false,
				displayField: 'name',
				valueField: 'id',
				value: '',
				mode: 'local',
				triggerAction: 'all',
				selectOnFocus: false,
				width: 150,
				listeners: {
					select: function(combo, record, index) {
						store.baseParams.cloudLocation = combo.getValue();
						store.load();
					},
					added: function() {
						this.setValue(this.store.getAt(0).get('id'));
						store.baseParams.cloudLocation = this.store.getAt(0).get('id');
					}
				}
			}),
			'-',
			{
				itemId: 'show_all',
				xtype: 'checkbox',
				boxLabel: 'Show all security groups',
				style: 'margin: 0px',
				listeners: {
					check: function(item, checked) {
						store.baseParams.showAll = checked ? 'true' : 'false';
						store.load();
					}
				}
			}
		],

    	rowOptionsMenu: [
			{ itemId: "option.edit", text:'Edit',
				menuHandler: function(item) {
					document.location = moduleParams['editUrl'] + '/' + item.currentRecordData.name + '/edit?cloudLocation=' + store.baseParams.cloudLocation;
				}
			}
     	],

		listViewOptions: {
			emptyText: "No security groups found",
			columns: [
				{ header: "Name", width: 70, dataIndex: 'name', sortable: true, hidden: 'no' },
				{ header: "Description", width: 50, dataIndex: 'description', sortable: false, hidden: 'no' }
			]
		},

		withSelected: {
			menu: [{
				text: 'Delete',
				iconCls: 'scalr-menu-icon-delete',
				request: {
					confirmBox: {
						type: 'delete',
						msg: 'Remove selected security group(s)?'
					},
					processBox: {
						type: 'delete',
						msg: 'Removing selected security group(s)'
					},
					url: moduleParams['removeUrl'],
					dataHandler: function (records) {
						var groups = [];
						for (var i = 0; i < records.length; i++) {
							groups[groups.length] = records[i].get('name');
						}
						return { groups: Ext.encode(groups), cloudLocation: store.baseParams.cloudLocation };
					},
					success: function (data) {
						if (Ext.isArray(data.successMessages)) {
							for (i = 0; i < data.successMessages.length; i++)
								Scalr.message.Success(data.successMessages[i]);
						}

						if (Ext.isArray(data.errorMessages)) {
							for (i = 0; i < data.errorMessages.length; i++)
								Scalr.message.Error(data.errorMessages[i]);
						}
					}
				}
			}]
		}
	});
});
