<?php

class Scalr_UI_Controller_Scripts extends Scalr_UI_Controller
{
	const CALL_PARAM_NAME = 'scriptId';

	public function __construct() {
		
		parent::__construct();
		
		$this->filterSql = "(" .
			" origin='".SCRIPT_ORIGIN_TYPE::SHARED."'" .
			" OR (origin='".SCRIPT_ORIGIN_TYPE::CUSTOM."' AND clientid='".$this->session->getClientId()."')" .
			" OR (origin='".SCRIPT_ORIGIN_TYPE::USER_CONTRIBUTED."' AND (scripts.approval_state='".APPROVAL_STATE::APPROVED."' OR clientid='".$this->session->getClientId()."'))" .
		")";
	}
	
	public function hasAccess()
	{
		return $this->session->getAuthToken()->hasAccess(Scalr_AuthToken::ACCOUNT_USER | Scalr_AuthToken::SCALR_ADMIN);
	}

    static public function getCustomVariables($template)
	{
		$text = preg_replace('/(\\\%)/si', '$$scalr$$', $template);
		preg_match_all("/\%([^\%\s]+)\%/si", $text, $matches);
		return $matches[1];
	}

	/** Actions **/
	
	public function defaultAction()
	{
		$this->viewAction();
	}
	
	public function viewAction()
	{
		$this->response->setJsonResponse(array(
			'success' => true,
			'module' => $this->response->template->fetchJs('scripts/view.js'),
			'moduleParams' => array(
				'isScalrAdmin' => $this->session->getAuthToken()->hasAccess(Scalr_AuthToken::SCALR_ADMIN),
				'clientId' => $this->session->getClientId()
			)
		));
	}
	
	public function xGetScriptContentAction()
	{
		$this->request->defineParams(array(
			'scriptId' => array('type' => 'int'), 
			'version' => array('type' => 'int')
		));
		
		try {
			$scriptInfo = $this->db->GetRow("SELECT * FROM scripts WHERE id=? AND {$this->filterSql}", array($this->getParam('scriptId')));
			if (!$scriptInfo)
				throw new Exception("Script not found");
				
			$content = $this->db->GetOne("SELECT script FROM script_revisions WHERE scriptid = ? AND revision =?", array(
				$this->getParam('scriptId'), $this->getParam('version')
			));
			
			$this->response->setJsonResponse(array(
				'success' => true,
				'scriptContents' => $content
			));
		}
		catch (Exception $e) {
			$this->response->setJsonResponse(array('success' => false, 'error' => $e->getMessage()));
		}
	}
	
	public function xRemoveAction()
	{
		$this->request->defineParams(array(
			'scriptId' => array('type' => 'int')
		));
		
		// Get template infor from database
		$script = $this->db->GetRow("SELECT * FROM scripts WHERE id=? AND {$this->filterSql}", array($this->getParam('scriptId')));
		if (!$script)
			throw new Exception(_("You don't have permissions to remove this script"));

		// Check template usage
		$roles_count = $this->db->GetOne("SELECT COUNT(*) FROM farm_role_scripts WHERE scriptid=? AND event_name NOT LIKE 'CustomEvent-%'",
			array($this->getParam('scriptId'))
		);

		// If script used redirect and show error
		if ($roles_count > 0)
			throw new Exception(_("This script being used and can't be deleted"));

		$this->db->BeginTrans();

		// Delete tempalte and all revisions
		$this->db->Execute("DELETE FROM farm_role_scripts WHERE scriptid=?", array($this->getParam('scriptId')));
		$this->db->Execute("DELETE FROM scripts WHERE id=?", array($this->getParam('scriptId')));
		$this->db->Execute("DELETE FROM script_revisions WHERE scriptid=?", array($this->getParam('scriptId')));

		$this->db->CommitTrans();

		$this->response->setJsonResponse(array('success' => true));
	}
	
	public function xSaveAction()
	{
		$this->request->defineParams(array(
			'scriptId' => array('type' => 'int'), 
			'scriptName', 'scriptDescription', 'scriptContents',
			'version' => array('type' => 'int'),
			'saveCurrentRevision' => array('type' => 'int')
		));
		
		try {
			if (!$this->getParam('scriptId')) {
				// Add new script
				$this->db->Execute("INSERT INTO scripts SET
					name = ?,
					description = ?,
					origin = ?,
					dtadded = NOW(),
					clientid = ?,
					approval_state = ?
				", array(
					htmlspecialchars($this->getParam('scriptName')),
					htmlspecialchars($this->getParam('scriptDescription')),
					SCRIPT_ORIGIN_TYPE::CUSTOM,
					$this->session->getClientId(),
					APPROVAL_STATE::APPROVED
				));
				
				$scriptId = $this->db->Insert_ID();
			} else {
				
				$scriptInfo = $this->db->GetRow("SELECT * FROM scripts WHERE id=? AND {$this->filterSql}", array($this->getParam('scriptId')));
				if (!$scriptInfo)
					throw new Exception("Script not found");
				
				$this->db->Execute("UPDATE scripts SET
					name = ?,
					description = ?
					WHERE id = ?
				", array(
					htmlspecialchars($this->getParam('scriptName')),
					htmlspecialchars($this->getParam('scriptDescription')),
					$this->getParam('scriptId')
				));
				
				$scriptId = $this->getParam('scriptId');
			}
			
			
			if (!$this->getParam('saveCurrentRevision')) {
				$revision = $this->db->GetOne("SELECT IF(MAX(revision), MAX(revision), 0) FROM script_revisions WHERE scriptid=?",
					array($scriptId)
				);
				
				$this->db->Execute("INSERT INTO script_revisions SET
					scriptid	= ?,
					revision    = ?,
					script      = ?,
					dtcreated   = NOW(),
					approval_state = ?
				", array(
					$scriptId,
					$revision+1,
					str_replace("\r\n", "\n", $this->getParam('scriptContents')),
					APPROVAL_STATE::APPROVED
				));
			} else {
				$this->db->Execute("UPDATE script_revisions SET
					script      = ?
					WHERE scriptId = ? AND revision = ?
				", array(
					str_replace("\r\n", "\n", $this->getParam('scriptContents')),
					$scriptId,
					$this->getParam('scriptVersion')
				));
			}
			
			$this->response->setJsonResponse(array('success' => true));
		} catch (Exception $e) {
			$this->response->setJsonResponse(array('success' => false, 'error' => $e->getMessage()));
		}
	}
	
	public function xForkAction()
	{
		$this->request->defineParams(array(
			'scriptId' => array('type' => 'int')
		));
		
		try {
			$scriptInfo = $this->db->GetRow("SELECT * FROM scripts WHERE id=? AND origin != ?", array($this->getParam('scriptId'), SCRIPT_ORIGIN_TYPE::CUSTOM));
			if (!$scriptInfo)
				throw new Exception("Script not found");
				
			$this->db->Execute("INSERT INTO scripts SET
				name = ?,
				description = ?,
				origin = ?,
				dtadded = NOW(),
				clientid = ?,
				approval_state = ?
			", array(
				'Custom ' . $scriptInfo['name'],
				$scriptInfo['description'],
				SCRIPT_ORIGIN_TYPE::CUSTOM,
				$this->session->getClientId(),
				APPROVAL_STATE::APPROVED
			));
			
			$content = $this->db->GetOne("SELECT script FROM script_revisions WHERE scriptid = ? ORDER BY id DESC LIMIT 0,1", array($this->getParam('scriptId')));
			
			$scriptId = $this->db->Insert_ID();
			
			$this->db->Execute("INSERT INTO script_revisions SET
				scriptid	= ?,
				revision    = ?,
				script      = ?,
				dtcreated   = NOW(),
				approval_state = ?
			", array(
				$scriptId,
				1,
				$content,
				APPROVAL_STATE::APPROVED
			));
			
			$this->response->setJsonResponse(array('success' => true));
			
		} catch (Exception $e) {
			$this->response->setJsonResponse(array('success' => false, 'error' => $e->getMessage()));
		}
	}
	
	public function editAction()
	{
		$this->request->defineParams(array(
			'scriptId' => array('type' => 'int'),
			'version' => array('type' => 'int')
		));
		
		$vars = CONFIG::getScriptingBuiltinVariables();
		
		try {
			
			$scriptInfo = $this->db->GetRow("SELECT * FROM scripts WHERE id=? AND {$this->filterSql}", array($this->getParam('scriptId')));
			if (!$scriptInfo)
				throw new Exception("Script not found");
			
			$latestRevision = $this->db->GetRow("SELECT MAX(revision) as rev FROM script_revisions WHERE scriptid=? GROUP BY scriptid", array($this->getParam('scriptId')));
			if ($this->getParam('version'))
				$rev = $this->db->GetRow("SELECT revision as rev, script FROM script_revisions WHERE scriptid=? AND revision=?", array($this->getParam('scriptId'), $this->getParam('version')));
			else
				$rev = $latestRevision;
			
			$this->response->setJsonResponse(array(
				'success' => true,
				'module' => $this->response->template->fetchJs('scripts/create.js'),
				'moduleParams' => array(
					'scriptName'	=> $scriptInfo['name'],
					'scriptId'		=> $scriptInfo['id'],
					'description'	=> $scriptInfo['description'],
					'scriptContents'=> $this->db->GetOne("SELECT script FROM script_revisions WHERE scriptid=? AND revision=?", array($this->getParam('scriptId'), $rev['rev'])),
					'latestVersion'=> $latestRevision['rev'],
					'version'		=> $rev['rev'],
					'versions'		=> range(1, $latestRevision['rev']),
					'variables'		=> "%" . implode("%, %", array_keys($vars)) . "%"
				)
			));
			
		} catch (Exception $e) {
			$this->response->setJsonResponse(array('success' => false, 'error' => $e->getMessage()));
		}
	}
	
	public function createAction()
	{
		$vars = CONFIG::getScriptingBuiltinVariables();
		
		$this->response->setJsonResponse(array(
			'success' => true,
			'module' => $this->response->template->fetchJs('scripts/create.js'),
			'moduleParams' => array(
				'scriptName'	=> '',
				'scriptId'		=> 0,
				'description'	=> '',
				'scriptContents'=> '',
				'version'		=> 1,
				'versions'		=> array(1),
				'variables'		=> "%" . implode("%, %", array_keys($vars)) . "%"
			)
		));
	}
	
	public function xListViewScriptsAction() 
	{
		try {
			$this->request->defineParams(array(
				'scriptId', 'origin', 'approvalState',
				'sort' => array('type' => 'string', 'default' => 'id'),
				'dir' => array('type' => 'string', 'default' => 'DESC')
			));
			
			if (!$this->session->getAuthToken()->hasAccess(Scalr_AuthToken::SCALR_ADMIN)) {
				$filterSql = $this->filterSql;
			}
			else {
				$filterSql = " (origin='".SCRIPT_ORIGIN_TYPE::SHARED."' OR origin='".SCRIPT_ORIGIN_TYPE::USER_CONTRIBUTED."')";
			}
			
		    $sql = "SELECT 
	    		scripts.id, 
	    		scripts.name, 
	    		scripts.description, 
	    		scripts.origin,
	    		scripts.clientid,
	    		scripts.approval_state,
	    		MAX(script_revisions.dtcreated) as dtupdated, MAX(script_revisions.revision) AS version FROM scripts 
	    	INNER JOIN script_revisions ON script_revisions.scriptid = scripts.id 
	    	WHERE 1=1 AND {$filterSql}";
		    
		    if ($this->getParam('origin'))
		    	$sql .= " AND origin=".$this->db->qstr($this->getParam('origin'));
		    	
		    if ($this->getParam('approvalState'))
		    	$sql .= " AND scripts.approval_state=".$this->db->qstr($this->getParam('approvalState'));
		    
		    $response = $this->buildResponseFromSql($sql, array("scripts.name", "scripts.description"), " GROUP BY script_revisions.scriptid", false);
		    	
			foreach ($response['data'] as &$row)
			{
				if ($row['clientid'] != 0)
				{
					$client = $this->db->GetRow("SELECT email, fullname FROM clients WHERE id = ?", array($row['clientid']));	
					$row["client_name"] = $client['fullname'];
				}
				
				$row['dtupdated'] = date("M j, Y", strtotime($row["dtupdated"]));
			}
		
			$this->response->setJsonResponse($response);
		} catch (Exception $e) {
			$this->response->setJsonResponse(array('success' => false, 'error' => $e->getMessage()));
		}
	}
	
	public function getList()
	{
		$scripts = array();

		$sql = "SELECT scripts.*, MAX(script_revisions.dtcreated) as dtupdated from scripts INNER JOIN script_revisions
			ON script_revisions.scriptid = scripts.id WHERE {$this->filterSql} GROUP BY script_revisions.scriptid ORDER BY dtupdated DESC";

		foreach ($this->db->GetAll($sql) as $script) {
			$dbVersions = $this->db->Execute("SELECT * FROM script_revisions WHERE scriptid=? AND (approval_state=? OR (SELECT clientid FROM scripts WHERE scripts.id=script_revisions.scriptid) = '".$this->session->getClientId()."')",
				array($script['id'], APPROVAL_STATE::APPROVED)
			);

			if ($dbVersions->RecordCount() > 0) {
				$versions = array();
				while ($version = $dbVersions->FetchRow()) {
					$data = array();
					foreach ((array)self::getCustomVariables($version["script"]) as $var) {
						if (! in_array($var, array_keys(CONFIG::getScriptingBuiltinVariables())))
							$data[$var] = ucwords(str_replace("_", " ", $var));
					}

					$versions[$version['revision']] = array("revision" => $version['revision'], "fields" => $data);
				}

				$scripts[$script['id']] = array(
					'id'			=> $script['id'],
					'name'			=> $script['name'],
					'description'	=> $script['description'],
					'issync'		=> $script['issync'],
					'timeout'		=> ($script['issync'] == 1) ? CONFIG::$SYNCHRONOUS_SCRIPT_TIMEOUT : CONFIG::$ASYNCHRONOUS_SCRIPT_TIMEOUT,
					'revisions'		=> $versions
				);
			}
		}

		return $scripts;
	}

	public function getFarmRolesAction()
	{
		$farmRolesController = self::loadController('Roles', 'Scalr_UI_Controller_Farms');
		if (is_null($farmRolesController))
			throw new Exception('Controller Farms_Roles not created');

		$farmRoles = $farmRolesController->getList();
		if (count($farmRoles))
			$farmRoles[0] = array('id' => 0, 'name' =>'On all roles');

		$this->response->setJsonResponse(array(
			'success' => true,
			'farmRoles' => $farmRoles
		));
	}

	public function getServersAction()
	{
		$dbFarmRole = DBFarmRole::LoadByID($this->getParam('farmRoleId'));
		$dbFarm = DBFarm::LoadById($dbFarmRole->FarmID);
		$servers = array();

		if (! $this->session->getAuthToken()->hasAccessEnvironment($dbFarm->EnvID))
			throw new Exception('You cannot execute script on selected farm/role/server');

		foreach ($dbFarmRole->GetServersByFilter(array('status' => SERVER_STATUS::RUNNING)) as $key => $value)
			$servers[$value->serverId] = $value->remoteIp;

		if (count($servers))
			$servers[0] = 'On all servers';

		$this->response->setJsonResponse(array(
			'success' => true,
			'servers' => $servers
		));
	}

	public function executeAction()
	{
		$farmId = $this->getParam('farmId');
		$farmRoleId = $this->getParam('farmRoleId');
		$serverId = $this->getParam('serverId');
		$scriptId = $this->getParam('scriptId');
		$eventName = $this->getParam('eventName');

		$farms = array();
		$farmRoles = array();
		$servers = array();
		$scripts = $this->getList();

		if ($eventName) {
			$scriptInfo = $this->db->GetRow("SELECT * FROM farm_role_scripts WHERE event_name=?", array($eventName));
			if (!$scriptInfo)
				throw new Exception("Scalr unable to find script execution options for used link");
				
			$farmId = $scriptInfo['farmid'];
			$farmRoleId = $scriptInfo['farm_roleid'];
			
			$scriptId = $scriptInfo['scriptid'];
		}


		if ($serverId) {
			$dbServer = DBServer::LoadByID($serverId);
			$farmRoleId = $dbServer->farmRoleId;
		}

		if ($farmRoleId) {
			$dbFarmRole = DBFarmRole::LoadByID($farmRoleId);
			$farmId = $dbFarmRole->FarmID;

			foreach ($dbFarmRole->GetServersByFilter(array('status' => SERVER_STATUS::RUNNING)) as $key => $value)
				$servers[$value->serverId] = $value->remoteIp;

			if (count($servers)) {
				$servers[0] = _('On all servers');
				
				if (!$serverId)
					$serverId = 0;
			}
		}
		
		if ($farmId) {
			$dbFarm = DBFarm::LoadById($farmId);

			if (! $this->session->getAuthToken()->hasAccessEnvironment($dbFarm->EnvID))
				throw new Exception(_('You cannot execute script on selected farm/role/server'));

			$this->request->setParams(array('farmId' => $farmId));
			$farmRolesController = self::loadController('Roles', 'Scalr_UI_Controller_Farms');
			if (is_null($farmRolesController))
				throw new Exception(_('Controller Farms_Roles not created'));

			$farmRoles = $farmRolesController->getList();
			if (count($farmRoles))
				$farmRoles[0] = array('id' => 0, 'name' =>'On all roles');
		}

		$farmsController = self::loadController('Farms');
		if (is_null($farmsController))
			throw new Exception(_('Controller Farms not created'));
		else
			$farms = $farmsController->getList();


		$this->response->setJsonResponse(array(
			'success' => true,
			'moduleParams' => array(
				'farms' => $farms,
				'farmRoles' => $farmRoles,
				'servers' => $servers,
				'scripts' => $scripts,
				'farmId' => $farmId,
				'farmRoleId' => $farmRoleId,
				'serverId' => $serverId,
				'scriptId' => $scriptId,
		
				'scriptIsSync' => $scriptInfo['issync'],
				'scriptTimeout' => $scriptInfo['timeout'],
				'scriptVersion' => $scriptInfo['version'],
				'scriptOptions' => @unserialize($scriptInfo['params'])
			),
			'module' => $this->response->template->fetchJs('scripts/execute.js')
		));
	}

	public function xExecuteAction()
	{
		$this->request->defineParams(array(
			'farmId' => array('type' => 'int'),
			'farmRoleId' => array('type' => 'int'),
			'serverId' => array('type' => 'string'),
			'scriptId' => array('type' => 'int'),
			'scriptIsSync' => array('type' => 'int'),
			'scriptTimeout' => array('type' => 'int'),
			'scriptVersion' => array('type' => 'int'),
			'scriptOptions' => array('type' => 'array'),
			'createMenuLink' => array('type' => 'int')
		));

		$eventName = 'CustomEvent-'.date("YmdHi").'-'.rand(1000,9999);
		$target = '';

		// @TODO: validation
		if ($this->getParam('serverId')) {
			$dbServer = DBServer::LoadByID($this->getParam('serverId'));

			if (! $this->session->getAuthToken()->hasAccessEnvironment($dbServer->envId))
				throw new Exception("Specified farm role not found");

			$target = SCRIPTING_TARGET::INSTANCE;
			$serverId = $dbServer->serverId;
			$farmRoleId = $dbServer->farmRoleId;
			$farmId = $dbServer->farmId;

		} else if ($this->getParam('farmRoleId')) {
			$dbFarmRole = DBFarmRole::LoadByID($this->getParam('farmRoleId'));

			if (! $this->session->getAuthToken()->hasAccessEnvironment($dbFarmRole->GetFarmObject()->EnvID))
				throw new Exception("Specified farm role not found");

			$target = SCRIPTING_TARGET::ROLE;
			$farmRoleId = $dbFarmRole->ID;
			$farmId = $dbFarmRole->FarmID;

		} else {
			$dbFarm = DBFarm::LoadByID($this->getParam('farmId'));
			$target = SCRIPTING_TARGET::FARM;

			if (! $this->session->getAuthToken()->hasAccessEnvironment($dbFarm->EnvID))
				throw new Exception("Specified farm not found");
				
			$farmId = $dbFarm->ID;
		}

		if (! $this->getParam('eventName')) {
			$this->db->Execute("INSERT INTO farm_role_scripts SET
				scriptid	= ?,
				farmid		= ?,
				farm_roleid	= ?,
				params		= ?,
				event_name	= ?,
				target		= ?,
				version		= ?,
				timeout		= ?,
				issync		= ?,
				ismenuitem	= ?
			", array(
				$this->getParam('scriptId'),
				(int)$farmId,
				(int)$farmRoleId,
				serialize($this->getParam('scriptOptions')),
				$eventName,
				$target,
				$this->getParam('scriptVersion'),
				$this->getParam('scriptTimeout'),
				$this->getParam('scriptIsSync'),
				$this->getParam('createMenuLink')
			));
			
			$farmScriptId = $this->db->Insert_ID();
			
			$executeScript = true;
		} else {
			
			$info = $this->db->Execute("SELECT farmid FROM farm_role_scripts WHERE event_name=?", array($this->getParam('eventName')));
			if ($info['farmid'] != $dbFarm->ID)
				throw new Exception("You cannot change farm for script shortcut");
			
			$this->db->Execute("UPDATE farm_role_scripts SET
				scriptid	= ?,
				farm_roleid	= ?,
				params		= ?,
				target		= ?,
				version		= ?,
				timeout		= ?,
				issync		= ?
			WHERE event_name = ? AND farmid = ?
			", array(
				$this->getParam('scriptId'),
				(int)$farmRoleId,
				serialize($this->getParam('scriptOptions')),
				$target,
				$this->getParam('scriptVersion'),
				$this->getParam('scriptTimeout'),
				$this->getParam('scriptIsSync'),
				$this->getParam('eventName'),
				$farmId
			));
			
			if (!$this->getParam('isShortcut'))
				$executeScript = true;
		}

		if ($executeScript) {
			switch($target) {
				case SCRIPTING_TARGET::FARM:
					$servers = $this->db->GetAll("SELECT server_id FROM servers WHERE status IN (?,?) AND farm_id=?",
						array(SERVER_STATUS::INIT, SERVER_STATUS::RUNNING, $farmId)
					);
					break;
				case SCRIPTING_TARGET::ROLE:
					$servers = $this->db->GetAll("SELECT server_id FROM servers WHERE status IN (?,?) AND farm_roleid=?",
						array(SERVER_STATUS::INIT, SERVER_STATUS::RUNNING, $farmRoleId)
					);
					break;
				case SCRIPTING_TARGET::INSTANCE:
					$servers = $this->db->GetAll("SELECT server_id FROM servers WHERE status IN (?,?) AND server_id=?",
						array(SERVER_STATUS::INIT, SERVER_STATUS::RUNNING, $serverId)
					);				
					break;
			}
			
			if (count($servers) > 0) {			
				foreach ($servers as $server) {
					$DBServer = DBServer::LoadByID($server['server_id']);
					$message = new Scalr_Messaging_Msg_ExecScript($eventName);
					$message->meta[Scalr_Messaging_MsgMeta::EVENT_ID] = "FRSID-{$farmScriptId}";
					$DBServer->SendMessage($message);
				}
			}
		}
		
		
		
		$this->response->setJsonResponse(array('success' => true));
	}
}
