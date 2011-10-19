{include file="inc/header.tpl"}
<script type="text/javascript" src="/js/ui-ng/data.js"></script>
<script type="text/javascript" src="/js/ui-ng/viewers/ListView.js"></script>
{literal}
<style>
	.x-grid3-cell-inner { white-space:normal !important; }
		
	.icon_bg {
		background-image: url(/images/dashboard/icon_bg.png); 
		width:74px; 
		height:93px;
	}
</style>
{/literal}   
<div>
	<div style="float:left;width:35%;">
		<div id="dashboard-acctinfo"></div>
	</div>
	<div style="width:5px;float:left;"></div>
	<div style="float:right;width:64%;">
		<div id="dashboard-ql"></div>
	</div>
</div>


<div style="clear:both;"></div>
<br />

<div id="listview-client-dashboard-view"></div>

<script type="text/javascript">
{literal}
Ext.onReady(function () {
	
	var acctinfo = [
		{/literal}
    	'<table width="100%" style="background-color:#f9faff;" cellpadding="8" cellspacing="8">',
	    	'<tr>',
				'<td width="30%">Logged in as:</td>',
				'<td>{$client.email} [<a href="profile.php">Profile</a>]</td>',
			'</tr>',
    	'</table>'
	   	{literal}
    ];

	var shortcuts = [
		{/literal}
		'<table width="100%" height="100%" style="background-color:#f9faff;" cellpadding="8" cellspacing="8">',
	    	'<tr>',
	    		/*
		    	'<td width="12%" align="center">',
					'<div onmouseout="this.style.backgroundImage = \'url(/images/dashboard/icon_bg.png)\';" onmouseover="this.style.backgroundImage = \'url(/images/dashboard/icon_bg_hover.png)\';" class="icon_bg">',
						'<img src="/images/dashboard/icons/app_wizard.png" alt="Application wizard" title="Application wizard" onclick="document.location=\'/app_wizard.php\';" style="cursor:pointer;margin:5px;" />',
					'</div>',
				'</td>',
				*/
				'<td width="12%" align="center">',
					'<div onmouseout="this.style.backgroundImage = \'url(/images/dashboard/icon_bg.png)\';" onmouseover="this.style.backgroundImage = \'url(/images/dashboard/icon_bg_hover.png)\';" class="icon_bg">',
						'<img src="/images/dashboard/icons/farms.png" alt="Farms" title="Farms" onclick="document.location=\'/farms_view.php\';" style="cursor:pointer;margin:5px;" />',
					'</div>',
				'</td>',
				'<td width="12%" align="center">',
					'<div onmouseout="this.style.backgroundImage = \'url(/images/dashboard/icon_bg.png)\';" onmouseover="this.style.backgroundImage = \'url(/images/dashboard/icon_bg_hover.png)\';" class="icon_bg">',
						'<img title="Manage roles" alt="Manage roles" onclick="document.location=\'/roles_view.php\';" src="/images/dashboard/icons/roles.png" style="margin:5px; cursor:pointer;">&nbsp;</div>',
					'</div>',
				'</td>',
				'<td width="12%" align="center">',
					'<div onmouseout="this.style.backgroundImage = \'url(/images/dashboard/icon_bg.png)\';" onmouseover="this.style.backgroundImage = \'url(/images/dashboard/icon_bg_hover.png)\';" class="icon_bg">',
						'<img title="Logs" alt="Logs" onclick="document.location=\'/logs_view.php\';" src="/images/dashboard/icons/logs.png" style="margin:5px; cursor:pointer;">&nbsp;</div>',
					'</div>',
				'</td>',
				'<td width="12%" align="center">',
					'<div onmouseout="this.style.backgroundImage = \'url(/images/dashboard/icon_bg.png)\';" onmouseover="this.style.backgroundImage = \'url(/images/dashboard/icon_bg_hover.png)\';" class="icon_bg">',
						'<img title="EBS Volumes & Snapshots" alt="EBS Volumes & Snapshots" onclick="document.location=\'/ebs_manage.php\';" src="/images/dashboard/icons/ebs.png" style="margin:5px;cursor:pointer;" />',
					'</div>',
				'</td>',
				'<td width="12%" align="center">',
					'<div onmouseout="this.style.backgroundImage = \'url(/images/dashboard/icon_bg.png)\';" onmouseover="this.style.backgroundImage = \'url(/images/dashboard/icon_bg_hover.png)\';" class="icon_bg">',
						'<img title="Manage Elastic IPs" alt="Manage Elastic IPs" onclick="document.location=\'/elastic_ips.php\';" src="/images/dashboard/icons/eip.png" style="margin:5px; cursor:pointer;">&nbsp;</div>',
					'</div>',
				'</td>',
				'<td width="12%" align="center">',
					'<div onmouseout="this.style.backgroundImage = \'url(/images/dashboard/icon_bg.png)\';" onmouseover="this.style.backgroundImage = \'url(/images/dashboard/icon_bg_hover.png)\';" class="icon_bg">',
						'<img title="Scheduler" alt="Scheduler" onclick="document.location=\'/scheduler.php\';" src="/images/dashboard/icons/scheduler.png" style="margin:5px; cursor:pointer;">&nbsp;</div>',
					'</div>',
				'</td>',
				'<td width="12%" align="center">',
					'<div onmouseout="this.style.backgroundImage = \'url(/images/dashboard/icon_bg.png)\';" onmouseover="this.style.backgroundImage = \'url(/images/dashboard/icon_bg_hover.png)\';" class="icon_bg">',
						'<img title="System settings" alt="System settings" onclick="document.location=\'/system_settings.php\';" src="/images/dashboard/icons/settings.png" style="margin:5px; cursor:pointer;">&nbsp;</div>',
					'</div>',
				'</td>',
			'</tr>',
    	'</table>'
		{literal}
	];
	
	var p3 = new Ext.Panel({
        title: 'Shortcuts',
        collapsible:false,
        renderTo: 'dashboard-ql',
        height: 140,
        bodyStyle: "background: #F9FAFF",        
       	html: shortcuts.join('')
    });
	
	var p = new Ext.Panel({
        title: 'Account information',
        collapsible:false,
        renderTo: 'dashboard-acctinfo',
        height: 140,
        bodyStyle: "background: #F9FAFF",
        html: acctinfo.join('')
    });

	var store = new Scalr.data.Store({
		reader: new Scalr.data.JsonReader({
			root: 'data',
			successProperty: 'success',
			errorProperty: 'error',
			totalProperty: 'total',
			id: 'id',
			fields: [
				'id','serverid','message','severity','time','source','farmid','servername','farm_name', 's_severity'
			]
		}),
		remoteSort: true,
		url: '/server/grids/event_log_list.php?a=1&severity[]=3&severity[]=4&severity[]=5&showLog=1'
	});
	
	Ext.apply(store.baseParams, Ext.ux.parseQueryString(window.location.href));

	var panel = new Scalr.Viewers.ListView({
		renderTo: 'listview-client-dashboard-view',
		autoRender: true,
		store: store,
		enableFilter: false,
		enablePaging: false,
		title: 'Latest errors & warnings {/literal}({$table_title_text}){literal}',

		listViewOptions: {
			emptyText: 'No errors found',
			columns: [
				{ header: "", width: 10, dataIndex: 'severity', sortable: false, align:'center', hidden: 'no', tpl:
					'<tpl if="severity == 0"><img src="/images/ui-ng/icons/log/debug.png" title="{s_severity}"></tpl>' +
					'<tpl if="severity == 2"><img src="/images/ui-ng/icons/log/info.png" title="{s_severity}"></tpl>' +
					'<tpl if="severity == 3"><img src="/images/ui-ng/icons/log/warning.png" title="{s_severity}"></tpl>' +
					'<tpl if="severity == 4"><img src="/images/ui-ng/icons/log/error.png" title="{s_severity}"></tpl>' +
					'<tpl if="severity == 5"><img src="/images/ui-ng/icons/log/fatal_error.png" title="{s_severity}"></tpl>'
				},
				{ header: "Time", width: 35, dataIndex: 'time', sortable: false, hidden: 'no' },
				{ header: "Farm", width: 25, dataIndex: 'farm_name', sortable: false, hidden: 'no', tpl:
					'<a href="farms_view.php?id={farmid}">{farm_name}</a>'
				},
				{ header: "Caller", width: 30, dataIndex: 'source', sortable: false, hidden: 'no', tpl:
					'<tpl if="servername"><a href="/servers_view.php?server_id={servername}&farmid={farmid}">{servername}</a>/{source}</tpl>' +
					'<tpl if="!servername">source</tpl>'
				},
				{ header: "Message", width: 160, dataIndex: 'message', sortable: false }
			]
		}
	});
});
{/literal}
</script>

{include file="inc/footer.tpl"}
