<?
	$response = array();
	
	// AJAX_REQUEST;
	$context = 6;
	
	$req_start = $req_start ? (int)$req_start : 0;
	$req_limit = $req_limit ? (int)$req_limit : 50;
	
	try
	{ 
		$enable_json = true;
		include("../../src/prepend.inc.php");
			
		Scalr_Session::getInstance()->getAuthToken()->hasAccessEx(Scalr_AuthToken::SCALR_ADMIN);
		
		if (!$get_trnid && !$get_strnid)
		   exit();

		$where = array("transactionid=".$db->qstr($get_trnid));
		if ($req_category)
			$where[] = "caller LIKE '".addslashes($req_category)."%'";
		
		if (!$req_severity)
			$req_severity = array("FATAL", "INFO", "ERROR", "WARN");
		$where_sev = array();
		foreach ($req_severity as $sev)
			$where_sev[] = $db->qstr($sev);
		$where[] = "severity IN (".join(",", $where_sev). ")";

		if ($req_query)
			$where[] = "message LIKE '%".addslashes($req_query)."%'";
		   
		if ($get_trnid && !$get_strnid)
		{
			$sql = "SELECT * FROM syslog WHERE transactionid != sub_transactionid AND ".join(" AND ", $where)." GROUP BY sub_transactionid
				UNION SELECT * FROM syslog WHERE transactionid = sub_transactionid AND ".join(" AND ", $where)." ORDER BY dtadded_time ASC, id ASC";
			
			$sql_total = "SELECT COUNT(*) FROM ($sql) AS logs";
			
			$sql = "SELECT * FROM ($sql) AS logs LIMIT {$req_start}, {$req_limit}";
			
		}
		else
		{
			$where[] = "sub_transactionid=".$db->qstr($get_strnid);
			
			$sql = "SELECT *, transactionid as sub_transactionid FROM syslog WHERE ".join(" AND ", $where)." ORDER BY dtadded_time ASC, id ASC";

			$sql_total = preg_replace("/SELECT[^F]FROM/", "SELECT COUNT(*) FROM", $sql);
			
			$sql .= " LIMIT {$req_start}, {$req_limit}";			
		}
		
		$t = $db->Execute($sql);
		$response["total"] = $db->GetOne($sql_total);
			
		$response['success'] = '';
		$response['error'] = '';
		$response['data'] = array();
		
		while ($row = $t->FetchRow())
		{
	        $row["message"] = nl2br(preg_replace("/[\n]+/", "\n", htmlentities($row["message"], ENT_QUOTES, "UTF-8")));
	        
	   	 	$response["data"][] = $row;
		}
	}
	catch(Exception $e)
	{
		$response = array("error" => $e->getMessage(), "data" => array());
	}
	
	print json_encode($response);
?>