<?php

class Scalr_UI_Controller_Tools_Aws_Rds_Snapshots extends Scalr_UI_Controller
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
			'module' => $this->response->template->fetchJs('tools/aws/rds/snapshots.js'),
			'moduleParams' => array(
				'locations' => Scalr_UI_Controller_Platforms::getCloudLocations(SERVER_PLATFORMS::RDS, false)
			)
		));
	}

	public function xListSnapshotsAction()
	{
		$this->request->defineParams(array(
			'cloudLocation', 'dbinstance',
			'sort' => array('type' => 'string', 'default' => 'id'),
			'dir' => array('type' => 'string', 'default' => 'ASC')
		));

		$amazonRDSClient = Scalr_Service_Cloud_Aws::newRds(
			$this->session->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Rds::ACCESS_KEY),
			$this->session->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Rds::SECRET_KEY),
			$this->getParam('cloudLocation')
		);

		$awsResponse = $amazonRDSClient->DescribeDBSnapshots($this->getParam('dbinstance'));
		$rows = $awsResponse->DescribeDBSnapshotsResult->DBSnapshots->DBSnapshot;
		$rowz = array();

		if ($rows instanceof stdClass)
			$rows = array($rows);

		foreach ($rows as $pv)
			$rowz[] = array(
				"dtcreated"		=> date("M j, Y H:i:s", strtotime((string)$pv->SnapshotCreateTime)),
				"port"			=> (string)$pv->Port,
				"status"		=> (string)$pv->Status,
				"engine"		=> (string)$pv->Engine,
				"avail_zone"	=> (string)$pv->AvailabilityZone,
				"idtcreated"	=> date("M j, Y H:i:s", strtotime((string)$pv->InstanceCreateTime)),
				"storage"		=> (string)$pv->AllocatedStorage,
				"name"			=> (string)$pv->DBSnapshotIdentifier,
				"id"			=> (string)$pv->DBSnapshotIdentifier,
			);

		$response = $this->buildResponseFromData($rowz);
		$this->response->setJsonResponse($response);
	}

	public function xCreateSnapshotAction()
	{
		$amazonRDSClient = Scalr_Service_Cloud_Aws::newRds(
			$this->session->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Rds::ACCESS_KEY),
			$this->session->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Rds::SECRET_KEY),
			$this->getParam('cloudLocation')
		);

		$snapId = "scalr-manual-" . dechex(microtime(true)*10000) . rand(0,9);

		try {
			$amazonRDSClient->CreateDBSnapshot($snapId, $this->getParam('dbinstance'));
			$this->db->Execute("INSERT INTO rds_snaps_info SET snapid=?, comment=?, dtcreated=NOW(), region=?",
				array($snapId, "manual RDS instance snapshot", $this->getParam('cloudLocation')));
		} catch (Exception $e) {
			throw new Exception (sprintf(_("Can't create db snapshot: %s"), $e->getMessage()));
		}

		$this->response->setJsonResponse(array(
			'success' => true,
			'message' => sprintf(_("DB snapshot '%s' successfully create"), $snapId)
		));
	}

	public function xDeleteSnapshotsAction()
	{
		$this->request->defineParams(array(
			'snapshots' => array('type' => 'json')
		));

		$amazonRDSClient = Scalr_Service_Cloud_Aws::newRds(
			$this->session->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Rds::ACCESS_KEY),
			$this->session->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Rds::SECRET_KEY),
			$this->getParam('cloudLocation')
		);

		$i = 0;
		$errors = array();
		foreach ($this->getParam('snapshots') as $snapName) {
			try {
				$amazonRDSClient->DeleteDBSnapshot($snapName);
				$this->db->Execute("DELETE FROM rds_snaps_info WHERE snapid=? ", array($snapName));
				$i++;
			} catch(Exception $e) {
				$errors[] = sprintf(_("Can't delete db snapshot %s: %s"), $snapName, $e->getMessage());
			}
		}

		$response = array();
		$response['success'] = true;
		$response['errorMessages'] = $errors;

		if ($i > 0)
			$response['successMessages'] = array(sprintf(_("%s db snapshot(s) successfully removed"), $i));

		$this->response->setJsonResponse($response);
	}
}
