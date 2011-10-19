<?php

class Scalr_UI_Controller_Farms_Builder extends Scalr_UI_Controller
{
	const CALL_PARAM_NAME = 'farmRoleId';

	public function xGetFarmAction()
	{
		$dbFarm = DBFarm::LoadByID($this->getParam('farmId'));
		if (! $this->session->getAuthToken()->hasAccessEnvironment($dbFarm->EnvID))
			throw new Exception("No access");

		$farmRoleId = $this->getParam('farmRoleId');

		foreach ($dbFarm->GetFarmRoles() as $dbFarmRole) {
			if ($farmRoleId && $farmRoleId != $dbFarmRole->ID)
				continue;

			$scripts = $this->db->GetAll("SELECT farm_role_scripts.*, scripts.name FROM farm_role_scripts
				INNER JOIN scripts ON scripts.id = farm_role_scripts.scriptid
				WHERE farm_roleid=? AND issystem='1'", array($dbFarmRole->ID)
			);
			$scriptsObject = array();
			foreach ($scripts as $script) {
				$scriptsObject[] = array(
					'script_id'		=> $script['scriptid'],
					'script'		=> $script['name'],
					'params'		=> unserialize($script['params']),
					'target'		=> $script['target'],
					'version'		=> $script['version'],
					'timeout'		=> $script['timeout'],
					'issync'		=> $script['issync'],
					'order_index'	=> $script['order_index'],
					'event' 		=> $script['event_name']
				);
			}

			$scalingManager = new Scalr_Scaling_Manager($dbFarmRole);
			$scaling = array();
			foreach ($scalingManager->getFarmRoleMetrics() as $farmRoleMetric)
				$scaling[$farmRoleMetric->metricId] = $farmRoleMetric->getSettings();

			$dbPresets = $this->db->GetAll("SELECT * FROM farm_role_service_config_presets WHERE farm_roleid=?", array($dbFarmRole->ID));
			$presets = array();
			foreach ($dbPresets as $preset)
				$presets[$preset['behavior']] = $preset['preset_id'];

				$farmRoles[$dbFarmRole->RoleID] = array(
					'role_id'		=> $dbFarmRole->RoleID,
					'platform'		=> $dbFarmRole->Platform,
					'generation'	=> $dbFarmRole->GetRoleObject()->generation,
					'arch'			=> $dbFarmRole->GetRoleObject()->architecture,
					'group'			=> ROLE_GROUPS::GetConstByBehavior($dbFarmRole->GetRoleObject()->getBehaviors()),
					'name'			=> $dbFarmRole->GetRoleObject()->name,
					'behaviors'		=> implode(",", $dbFarmRole->GetRoleObject()->getBehaviors()),
					'scripting'		=> $scriptsObject,
					'settings'		=> $dbFarmRole->GetAllSettings(),
					'cloud_location'=> $dbFarmRole->GetSetting(DBFarmRole::SETTING_CLOUD_LOCATION),
					'launch_index'	=> (int)$dbFarmRole->LaunchIndex,
					'scaling'		=> $scaling,
					'config_presets'=> $presets,
					'tags'			=> $dbFarmRole->GetRoleObject()->getTags()
				);
			}

			$this->response->setJsonResponse(array(
				'success' => true,
				'farm' => array(
					'name' 				=> $dbFarm->Name,
					'description'		=> $dbFarm->Comments,
					'roles_launch_order'=> $dbFarm->RolesLaunchOrder
				),
				'roles' => $farmRoles
			));
		}

	public function xGetRolesAction()
	{
		$roles = array();
		$platforms = Scalr_UI_Controller_Platforms::getEnabledPlatforms();

		$rolesSql = "SELECT id FROM roles WHERE (env_id = 0 OR env_id=?) AND id IN (SELECT role_id FROM role_images WHERE platform IN ('".implode("','", array_keys($platforms))."'))";

		$dbroles = $this->db->Execute($rolesSql, array($this->sesion->getEnvironmentId()));
		while ($role = $dbroles->FetchRow()) {
			if ($this->db->GetOne("SELECT id FROM roles_queue WHERE role_id=?", array($role['id'])))
				continue;

			$dbRole = DBRole::loadById($role['id']);

			$rolePlatforms = $dbRole->getPlatforms();
			$roleLocations = array();
			foreach ($rolePlatforms as $platform)
				$roleLocations[$platform] = $dbRole->getCloudLocations($platform);

			$roles[] = array(
				'role_id'				=> $dbRole->id,
				'arch'					=> $dbRole->architecture,
				'group'					=> ROLE_GROUPS::GetConstByBehavior($dbRole->getBehaviors()),
				'name'					=> $dbRole->name,
				'generation'			=> $dbRole->generation,
				'behaviors'				=> implode(",", $dbRole->getBehaviors()),
				'origin'				=> $dbRole->origin,
				'isstable'				=> (bool)$dbRole->isStable,
				'platforms'				=> implode(",", $rolePlatforms),
				'locations'				=> $roleLocations,
				'os'					=> $dbRole->os == 'Unknown' ? 'Unknown OS' : $dbRole->os,
				'tags'					=> $dbRole->getTags()
			);
		}

		$this->response->setJsonResponse(array(
			'success' => true,
			'roles' => $roles
		));
	}

	public function xGetScriptsAction()
	{
		$filterSql = " AND (";
		// Show shared roles
		$filterSql .= " origin='".SCRIPT_ORIGIN_TYPE::SHARED."'";

		// Show custom roles
		$filterSql .= " OR (origin='".SCRIPT_ORIGIN_TYPE::CUSTOM . "' AND clientid='" . $this->session->getClientId() . "')";

		//Show approved contributed roles
		$filterSql .= " OR (origin='".SCRIPT_ORIGIN_TYPE::USER_CONTRIBUTED . "' AND (approval_state='" . APPROVAL_STATE::APPROVED . "' OR clientid='" . $this->session->getClientId() . "'))";
		$filterSql .= ")";

		$scripts = $this->db->Execute("SELECT * FROM scripts WHERE 1=1 {$filterSql}");
		$scriptsList = array();
		while ($script = $scripts->FetchRow()) {
			$dbversions = $this->db->Execute("SELECT * FROM script_revisions WHERE scriptid=? AND (approval_state=? OR (SELECT clientid FROM scripts WHERE scripts.id=script_revisions.scriptid) = '" . $this->session->getClientId() . "')",
				array($script['id'], APPROVAL_STATE::APPROVED)
			);

			$versions = array();
			while ($version = $dbversions->FetchRow()) {
				$vars = Scalr_UI_Controller_Scripts::GetCustomVariables($version["script"]);
				$data = array();
				foreach ($vars as $var) {
					if (!in_array($var, array_keys(CONFIG::getScriptingBuiltinVariables())))
						$data[$var] = ucwords(str_replace("_", " ", $var));
				}

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

			$scriptsList[$script['id']] = $scr;
		}

		$this->response->setJsonResponse(array(
			'success' => true,
			'scripts' => $scriptsList,
			'events' => array(
				EVENT_TYPE::HOST_UP => EVENT_TYPE::GetEventDescription(EVENT_TYPE::HOST_UP),
				EVENT_TYPE::HOST_INIT => EVENT_TYPE::GetEventDescription(EVENT_TYPE::HOST_INIT),
				EVENT_TYPE::HOST_DOWN => EVENT_TYPE::GetEventDescription(EVENT_TYPE::HOST_DOWN),
				EVENT_TYPE::REBOOT_COMPLETE => EVENT_TYPE::GetEventDescription(EVENT_TYPE::REBOOT_COMPLETE),
				EVENT_TYPE::INSTANCE_IP_ADDRESS_CHANGED => EVENT_TYPE::GetEventDescription(EVENT_TYPE::INSTANCE_IP_ADDRESS_CHANGED),
				EVENT_TYPE::NEW_MYSQL_MASTER => EVENT_TYPE::GetEventDescription(EVENT_TYPE::NEW_MYSQL_MASTER),
				EVENT_TYPE::EBS_VOLUME_MOUNTED => EVENT_TYPE::GetEventDescription(EVENT_TYPE::EBS_VOLUME_MOUNTED),
				EVENT_TYPE::BEFORE_INSTANCE_LAUNCH => EVENT_TYPE::GetEventDescription(EVENT_TYPE::BEFORE_INSTANCE_LAUNCH),
				EVENT_TYPE::BEFORE_HOST_TERMINATE => EVENT_TYPE::GetEventDescription(EVENT_TYPE::BEFORE_HOST_TERMINATE),
				EVENT_TYPE::DNS_ZONE_UPDATED =>  EVENT_TYPE::GetEventDescription(EVENT_TYPE::DNS_ZONE_UPDATED),
				EVENT_TYPE::EBS_VOLUME_ATTACHED => EVENT_TYPE::GetEventDescription(EVENT_TYPE::EBS_VOLUME_ATTACHED)
			)
		));
	}
}
