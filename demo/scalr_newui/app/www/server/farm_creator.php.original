<?
    define("NO_TEMPLATES", true);

    try
    {
		require(dirname(__FILE__)."/../src/prepend.inc.php");

		$Validator = new Validator();

	    header('Pragma: private');
		header('Cache-control: private, must-revalidate');

		//var_dump($_REQUEST);
		//exit();

		/*

		  var FarmRole = {
		  	role_id	= 1,
		  	launch_index = 1,
		  	platform = 1,
		  	cloud_location = 1,
		  	scaling_settings = {},
		  	params = [],
		  	scripts = [],
		  	settings = []
		  }

		 */
		/*
		$request = array(
			'roles'	=> array(
				array(
					'role_id' => 1,
					'launch_index' => 1,
					'platform' => 'ec2',
					'cloud_location' => 'us-east-1',
					'scaling' => array(
						'id' 	=> settings,
						'id'	=> settings
					),
					'params' 	=> array('k'=>'v'),
					'scripts'	=> array(),
					'settings' 	=> array('key1'=>'value1', 'key2'=>'value2'),
					'config_presets' => array('behavior' => preset_id || null, 'behavior2' => preset_id)
				),
				array(
					'role_id'		=> 1,
					'launch_index' => 1,
					'platform' => 'rds',
					'cloud_location' => 'us-west-1',
					'scaling_settings' => array(),
					'params' 	=> array('k'=>'v'),
					'scripts'	=> array(),
					'settings' 	=> array('key1'=>'value2', 'key2'=>'value2')
				)
			),
			'farm'	=> array(
				'id'					=> 1,
				'name'					=> 'name',
				'description'			=> 'description',
				'roles_launch_order'	=> 'roles_launch_order'
			)
		);
		*/
		$request = $_REQUEST;


		try {

			// Get User ID
	    	if (Scalr_Session::getInstance()->getAuthToken()->hasAccess(Scalr_AuthToken::SCALR_ADMIN))
		    	throw new Exception(_("You don't have permissions for this action"));

		    $Client = Client::Load(Scalr_Session::getInstance()->getClientId());

			//Validate information
			if (!$Validator->IsNotEmpty($request['farm']['name']))
				throw new Exception(_("Farm name required"));

			foreach ($request['roles'] as $role)
			{
				$role = json_decode($role, true);

				$DBRole = DBRole::loadById($role['role_id']);

				if (!$DBRole->getImageId($role['platform'], $role['cloud_location'])) {
					throw new Exception(sprintf(_("Role '%s' is not available in %s on %s"),
						$DBRole->name, $role['platform'], $role['cloud_location'])
					);
				}

				/* Validate scaling */
	            $minCount = (int)$role['settings'][DBFarmRole::SETTING_SCALING_MIN_INSTANCES];
	            if (!$minCount && $minCount != 0)
	            	$minCount = 1;

				if ($minCount < 0 || $minCount > 400)
					throw new Exception(sprintf(_("Min instances for '%s' must be a number between 1 and 400"), $DBRole->name));

				$maxCount = (int)$role['settings'][DBFarmRole::SETTING_SCALING_MAX_INSTANCES];
				if (!$maxCount)
					$maxCount = 1;

				if ($maxCount < 1 || $maxCount > 400)
					throw new Exception(sprintf(_("Max instances for '%s' must be a number between 1 and 400"), $DBRole->name));

				if ($maxCount < $minCount)
					throw new Exception(sprintf(_("Max instances should be greater or equal than Min instances for role '%s'"), $DBRole->name));

				if (isset($role['settings'][DBFarmRole::SETTING_SCALING_POLLING_INTERVAL]))
					$polling_interval = (int)$role['settings'][DBFarmRole::SETTING_SCALING_POLLING_INTERVAL];
				else
					$polling_interval = 2;

				if ($polling_interval < 1 || $polling_interval > 50)
					throw new Exception(sprintf(_("Polling interval for role '%s' must be a number between 1 and 50"), $DBRole->name));

	            /** Validate platform specified settings **/
	            switch($role['platform'])
				{
					case SERVER_PLATFORMS::EC2:
						Modules_Platforms_Ec2_Helpers_Ebs::farmValidateRoleSettings($role['settings'], $DBRole->name);
						Modules_Platforms_Ec2_Helpers_Eip::farmValidateRoleSettings($role['settings'], $DBRole->name);
						Modules_Platforms_Ec2_Helpers_Elb::farmValidateRoleSettings($role['settings'], $DBRole->name);

						if ($DBRole->hasBehavior(ROLE_BEHAVIORS::MYSQL)) {
							if ($role['settings'][DBFarmRole::SETTING_MYSQL_DATA_STORAGE_ENGINE] == MYSQL_STORAGE_ENGINE::EBS)
							{
								if ($role['settings'][DBFarmRole::SETTING_AWS_AVAIL_ZONE] == "" || $role['settings'][DBFarmRole::SETTING_AWS_AVAIL_ZONE] == "x-scalr-diff")
									throw new Exception(sprintf(_("Requirement for EBS MySQL data storage is specific 'Placement' parameter for role '%s'"), $DBRole->name));
							}
						}

						break;

					case SERVER_PLATFORMS::RDS:
							Modules_Platforms_Rds_Helpers_Rds::farmValidateRoleSettings($role['settings'], $DBRole->name);
						break;

					case SERVER_PLATFORMS::EUCALYPTUS:
							Modules_Platforms_Eucalyptus_Helpers_Eucalyptus::farmValidateRoleSettings($role['settings'], $DBRole->name);
						break;
				}

				Scalr_Helpers_Dns::farmValidateRoleSettings($role['settings'], $DBRole->name);
			}

			$db->BeginTrans();

			if ($request['farm']['id'])
			{
				$dbFarm = DBFarm::LoadByID($request['farm']['id']);

				if (!Scalr_Session::getInstance()->getAuthToken()->hasAccessEnvironment($dbFarm->EnvID))
					throw new Exception("You cannot edit this farm");
			}
			else
			{
				// Count client farms
	        	$farms_count = $db->GetOne("SELECT COUNT(*) FROM farms WHERE clientid=?", array($Client->ID));

	        	// Check farms limit
	        	if ($farms_count >= $Client->FarmsLimit && $Client->FarmsLimit != 0)
					throw new Exception(_("Sorry, you have reached maximum allowed amount of farms."));

				$dbFarm = new DBFarm();
				$dbFarm->Status = FARM_STATUS::TERMINATED;
			}

			$dbFarm->Name = $request['farm']['name'];
			$dbFarm->RolesLaunchOrder = $request['farm']['roles_launch_order'];
			$dbFarm->Comments = trim($request['farm']['description']);

			$dbFarm->save();

			if (!$dbFarm->GetSetting(DBFarm::SETTING_CRYPTO_KEY))
				$dbFarm->SetSetting(DBFarm::SETTING_CRYPTO_KEY, Scalr::GenerateRandomKey(40));

			$roles_signatures = array();
			foreach ($request['roles'] as $role) {
				$role = json_decode($role, true);
				$roles_signatures[] = "{$role['role_id']}_{$role['platform']}_{$role['cloud_location']}";
			}

			foreach ($dbFarm->GetFarmRoles() as $dbFarmRole) {
				if (!in_array("{$dbFarmRole->RoleID}_{$dbFarmRole->Platform}_".$dbFarmRole->GetSetting(DBFarmRole::SETTING_CLOUD_LOCATION), $roles_signatures))
					$dbFarmRole->Delete();
			}

			$usedPlatforms = array();
			foreach ($request['roles'] as $role) {
				$role = json_decode($role, true);
				$dbRole = DBRole::loadById($role['role_id']);
				$update = false;
				
				try {
					$dbFarmRole = DBFarmRole::Load($dbFarm->ID, $dbRole->id);
					$update = true;
				}
				catch(Exception $e)
				{
					$dbFarmRole = $dbFarm->AddRole($dbRole, $role['platform'], $role['cloud_location'], (int)$role['launch_index']);
				}
				
				if ($update) {
					$dbFarmRole->LaunchIndex = (int)$role['launch_index'];
					$dbFarmRole->Save();
				}

				$usedPlatforms[$role['platform']] = 1;

				$oldRoleSettings = $dbFarmRole->GetAllSettings();
				foreach ($role['scaling_settings'] as $k => $v)
				{
					if ($k != TimeScalingAlgo::PROPERTY_TIME_PERIODS)
						$dbFarmRole->SetSetting($k, $v);
				}

				foreach ($role['settings'] as $k => $v)
					$dbFarmRole->SetSetting($k, $v);

				/****** Scaling settings ******/
				$scalingManager = new Scalr_Scaling_Manager($dbFarmRole);
				$scalingManager->setFarmRoleMetrics($role['scaling']);

				//TODO: optimize this code...
				$db->Execute("DELETE FROM farm_role_scaling_times WHERE farm_roleid=?",
					array($dbFarmRole->ID)
				);

				// 5 = Time based scaling -> move to constants
				if ($role['scaling'][5])
				{
					foreach ($role['scaling'][5] as $scal_period)
					{
						$chunks = explode(":", $scal_period['id']);
						$db->Execute("INSERT INTO farm_role_scaling_times SET
							farm_roleid		= ?,
							start_time		= ?,
							end_time		= ?,
							days_of_week	= ?,
							instances_count	= ?
						", array(
							$dbFarmRole->ID,
							$chunks[0],
							$chunks[1],
							$chunks[2],
							$chunks[3]
						));
					}
				}
				/*****************/

				/* Update role params */
				$dbFarmRole->SetParameters($role['params']);
				/* End of role params management */

				/* Add script options to databse */
				$dbFarmRole->SetScripts($role['scripting']);
				/* End of scripting section */

				/* Add services configuration */
				$dbFarmRole->SetServiceConfigPresets($role['config_presets']);
				/* End of scripting section */

				Scalr_Helpers_Dns::farmUpdateRoleSettings($dbFarmRole, $oldRoleSettings, $role['settings']);

				/**
				 * Platfrom specified updates
				 */
				if ($dbFarmRole->Platform == SERVER_PLATFORMS::EC2)
				{
					Modules_Platforms_Ec2_Helpers_Ebs::farmUpdateRoleSettings($dbFarmRole, $oldRoleSettings, $role['settings']);
					Modules_Platforms_Ec2_Helpers_Eip::farmUpdateRoleSettings($dbFarmRole, $oldRoleSettings, $role['settings']);
					Modules_Platforms_Ec2_Helpers_Elb::farmUpdateRoleSettings($dbFarmRole, $oldRoleSettings, $role['settings']);
				}

				$dbFarmRolesList[] = $dbFarmRole;
			}

			if ($usedPlatforms[SERVER_PLATFORMS::EC2])
				Modules_Platforms_Ec2_Helpers_Ec2::farmSave($dbFarm, $dbFarmRolesList);

			if ($usedPlatforms[SERVER_PLATFORMS::EUCALYPTUS])
				Modules_Platforms_Eucalyptus_Helpers_Eucalyptus::farmSave($dbFarm, $dbFarmRolesList);

			$dbFarm->save();

			$db->CommitTrans();

			$client = Client::Load(Scalr_Session::getInstance()->getClientId());
			if (!$client->GetSettingValue(CLIENT_SETTINGS::DATE_FARM_CREATED))
	        	$client->SetSettingValue(CLIENT_SETTINGS::DATE_FARM_CREATED, time());
			
			$result = array('success' => true, 'farm_id' => $dbFarm->ID);
		}
		catch(Exception $e)
		{
			$db->RollbackTrans();
			throw new Exception($e->getMessage());
		}
    }
    catch(Exception $e)
    {
    	$result = array('success' => false, 'error' => $e->getMessage());
    }

    $result = json_encode($result);
    header("Content-length: ".strlen($result));
    print $result;
    exit();
?>