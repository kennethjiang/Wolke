<?php
	$response = array();
	
	// AJAX_REQUEST;
	$context = 6;
	
	try
	{
		$enable_json = true;
		include("../../src/prepend.inc.php");
		
		$sql = "SELECT subscriptions.*, COUNT(payments.id) as payments FROM subscriptions 
				INNER JOIN payments ON payments.subscriptionid = subscriptions.subscriptionid 
				WHERE subscriptions.id > 0";
	
		//
		// If specified user id
		//
		if ($get_clientid)
		{
			$clientid = (int)$get_clientid;
			$sql .= " AND subscriptions.clientid='{$clientid}'";
		}
	
		if ($req_subscrid)
			$sql .= " AND subscriptions.subscriptionid=".$db->qstr($req_subscrid);

		if ($req_status)
			$sql .= " AND subscriptions.status=".$db->qstr($req_status);
			
	    if ($req_query)
		{
			$filter = mysql_escape_string($req_query);
			foreach(array("subscriptions.subscriptionid", "subscriptions.clientid", "subscriptions.dtstart") as $field)
			{
				$likes[] = "$field LIKE '%{$filter}%'";
			}
			$sql .= !stristr($sql, "WHERE") ? " WHERE " : " AND (";
			$sql .= join(" OR ", $likes);
			$sql .= ")";
		}
		
		$sql .= ' GROUP BY subscriptions.subscriptionid';
		
		if ($req_sort != 'payments' && $req_sort)
			$req_sort = "subscriptions.{$req_sort}";
		
		$sort = $req_sort ? mysql_escape_string($req_sort) : "subscriptions.id";
		$dir = $req_dir ? mysql_escape_string($req_dir) : "ASC";
		$sql .= " ORDER BY $sort $dir";
			
		$response["total"] = $db->Execute($sql)->RecordCount();
		
		$start = $req_start ? (int) $req_start : 0;
		$limit = $req_limit ? (int) $req_limit : 20;
		$sql .= " LIMIT $start, $limit";
		
		$response["data"] = array();
		
		//
		// Rows
		//
		foreach ($db->GetAll($sql) as $row)
		{
			$row["client_email"] = $db->GetOne("SELECT email FROM clients WHERE id=?", array($row['clientid']));
			//$row["payments"] = $db->GetOne("SELECT COUNT(*) FROM payments WHERE subscriptionid=?", array($row['subscriptionid']));
			
			$response["data"][] = $row;
		}
	}
	catch(Exception $e)
	{
		$response = array("error" => $e->getMessage(), "data" => array());
	}
	
	print json_encode($response);
?>