<?php

class Scalr_UI_Controller_Tools_Aws_Rds_Instances extends Scalr_UI_Controller
{
	const CALL_PARAM_NAME = 'instanceId';

	public function defaultAction()
	{
		$this->viewAction();
	}

	public function viewAction()
	{
		$this->response->setJsonResponse(array(
			'success' => true,
			'moduleName' => $this->getModuleName('ui/tools/aws/rds/instances/view.js'),
			'moduleParams' => array(
				'locations' => Scalr_UI_Controller_Platforms::getCloudLocations(SERVER_PLATFORMS::RDS, false)
			)
		));
	}

	public function createAction()
	{
		$this->response->setJsonResponse(array(
			'success' => true,
			'module' => $this->response->template->fetchJs('tools/aws/rds/instances/create.js'),
			'moduleParams' => array(
				'locations' => Scalr_UI_Controller_Platforms::getCloudLocations(SERVER_PLATFORMS::RDS, false)
			)
		));
	}

	public function detailsAction()
	{
		$amazonRDSClient = Scalr_Service_Cloud_Aws::newRds(
			$this->session->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Rds::ACCESS_KEY),
			$this->session->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Rds::SECRET_KEY),
			$this->getParam('cloudLocation')
		);

		$info = $amazonRDSClient->DescribeDBInstances($this->getParam(self::CALL_PARAM_NAME));
		$dbinstance = $info->DescribeDBInstancesResult->DBInstances->DBInstance;
		$dbinstance->InstanceCreateTime = date("M j, Y H:i:s", strtotime($dbinstance->InstanceCreateTime));

		$sGroups = array();
		$sg = (array) $dbinstance->DBSecurityGroups;
		if (is_array($sg['DBSecurityGroup'])) {
			foreach ($sg['DBSecurityGroup'] as $g)
				$sGroups[] = "{$g->DBSecurityGroupName} ({$g->Status})";
		} else
			$sGroups[] = "{$sg['DBSecurityGroup']->DBSecurityGroupName} ({$sg['DBSecurityGroup']->Status})";

		$pGroups = array();
		$pg = (array)$dbinstance->DBParameterGroups;

		if (is_array($pg['DBParameterGroup'])) {
			foreach ($pg['DBParameterGroup'] as $g)
				$pGroups[] = "{$g->DBParameterGroupName} ({$g->ParameterApplyStatus})";
		} else
			$pGroups[] = "{$pg['DBParameterGroup']->DBParameterGroupName} ({$pg['DBParameterGroup']->ParameterApplyStatus})";

		$form = array(
			array(
				'xtype' => 'fieldset',
				'labelWidth' => 200,
				'items' => array(
					array(
						'xtype' => 'displayfield',
						'fieldLabel' => 'Name',
						'value' => (string) $dbinstance->DBInstanceIdentifier
					),
					array(
						'xtype' => 'displayfield',
						'fieldLabel' => 'Engine',
						'value' => (string) $dbinstance->Engine
					),
					array(
						'xtype' => 'displayfield',
						'fieldLabel' => 'DNS Name',
						'value' => (string) $dbinstance->Endpoint->Address
					),
					array(
						'xtype' => 'displayfield',
						'fieldLabel' => 'Port',
						'value' => (string) $dbinstance->Endpoint->Port
					),
					array(
						'xtype' => 'displayfield',
						'fieldLabel' => 'Created at',
						'value' => (string) $dbinstance->InstanceCreateTime
					),
					array(
						'xtype' => 'displayfield',
						'fieldLabel' => 'Status',
						'value' => (string) $dbinstance->DBInstanceStatus
					)
				)
			),
			array(
				'xtype' => 'fieldset',
				'labelWidth' => 200,
				'items' => array(
					array(
						'xtype' => 'displayfield',
						'fieldLabel' => 'Availability Zone',
						'value' => (string) $dbinstance->AvailabilityZone
					),
					array(
						'xtype' => 'displayfield',
						'fieldLabel' => 'MultiAZ',
						'value' => $dbinstance->MultiAZ == 'true' ? 'Enabled' : 'Disabled'
					),
					array(
						'xtype' => 'displayfield',
						'fieldLabel' => 'Type',
						'value' => (string) $dbinstance->DBInstanceClass
					),
					array(
						'xtype' => 'displayfield',
						'fieldLabel' => 'Allocated storage',
						'value' => (string) $dbinstance->AllocatedStorage . ' GB'
					)
				)
			),
			array(
				'xtype' => 'fieldset',
				'labelWidth' => 200,
				'items' => array(
					'xtype' => 'displayfield',
					'fieldLabel' => 'Security groups',
					'value' => implode(', ', $sGroups)
				)
			),
			array(
				'xtype' => 'fieldset',
				'labelWidth' => 200,
				'items' => array(
					'xtype' => 'displayfield',
					'fieldLabel' => 'Parameter groups',
					'value' => implode(', ', $pGroups)
				)
			),
			array(
				'xtype' => 'fieldset',
				'labelWidth' => 200,
				'items' => array(
					array(
						'xtype' => 'displayfield',
						'fieldLabel' => 'Preferred maintenance window',
						'value' => (string) $dbinstance->PreferredMaintenanceWindow
					),
					array(
						'xtype' => 'displayfield',
						'fieldLabel' => 'Preferred backup window',
						'value' => (string) $dbinstance->PreferredBackupWindow
					),
					array(
						'xtype' => 'displayfield',
						'fieldLabel' => 'Backup retention period',
						'value' => (string) $dbinstance->BackupRetentionPeriod
					)
				)
			)
		);

		$dbinstance->PendingModifiedValues = (array) $dbinstance->PendingModifiedValues;
		if (! empty($dbinstance->PendingModifiedValues)) {
			if ($dbinstance->PendingModifiedValues->MultiAZ)
				$dbinstance->PendingModifiedValues->MultiAZ = $dbinstance->PendingModifiedValues->MultiAZ == 'true' ? 'Enable' : 'Disable';

			$f = array();
			foreach ($dbinstance->PendingModifiedValues as $key => $value)
				$f[] = array(
					'xtype' => 'displayfield',
					'fieldLabel' => $key,
					'value' => (string) $value
				);

			$form[] = array(
				'xtype' => 'fieldset',
				'labelWidth' => 200,
				'items' => $f
			);
		}

		$this->response->setJsonResponse(array(
			'success' => true,
			'module' => $this->response->template->fetchJs('tools/aws/rds/instances/details.js'),
			'moduleParams' => $form
		));
	}

	public function xRebootAction()
	{
		$amazonRDSClient = Scalr_Service_Cloud_Aws::newRds(
			$this->session->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Rds::ACCESS_KEY),
			$this->session->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Rds::SECRET_KEY),
			$this->getParam('cloudLocation')
		);

		$amazonRDSClient->RebootDBInstance($this->getParam('instanceId'));
		$this->response->setJsonResponse(array('success' => true));
	}

	public function xTerminateAction()
	{
		$amazonRDSClient = Scalr_Service_Cloud_Aws::newRds(
			$this->session->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Rds::ACCESS_KEY),
			$this->session->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Rds::SECRET_KEY),
			$this->getParam('cloudLocation')
		);

		$amazonRDSClient->DeleteDBInstance($this->getParam('instanceId'));
		$this->response->setJsonResponse(array('success' => true));
	}

	public function xGetParametersAction()
	{
		$amazonRDSClient = Scalr_Service_Cloud_Aws::newRds(
			$this->session->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Rds::ACCESS_KEY),
			$this->session->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Rds::SECRET_KEY),
			$this->getParam('cloudLocation')
		);

		$dbParameterGroups = $amazonRDSClient->DescribeDBParameterGroups();
		$groups = (array) $dbParameterGroups->DescribeDBParameterGroupsResult->DBParameterGroups;
		$groups = $groups['DBParameterGroup'];

		if ($groups) {
			if (!is_array($groups))
				$groups = array($groups);
		}

		$describeDBSecurityGroups = $amazonRDSClient->DescribeDBSecurityGroups();
		$sgroups = (array) $describeDBSecurityGroups->DescribeDBSecurityGroupsResult->DBSecurityGroups;
		$sgroups = $sgroups['DBSecurityGroup'];

		if ($sgroups) {
			if (!is_array($sgroups))
				$sgroups = array($sgroups);
		}

		$zones = array('' => 'Choose randomly');

		foreach ($amazonEC2Client->DescribeAvailabilityZones() as $zone) {
			if (stristr($zone->zoneState, 'available'))
				$zones[(string)$zone->zoneName] = (string)$zone->zoneName;
		}

		$this->response->setJsonResponse(array(
			'success' => true,
			'groups' => $groups,
			'sgroups' => $sgroups,
			'zones' => $zones
		));
	}

	public function xListInstancesAction()
	{
		$this->request->defineParams(array(
			'cloudLocation',
			'sort' => array('type' => 'string', 'default' => 'id'),
			'dir' => array('type' => 'string', 'default' => 'ASC')
		));

		$amazonRDSClient = Scalr_Service_Cloud_Aws::newRds(
			$this->session->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Rds::ACCESS_KEY),
			$this->session->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Rds::SECRET_KEY),
			$this->getParam('cloudLocation')
		);

		$awsResponse = $amazonRDSClient->DescribeDBInstances();
		$rows = $awsResponse->DescribeDBInstancesResult->DBInstances->DBInstance;
		$rowz = array();

		if ($rows instanceof stdClass)
			$rows = array($rows);

		foreach ($rows as $pv)
			$rowz[] = array(
				'engine'	=> (string)$pv->Engine,
				'status'	=> (string)$pv->DBInstanceStatus,
				'hostname'	=> (string)$pv->Endpoint->Address,
				'port'		=> (string)$pv->Endpoint->Port,
				'name'		=> (string)$pv->DBInstanceIdentifier,
				'username'	=> (string)$pv->MasterUsername,
				'type'		=> (string)$pv->DBInstanceClass,
				'storage'	=> (string)$pv->AllocatedStorage,
				'dtadded'	=> ($pv->InstanceCreateTime) ? date("M j, Y H:i:s", strtotime((string)$pv->InstanceCreateTime)) : "",
				'avail_zone'=> (string)$pv->AvailabilityZone
			);

		$response = $this->buildResponseFromData($rowz);
		$this->response->setJsonResponse($response);
	}
}
