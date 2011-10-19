<?php

class Scalr_UI_Controller_Environments extends Scalr_UI_Controller
{
	const CALL_PARAM_NAME = 'envId';
	
	private $checkVarError;

	public function hasAccess()
	{
		return $this->session->getAuthToken()->hasAccess(Scalr_AuthToken::SCALR_ADMIN | Scalr_AuthToken::ACCOUNT_ADMIN);
	}

	public function defaultAction()
	{
		$this->viewAction();
	}

	public function viewAction()
	{
		$this->response->setJsonResponse(array(
			'success' => true,
			'module' => $this->response->template->fetchJs('environments/view.js')
		));
	}

	public function xListViewEnvAction()
	{
		$sql = "SELECT
			id,
			name,
			dt_added AS dtAdded,
			is_system AS isSystem
			FROM client_environments
			WHERE client_id = ?";

		$rows = $this->db->GetAll($sql, array($this->session->getClientId()));
		foreach ($rows as &$row)
		{
			foreach (Scalr_Model::init(Scalr_Model::ENVIRONMENT)->loadById($row['id'])->getEnabledPlatforms() as $platform)
				$row['platforms'][] = SERVER_PLATFORMS::GetName($platform);
		}

		$this->response->setJsonResponse(array('success' => true, 'data' => $rows, 'total' => count($rows)));
	}

	public function editAction()
	{
		$this->request->defineParams(array('envId' => array('type' => 'int')));

		$env = Scalr_Model::init(Scalr_Model::ENVIRONMENT)->loadById($this->getParam('envId'));
		if ($env->clientId != $this->session->getClientId())
			throw new Exception('未找到该环境');

		$params = array();
		$eucaParams = array();

		$params[ENVIRONMENT_SETTINGS::MAX_INSTANCES_LIMIT] = $env->getPlatformConfigValue(ENVIRONMENT_SETTINGS::MAX_INSTANCES_LIMIT);
		$params[ENVIRONMENT_SETTINGS::MAX_EIPS_LIMIT] = $env->getPlatformConfigValue(ENVIRONMENT_SETTINGS::MAX_EIPS_LIMIT);
		$params[ENVIRONMENT_SETTINGS::SYNC_TIMEOUT] = $env->getPlatformConfigValue(ENVIRONMENT_SETTINGS::SYNC_TIMEOUT);
		$params[ENVIRONMENT_SETTINGS::TIMEZONE] = $env->getPlatformConfigValue(ENVIRONMENT_SETTINGS::TIMEZONE);

		$params[ENVIRONMENT_SETTINGS::API_ENABLED] = $env->getPlatformConfigValue(ENVIRONMENT_SETTINGS::API_ENABLED);
		$params[ENVIRONMENT_SETTINGS::API_ALLOWED_IPS] = $env->getPlatformConfigValue(ENVIRONMENT_SETTINGS::API_ALLOWED_IPS);
		$params[ENVIRONMENT_SETTINGS::API_KEYID] = $env->getPlatformConfigValue(ENVIRONMENT_SETTINGS::API_KEYID);
		$params[ENVIRONMENT_SETTINGS::API_ACCESS_KEY] = $env->getPlatformConfigValue(ENVIRONMENT_SETTINGS::API_ACCESS_KEY);

		foreach ($env->getEnabledPlatforms() as $platform) {
			$params[$platform . '.is_enabled'] = true;
			if ($platform == SERVER_PLATFORMS::EC2) {
				$params[Modules_Platforms_Ec2::ACCOUNT_ID] = $env->getPlatformConfigValue(Modules_Platforms_Ec2::ACCOUNT_ID);
				$params[Modules_Platforms_Ec2::ACCESS_KEY] = $env->getPlatformConfigValue(Modules_Platforms_Ec2::ACCESS_KEY);
				$params[Modules_Platforms_Ec2::SECRET_KEY] = $env->getPlatformConfigValue(Modules_Platforms_Ec2::SECRET_KEY) != '' ? '******' : '';
				$params[Modules_Platforms_Ec2::PRIVATE_KEY] = $env->getPlatformConfigValue(Modules_Platforms_Ec2::PRIVATE_KEY) != '' ? true : false;
				$params[Modules_Platforms_Ec2::CERTIFICATE] = $env->getPlatformConfigValue(Modules_Platforms_Ec2::CERTIFICATE) != '' ? true : false;

			} elseif ($platform == SERVER_PLATFORMS::RDS) {
				$params[Modules_Platforms_Rds::ACCOUNT_ID] = $env->getPlatformConfigValue(Modules_Platforms_Rds::ACCOUNT_ID);
				$params[Modules_Platforms_Rds::ACCESS_KEY] = $env->getPlatformConfigValue(Modules_Platforms_Rds::ACCESS_KEY);
				$params[Modules_Platforms_Rds::SECRET_KEY] = $env->getPlatformConfigValue(Modules_Platforms_Rds::SECRET_KEY) != '' ? '******' : false;
				$params[Modules_Platforms_Rds::PRIVATE_KEY] = $env->getPlatformConfigValue(Modules_Platforms_Rds::PRIVATE_KEY) != '' ? true : false;
				$params[Modules_Platforms_Rds::CERTIFICATE] = $env->getPlatformConfigValue(Modules_Platforms_Rds::CERTIFICATE) != '' ? true : false;

				if (
					$env->getPlatformConfigValue(Modules_Platforms_Ec2::ACCOUNT_ID) == $env->getPlatformConfigValue(Modules_Platforms_Rds::ACCOUNT_ID) &&
					$env->getPlatformConfigValue(Modules_Platforms_Ec2::ACCESS_KEY) == $env->getPlatformConfigValue(Modules_Platforms_Rds::ACCESS_KEY) &&
					$env->getPlatformConfigValue(Modules_Platforms_Ec2::SECRET_KEY) == $env->getPlatformConfigValue(Modules_Platforms_Rds::SECRET_KEY) &&
					$env->getPlatformConfigValue(Modules_Platforms_Ec2::PRIVATE_KEY) == $env->getPlatformConfigValue(Modules_Platforms_Rds::PRIVATE_KEY) &&
					$env->getPlatformConfigValue(Modules_Platforms_Ec2::CERTIFICATE) == $env->getPlatformConfigValue(Modules_Platforms_Rds::CERTIFICATE)
				)
					$params['rds.the_same_as_ec2'] = true;

			} elseif ($platform == SERVER_PLATFORMS::RACKSPACE) {
				$rows = $this->db->GetAll('SELECT * FROM client_environment_properties WHERE env_id = ? AND name LIKE "rackspace.%" AND `group` != "" GROUP BY `group', $env->id);
				foreach ($rows as $value) {
					$cloud = $value['group'];
					$rsParams[$cloud] = array(
						Modules_Platforms_Rackspace::USERNAME => $env->getPlatformConfigValue(Modules_Platforms_Rackspace::USERNAME, true, $cloud),
						Modules_Platforms_Rackspace::API_KEY => $env->getPlatformConfigValue(Modules_Platforms_Rackspace::API_KEY, true, $cloud),
						Modules_Platforms_Rackspace::IS_MANAGED => $env->getPlatformConfigValue(Modules_Platforms_Rackspace::IS_MANAGED, true, $cloud),
					);
				}
				$params[Modules_Platforms_Rackspace::USERNAME] = $env->getPlatformConfigValue(Modules_Platforms_Rackspace::USERNAME);
				$params[Modules_Platforms_Rackspace::API_KEY] = $env->getPlatformConfigValue(Modules_Platforms_Rackspace::API_KEY);

			} elseif ($platform == SERVER_PLATFORMS::EUCALYPTUS) {
				$rows = $this->db->GetAll('SELECT * FROM client_environment_properties WHERE env_id = ? AND name LIKE "eucalyptus.%" AND `group` != "" GROUP BY `group', $env->id);
				foreach ($rows as $value) {
					$cloud = $value['group'];
					$eucaParams[$cloud] = array(
						Modules_Platforms_Eucalyptus::ACCOUNT_ID => $env->getPlatformConfigValue(Modules_Platforms_Eucalyptus::ACCOUNT_ID, true, $cloud),
						Modules_Platforms_Eucalyptus::ACCESS_KEY => $env->getPlatformConfigValue(Modules_Platforms_Eucalyptus::ACCESS_KEY, true, $cloud),
						Modules_Platforms_Eucalyptus::EC2_URL => $env->getPlatformConfigValue(Modules_Platforms_Eucalyptus::EC2_URL, true, $cloud),
						Modules_Platforms_Eucalyptus::S3_URL => $env->getPlatformConfigValue(Modules_Platforms_Eucalyptus::S3_URL, true, $cloud),
						Modules_Platforms_Eucalyptus::SECRET_KEY => $env->getPlatformConfigValue(Modules_Platforms_Eucalyptus::SECRET_KEY, true, $cloud) != '' ? '******' : false,
						Modules_Platforms_Eucalyptus::PRIVATE_KEY => $env->getPlatformConfigValue(Modules_Platforms_Eucalyptus::PRIVATE_KEY, true, $cloud) != '' ? true : false,
						Modules_Platforms_Eucalyptus::CLOUD_CERTIFICATE => $env->getPlatformConfigValue(Modules_Platforms_Eucalyptus::CLOUD_CERTIFICATE, true, $cloud) != '' ? true : false
					);
				}
			} elseif ($platform == SERVER_PLATFORMS::NIMBULA) {
				$params[Modules_Platforms_Nimbula::API_URL] = $env->getPlatformConfigValue(Modules_Platforms_Nimbula::API_URL);
				$params[Modules_Platforms_Nimbula::USERNAME] = $env->getPlatformConfigValue(Modules_Platforms_Nimbula::USERNAME);
				$params[Modules_Platforms_Nimbula::PASSWORD] = $env->getPlatformConfigValue(Modules_Platforms_Nimbula::PASSWORD);
			}
		}

		$timezones = array();
		$timezoneAbbreviationsList = timezone_abbreviations_list();
		foreach ($timezoneAbbreviationsList as $timezoneAbbreviations) {
			foreach ($timezoneAbbreviations as $value) {
				if (preg_match( '/^(America|Antartica|Arctic|Asia|Atlantic|Europe|Indian|Pacific|Australia)\//', $value['timezone_id']))
					$timezones[$value['timezone_id']] = $value['offset'];
			}
		}
		
		@ksort($timezones);
		$timezones = array_keys($timezones);

		$this->response->setJsonResponse(array(
			'success' => true,
			'module' => $this->response->template->fetchJs('environments/edit.js'),
			'moduleParams' => array(
				'env' => $env,
				'params' => $params,
				'eucaParams' => $eucaParams,
				'rsParams'	=> (array)$rsParams,
				'timezones' => $timezones
			)
		));
	}

	private function checkVar($name, $type, $env, $requiredError = '', $group = '')
	{
		$varName = str_replace('.', '_', ($group != '' ? $name . '.' . $group : $name));
		
		switch ($type) {
			case 'int':
				if ($this->getParam($varName)) {
					return intval($this->getParam($varName));
				} else {
					$value = $env->getPlatformConfigValue($name, true, $group);
					if (!$value && $requiredError)
						$this->checkVarError[$name] = $requiredError;

					return $value;
				}
				break;

			case 'string':
				if ($this->getParam($varName)) {
					return $this->getParam($varName);
				} else {
					$value = $env->getPlatformConfigValue($name, true, $group);
					if ($value == '' && $requiredError)
						$this->checkVarError[$name] = $requiredError;

					return $value;
				}
				break;

			case 'password':
				if ($this->getParam($varName) && $this->getParam($varName) != '******') {
					return $this->getParam($varName);
				} else {
					$value = $env->getPlatformConfigValue($name, true, $group);
					if ($value == '' && $requiredError)
						$this->checkVarError[$name] = $requiredError;

					return $value;
				}
				break;

			case 'bool':
				return $this->getParam($varName) ? 1 : 0;

			case 'file':
				if (isset($_FILES[$varName]['tmp_name']) && ($value = @file_get_contents($_FILES[$varName]['tmp_name'])) != '') {
					return trim($value);
				} else {
					$value = $env->getPlatformConfigValue($name, true, $group);
					if ($value == '' && $requiredError)
						$this->checkVarError[$name] = $requiredError;

					return $value;
				}
				break;
		}
	}

	public function saveAction()
	{
		$this->request->defineParams(array('envId' => array('type' => 'int'), 'eucalyptusClouds' => array('type' => 'json')));

		$env = Scalr_Model::init(Scalr_Model::ENVIRONMENT)->loadById($this->getParam('envId'));
		if ($env->clientId != $this->session->getClientId()) {
			throw new Exception('Environment not found');
		}

		$this->checkVarError = array();
		$pars = array();
		$checkErr = array();
		$glErr = array();
		$glCheckErr = array();
		$enabled = array();

		// check for settings
		$pars[ENVIRONMENT_SETTINGS::MAX_INSTANCES_LIMIT] = $this->checkVar(ENVIRONMENT_SETTINGS::MAX_INSTANCES_LIMIT, 'int', $env, "Max instances limit required");
		$pars[ENVIRONMENT_SETTINGS::MAX_EIPS_LIMIT] = $this->checkVar(ENVIRONMENT_SETTINGS::MAX_EIPS_LIMIT, 'int', $env, "Max elastic ips limit required");
		$pars[ENVIRONMENT_SETTINGS::SYNC_TIMEOUT] = $this->checkVar(ENVIRONMENT_SETTINGS::SYNC_TIMEOUT, 'int', $env, "Sync timeout required");
		$pars[ENVIRONMENT_SETTINGS::TIMEZONE] = $this->checkVar(ENVIRONMENT_SETTINGS::TIMEZONE, 'string', $env, "Timezone required");
		$pars[ENVIRONMENT_SETTINGS::API_ENABLED] = $this->checkVar(ENVIRONMENT_SETTINGS::API_ENABLED, 'bool', $env);
		$pars[ENVIRONMENT_SETTINGS::API_ALLOWED_IPS] = $this->checkVar(ENVIRONMENT_SETTINGS::API_ALLOWED_IPS, 'string', $env);

		// check for EC2
		if ($this->getParam(SERVER_PLATFORMS::EC2 . '_is_enabled') == 'on') {
			$enabled[SERVER_PLATFORMS::EC2] = true;

			$pars[Modules_Platforms_Ec2::ACCOUNT_ID] = $this->checkVar(Modules_Platforms_Ec2::ACCOUNT_ID, 'string', $env, "AWS Key ID required");

			if (! is_numeric($pars[Modules_Platforms_Ec2::ACCOUNT_ID]) || strlen($pars[Modules_Platforms_Ec2::ACCOUNT_ID]) != 12)
				//$err[Modules_Platforms_Ec2::ACCOUNT_ID] = _("AWS numeric account ID required (See <a href='/faq.html'>FAQ</a> for info on where to get it).");
				$this->checkVarError[Modules_Platforms_Ec2::ACCOUNT_ID] = _("AWS numeric account ID required");
			else
				$pars[Modules_Platforms_Ec2::ACCOUNT_ID] = preg_replace("/[^0-9]+/", "", $pars[Modules_Platforms_Ec2::ACCOUNT_ID]);

			$pars[Modules_Platforms_Ec2::ACCESS_KEY] = $this->checkVar(Modules_Platforms_Ec2::ACCESS_KEY, 'string', $env, "AWS Access Key required");
			$pars[Modules_Platforms_Ec2::SECRET_KEY] = $this->checkVar(Modules_Platforms_Ec2::SECRET_KEY, 'password', $env, "AWS Access Key required");
			$pars[Modules_Platforms_Ec2::PRIVATE_KEY] = $this->checkVar(Modules_Platforms_Ec2::PRIVATE_KEY, 'file', $env, "AWS x.509 Private Key required");
			$pars[Modules_Platforms_Ec2::CERTIFICATE] = $this->checkVar(Modules_Platforms_Ec2::CERTIFICATE, 'file', $env, "AWS x.509 Certificate required");

			if (! count($this->checkVarError)) {
				if (
					$pars[Modules_Platforms_Ec2::ACCOUNT_ID] != $env->getPlatformConfigValue(Modules_Platforms_Ec2::ACCOUNT_ID) or
					$pars[Modules_Platforms_Ec2::ACCESS_KEY] != $env->getPlatformConfigValue(Modules_Platforms_Ec2::ACCESS_KEY) or
					$pars[Modules_Platforms_Ec2::SECRET_KEY] != $env->getPlatformConfigValue(Modules_Platforms_Ec2::SECRET_KEY) or
					$pars[Modules_Platforms_Ec2::PRIVATE_KEY] != $env->getPlatformConfigValue(Modules_Platforms_Ec2::PRIVATE_KEY) or
					$pars[Modules_Platforms_Ec2::CERTIFICATE] != $env->getPlatformConfigValue(Modules_Platforms_Ec2::CERTIFICATE)
				) {
					try {
						$AmazonEC2Client = Scalr_Service_Cloud_Aws::newEc2(
							'us-east-1',
							$pars[Modules_Platforms_Ec2::PRIVATE_KEY],
							$pars[Modules_Platforms_Ec2::CERTIFICATE]
						);
						$AmazonEC2Client->describeInstances();
					} catch (Exception $e) {
						$checkErr[] = sprintf(_("Failed to verify your EC2 certificate and private key. %s"), $e->getMessage());
					}

					try {
						$AmazonS3 = new AmazonS3($pars[Modules_Platforms_Ec2::ACCESS_KEY], $pars[Modules_Platforms_Ec2::SECRET_KEY]);
						$buckets = $AmazonS3->ListBuckets();
					} catch(Exception $e) {
						$checkErr[] = sprintf(_("Failed to verify your EC2 access key and secret key. %s"), $e->getMessage());
					}
				}
			}

			$glErr = array_merge($glErr, $this->checkVarError);
			$glCheckErr = array_merge($glCheckErr, $checkErr);
		} else {
			$enabled[SERVER_PLATFORMS::EC2] = false;
		}

		// check for RDS
		if ($this->getParam(SERVER_PLATFORMS::RDS . '_is_enabled') == 'on') {
			$enabled[SERVER_PLATFORMS::RDS] = true;

			if ($this->getParam(SERVER_PLATFORMS::RDS . '_the_same_as_ec2') == 'on') {
				$pars[Modules_Platforms_Rds::ACCOUNT_ID] = $pars[Modules_Platforms_Ec2::ACCOUNT_ID];
				$pars[Modules_Platforms_Rds::ACCESS_KEY] = $pars[Modules_Platforms_Ec2::ACCESS_KEY];
				$pars[Modules_Platforms_Rds::SECRET_KEY] = $pars[Modules_Platforms_Ec2::SECRET_KEY];
				$pars[Modules_Platforms_Rds::PRIVATE_KEY] = $pars[Modules_Platforms_Ec2::PRIVATE_KEY];
				$pars[Modules_Platforms_Rds::CERTIFICATE] = $pars[Modules_Platforms_Ec2::CERTIFICATE];
			} else {
				$this->checkVarError = array();
				$checkErr = array();

				$pars[Modules_Platforms_Rds::ACCOUNT_ID] = $this->checkVar(Modules_Platforms_Rds::ACCOUNT_ID, 'int', $env, "AWS Key ID required");

				if (! is_numeric($pars[Modules_Platforms_Rds::ACCOUNT_ID]) || strlen($pars[Modules_Platforms_Rds::ACCOUNT_ID]) != 12)
					//$err[Modules_Platforms_Ec2::ACCOUNT_ID] = _("AWS numeric account ID required (See <a href='/faq.html'>FAQ</a> for info on where to get it).");
					$this->checkVarError[Modules_Platforms_Rds::ACCOUNT_ID] = _("AWS numeric account ID required");
				else
					$pars[Modules_Platforms_Rds::ACCOUNT_ID] = preg_replace("/[^0-9]+/", "", $pars[Modules_Platforms_Rds::ACCOUNT_ID]);

				$pars[Modules_Platforms_Rds::ACCESS_KEY] = $this->checkVar(Modules_Platforms_Rds::ACCESS_KEY, 'string', $env, "AWS Access Key required");
				$pars[Modules_Platforms_Rds::SECRET_KEY] = $this->checkVar(Modules_Platforms_Rds::SECRET_KEY, 'password', $env, "AWS Access Key required");
				$pars[Modules_Platforms_Rds::PRIVATE_KEY] = $this->checkVar(Modules_Platforms_Rds::PRIVATE_KEY, 'file', $env, "AWS x.509 Private Key required");
				$pars[Modules_Platforms_Rds::CERTIFICATE] = $this->checkVar(Modules_Platforms_Rds::CERTIFICATE, 'file', $env, "AWS x.509 Certificate required");
			}

			if (! count($this->checkVarError)) {
				/* TODO: check
				*/
			}

			$glErr = array_merge($glErr, $this->checkVarError);
			$glCheckErr = array_merge($glCheckErr, $checkErr);
		} else {
			$enabled[SERVER_PLATFORMS::RDS] = false;
		}

		// check for Nimbula
		if ($this->getParam(SERVER_PLATFORMS::NIMBULA . '_is_enabled') == 'on') {
			$enabled[SERVER_PLATFORMS::NIMBULA] = true;

			$this->checkVarError = array();
			$checkErr = array();

			$pars[Modules_Platforms_Nimbula::API_URL] = $this->checkVar(Modules_Platforms_Nimbula::API_URL, 'string', $env, "API URL required");
			$pars[Modules_Platforms_Nimbula::USERNAME] = $this->checkVar(Modules_Platforms_Nimbula::USERNAME, 'string', $env, "Username required");
			$pars[Modules_Platforms_Nimbula::PASSWORD] = $this->checkVar(Modules_Platforms_Nimbula::PASSWORD, 'string', $env, "Password required");

			if (! count($this->checkVarError)) {
				// TODO: check Rackspace's credentials
			}

			$glErr = array_merge($glErr, $this->checkVarError);
			$glCheckErr = array_merge($glCheckErr, $checkErr);
		} else {
			$enabled[SERVER_PLATFORMS::NIMBULA] = false;
		}
		
		$cloudsPars = array();
		
		/**** Rackspace ****/
		$rsLocations = array('rs-ORD1', 'rs-LONx');
		$enabled[SERVER_PLATFORMS::RACKSPACE] = false;
		
		foreach ($rsLocations as $rsLocation)
		{
			if ($this->getParam("rackspace_is_enabled_{$rsLocation}") == 'on') {
				$enabled[SERVER_PLATFORMS::RACKSPACE] = true;
				
				$cloudsPars[$rsLocation][Modules_Platforms_Rackspace::USERNAME] = $this->checkVar(Modules_Platforms_Rackspace::USERNAME, 'string', $env, "Username required", $rsLocation);
				$cloudsPars[$rsLocation][Modules_Platforms_Rackspace::API_KEY] = $this->checkVar(Modules_Platforms_Rackspace::API_KEY, 'string', $env, "API Key required", $rsLocation);
				$cloudsPars[$rsLocation][Modules_Platforms_Rackspace::IS_MANAGED] = $this->checkVar(Modules_Platforms_Rackspace::IS_MANAGED, 'bool', $env, "", $rsLocation);			
			}
			else {
				$cloudsPars[$rsLocation][Modules_Platforms_Rackspace::USERNAME] = false;
				$cloudsPars[$rsLocation][Modules_Platforms_Rackspace::API_KEY] = false;
				$cloudsPars[$rsLocation][Modules_Platforms_Rackspace::IS_MANAGED] = false;
			}
		}
		
		/*******************/

		// check for Eucalyptus
		$clouds = $this->getParam('eucalyptusClouds');
		$cloudsDeleted = array();
		if (count($clouds)) {
			$enabled[SERVER_PLATFORMS::EUCALYPTUS] = true;

			$this->checkVarError = array();
			$checkErr = array();

			foreach ($clouds as $cloud) {
				$cloudsPars[$cloud][Modules_Platforms_Eucalyptus::ACCOUNT_ID] = $this->checkVar(Modules_Platforms_Eucalyptus::ACCOUNT_ID, 'string', $env, "Account ID required", $cloud);
				$cloudsPars[$cloud][Modules_Platforms_Eucalyptus::ACCESS_KEY] = $this->checkVar(Modules_Platforms_Eucalyptus::ACCESS_KEY, 'string', $env, "Access Key required", $cloud);
				$cloudsPars[$cloud][Modules_Platforms_Eucalyptus::EC2_URL] = $this->checkVar(Modules_Platforms_Eucalyptus::EC2_URL, 'string', $env, "EC2 URL required", $cloud);
				$cloudsPars[$cloud][Modules_Platforms_Eucalyptus::S3_URL] = $this->checkVar(Modules_Platforms_Eucalyptus::S3_URL, 'string', $env, "S3 URL required", $cloud);
				$cloudsPars[$cloud][Modules_Platforms_Eucalyptus::SECRET_KEY] = $this->checkVar(Modules_Platforms_Eucalyptus::SECRET_KEY, 'password', $env, "Secret Key required", $cloud);
				$cloudsPars[$cloud][Modules_Platforms_Eucalyptus::PRIVATE_KEY] = $this->checkVar(Modules_Platforms_Eucalyptus::PRIVATE_KEY, 'file', $env, "x.509 Private Key required", $cloud);
				$cloudsPars[$cloud][Modules_Platforms_Eucalyptus::CERTIFICATE] = $this->checkVar(Modules_Platforms_Eucalyptus::CERTIFICATE, 'file', $env, "x.509 Certificate required", $cloud);
				$cloudsPars[$cloud][Modules_Platforms_Eucalyptus::CLOUD_CERTIFICATE] = $this->checkVar(Modules_Platforms_Eucalyptus::CLOUD_CERTIFICATE, 'file', $env, "x.509 Cloud Certificate required", $cloud);
			}

			if (! count($this->checkVarError)) {
				/* TODO: check
				*/
			}

			$glErr = array_merge($glErr, $this->checkVarError);
			$glCheckErr = array_merge($glCheckErr, $checkErr);
		} else {
			$enabled[SERVER_PLATFORMS::EUCALYPTUS] = false;
		}
		// clear old cloud locations
		foreach ($this->db->GetAll('SELECT * FROM client_environment_properties WHERE env_id = ? AND name LIKE "eucalyptus.%" AND `group` != "" GROUP BY `group', $env->id) as $key => $value) {
			if (! in_array($value['group'], $clouds))
				$cloudsDeleted[] = $value['group'];
		}

		if (count($glErr)) {
			$this->response->setJsonResponse(array('success' => false, 'errors' => $glErr), 'text');
		} elseif (count($glCheckErr)) {
			$this->response->setJsonResponse(array('success' => false, 'error' => $glCheckErr), 'text');
		} else {
			$this->db->BeginTrans();
			try {
				foreach ($enabled as $key => $flag) {
					$env->enablePlatform($key, $flag);
				}
				$env->setPlatformConfig($pars);

				foreach ($cloudsPars as $cloud => $prs)
					$env->setPlatformConfig($prs, true, $cloud);

				foreach ($cloudsDeleted as $key => $cloud)
					$this->db->Execute('DELETE FROM client_environment_properties WHERE env_id = ? AND `group` = ? AND name LIKE "eucalyptus.%"', array($env->id, $cloud));

				$client = Client::Load($this->session->getClientId());
				if (!$client->GetSettingValue(CLIENT_SETTINGS::DATE_ENV_CONFIGURED))
		        	$client->SetSettingValue(CLIENT_SETTINGS::DATE_ENV_CONFIGURED, time());
					
				$this->response->setJsonResponse(array('success' => true), 'text');
			} catch (Exception $e) {
				$this->db->RollbackTrans();
				$this->response->setJsonResponse(array('success' => false, 'error' => _('Failed to save AWS settings')), 'text');
			}
			$this->db->CommitTrans();
		}
	}
}
