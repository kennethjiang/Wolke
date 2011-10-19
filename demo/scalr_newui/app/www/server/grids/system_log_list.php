<?php
	$response = array();
	
	// AJAX_REQUEST;
	$context = 6;
	
	try
	{
		$enable_json = true;
		include("../../src/prepend.inc.php");
	
		Scalr_Session::getInstance()->getAuthToken()->hasAccessEx(Scalr_AuthToken::SCALR_ADMIN);
		
		Core::Load("Data/Formater");
		   
		$sql = "SELECT DISTINCT(transactionid) as transactionid FROM syslog WHERE 1=1";
	
		if ($req_farmid)
		{
			$farmid = (int)$_REQUEST["farmid"];
			$sql_where  .= " AND farmid = '{$farmid}'";
		}
			
		if ($req_severity)
		{		
			$severities = ($req_severity) ? $req_severity : explode(",", $req_severities);
						
			foreach($severities as $severity)
				$_sql[] = "severity = '{$severity}'";
							
			if (count($_sql) > 0)
				$sql_where .= " AND (".implode(" OR ", $_sql).")";
		}
		else
			$sql_where  .= " AND severity IN ('INFO','WARN','ERROR','FATAL')";
		
		if ($req_dt)
		{
			$date = strtotime($req_dt);
			$sql .= " AND TO_DAYS(dtadded) = TO_DAYS(FROM_UNIXTIME('{$date}'))";
			$display["dt"] = $req_dt;
		}
		
		if ($req_query)
		{
			$filter = mysql_escape_string($req_query);
			foreach(array("message", "transactionid") as $field)
			{
				$likes[] = "$field LIKE '%{$filter}%'";
			}
			$sql .= !stristr($sql, "WHERE") ? " WHERE " : " AND (";
			$sql .= join(" OR ", $likes);
			$sql .= ")";
		}
		
		$sql_total = "SELECT COUNT(DISTINCT transactionid) FROM syslog WHERE 1=1 " . $sql_where;
		
		$response["total"] = $db->GetOne($sql_total);
		
		$sql .= $sql_where;
		
		$sort = $req_sort ? mysql_escape_string($req_sort) : "id";
		$dir = $req_dir ? mysql_escape_string($req_dir) : "DESC";
		$sql .= " ORDER BY $sort $dir";
			
		$start = $req_start ? (int) $req_start : 0;
		$limit = $req_limit ? (int) $req_limit : 20;
		$sql .= " LIMIT $start, $limit";
		
		$response['success'] = '';
		$response['error'] = '';
		$response['data'] = array();
		
		$rows = $db->Execute($sql);
		
		while ($row = $rows->FetchRow())
		{
	        $row = $db->GetRow("SELECT id,dtadded,message,severity,transactionid,farmid FROM syslog WHERE transactionid='{$row['transactionid']}' ORDER BY id ASC");
	        
	        $meta = $db->GetRow("SELECT * FROM syslog_metadata WHERE transactionid=?", array($row['transactionid']));
	        
	        $row["warns"] = $meta["warnings"] ? $meta["warnings"] : 0;
	        $row["errors"] = $meta["errors"] ? $meta["errors"] : 0;
	        
	        $row["dtadded"] = Formater::FuzzyTimeString(strtotime($row["dtadded"]));
	        $row["action"] = stripslashes($row["message"]);
	        $row["action"] = htmlentities($row["action"], ENT_QUOTES, "UTF-8");
	        
	   	 	$response["data"][] = $row;
		}
	}
	catch(Exception $e)
	{
		$response = array("error" => $e->getMessage(), "data" => array());
	}
	
	print json_encode($response);
?>