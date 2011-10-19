<div id="scaling.time.table"></div>
{literal}
	<script language="Javascript" type="text/javascript">

		var TSTimersGrid = null;
		var TSTimersStore = null;
		var TSTimersAddWindow = null;

		function RemoveTimePeriod(record_id)
		{
			TSTimersStore.remove(TSTimersStore.getById(record_id));
		}
		
		Ext.onReady(function(){
			var myData = [];

			function TSremoveRenderer(value, p, record){
       			return '<img onclick="RemoveTimePeriod(\''+record.id+'\');" style="cursor:pointer;" src="/images/delete_zone.gif" />';
       	    }

						
 		    // create the data store
 		    TSTimersStore = new Ext.data.SimpleStore({
 		        fields: [
 		           'start_time', 'end_time', 'week_days', 'instances_count', 'id'
 		        ]
 		    });
 		    
       		// create the Grid
       		TSTimersGrid = new Ext.grid.GridPanel({
				store: TSTimersStore,
 		     	viewConfig: { 
 		        	emptyText: "No periods defined"
 		        },
 		        columns: [
 		            {header: "Start time", width: 150, sortable: true, dataIndex: 'start_time'},
 		            {header: "End time", width: 150, sortable: true, dataIndex: 'end_time'},
 		            {header: "Week days", width: 180, sortable: true, dataIndex: 'week_days'},
 		           	{header: "Instances count", width: 180, sortable: true, dataIndex: 'instances_count'},
 		           	{id:'remove', header: "Remove", width: 60, sortable: false, renderer:TSremoveRenderer, dataIndex: 'id', align:'center'}
 		        ],
    		    stripeRows: true,
    		    autoHeight:true,
    		    style:'width:90%',
    		    enableColumnHide:false, 
    		    title:'Number of instances depending on time and day of week',
    		    bbar:[{
   			        icon: '/images/add.png', // icons can also be specified inline
   			        cls: 'x-btn-icon',
   			        tooltip: 'Add new period',
	   			    handler: function() 
	  			    {
    		    		TSTimersAddWindow.show();
	  			    }
  				}],
  				listeners: {
					'afterrender': function() {
						this.getBottomToolbar().el.dom.style.width = 'auto';
						this.getBottomToolbar().el.dom.parentNode.style.width = 'auto';
					}
				}
			});

       		TSTimersAddWindow = new Ext.Window({
                applyTo     : 'scaling.time.add_period',
                layout      : 'fit',
                width       : 390,
                height      : 280,
                closeAction :'hide',
                plain       : true,
                modal		: true,
                draggable	: false,
                items       : [
				
				new Ext.FormPanel({
				    labelWidth: 100, // label settings here cascade unless overridden
				    url:'save-form.php',
				    header:false,
				    bodyStyle:'padding:10px',
				    border: false,
				    width: 300,
				    defaults: {width: 240},
				    defaultType: 'textfield',
				
				    items: [
						new Ext.form.TimeField({
				            fieldLabel: 'Start time',
				            name: 'ts_s_time',
				            minValue: '0:15am',
				            maxValue: '23:45pm',
				            allowBlank: false
				        }),
				        new Ext.form.TimeField({
				            fieldLabel: 'End time',
				            name: 'ts_e_time',
				            minValue: '0:15am',
				            maxValue: '23:45pm',
				            allowBlank: false
				        }),
				        {
			                xtype: 'checkboxgroup',
			                fieldLabel: 'Days of week',
			                columns: 3,
			                items: [
			                    {boxLabel: 'Sun', name: 'ts_dw_Sun', width:50},
			                    {boxLabel: 'Mon', name: 'ts_dw_Mon'},
			                    {boxLabel: 'Tue', name: 'ts_dw_Tue'},
			                    {boxLabel: 'Wed', name: 'ts_dw_Wed'},
			                    {boxLabel: 'Thu', name: 'ts_dw_Thu'},
			                    {boxLabel: 'Fri', name: 'ts_dw_Fri'},
			                    {boxLabel: 'Sat', name: 'ts_dw_Sat'}
			                ]
			            },
			            {
		                    xtype:'numberfield',
		                    fieldLabel: 'Instances count',
		                    name: 'ts_instances_count',
		                    anchor:'95%',
		                    allowNegative: false,
		                    allowDecimals: false,
		                    allowBlank: false
		                }
				    ],
				
				    buttons: [{
				        text: 'Add',
					    handler:function(button)
					    {
							var frm = button.findParentByType('form').getForm()
							var form_values = frm.getValues();
							
							if (!frm.isValid())
								return false;
							
							var week_days_list = '';
							var i = 0;
							
							for (k in form_values)
							{
								if (k.indexOf('ts_dw_') != -1 && form_values[k] == 'on')
								{
									week_days_list += k.replace('ts_dw_','')+', ';
									i++;
								}
							}													
							
							if (i == 0)
							{
								Ext.MessageBox.show({
						           title: 'Error',
						           msg: 'You should select at least one week day',
						           buttons: Ext.MessageBox.OK,
						           animEl: 'mb9',
						           icon: Ext.MessageBox.ERROR
						        });
						        
						        return false;
							}
							else
								week_days_list = week_days_list.substr(0, week_days_list.length-2);

							var int_s_time = parseInt(form_values.ts_s_time.replace(/\D/g,''));
							var int_e_time = parseInt(form_values.ts_e_time.replace(/\D/g,''));

							if (form_values.ts_s_time.indexOf('AM') && int_s_time >= 1200)
								int_s_time = int_s_time-1200;

							if (form_values.ts_e_time.indexOf('AM') && int_e_time >= 1200)
								int_e_time = int_e_time-1200;
							
							if (form_values.ts_s_time.indexOf('PM') != -1)
								int_s_time = int_s_time+1200;

							if (form_values.ts_e_time.indexOf('PM') != -1)
								int_e_time = int_e_time+1200;
							
							if (int_e_time <= int_s_time)
							{
								Ext.MessageBox.show({
						           title: 'Error',
						           msg: 'End time value must be greater than Start time value',
						           buttons: Ext.MessageBox.OK,
						           animEl: 'mb9',
						           icon: Ext.MessageBox.ERROR
						        });
						        
						        return false;
							}

							var record_id = int_s_time+':'+int_e_time+':'+week_days_list+':'+form_values.ts_instances_count;
							
							var recordData = {
								start_time:form_values.ts_s_time,
								end_time:form_values.ts_e_time,
								instances_count:form_values.ts_instances_count,
								week_days:week_days_list,
								id:record_id
          	        		};

							var list_exists = false;
							var list_exists_overlap = false;
							var week_days_list_array = week_days_list.split(", ");

							
							TSTimersStore.each(function(item, index, length) {

								if (item.data.id == recordData.id)
								{
									Ext.MessageBox.show({
							           title: 'Error',
							           msg: 'Such record already exists',
							           buttons: Ext.MessageBox.OK,
							           animEl: 'mb9',
							           icon: Ext.MessageBox.ERROR
							        });
	
									list_exists = true;
									
							        return false;
								}

								var chunks = item.data.id.split(':');
								var s_time = chunks[0];
								var e_time = chunks[1];
								if (
									(int_s_time >= s_time && int_s_time <= e_time) ||
									(int_e_time >= s_time && int_e_time <= e_time)
								)
								{
									var week_days_list_array_item = (chunks[2]).split(", ");
									for (var ii = 0; ii < week_days_list_array_item.length; ii++)
									{
										for (var kk = 0; kk < week_days_list_array.length; kk++)
										{
											if (week_days_list_array[kk] == week_days_list_array_item[ii] && week_days_list_array[kk] != '')
											{
												list_exists_overlap = "Period "+week_days_list+" "+form_values.ts_s_time+" - "+form_values.ts_e_time+" overlaps with period "+chunks[2]+" "+item.data.start_time+" - "+item.data.end_time;
												return true;
											}
										}	
									}
								}
									
							}, this);

							if (!list_exists && !list_exists_overlap)
							{
								TSTimersStore.add(new TSTimersStore.reader.recordType(recordData));
								frm.reset();
								TSTimersAddWindow.hide();
							}
							else
							{
								Ext.MessageBox.show({
						           title: 'Error',
						           msg: (!list_exists_overlap) ? 'Such record already exists' : list_exists_overlap,
						           buttons: Ext.MessageBox.OK,
						           animEl: 'mb9',
						           icon: Ext.MessageBox.ERROR
						        });
							}
					    }
				    },{
				        text: 'Cancel',
				        handler:function()
				        {
			    			TSTimersAddWindow.hide();
				        }
				    }]
				})	
                

                ],
                title: 'Add new time scaling period'
            });
       		
       		TSTimersGrid.render('scaling.time.table');
       		TSTimersStore.loadData(myData);	         

       		/**
       		TODO: Remove this workaround.
       		*/
	       	(function (){
	       		var recordData = {
    				start_time:1,
    				end_time:1,
    				instances_count:1,
    				week_days:1,
    				id:'fix_id'
           		};
           		var t = new TSTimersStore.reader.recordType(recordData);
           		TSTimersStore.add(t);
	       		TSTimersStore.remove(TSTimersStore.getById(t.id));
	       	}).defer(20);
      	});
	</script>
{/literal}