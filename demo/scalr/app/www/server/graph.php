<?php
	include dirname(__FILE__)."/../../src/prepend.inc.php";
	
//	$cfg["db"]['name'] = 'scalr_graphs';
	
//	$db = Core::GetDBInstance($cfg["db"], true);
	
	header("Content-type: text/xml");
	
	print "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
	print "<rows>";
	
	if ($req_action == 'get_clients_xml')
	{
	    $clients = $db->GetAll("SELECT COUNT(*) as regs, DATE_FORMAT(dtadded, '%b %Y') as date FROM clients WHERE dtadded IS NOT NULL AND DATE_FORMAT(dtadded, '%b %Y') != 'Oct 2008' GROUP BY DATE_FORMAT(dtadded, '%m-%y') ORDER BY dtadded");
		foreach ($clients as &$client)
		{
			$client['orders'] = $db->GetOne("SELECT COUNT(*) FROM subscriptions WHERE DATE_FORMAT(dtstart, '%b %Y') = '{$client['date']}'");
			
			$client['unsubs'] = $db->GetOne("SELECT COUNT(*) FROM subscriptions WHERE status IN ('Cancelled', 'Finished') AND DATE_FORMAT(DATE_ADD((SELECT MAX(dtpaid) FROM payments WHERE payments.subscriptionid = subscriptions.subscriptionid), INTERVAL 1 MONTH), '%b %Y') = '{$client['date']}' AND (SELECT isactive FROM clients WHERE clients.id = subscriptions.clientid) = '0'");
			
			print "<row date='{$client['date']}'>
				<regs>{$client['regs']}</regs>
				<orders>{$client['orders']}</orders>
				<unsubs>{$client['unsubs']}</unsubs>
				<date>{$client['date']}</date>
			</row>";
		}
	}
	elseif ($req_action == 'instances_usage')
	{
		$stats = $db->GetAll("SELECT COUNT(*) as cnt, ROUND(SUM(m1_small)/60/60) as m1_small, ROUND(SUM(m1_large)/60/60) as m1_large, ROUND(SUM(m1_xlarge)/60/60) as m1_xlarge, ROUND(SUM(c1_medium)/60/60) as c1_medium, ROUND(SUM(c1_xlarge)/60/60) as c1_xlarge, `month`, `year` FROM farm_stats GROUP BY `month`, year ORDER BY year ASC, month ASC");
		foreach ($stats as $stat)
		{
			$date = date("M Y", mktime(0,0,0, $stat['month'], 1, $stat['year']));
			
			print "<row date='{$date}'>
				<m1_small>".round(($stat['m1_small']/$stat['cnt']))."</m1_small>
				<m1_large>".round(($stat['m1_large']/$stat['cnt']))."</m1_large>
				<m1_xlarge>".round(($stat['m1_xlarge']/$stat['cnt']))."</m1_xlarge>
				<c1_medium>".round(($stat['c1_medium']/$stat['cnt']))."</c1_medium>
				<c1_xlarge>".round(($stat['c1_xlarge']/$stat['cnt']))."</c1_xlarge>
			</row>";	
		}
	}
	elseif ($req_action == 'totals')
	{
		$running_instances = $db->GetOne("SELECT COUNT(*) FROM servers");
		$total_instances = $db->GetOne("SELECT MAX(id) FROM servers");
		
		print "<running>{$running_instances}</running>";
		print "<total>{$total_instances}</total>";
	}
	elseif ($req_action == 'countries')
	{
		$countries = $db->GetAll("SELECT COUNT(*) as num, country FROM clients GROUP BY country ORDER BY COUNT(*) DESC");
		foreach ($countries as $v)
		{
			print "<row count='{$v['num']}'>
				<count>{$v['num']}</count>
				<name>{$v['country']}</name>
			</row>";
		}
	}
	elseif ($req_action == 'instances')
	{
		$farms = $db->GetAll("SELECT * FROM farms WHERE status='1'");
		$res = array();
		foreach ($farms as &$farm)
		{
			$res[$farm['clientid']] = $res[$farm['clientid']] + $db->GetOne("SELECT COUNT(*) FROM servers WHERE farm_id='{$farm['id']}'");
		}
		
		$result = array();
		foreach ($res as $v)
		{
			if ($v != 0)
				$result[$v]++;
		}
		ksort($result);
		
		foreach ($result as $k => $v)
		{
			print "<row clients='{$v}'>
				<count>{$k}</count>
				<name>Clients with {$k} running instances</name>
			</row>";
		}
	}
	elseif ($req_action == 'farms')
	{
		$farms = $db->GetAll("SELECT COUNT(*) as farms, DATE_FORMAT(dtadded, '%b %Y') as date FROM farms GROUP BY DATE_FORMAT(dtadded, '%m-%y') ORDER BY dtadded");
		foreach ($farms as &$farm)
		{
			print "<row date='{$farm['date']}'>
				<farms>{$farm['farms']}</farms>
			</row>";
		}
	}
	elseif ($req_action == 'payments')
	{
		if (!$req_year)
			$req_year = '2009';
		
		$year = (int)$req_year;
			
		$rows = $db->GetAll("SELECT COUNT(*) as payments, DATE_FORMAT(dtpaid, '%b %Y') as date FROM payments WHERE amount='50.00' AND DATE_FORMAT(dtpaid, '%Y') = '{$year}' GROUP BY DATE_FORMAT(dtpaid, '%m-%y') ORDER BY dtpaid");
		foreach ($rows as &$row)
		{
			$p99 = (int)$db->GetOne("SELECT COUNT(*) FROM payments WHERE amount='99.00' AND DATE_FORMAT(dtpaid, '%b %Y') = '{$row['date']}'");
			$p399 = (int)$db->GetOne("SELECT COUNT(*) FROM payments WHERE amount='399.00' AND DATE_FORMAT(dtpaid, '%b %Y') = '{$row['date']}'");
			
			print "<row date='{$row['date']}'>
				<payments50>{$row['payments']}</payments50>
				<payments99>{$p99}</payments99>
				<payments399>{$p399}</payments399>
			</row>";
		}
	}
	
	print "</rows>";
	
?>