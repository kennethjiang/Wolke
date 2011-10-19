{
	create: function (loadParams, moduleParams) {
		var store = new Scalr.data.Store({
			reader: new Scalr.data.JsonReader({
				id: 'reservedInstancesId',
				fields: [
					'reservedInstancesId', 'instanceType', 'availabilityZone', 'duration',
					'usagePrice', 'fixedPrice', 'instanceCount', 'productDescription', 'state'
				]
			}),
			remoteSort: true,
			url: '/tools/aws/ec2/reserved/xListInstances/'
		});

		return new Scalr.Viewers.ListView({
			title: 'Tools &raquo; Amazon Web Services &raquo; EC2 &raquo; Reserved Instances',
			scalrOptions: {
				'reload': false,
				'maximize': 'all'
			},
			enableFilter: false,

			store: store,
			stateId: 'listview-tools-aws-ec2-reserved-instances-view',

			tbar: [ 'Location:',
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
				})
			],

			listViewOptions: {
				emptyText: 'No reserved instances found',
				columns: [
					{ header: "ID", width: 115, dataIndex: 'reservedInstancesId', sortable: true, hidden: 'no' },
					{ header: "Type", width: 35, dataIndex: 'instanceType', sortable: false, hidden: 'no' },
					{ header: "Placement", width: 35, dataIndex: 'availabilityZone', sortable: true, hidden: 'no' },
					{ header: "Duration", width: 35, dataIndex: 'duration', sortable: false, align:'center', hidden: 'no', tpl:
						'<tpl if="duration == 1">{duration} year</tpl><tpl if="duration != 1">{duration} years</tpl>'
					},
					{ header: "Usage Price", width: 40, dataIndex: 'usagePrice', sortable: false, align:'center', hidden: 'no', tpl: '${usagePrice}' },
					{ header: "Fixed Price", width: 35, dataIndex: 'fixedPrice', sortable: false, align:'center', hidden: 'no', tpl: '${fixedPrice}' },
					{ header: "Count", width: 25, dataIndex: 'instanceCount', sortable: false, align:'center', hidden: 'no' },
					{ header: "Description", width: 50, dataIndex: 'productDescription', sortable: false, hidden: 'no' },
					{ header: "State", width: 50, dataIndex: 'state', sortable: false, hidden: 'no' }
				]
			}
		});
	}
}
