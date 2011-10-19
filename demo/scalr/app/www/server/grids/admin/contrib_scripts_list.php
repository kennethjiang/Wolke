<?php

	$response = array();	
	// AJAX_REQUEST;
	$context = 6;
	
	try
	{
		$enable_json = true;
		include("../../../src/prepend.inc.php");	

		Scalr_Session::getInstance()->getAuthToken()->hasAccessEx(Scalr_AuthToken::SCALR_ADMIN);
		
		$sql = "select *, '' as script, script_revisions.id as id, script_revisions.dtcreated as dtcreated, script_revisions.approval_state as approval_state 
			FROM script_revisions 
			INNER JOIN scripts ON scripts.id = script_revisions.scriptid 
			WHERE 1=1 AND scripts.origin='".SCRIPT_ORIGIN_TYPE::USER_CONTRIBUTED."'
		";
	
	    if (isset($req_approval_state) && $req_approval_state != '')
	    {
	    	$approval_state = preg_replace("/[^A-Za-z0-9-]+/", "", $req_approval_state);
	    	$sql .= " AND script_revisions.approval_state='{$approval_state}'";
	    }
	    
	    if ($req_query)
		{
			$filter = mysql_escape_string($req_query);
			foreach(array("name", "description") as $field)
			{
				$likes[] = "$field LIKE '%{$filter}%'";
			}
			$sql .= !stristr($sql, "WHERE") ? " WHERE " : " AND (";
			$sql .= join(" OR ", $likes);
			$sql .= ")";
		}
		
		$sort = $req_sort ? mysql_escape_string($req_sort) : "dtcreated";
		$dir = $req_dir ? mysql_escape_string($req_dir) : "ASC";
		$sql .= " ORDER BY $sort $dir";
			
		$response["total"] = $db->Execute($sql)->RecordCount();
		
		$start = $req_start ? (int) $req_start : 0;
		$limit = $req_limit ? (int) $req_limit : 20;
		$sql .= " LIMIT $start, $limit";
	    
		$scripts = $db->Execute($sql);
		while ($row = $scripts->FetchRow())
		{
			$row['client_email'] = $db->GetOne("SELECT email FROM clients WHERE id=?", array($row['clientid']));
			$response['data'][] = $row;
		}		
	}
	catch(Exception $e)
	{
		$response = array("error" => $e->getMessage(), "data" => array());
	}
	
	print json_encode($response);
?>