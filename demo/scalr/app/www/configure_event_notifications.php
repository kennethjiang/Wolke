<?
    require("src/prepend.inc.php"); 
    
    if (Scalr_Session::getInstance()->getAuthToken()->hasAccess(Scalr_AuthToken::SCALR_ADMIN))
        $farminfo = $db->GetRow("SELECT * FROM farms WHERE id=?", array($req_farmid));
    else 
        $farminfo = $db->GetRow("SELECT * FROM farms WHERE id=? AND env_id=?", 
        	array($req_farmid, Scalr_Session::getInstance()->getEnvironmentId())
        );

    if (!$farminfo)
        UI::Redirect("/#/farms/view");

    if ($_POST)
    {
    	// Save observer settings
    	foreach ($post_settings as $observer_name => $observer_settings)
    	{
    	    $farm_observer_id = $db->GetOne("SELECT * FROM farm_event_observers 
				WHERE farmid=? AND event_observer_name=?",
				array($farminfo['id'], $observer_name)
			);
			
			if (!$farm_observer_id)
			{
				$db->Execute("INSERT INTO farm_event_observers SET farmid=?, event_observer_name=?",
					array($farminfo['id'], $observer_name)
				);	
				$farm_observer_id = $db->Insert_ID();
			}
			
			// Remove old settings
			$db->Execute("DELETE FROM farm_event_observers_config WHERE observerid = ?", 
				array($farm_observer_id)
			);
			
			// Store new settings
			foreach ($observer_settings as $key=>$value)
				$db->Execute("INSERT INTO farm_event_observers_config SET
					`key` =?,
					`value` = ?,
					`observerid` = ?
				", array($key, $value, $farm_observer_id));
    	}
    	
    	//
    	// We must reconfigure event daemon
    	//
    	$db->Execute("REPLACE INTO client_settings SET `clientid`=?, `key`=?, `value`=?",
    		array($farminfo['clientid'], 'reconfigure_event_daemon', '1')
    	);
    	
    	$okmsg = _("Notification settings successfully updated");
    	UI::Redirect("configure_event_notifications.php?farmid={$req_farmid}");
    }
    
    
	$display["farminfo"] = $farminfo;
	$display["title"] = sprintf(_("Configure notifications for %s farm"), $farminfo['name']);
	
	$observers = glob(APPPATH."/observers/class.*.php");
	foreach ($observers as $observer)
	{
		preg_match("/class\.(.*?)\.php/", basename($observer), $matches);
		$name = $matches[1];
	
		try
		{
			$reflection = new ReflectionClass("{$name}");
			if (!$reflection->implementsInterface("IDeferredEventObserver"))
				continue;
		}
		catch(Exception $e){ continue; }

		$form = $reflection->getMethod("GetConfigurationForm")->invoke(null);

		$farm_observer_id = $db->GetOne("SELECT id FROM farm_event_observers 
			WHERE farmid=? AND event_observer_name=?",
			array($farminfo['id'], $name)
		);

		// Get Configuration values
		if ($farm_observer_id)
		{
			$config_opts = $db->Execute("SELECT * FROM farm_event_observers_config 
				WHERE observerid=?", array($farm_observer_id)
			);
			
			while($config_opt = $config_opts->FetchRow())
			{
				$field = &$form->GetFieldByName($config_opt['key']);
				if ($field)
					$field->Value = $config_opt['value'];
			}
		}
		
		$display["observers"][$name] = $form;
	}
		
	require_once("src/append.inc.php");
?>