<?
    require("../src/prepend.inc.php");

    class AjaxUIServer
    {
    	public function __construct()
    	{
    		$this->DB = Core::GetDBInstance();
    		$this->Logger = Logger::getLogger(__CLASS__);
    	}

    	// used in: tab_fb_params.tpl
		public function GetRoleParams($farmId, $roleId)
		{
			$roleId = intval($roleId);

			try {
				$DBRole = DBRole::loadById($roleId);
				if ($DBRole->envId != 0 && !Scalr_Session::getInstance()->getAuthToken()->hasAccessEnvironment($DBRole->envId))
					throw new Exception("");
			}
			catch (Exception $e) {
				return array();
			}

			$params = $this->DB->GetAll("SELECT * FROM role_parameters WHERE role_id=? AND hash NOT IN('apache_http_vhost_template','apache_https_vhost_template')",
				array($DBRole->id)
			);

			foreach ($params as $key => $param) {
				// Prepare options array
				/*if ($param['options'])
				{
					$options = json_decode($param['options'], true);
					$fopts = array();
					foreach ($options as $option)
						$fopts[$option[0]] = $option[1];
				}*/

				$value = false;

				try
				{
					$DBFarmRole = DBFarmRole::Load($farmId, $roleId);

					$value = $this->DB->GetOne("SELECT value FROM farm_role_options WHERE farm_roleid=? AND hash=?",
						array($DBFarmRole->ID, $param['hash'])
					);
				}
				catch(Exception $e) { }

				// Get field value
				if ($value === false || $value === null)
					$value = $param['defval'];

				$params[$key]['value'] = $value;
			}

			return $params;
		}

    	public function GetServiceConfigurationsList(array $behaviors)
    	{
    		if (!is_array($behaviors))
	    		$behaviors = array($behaviors);

	    	$retval = array();

    		foreach ($behaviors as $behavior)
    		{
	    		$presets = $this->DB->Execute("SELECT id, name FROM service_config_presets WHERE env_id = ? AND role_behavior=?", array(
	    			Scalr_Session::getInstance()->getEnvironmentId(),
	    			$behavior
	    		));

	    		$itm = array();
	    		while ($preset = $presets->FetchRow())
	    			$itm[] = array('name' => $preset['name'], 'id' => $preset['id']);

	    		$retval[$behavior] = $itm;
    		}

    		return $retval;
    	}

    	public function GetServerLA($serverId)
    	{
    		$DBServer = DBServer::LoadByID($serverId);

    		if (!Scalr_Session::getInstance()->getAuthToken()->hasAccessEnvironment($DBServer->envId))
    			throw new Exception ("Server not found");

    		$snmpClient = new Scalr_Net_Snmp_Client();

    		$port = 161;
    		if ($DBServer->GetProperty(SERVER_PROPERTIES::SZR_SNMP_PORT))
    			$port = $DBServer->GetProperty(SERVER_PROPERTIES::SZR_SNMP_PORT);

    		$snmpClient->connect($DBServer->remoteIp, $port, $DBServer->GetFarmObject()->Hash);

    		return $snmpClient->get('.1.3.6.1.4.1.2021.10.1.3.1');
    	}

    	public function RemoveSnapshots(array $snapshots)
    	{
            $AmazonEC2Client = Scalr_Service_Cloud_Aws::newEc2($_SESSION['aws_region']);
			$AmazonEC2Client->SetAuthKeys(
				Scalr_Session::getInstance()->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Ec2::PRIVATE_KEY),
				Scalr_Session::getInstance()->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Ec2::CERTIFICATE)
			);
    		foreach ($snapshots as $snapshot)
    		{
    			$AmazonEC2Client->DeleteSnapshot($snapshot);
    		}

    		return true;
    	}

    	public function LoadServers($farmId, $farm_roleId)
    	{
    		$serverNames = $this->DB->GetAll("SELECT server_id, remote_ip
				FROM servers WHERE farm_id = ? AND farm_roleid = ? AND `status` = ?",
    			array($farmId, $farm_roleId, SERVER_STATUS::RUNNING)
    		);

    		print json_encode(array(
	    		"result"	=> "ok",
				"data"		=> $serverNames
	    	));

    		exit();

    	}

    	public function LoadFarmRoles($farmId, $behavior = null)
    	{
    		$sql = "SELECT farm_roles.id, roles.name FROM farm_roles
    		INNER JOIN roles ON roles.id = farm_roles.role_id WHERE farmid=?";
    		$args[] = $farmId;

    		if ($behavior) {
    			$sql .= " AND roles.id IN (SELECT role_id FROM role_behaviors WHERE behavior=?)";
    			$args[] = $behavior;
    		}

    		$roleNames = $this->DB->GetAll($sql, $args);

			print json_encode(array(
	    		"result"	=> "ok",
				"data"		=> $roleNames
	    	));

    		exit();
    	}

    	public function LoadFarms()
    	{
    		if (Scalr_Session::getInstance()->getAuthToken()->hasAccess(Scalr_AuthToken::ACCOUNT_USER))
    		{
	    		$farmsInfo = $this->DB->GetAll("SELECT id, name FROM `farms` WHERE env_id = ?",
	    			array(Scalr_Session::getInstance()->getEnvironmentId())
	    		);
    		}

    		print json_encode(array(
	    		"result"	=> "ok",
				"data"		=> $farmsInfo
	    	));

    		exit();
    	}

    	public function LoadScripts()
    	{

			if (Scalr_Session::getInstance()->getAuthToken()->hasAccess(Scalr_AuthToken::ACCOUNT_USER))
			{
				$script_filter_sql .= " AND (";
					// Show shared roles
					$script_filter_sql .= " origin='".SCRIPT_ORIGIN_TYPE::SHARED."'";

					// Show custom roles
					$script_filter_sql .= " OR (origin='".SCRIPT_ORIGIN_TYPE::CUSTOM."'
							AND clientid='".Scalr_Session::getInstance()->getClientId()."')";

					//Show approved contributed roles
					$script_filter_sql .= " OR (origin='".SCRIPT_ORIGIN_TYPE::USER_CONTRIBUTED."'
							AND (scripts.approval_state='".APPROVAL_STATE::APPROVED."'
							OR clientid='".Scalr_Session::getInstance()->getClientId()."'))";
				$script_filter_sql .= ")";


			    $sql = "SELECT scripts.id, scripts.name, MAX(script_revisions.dtcreated) as dtupdated from scripts INNER JOIN script_revisions
			    	ON script_revisions.scriptid = scripts.id WHERE 1=1 {$script_filter_sql} GROUP BY script_revisions.scriptid ORDER BY dtupdated DESC";

			    // Get list of scripts
			    $scripts = $this->DB->GetAll($sql);

			    foreach ($scripts as $script)
			    {
			    	if ($this->DB->GetOne("SELECT COUNT(*) FROM script_revisions WHERE approval_state=? AND scriptid=?",
			    		array(APPROVAL_STATE::APPROVED, $script['id'])) > 0
			    	)
			    	$result[] = $script;
			    }
			}
		    return $result;
	    }

	    public function GetScriptArgs($scriptId)
	    {
	    	if (Scalr_Session::getInstance()->getAuthToken()->hasAccess(Scalr_AuthToken::ACCOUNT_USER))
		    {
		    	$scriptId = (int)$scriptId;

	    		$dbversions = $this->DB->GetAll("SELECT * FROM script_revisions WHERE scriptid=? AND approval_state=? ORDER BY revision DESC",
		        	array($scriptId, APPROVAL_STATE::APPROVED)
		        );

	    		$versions = array();
		        foreach ($dbversions as $version)
		        {
		        	$text = preg_replace('/(\\\%)/si', '$$scalr$$', $version["script"]);
		        	preg_match_all("/\%([^\%\s]+)\%/si", $text, $matches);
		        	$vars = $matches[1];
				    $data = array();
				    foreach ($vars as $var)
				    {
				    	if (!in_array($var, array_keys(CONFIG::getScriptingBuiltinVariables())))
				    		$data[$var] = ucwords(str_replace("_", " ", $var));
				    }
				    $data = json_encode($data);

		        	$versions[] = array("revision" => $version['revision'], "fields" => $data);
		        }
		    }
	        return $versions;
	    }


	    public function LoadSecurityGroupsFromAWS()
	    {
	    	try
	    	{
				if (Scalr_Session::getInstance()->getAuthToken()->hasAccess(Scalr_AuthToken::ACCOUNT_USER))
		    	{
		    		$securityGroups = Modules_Platforms_Ec2_Helpers_Ec2::loadSecurityGroups();

		    	 	if(!$securityGroups)
		    	 		throw new Exception("No security groups");

		    		print json_encode(array(
	    			"result"	=> "ok",
					"data"		=> $securityGroups
	    			));

    				exit();
				}
				else
				  throw new Exception("You can't use it from admin's account");
			}
			catch(Exception $e)
		    {
				print json_encode(array(
	    			"result"	=> "error",
					"msg"		=> $e->getMessage()
	    			));

    				exit();
		    }
	    }
    }

    // Run
    try
    {
    	$AjaxUIServer = new AjaxUIServer();

    	$Reflect = new ReflectionClass($AjaxUIServer);
    	if (!$Reflect->hasMethod($req_action))
    		throw new Exception(sprintf("Unknown action: %s", $req_action));

    	$ReflectMethod = $Reflect->getMethod($req_action);

    	$args = array();
    	foreach ($ReflectMethod->getParameters() as $param)
    	{
    		if (!$param->isArray())
    			$args[$param->name] = $_REQUEST[$param->name];
    		else
    			$args[$param->name] = json_decode($_REQUEST[$param->name]);
    	}

    	$result = $ReflectMethod->invokeArgs($AjaxUIServer, $args);

    	if(empty($result))
	    	throw new Exception("empty result");

    	print json_encode(array(
    		"result"	=> "ok",
			"data"		=> $result
    	));

    }
    catch(Exception $e)
    {
    	print json_encode(array(
    		"result"	=> "error",
    		"msg"		=> $e->getMessage()
    	));
    }

    exit();
?>