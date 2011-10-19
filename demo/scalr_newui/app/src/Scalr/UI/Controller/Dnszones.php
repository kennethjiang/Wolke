<?php

class Scalr_UI_Controller_Dnszones extends Scalr_UI_Controller
{
	const CALL_PARAM_NAME = 'dnsZoneId';

	public function hasAccess()
	{
		return $this->session->getAuthToken()->hasAccess(Scalr_AuthToken::ACCOUNT_USER);
	}

	public function defaultAction()
	{
		$this->viewAction();
	}

	public function viewAction()
	{
		$this->response->setJsonResponse(array(
			'success' => true,
			'module' => $this->response->template->fetchJs('dnszones/view.js')
		));
	}

	public function saveSettingsAction()
	{
		$this->request->defineParams(array(
			'dnsZoneId' => array('type' => 'int'),
			'axfrAllowedHosts' => array('type' => 'string'),
			'allowedAccounts' => array('type' => 'string'),
			'allowManageSystemRecords' => array('type' => 'int')
		));

		$DBDNSZone = DBDNSZone::loadById($this->getParam('dnsZoneId'));
		$this->session->getAuthToken()->hasAccessEnvironmentEx($DBDNSZone->envId);
		
		$Validator = new Validator();
		
		if ($this->getParam('axfrAllowedHosts') != '') {
			$hosts = explode(";", $this->getParam('axfrAllowedHosts'));
			foreach ($hosts as $host) {
				$chunks = explode("/", $host);
				$ip_chunks = explode(".", $chunks[0]);
				if (!$Validator->IsIPAddress($chunks[0]) || ($chunks[1] && !$Validator->IsNumeric($chunks[1])) || count($chunks) > 2 || count($ip_chunks) != 4)
					$errors['axfrAllowedHosts'] = sprintf(_("'%s' is not valid IP address or CIDR"), $host);
			}
		}
		
		if ($this->getParam('allowedAccounts')) {
			$accounts = explode(";", $this->getParam('allowedAccounts'));
			foreach ($accounts as $account) {
				if (!$Validator->IsEmail($account))
					$errors['allowedAccounts'] = sprintf(_("'%s' is not valid Email address"), $account);
			}
		}
		
		if (count($errors) == 0) {
			if ($this->getParam('axfrAllowedHosts') != $DBDNSZone->axfrAllowedHosts) {
				$DBDNSZone->axfrAllowedHosts = $this->getParam('axfrAllowedHosts');
				$DBDNSZone->isZoneConfigModified = 1;
			}
			
			$DBDNSZone->allowManageSystemRecords = $this->getParam('allowManageSystemRecords');
			$DBDNSZone->allowedAccounts = $this->getParam('allowedAccounts');
			$DBDNSZone->save();
			
			$this->response->setJsonResponse(array('success' => true));
		}
		else {
			$this->response->setJsonResponse(array(
				'success' => false,
				'errors' => $errors
			));
		}	
	}
	
	public function settingsAction()
	{
		$this->request->defineParams(array(
			'dnsZoneId' => array('type' => 'int')
		));

		$DBDNSZone = DBDNSZone::loadById($this->getParam('dnsZoneId'));
		$this->session->getAuthToken()->hasAccessEnvironmentEx($DBDNSZone->envId);
		
		$this->response->setJsonResponse(array(
			'success' => true,
			'moduleParams' => array(
				'axfrAllowedHosts'			=> $DBDNSZone->axfrAllowedHosts,
				'allowManageSystemRecords'	=> $DBDNSZone->allowManageSystemRecords,
				'allowedAccounts'			=> $DBDNSZone->allowedAccounts
			),
			'module' => $this->response->template->fetchJs('dnszones/settings.js')
		));
	}
	
	public function createAction()
	{
		$farmsController = self::loadController('Farms');
		if (is_null($farmsController))
			throw new Exception('Controller Farms not created');
		else
			$farms = $farmsController->getList();

		$farms[0] = "";

		$records = array();
		$nss = $this->db->GetAll("SELECT * FROM nameservers WHERE isbackup='0'");
		foreach ($nss as $ns)
			$records[] = array("id" => "c".rand(10000, 999999), "type" => "NS", "ttl" => 14400, "value" => "{$ns["host"]}.", "name" => "%hostname%.", "issystem" => 0);

		$defRecords = $this->db->GetAll("SELECT * FROM default_records WHERE clientid=?", array($this->session->getClientId()));
		foreach ($defRecords as $record)
			$records[] = $record;

		$this->response->setJsonResponse(array(
			'success' => true,
			'module' => $this->response->template->fetchJs('dnszones/create.js'),
			'moduleParams' => array(
				'farms' => $farms,
				'farmRoles' => array(),
				'action' => 'create',
				'allowManageSystemRecords' => '0',
				'zone' => array(
					'domainName' => Scalr::GenerateUID() . '.' . CONFIG::$DNS_TEST_DOMAIN_NAME,
					'domainType' => 'scalr',
					'soaRetry' => '7200',
					'soaRefresh' => '14400',
					'soaExpire' => '86400'
				),
				'records' => $records
			)
		));
	}

	public function editAction()
	{
		$this->request->defineParams(array(
			'dnsZoneId' => array('type' => 'int')
		));

		$DBDNSZone = DBDNSZone::loadById($this->getParam('dnsZoneId'));
		$this->session->getAuthToken()->hasAccessEnvironmentEx($DBDNSZone->envId);

		$farmsController = self::loadController('Farms');
		if (is_null($farmsController))
			throw new Exception('Controller Farms not created');
		else
			$farms = $farmsController->getList();

		$farms[0] = '';
		$farmRoles = array();

		if ($DBDNSZone->farmId) {
			$this->request->setParams(array('farmId' => $DBDNSZone->farmId));

			$farmRolesController = self::loadController('Roles', 'Scalr_UI_Controller_Farms');
			if (is_null($farmRolesController))
				throw new Exception('Controller Farms_Roles not created');

			$farmRoles = $farmRolesController->getList();
			if (count($farmRoles))
				$farmRoles[0] = array('id' => 0, 'name' => '');
		}

		$this->response->setJsonResponse(array(
			'success' => true,
			'module' => $this->response->template->fetchJs('dnszones/create.js'),
			'moduleParams' => array(
				'farms' => $farms,
				'farmRoles' => $farmRoles,
				'action' => 'edit',
				'allowManageSystemRecords' => $DBDNSZone->allowManageSystemRecords,
				'zone' => array(
					'domainId' => $DBDNSZone->id,
					'domainName' => $DBDNSZone->zoneName,
					'soaRetry' => $DBDNSZone->soaRetry,
					'soaRefresh' => $DBDNSZone->soaRefresh,
					'soaExpire' => $DBDNSZone->soaExpire,
					'domainFarm' => $DBDNSZone->farmId,
					'domainFarmRole' => $DBDNSZone->farmRoleId
				),
				'records' => $DBDNSZone->getRecords()
			)
		));
	}

	public function saveAction()
	{
		$this->request->defineParams(array(
			'domainId' => array('type' => 'int'),

			'domainName', 'domainType',

			'domainFarm' => array('type' => 'int'),
			'domainFarmRole' => array('type' => 'int'),

			'soaRefresh' => array('type' => 'int'),
			'soaExpire' => array('type' => 'int'),
			'soaRetry' => array('type' => 'int'),

			'records' => array('type' => 'json')
		));

		$errors = array();

		// validate farmId, farmRoleId
		$farmId = 0;
		$farmRoleId = 0;
		if ($this->getParam('domainFarm')) {
			$DBFarm = DBFarm::LoadByID($this->getParam('domainFarm'));

			if (! $this->session->getAuthToken()->hasAccessEnvironment($DBFarm->EnvID))
				$errors['domainFarm'] = _('Farm not found');
			else {
				$farmId = $DBFarm->ID;

				if ($this->getParam('domainFarmRole')) {
					$DBFarmRole = DBFarmRole::LoadByID($this->getParam('domainFarmRole'));
					if ($DBFarmRole->FarmID != $DBFarm->ID)
						$errors['domainFarmRole'] = _('Role not found');
					else
						$farmRoleId = $DBFarmRole->ID;
				}
			}
		}
		
		// validate domain name
		$domainName = '';
		if (! $this->getParam('domainId')) {
			if ($this->getParam('domainType') == 'own') {
				$Validator = new Validator();
				if (! $Validator->IsDomain($this->getParam('domainName')))
					$errors['domainName'] = _("Invalid domain name");
				else {
					$domainChunks = explode(".", $this->getParam('domainName'));
					$chkDmn = '';

					while (count($domainChunks) > 0) {
						$chkDmn = trim(array_pop($domainChunks).".{$chkDmn}", ".");
						$chkDomainId = $this->db->GetOne("SELECT id FROM dns_zones WHERE zone_name=? AND env_id != ?", array($chkDmn, $this->session->getEnvironmentId()));
						if ($chkDomainId) {
							if ($chkDmn == $this->getParam('domainName'))
								$errors['domainName'] = sprintf(_("%s already exists on scalr nameservers"), $this->getParam('domainName'));
							else {
								$chkDnsZone = DBDNSZone::loadById($chkDomainId);
								$client = Client::Load($this->session->getClientId());
								$access = false;
								foreach (explode(";", $chkDnsZone->allowedAccounts) as $email) {
									if ($email == $client->Email)
										$access = true;
								}
								
								if (!$access)
									$errors['domainName'] = sprintf(_("You cannot use %s domain name because top level domain %s does not belong to you"), $this->getParam('domainName'), $chkDmn);
							}
						}
					}

					//if (! $errors['domainName'])
						$domainName = $this->getParam('domainName');
				}
			} else
				$domainName = Scalr::GenerateUID() . '.' . CONFIG::$DNS_TEST_DOMAIN_NAME;

			// check in DB
			$rez = $this->db->GetOne("SELECT id FROM dns_zones WHERE zone_name = ?", array($domainName));
			if ($rez)
				$errors['domainName'] = 'Domain name already exist in database';
		}

		$records = array();
		foreach ($this->getParam('records') as $key => $r) {
			if ($r['name'] || $r['value']) {
				$r['name'] = str_replace("%hostname%", "{$domainName}", $r['name']);
				$r['value'] = str_replace("%hostname%", "{$domainName}", $r['value']);

				$records[$key] = $r;
			}
		}

		$recordsValidation = Scalr_Net_Dns_Zone::validateRecords($records);
		if ($recordsValidation !== true)
			$errors['records'] = $recordsValidation;

		if (count($errors) == 0) {
			if ($this->getParam('domainId')) {
				$DBDNSZone = DBDNSZone::loadById($this->getParam('domainId'));
				$this->session->getAuthToken()->hasAccessEnvironmentEx($DBDNSZone->envId);

				$DBDNSZone->soaRefresh = $this->getParam('soaRefresh');
				$DBDNSZone->soaExpire = $this->getParam('soaExpire');
				$DBDNSZone->soaRetry = $this->getParam('soaRetry');

				$successMessage = _("DNS zone successfully updated. It could take up to 5 minutes to update it on NS servers.");
			} else {
				$DBDNSZone = DBDNSZone::create(
					$domainName,
					$this->getParam('soaRefresh'),
					$this->getParam('soaExpire'),
					str_replace('@', '.', $this->db->GetOne("SELECT email FROM clients WHERE id=?", array(
						$this->session->getClientId()
					))),
					$this->getParam('soaRetry')
				);

				$DBDNSZone->clientId = $this->session->getClientId();
				$DBDNSZone->envId = $this->session->getEnvironmentId();

				$successMessage = _("DNS zone successfully added to database. It could take up to 5 minutes to setup it on NS servers.");
			}

			$DBDNSZone->farmRoleId = $farmRoleId;
			$DBDNSZone->farmId = $farmId;

			$DBDNSZone->setRecords($records);
			$DBDNSZone->save(true);

			$this->response->setJsonResponse(array(
				'success' => true,
				'message' => $successMessage
			));
		} else {
			$this->response->setJsonResponse(array(
				'success' => false,
				'errors' => $errors
			));
		}
	}

	public function getFarmRolesAction()
	{
		$farmRolesController = self::loadController('Roles', 'Scalr_UI_Controller_Farms');
		if (is_null($farmRolesController))
			throw new Exception('Controller Farms_Roles not created');

		$farmRoles = $farmRolesController->getList();
		if (count($farmRoles))
			$farmRoles[0] = array('id' => 0, 'name' => '');

		$this->response->setJsonResponse(array(
			'success' => true,
			'farmRoles' => $farmRoles
		));
	}


	public function xRemoveZonesAction()
	{
		$this->request->defineParams(array(
			'zones' => array('type' => 'json')
		));
		
		foreach ($this->getParam('zones') as $dd) {
			$zone = DBDNSZone::loadById($dd);
			if (!$this->session->getAuthToken()->hasAccessEnvironment($zone->envId))
				continue;

			$zone->status = DNS_ZONE_STATUS::PENDING_DELETE;
			$zone->save();
		}

		$this->response->setJsonResponse(array('success' => true));
	}
	
	public function xListViewZonesAction()
	{
		$this->request->defineParams(array(
			'clientId' => array('type' => 'int'),
			'farmRoleId' => array('type' => 'int'),
			'farmId' => array('type' => 'int'),
			'dnsZoneId' => array('type' => 'int'),
			'sort' => array('type' => 'string', 'default' => 'id'),
			'dir' => array('type' => 'string', 'default' => 'ASC')
		));

		$sql = "select * FROM dns_zones WHERE env_id='{$this->session->getEnvironmentId()}'";

		if ($this->getParam('clientId'))
			$sql .= " AND client_id=".$this->db->qstr($this->getParam('clientId'));

		if ($this->getParam('farmRoleId'))
			$sql .= " AND farm_roleid=".$this->db->qstr($this->getParam('farmRoleId'));

		if ($this->getParam('farmId'))
			$sql .= " AND farm_id=".$this->db->qstr($this->getParam('farmId'));

		if ($this->getParam('dnsZoneId'))
			$sql .= " AND id=".$this->db->qstr($this->getParam('dnsZoneId'));


		$response = $this->buildResponseFromSql($sql, array("zone_name", "id", "farm_id", "farm_roleid"));

		foreach ($response["data"] as &$row) {
			if ($row['farm_roleid']) {
				try {
					$DBFarmRole = DBFarmRole::LoadByID($row['farm_roleid']);

					$row['role_name'] = $DBFarmRole->GetRoleObject()->name;
					$row['farm_name'] = $DBFarmRole->GetFarmObject()->Name;
					$row['farm_id'] = $DBFarmRole->FarmID;
				}
				catch(Exception $e)
				{
					$row['farm_roleid'] = false;
				}
			}

			if ($row['farm_id'] && !$row['farm_name'])
			{
				$DBFarm = DBFarm::LoadByID($row['farm_id']);

				$row['farm_name'] = $DBFarm->Name;
				$row['farm_id'] = $DBFarm->ID;
			}
		}

		$this->response->setJsonResponse($response);
	}
}
