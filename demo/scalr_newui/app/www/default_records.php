<? 
	require("src/prepend.inc.php"); 
	
	$display["title"] = _("Settings&nbsp;&raquo;&nbsp;Default DNS records");
	
	if ($_POST) 
	{
		//var_dump($_POST);
		
		$db->Execute("DELETE FROM default_records WHERE clientid=?", array(Scalr_Session::getInstance()->getClientId()));
		
		$records = array();
		foreach ($post_records as $r)
			if ($r['name'] || $r['value'])
			{
				$r['value'] = str_replace('%hostname%', 'fakedomainname.qq', $r['value']);
				$r['name'] = str_replace('%hostname%', 'fakedomainname.qq', $r['name']);
				$r['id'] = rand(10000,99999);
				array_push($records, $r);
			}
				
		$recordsValidation = Scalr_Net_Dns_Zone::validateRecords($records);
		if ($recordsValidation === true)
		{
			foreach ($records as $record)
			{
				if (!$record["ttl"])
					$record["ttl"] = 14400;
				
				$record['value'] = str_replace('fakedomainname.qq', '%hostname%', $record['value']);
				$record['name'] = str_replace('fakedomainname.qq', '%hostname%', $record['name']);
					
				$db->Execute("INSERT INTO default_records SET clientid=?, `type`=?, `ttl`=?, `priority`=?, `value`=?, `name`=?", 
					array(Scalr_Session::getInstance()->getClientId(), $record["type"], (int)$record["ttl"], (int)$record["priority"], $record["value"], $record["name"])
				);
			}
		}
		else
			$err = $recordsValidation;
		
		if (count($err) == 0)
		{
			$okmsg = _("Default records successfully changed");
			CoreUtils::Redirect("default_records.php");
		}
		else
			$display["records"] = $records;
	}
	
	if (!$display["records"])
	{
		if (Scalr_Session::getInstance()->getClientId() == 0)	
			$display["records"] = $db->GetAll("SELECT * FROM default_records WHERE clientid='0' ORDER BY `type`");
		else
			$display["records"] = $db->GetAll("SELECT * FROM default_records WHERE clientid=? ORDER BY `type`", array(Scalr_Session::getInstance()->getClientId()));
	}
		
	$display["add"] = array(1, 2, 3, 4, 5);
		
	$display["help"] = _("Default DNS records will be automatically added to all your new Application DNS Zones - If you want to edit existing zone, you should go to Applications -> View and choose the 'Edit DNS zone' option. You can use the %hostname% tag, which will be replaced with full zone hostname.");
	
	require("src/append.inc.php"); 
?>