<?
    define("NO_TEMPLATES", true);

    function GetCustomVariables($template)
	{
		$text = preg_replace('/(\\\%)/si', '$$scalr$$', $template);
		preg_match_all("/\%([^\%\s]+)\%/si", $text, $matches);
		return $matches[1];
	}

    try
    {
		require(dirname(__FILE__)."/../src/prepend.inc.php");

	    header('Pragma: private');
		header('Cache-control: private, must-revalidate');

		//header("Content-type: text/javascript");

		if ($req_list == 'scaling_metrics') {
			$dbmetrics = $db->Execute("SELECT * FROM scaling_metrics WHERE env_id=0 OR env_id=?",
				array(Scalr_Session::getInstance()->getEnvironmentId())
			);

			$metrics = array();
			while ($metric = $dbmetrics->FetchRow())
			{
				$metrics[] = array(
					'id'	=> $metric['id'],
					'name'	=> $metric['name'],
					'alias'	=> $metric['alias']
				);
			}

			$result = array(
				'metrics' => $metrics
			);
		}
		elseif ($req_list == 'scripting') {
			$filter_sql .= " AND (";
			// Show shared roles
			$filter_sql .= " origin='".SCRIPT_ORIGIN_TYPE::SHARED."'";

			// Show custom roles
			$filter_sql .= " OR (origin='".SCRIPT_ORIGIN_TYPE::CUSTOM."' AND clientid='".Scalr_Session::getInstance()->getClientId()."')";

			//Show approved contributed roles
			$filter_sql .= " OR (origin='".SCRIPT_ORIGIN_TYPE::USER_CONTRIBUTED."' AND (approval_state='".APPROVAL_STATE::APPROVED."' OR clientid='".Scalr_Session::getInstance()->getClientId()."'))";
			$filter_sql .= ")";

	    	$sql = "select * from scripts WHERE 1=1 {$filter_sql}";

	    	$scripts = $db->Execute($sql);
	    	$scriptsList = array();
	    	while ($script = $scripts->FetchRow())
	    	{
		    	$dbversions = $db->Execute("SELECT * FROM script_revisions WHERE scriptid=? AND (approval_state=? OR (SELECT clientid FROM scripts WHERE scripts.id=script_revisions.scriptid) = '".Scalr_Session::getInstance()->getClientId()."')",
		        	array($script['id'], APPROVAL_STATE::APPROVED)
		        );
		        $versions = array();
		        while ($version = $dbversions->FetchRow())
		        {
		        	$vars = GetCustomVariables($version["script"]);
				    $data = array();
				    foreach ($vars as $var)
				    {
				    	if (!in_array($var, array_keys(CONFIG::getScriptingBuiltinVariables())))
				    		$data[$var] = ucwords(str_replace("_", " ", $var));
				    }
				    $data = json_encode($data);

		        	$versions[] = array("revision" => $version['revision'], "fields" => $data);
		        }

	    		$scr = array(
	    			'id'			=> $script['id'],
	    			'name'			=> $script['name'],
	    			'description'	=> $script['description'],
	    			'issync'		=> $script['issync'],
	    			'timeout'		=> ($script['issync'] == 1) ? CONFIG::$SYNCHRONOUS_SCRIPT_TIMEOUT : CONFIG::$ASYNCHRONOUS_SCRIPT_TIMEOUT,
	    			'revisions'		=> $versions
	    		);

	    		$scriptsList[] = $scr;
	    	}

			$result = array(
				'scripts'		=> $scriptsList,
				'events'		=> array(
					array(EVENT_TYPE::HOST_UP, EVENT_TYPE::GetEventDescription(EVENT_TYPE::HOST_UP)),
					array(EVENT_TYPE::HOST_INIT, EVENT_TYPE::GetEventDescription(EVENT_TYPE::HOST_INIT)),
					array(EVENT_TYPE::HOST_DOWN, EVENT_TYPE::GetEventDescription(EVENT_TYPE::HOST_DOWN)),
					array(EVENT_TYPE::REBOOT_COMPLETE, EVENT_TYPE::GetEventDescription(EVENT_TYPE::REBOOT_COMPLETE)),
					array(EVENT_TYPE::INSTANCE_IP_ADDRESS_CHANGED, EVENT_TYPE::GetEventDescription(EVENT_TYPE::INSTANCE_IP_ADDRESS_CHANGED)),
					array(EVENT_TYPE::NEW_MYSQL_MASTER, EVENT_TYPE::GetEventDescription(EVENT_TYPE::NEW_MYSQL_MASTER)),
					array(EVENT_TYPE::EBS_VOLUME_MOUNTED, EVENT_TYPE::GetEventDescription(EVENT_TYPE::EBS_VOLUME_MOUNTED)),
					array(EVENT_TYPE::BEFORE_INSTANCE_LAUNCH, EVENT_TYPE::GetEventDescription(EVENT_TYPE::BEFORE_INSTANCE_LAUNCH)),
					array(EVENT_TYPE::BEFORE_HOST_TERMINATE, EVENT_TYPE::GetEventDescription(EVENT_TYPE::BEFORE_HOST_TERMINATE)),
					array(EVENT_TYPE::DNS_ZONE_UPDATED, EVENT_TYPE::GetEventDescription(EVENT_TYPE::DNS_ZONE_UPDATED)),
					array(EVENT_TYPE::EBS_VOLUME_ATTACHED, EVENT_TYPE::GetEventDescription(EVENT_TYPE::EBS_VOLUME_ATTACHED))
				)
			);
		}
		elseif ($req_list == 'roles')
		{
			$roles = array();

			$e_platforms = Scalr_Session::getInstance()->getEnvironment()->getEnabledPlatforms();
			$platforms = array();
			$l_platforms = SERVER_PLATFORMS::GetList();
			foreach ($e_platforms as $platform)
				$platforms[$platform] = $l_platforms[$platform];
			
		    $roles_sql = "SELECT id FROM roles WHERE (env_id = 0 OR env_id=?) AND id IN (SELECT role_id FROM role_images WHERE platform IN ('".implode("','", array_keys($platforms))."'))";
			$args[] = Scalr_Session::getInstance()->getEnvironmentId();
			
			//following lines added by dbao
           		Logger::getLogger('farm-builder-role-list')->INFO($roles_sql);

			$dbroles = $db->Execute($roles_sql, $args);
			while ($role = $dbroles->FetchRow())
			{
				if ($db->GetOne("SELECT id FROM roles_queue WHERE role_id=?", array($role['id'])))
					continue;
				
				$dbRole = DBRole::loadById($role['id']);

		        $role_platforms = $dbRole->getPlatforms();
		        $role_locations = array();
		        foreach ($role_platforms as $platform)
		        	$role_locations[$platform] = $dbRole->getCloudLocations($platform);

		        $roles[] = array(
		        	'role_id'				=> $dbRole->id,
		        	'arch'					=> $dbRole->architecture,
		        	'group'					=> ROLE_GROUPS::GetConstByBehavior($dbRole->getBehaviors()),
		        	'name'					=> $dbRole->name,
		        	'generation'			=> $dbRole->generation,
		        	'behaviors'				=> implode(",", $dbRole->getBehaviors()),
		        	'origin'				=> $dbRole->origin,
		        	'isstable'				=> (bool)$dbRole->isStable,
		        	'platforms'				=> implode(",", $role_platforms),
		        	'locations'				=> $role_locations,
		        	'os'					=> $dbRole->os == 'Unknown' ? 'Unknown OS' : $dbRole->os,
		        	'tags'					=> $dbRole->getTags()
		        );
			}

			$result = array(
				'roles'			=> $roles
			);
		}
		else
		{
			$farm_roles = array();

			if ($req_farmid)
			{
				try
				{
					$dbFarm = DBFarm::LoadByID($req_farmid);
					if (!Scalr_Session::getInstance()->getAuthToken()->hasAccessEnvironment($dbFarm->EnvID))
						throw new Exception("No access");

					foreach ($dbFarm->GetFarmRoles() as $dbFarmRole)
					{
						$scripts = $db->GetAll("SELECT farm_role_scripts.*, scripts.name FROM farm_role_scripts
						INNER JOIN scripts ON scripts.id = farm_role_scripts.scriptid
						WHERE farm_roleid=? AND issystem='1'",
							array($dbFarmRole->ID)
						);
						$scripts_object = array();
						foreach ($scripts as $script)
						{
							$scripts_object[] = array(
								'script_id'		=> $script['scriptid'],
								'script'		=> $script['name'],
								'params'		=> unserialize($script['params']),
								'target'		=> $script['target'],
								'version'		=> $script['version'],
								'timeout'		=> $script['timeout'],
								'issync'		=> $script['issync'],
								'order_index'	=> $script['order_index'],
								'event' 		=> $script['event_name'],
								'order_index'	=> $script['order_index']
							);
						}

						$scalingManager = new Scalr_Scaling_Manager($dbFarmRole);
						$scaling = array();
						foreach ($scalingManager->getFarmRoleMetrics() as $farmRoleMetric)
							$scaling[$farmRoleMetric->metricId] = $farmRoleMetric->getSettings();

						$dbPresets = $db->GetAll("SELECT * FROM farm_role_service_config_presets WHERE farm_roleid=?", array($dbFarmRole->ID));
						$presets = array();
						foreach ($dbPresets as $preset)
							$presets[$preset['behavior']] = $preset['preset_id'];

						$farm_role = array(
				        	'role_id'		=> $dbFarmRole->RoleID,
							'platform'		=> $dbFarmRole->Platform,
							'generation'	=> $dbFarmRole->GetRoleObject()->generation,
							'arch'			=> $dbFarmRole->GetRoleObject()->architecture,
							'group'			=> ROLE_GROUPS::GetConstByBehavior($dbFarmRole->GetRoleObject()->getBehaviors()),
				        	'name'			=> $dbFarmRole->GetRoleObject()->name,
				        	'behaviors'		=> implode(",", $dbFarmRole->GetRoleObject()->getBehaviors()),
			        		'scripting'		=> $scripts_object,
			        		'settings'		=> $dbFarmRole->GetAllSettings(),
							'cloud_location'=> $dbFarmRole->GetSetting(DBFarmRole::SETTING_CLOUD_LOCATION),
			        		'launch_index'	=> (int)$dbFarmRole->LaunchIndex,
							'scaling'		=> $scaling,
							'config_presets'=> $presets,
							'tags'			=> $dbFarmRole->GetRoleObject()->getTags()
		        		);

						array_push($farm_roles, $farm_role);
					}

					$farm = array(
						'name' 				=> $dbFarm->Name,
						'description'		=> $dbFarm->Comments,
						'roles_launch_order'=> $dbFarm->RolesLaunchOrder
					);
				}
				catch (Exception $e)
				{
					var_dump($e->getMessage());
				}
			}

			$result = array('farm_roles' => $farm_roles, 'farm' => $farm);
		}
    }
    catch(Exception $e)
    {
    	var_dump($e->getMessage());
    }

    $result = json_encode($result);
    header("Content-length: ".strlen($result));
    print $result;
    exit();
?>
