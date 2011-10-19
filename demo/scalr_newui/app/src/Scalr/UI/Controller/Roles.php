<?php

class Scalr_UI_Controller_Roles extends Scalr_UI_Controller
{
	const CALL_PARAM_NAME = 'roleId';

	public function xRemoveAction()
	{
		$this->request->defineParams(array(
			'roles' => array('type' => 'json')
		));

		try {
			foreach ($this->getParam('roles') as $id) {
				$dbRole = DBRole::loadById($id);

				if (Scalr_Session::getInstance()->getAuthToken()->hasAccessEnvironment($dbRole->envId) &&
					$dbRole->clientId == Scalr_Session::getInstance()->getClientId()) {
						
					if ($this->db->GetOne("SELECT COUNT(*) FROM farm_roles WHERE role_id=? AND farmid IN (SELECT id FROM farms WHERE clientid=?)", array($dbRole->id, $this->session->getClientId())) == 0)
						$this->db->Execute("INSERT INTO roles_queue SET `role_id`=?, `action`=?, dtadded=NOW()", array($dbRole->id, 'remove'));
					else 
						throw new Exception(sprintf(_("Role '%s' used by your farms and cannot be removed."), $dbRole->name));
				}
			}

			$this->response->setJsonResponse(array('success' => true), 'text');

		} catch (Exception $e) {
			$this->response->setJsonResponse(array('success' => false, 'error' => $e->getMessage()), 'text');
		}
	}
	
	public function builderAction()
	{
		try {
			$platforms = array();
			
			foreach ($this->session->getEnvironment()->getEnabledPlatforms() as $platform) {
				if (in_array($platform, array(SERVER_PLATFORMS::RACKSPACE, SERVER_PLATFORMS::EC2)))
					$platforms[$platform] = SERVER_PLATFORMS::GetName($platform);
			}

			$images = array();
			foreach ($platforms as $platform => $name)
				$images[$platform] = PlatformFactory::NewPlatform($platform)->getRoleBuilderBaseImages();

			$this->response->setJsonResponse(array(
				'success' => true,
				'module' => $this->response->template->fetchJs('roles/builder.js'),
				'moduleParams' => array(
					'platforms' => $platforms,
					'images' => $images,
					'environment' => '#/environments/' . $this->session->getEnvironmentId() . '/edit'
				)
			));
		} catch (Exception $e) {
			$this->response->setJsonResponse(array('success' => false, 'error' => $e->getMessage()));
		}
	}

	public function xBuildAction()
	{
		$this->request->defineParams(array(
			'platform' 		=> array('type' => 'string'),
			'architecture'	=> array('type' => 'string'),
			'behaviors'		=> array('type' => 'json'),
			'roleName'		=> array('type' => 'string'),
			'imageId'		=> array('type' => 'string'),
			'location'		=> array('type' => 'string'),
			'mysqlServerType' => array('type' => 'string')
		));
		
		try {
			if ($this->db->GetOne("SELECT id FROM roles WHERE name=? AND (env_id = '0' OR env_id = ?)", 
				array($this->getParam('roleName'), $this->session->getEnvironmentId()))
			)
				throw new Exception('Selected role name is already used. Please select another one.');
			
			$imageId = $this->getParam('imageId');
			
			if ($this->getParam('platform') == SERVER_PLATFORMS::RACKSPACE)
				$imageId = str_replace('lon', '', $imageId);
			
			$behaviours = implode(",", array_values($this->getParam('behaviors')));
			
			// Create server
			$creInfo = new ServerCreateInfo($this->getParam('platform'), null, 0, 0);
			$creInfo->clientId = Scalr_Session::getInstance()->getClientId();
			$creInfo->envId = Scalr_Session::getInstance()->getEnvironmentId();
			$creInfo->farmId = 0;
			$creInfo->SetProperties(array(
				SERVER_PROPERTIES::SZR_IMPORTING_BEHAVIOR => $behaviours,
				SERVER_PROPERTIES::SZR_KEY => Scalr::GenerateRandomKey(40),
				SERVER_PROPERTIES::SZR_KEY_TYPE => SZR_KEY_TYPE::PERMANENT,
				SERVER_PROPERTIES::SZR_VESION => "0.6",
				SERVER_PROPERTIES::SZR_IMPORTING_MYSQL_SERVER_TYPE => $this->getParam('mysqlServerType')
			));
			
			$dbServer = DBServer::Create($creInfo, true);
			$dbServer->status = SERVER_STATUS::TEMPORARY;
			$dbServer->save();
			
			//Launch server
			$launchOptions = new Scalr_Server_LaunchOptions();
			$launchOptions->imageId = $imageId;
			$launchOptions->cloudLocation = $this->getParam('location');
			$launchOptions->architecture = $this->getParam('architecture');
			
			
			switch($this->getParam('platform')) {
				case SERVER_PLATFORMS::RACKSPACE:
					$launchOptions->serverType = 1;
					break;
				case SERVER_PLATFORMS::EC2:
					if ($this->getParam('architecture') == 'i386')
						$launchOptions->serverType = 'm1.small';
					else
						$launchOptions->serverType = 'm1.large';
						$launchOptions->userData = "#cloud-config\ndisable_root: false";
					break;
			}
			
			//Add Bundle task
			$creInfo = new ServerSnapshotCreateInfo(
				$dbServer, 
				$this->getParam('roleName'),
				SERVER_REPLACEMENT_TYPE::NO_REPLACE
			);
	       	
			$bundleTask = BundleTask::Create($creInfo, true);
			
			$bundleTask->cloudLocation = $launchOptions->cloudLocation;
			$bundleTask->save();
			
			$bundleTask->Log(sprintf("Launching temporary server (%s)", serialize($launchOptions)));
			
			$dbServer->SetProperty(SERVER_PROPERTIES::SZR_IMPORTING_BUNDLE_TASK_ID, $bundleTask->id);
			
			try {
				PlatformFactory::NewPlatform($this->getParam('platform'))->LaunchServer($dbServer, $launchOptions);
				$bundleTask->Log(_("Temporary server launched. Waiting for running state..."));
			}
			catch(Exception $e) {
				$bundleTask->SnapshotCreationFailed(sprintf(_("Unable to launch temporary server: %s"), $e->getMessage()));
			}
			
			$this->response->setJsonResponse(array('success' => true, 'bundleTaskId' => $bundleTask->id), 'text');
		}
		catch (Exception $e) {
			$this->response->setJsonResponse(array('success' => false, 'error' => $e->getMessage()));
		}
	}
	
	/**
	* View roles listView with filters
	*/
	public function viewAction()
	{		
		$this->response->setJsonResponse(array(
			'success' => true,
			'module' => $this->response->template->fetchJs('roles/view.js'),
			'moduleParams' => array(
				'locations' => Scalr_UI_Controller_Platforms::getCloudLocations('all'),
				'isScalrAdmin' => $this->session->getAuthToken()->hasAccess(Scalr_AuthToken::SCALR_ADMIN)
			)
		));
	}

	/**
	* View edit role page
	*/
	public function editAction()
	{
		// declare types of input variables (available types: int, string (default), bool, json, array; may be include default value for variable)
		$this->request->defineParams(array(
			'roleId' => array('type' => 'int')
		));

		$params = array('platforms' => array(), 'isScalrAdmin' => $this->session->getAuthToken()->hasAccess(Scalr_AuthToken::SCALR_ADMIN));

		if (! $this->session->getAuthToken()->hasAccess(Scalr_AuthToken::SCALR_ADMIN))
			$ePlatforms = $this->session->getEnvironment()->getEnabledPlatforms();
		else
			$ePlatforms = array_keys(SERVER_PLATFORMS::GetList());

		$lPlatforms = SERVER_PLATFORMS::GetList();

		$llist = array();
		foreach ($ePlatforms as $platform) {
			$locations = array();
			foreach (PlatformFactory::NewPlatform($platform)->getLocations() as $key => $loc) {
				$locations[] = array('id' => $key, 'name' => $loc);
				$llist[$key] = $loc;
			}

			$params['platforms'][] = array(
				'id' => $platform,
				'name' => $lPlatforms[$platform],
				'locations' => $locations
			);
		}

		if ($this->getParam('roleId')) {
			try {
				$dbRole = DBRole::loadById($this->getParam('roleId'));

				if ($this->session->getClientId() != 0) {
					if (! $this->session->getAuthToken()->hasAccessEnvironment($dbRole->envId))
						throw new Exception ("No access");
				}

				$images = array();
				foreach ($dbRole->getImages() as $platform => $locations) {
					foreach ($locations as $location => $imageId)
						$images[] = array(
							'image_id' 		=> $imageId,
							'platform' 		=> $platform,
							'location' 		=> $location,
							'platform_name' => SERVER_PLATFORMS::GetName($platform),
							'location_name'	=> $llist[$location]
						);
				}

				$params['tags'] = array_flip($dbRole->getTags());

				$params['role'] = array(
					'id'			=> $dbRole->id,
					'name'			=> $dbRole->name,
					'arch'			=> $dbRole->architecture,
					'os'			=> $dbRole->os,
					'agent'			=> $dbRole->generation,
					'description'	=> $dbRole->description,
					'behaviors'		=> $dbRole->getBehaviors(),
					'properties'	=> array(DBRole::PROPERTY_SSH_PORT => $dbRole->getProperty(DBRole::PROPERTY_SSH_PORT)),
					'images'		=> $images,
					'parameters'	=> $dbRole->getParameters(),
					'szr_version'	=> $dbRole->szrVersion
				);

				if (!$params['role']['properties'][DBRole::PROPERTY_SSH_PORT])
					$params['role']['properties'][DBRole::PROPERTY_SSH_PORT] = 22;
				
				$this->response->setJsonResponse(array(
					'success' => true,
					'module' => $this->response->template->fetchJs('roles/edit.js'),
					'moduleParams' => $params
				));

			} catch(Exception $e) {
				$this->response->setJsonResponse(array('success' => false, 'error' => $e->getMessage()));
			}
		} else {
			if (! $this->session->getAuthToken()->hasAccess(Scalr_AuthToken::SCALR_ADMIN)) {
				$this->response->setJsonResponse(array('success' => false, 'error' => 'No access'));
			} else {
				$params['tags'] = array();
				$params['role'] = array(
					'id'			=> 0,
					'name'			=> "",
					'arch'			=> "i386",
					'agent'			=> 2,
					'description'	=> "",
					'behaviors'		=> array(),
					'properties'	=> array(DBRole::PROPERTY_SSH_PORT => 22),
					'images'		=> array(),
					'parameters'	=> array()
				);

				$this->response->setJsonResponse(array(
					'success' => true,
					'module' => $this->response->template->fetchJs('roles/edit.js'),
					'moduleParams' => $params
				));
			}
		}
	}

	/**
	* Save role informatiom
	*/
	public function xSaveRoleAction()
	{
		$this->request->defineParams(array(
			'roleId' => array('type' => 'int'),
			'agent' => array('type' => 'int'),
			'behaviors' => array('type' => 'array'),
			'tags' => array('type' => 'array'),
			'arch', 'description', 'name', 'os',
			'parameters' => array('type' => 'json'),
			'remove_images' => array('type' => 'json'),
			'images' => array('type' => 'json'),
			'properties' => array('type' => 'json'),
			'szr_version' => array('type' => 'string')
		));

		$id = $this->getParam('roleId');
		try {
			if ($id == 0) {
				if (! $this->session->getAuthToken()->hasAccess(Scalr_AuthToken::SCALR_ADMIN)) {
					$this->response->pageAccessDenied();
					return;
				}

				$dbRole = new DBRole(0);

				$dbRole->generation = ($this->getParam('agent') == 'scalarizr' || $this->getParam('agent') == 2) ? 2 : 1; // ($post_agent != 'scalarizr') ? 1 : 2;
				$dbRole->architecture = $this->getParam('arch');
				$dbRole->origin = ROLE_TYPE::SHARED;
				$dbRole->envId = 0;
				$dbRole->clientId = 0;
				$dbRole->name = $this->getParam('name');
				$dbRole->os = $this->getParam('os');
				$dbRole->szrVersion = $this->getParam('szr_version');

				$rules = array(
					'icmp:-1:-1:0.0.0.0/0',
					'tcp:22:22:0.0.0.0/0',
					'tcp:8013:8013:0.0.0.0/0',
					'udp:8014:8014:0.0.0.0/0',
					'udp:161:162:0.0.0.0/0'
				);
				
				foreach ($this->getParam('behaviors') as $behavior) {
					if ($behavior == ROLE_BEHAVIORS::NGINX || $behavior == ROLE_BEHAVIORS::APACHE) {
						$rules[] = "tcp:80:80:0.0.0.0/0";
						$rules[] = "tcp:443:443:0.0.0.0/0";

						/*if (!empty($this->getParam('parameters'))) @TODO check
						{
							$param = new stdClass();
							$param->name = 'Nginx HTTPS Vhost Template';
							$param->required = '1';
							$param->defval = @file_get_contents(dirname(__FILE__)."/../templates/services/nginx/ssl.vhost.tpl");
							$param->type = 'text';
							$post_parameters = json_encode(array($param));
						}*/
					}

					if ($behavior == ROLE_BEHAVIORS::MYSQL) {
						$rules[] = "tcp:3306:3306:0.0.0.0/0";
					}

					if ($behavior == ROLE_BEHAVIORS::CASSANDRA) {
						$rules[] = "tcp:9160:9160:0.0.0.0/0";
					}
				}

				$dbRole->save();

				foreach ($rules as $rule) {
					$this->db->Execute("INSERT INTO role_security_rules SET `role_id`=?, `rule`=?", array(
						$dbRole->id, $rule
					));
				}

				$soft = explode("\n", trim($this->getParam('software')));
				$software = array();
				if (count($soft) > 0) {
					foreach ($soft as $softItem) {
						$itm = explode("=", $softItem);
						$software[trim($itm[0])] = trim($itm[1]);
					}

					$dbRole->setSoftware($software);
				}

				$dbRole->setBehaviors(array_values($this->getParam('behaviors')));
			} else {
				$dbRole = DBRole::loadById($id);

				if ($this->session->getClientId() != 0) {
					if (! $this->session->getAuthToken()->hasAccessEnvironment($dbRole->envId))
						throw new Exception ("No access");
				}
			}

			$dbRole->description = $this->getParam('description');

			foreach ($this->getParam('remove_images') as $imageId)
				$dbRole->removeImage($imageId);

			foreach ($this->getParam('images') as $image) {
				$image = (array)$image;
				$dbRole->setImage($image['image_id'], $image['platform'], $image['location']);
			}

			foreach ($this->getParam('properties') as $k => $v)
				$dbRole->setProperty($k, $v);

			$dbRole->setParameters($this->getParam('parameters'));

			if ($this->session->getAuthToken()->hasAccess(Scalr_AuthToken::SCALR_ADMIN))
				$dbRole->setTags($this->getParam('tags'));

			$dbRole->save();

			$this->response->template->messageSuccess('Role saved');
			$this->response->setJsonResponse(array('success' => true), 'text');

		} catch (Exception $e) {
			$this->response->setJsonResponse(array('success' => false, 'error' => $e->getMessage()), 'text');
		}
	}

	/**
	* Get list of roles for listView
	*/
	public function xListViewRolesAction()
	{
		$this->request->defineParams(array(
			'client_id' => array('type' => 'int'),
			'roleId' => array('type' => 'int'),
			'cloudLocation', 'origin', 'approval_state', 'query',
			'sort' => array('type' => 'string', 'default' => 'id'),
			'dir' => array('type' => 'string', 'default' => 'ASC')
		));

		try {
			if ($this->session->getAuthToken()->hasAccess(Scalr_AuthToken::SCALR_ADMIN))
				$sql = "SELECT id from roles WHERE env_id = '0'";
			else
				$sql = "SELECT id from roles WHERE env_id IN ({$this->session->getEnvironmentId()},0)";

			if ($this->getParam('cloudLocation'))
				$sql .= " AND id IN (SELECT role_id FROM role_images WHERE cloud_location={$this->db->qstr($this->getParam('cloudLocation'))})";

			if ($this->getParam('roleId'))
				$sql .= " AND id='{$this->getParam('roleId')}'";

			if ($this->getParam('origin')) {
				$sql .= " AND origin = " . $this->db->qstr($this->getParam('origin'));
			}

			$response = $this->buildResponseFromSql($sql, array("name", "description"));
			
			foreach ($response["data"] as &$row) {
				$dbRole = DBRole::loadById($row['id']);

				$platforms = array();
				foreach ($dbRole->getPlatforms() as $platform)
					$platforms[] = SERVER_PLATFORMS::GetName($platform);

				$status = '<span style="color:gray;">未使用</span>';
				if ($this->db->GetOne("SELECT id FROM roles_queue WHERE role_id=?", array($dbRole->id)))
					$status = '<span style="color:red;">正在删除</span>';
				elseif ($this->db->GetOne("SELECT COUNT(*) FROM farm_roles WHERE role_id=? AND farmid IN (SELECT id FROM farms WHERE clientid=?)", array($dbRole->id, $this->session->getClientId())) > 0)
					$status = '<span style="color:green;">正在使用</span>';
					
				$role = array(
					'name'			=> $dbRole->name,
					'behaviors'		=> implode(", ", $dbRole->getBehaviors()),
					'id'			=> $dbRole->id,
					'architecture'	=> $dbRole->architecture,
					'client_id'		=> $dbRole->clientId,
					'env_id'		=> $dbRole->envId,
					'status'		=> $status,
					'origin'		=> $dbRole->origin,
					'os'			=> $dbRole->os,
					'tags'			=> implode(", ", $dbRole->getTags()),
					'platforms'		=> implode(", ", $platforms),
					'generation'	=> ($dbRole->generation == 2) ? '系统代理' : 'ami-scripts'
				);

				try {
					$envId = $this->session->getEnvironmentId();

					$role['used_servers'] = $this->db->GetOne("SELECT COUNT(*) FROM servers WHERE role_id=? AND env_id=?",
						array($dbRole->id, $envId)
					);
				}
				catch(Exception $e) {
					
					if ($this->session->getClientId() == 0) {
						$role['used_servers'] = $this->db->GetOne("SELECT COUNT(*) FROM servers WHERE role_id=?",
							array($dbRole->id)
						);
						
						if ($this->db->GetOne("SELECT COUNT(*) FROM farm_roles WHERE role_id=?", array($dbRole->id)) > 0)
							$status = '<span style="color:green;">In use</span>';
							
						$role['status'] = $status;
					}
				}

				if ($dbRole->clientId == 0)
					$role["client_name"] = "系统";
				else
					$role["client_name"] = $this->db->GetOne("SELECT fullname FROM clients WHERE id='{$dbRole->clientId}'");

				if (! $role["client_name"])
					$role["client_name"] = "";

				$row = $role;
			}

			$this->response->setJsonResponse($response);
		} catch (Exception $e) {

			var_dump($e);

			$this->response->setJsonResponse(array('success' => false, 'error' => $e->getMessage()));
		}
	}

	/**
	* Get information about role
	*/
	public function infoAction()
	{
		$this->request->defineParams(array(
			'roleId' => array('type' => 'int')
		));

		$roleId = $this->getParam('roleId');

		try {
			$dbRole = DBRole::loadById($roleId);

			if ($this->session->getClientId() != 0 && $dbRole->clientId != 0 && $dbRole->clientId != $this->session->getClientId()) {
					throw new Exception(_("你没权访问所选服务角色"));
			}

			$dbRole->groupName = ROLE_GROUPS::GetNameByBehavior($dbRole->getBehaviors());
			$dbRole->behaviorsList = implode(", ", $dbRole->getBehaviors());
			foreach ($dbRole->getSoftwareList() as $soft)
				$dbRole->softwareList[] = "{$soft['name']} {$soft['version']}";

			$dbRole->softwareList = implode(", ", $dbRole->softwareList);
			$dbRole->tagsString = implode(", ", $dbRole->getTags());

			$dbRole->platformsList = array();
			foreach ($dbRole->getPlatforms() as $platform) {
				$dbRole->platformsList[] = array(
					'name' 		=> SERVER_PLATFORMS::GetName($platform),
					'locations'	=> implode(", ", $dbRole->getCloudLocations($platform))
				);
			}

			$this->response->setJsonResponse(array(
				'success' => true,
				'module' => $this->response->template->fetchJs('roles/info.js'),
				'moduleParams' => array(
					'name' => $dbRole->name,
					'info' => get_object_vars($dbRole)
				)
			));
		} catch (Exception $e) {
			$this->response->setJsonResponse(array('success' => false, 'error' => $e->getMessage()));
		}
	}
}
