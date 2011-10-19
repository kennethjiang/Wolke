{
	create: function (loadParams, moduleParams) {
		var store = new Scalr.data.Store({
			reader: new Scalr.data.JsonReader({
				id: 'reservedInstancesOfferingId',
				fields: [
					'reservedInstancesOfferingId', 'instanceType', 'availabilityZone', 'duration',
					'fixedPrice', 'usagePrice', 'productDescription'
				]
			}),
			remoteSort: true,
			url: '/tools/aws/ec2/reserved/xListOfferings/'
		});

		return new Scalr.Viewers.ListView({
			title: 'Tools &raquo; Amazon Web Services &raquo; EC2 &raquo; Reserved Instances Offerings',
			scalrOptions: {
				'reload': false,
				'maximize': 'all'
			},
			enableFilter: false,

			store: store,
			stateId: 'listview-tools-aws-ec2-reserved-offerings-view',

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

			rowOptionsMenu: [{
				text:'Purchase',
				request: {
					confirmBox: {
						type: 'action',
						msg: 'Are you sure want to purchase offering "{reservedInstancesOfferingId}" ?'
					},
					processBox: {
						type: 'action'
					},
					url: '/tools/aws/ec2/reserved/xPurchaseReservedOffering',
					dataHandler: function (record) {
						return {
							offeringId: record.get('reservedInstancesOfferingId'),
							cloudLocation: store.baseParams.cloudLocation
						};
					},
					success: function () {
						Scalr.Message.Success(_('Reserved instances offering successfully purchased.'));
					}
				}
			}],

			listViewOptions: {
				emptyText: 'No reserved instances offerings found',
				columns: [
					{ header: "ID", width: 150, dataIndex: 'reservedInstancesOfferingId', sortable: false, hidden: 'no' },
					{ header: "Type", width: 70, dataIndex: 'instanceType', sortable: false, hidden: 'no' },
					{ header: "Placement", width: 70, dataIndex: 'availabilityZone', sortable: false, hidden: 'no' },
					{ header: "Duration", width: 50, dataIndex: 'duration',  hidden: 'no', tpl: '{duration} <tpl if="duration == 1">year</tpl><tpl if="duration != 1">years</tpl>', sortable: false, align:'center'},
					{ header: "Usage Price", width: 50, dataIndex: 'usagePrice',  hidden: 'no', tpl: '${usagePrice}', sortable: false, align:'center'},
					{ header: "Fixed Price", width: 50, dataIndex: 'fixedPrice',  hidden: 'no', tpl: '${fixedPrice}', sortable: false, align:'center'},
					{ header: "Description", width: 70, dataIndex: 'productDescription', sortable: false, hidden: 'no' }
				]
			}
		});
	}
}
