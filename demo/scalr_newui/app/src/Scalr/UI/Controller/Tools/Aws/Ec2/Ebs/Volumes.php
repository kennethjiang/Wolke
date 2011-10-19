<?php

class Scalr_UI_Controller_Tools_Aws_Ec2_Ebs_Volumes extends Scalr_UI_Controller
{
	const CALL_PARAM_NAME = 'volumeId';
	
	public function hasAccess()
	{
		return $this->session->getAuthToken()->hasAccess(Scalr_AuthToken::ACCOUNT_USER);
	}
	
	public function defaultAction()
	{
		$this->viewAction();
	}
	
	public function attachAction()
	{
		$dbServers = $this->db->GetAll("SELECT server_id FROM servers WHERE platform=? AND status=? AND env_id=?", array(
			SERVER_PLATFORMS::EC2,
			SERVER_STATUS::RUNNING,
			$this->session->getEnvironmentId()
		));
		
		if (count($dbServers) == 0)
			throw new Exception("You have no running servers on EC2 platform");
			
		$amazonEC2Client = Scalr_Service_Cloud_Aws::newEc2(
			$this->getParam('cloudLocation'), 
			$this->session->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Ec2::PRIVATE_KEY), 
			$this->session->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Ec2::CERTIFICATE)
		);
		
		$vol = $amazonEC2Client->DescribeVolumes($this->getParam('volumeId'));
		$vol = $vol->volumeSet->item;
			
		$servers = array();
		foreach ($dbServers as $dbServer) {
			$dbServer = DBServer::LoadByID($dbServer['server_id']);

			if ($dbServer->GetProperty(EC2_SERVER_PROPERTIES::AVAIL_ZONE) == $vol->availabilityZone) {
				$servers[$dbServer->serverId] = "{$dbServer->remoteIp} ({$dbServer->serverId})";
			}
		}
		
		if (count($servers) == 0)
			throw new Exception("You have no running servers on EC2 platform");
		
		$this->response->setJsonResponse(array(
			'success' => true,
			'moduleParams' => array(
				'servers' => $servers
			),
			'module' => $this->response->template->fetchJs('tools/aws/ec2/ebs/volumes/attach.js')
		));
	}
	
	public function createAction()
	{
		$this->response->setJsonResponse(array(
			'success' => true,
			'moduleParams' => array(
				'locations'	=> Scalr_UI_Controller_Platforms::getCloudLocations(SERVER_PLATFORMS::EC2, false)
			),
			'module' => $this->response->template->fetchJs('tools/aws/ec2/ebs/volumes/create.js')
		));
	}
	
	public function viewAction()
	{
		$this->response->setJsonResponse(array(
			'success' => true,
			'moduleParams' => array(
				'locations'	=> Scalr_UI_Controller_Platforms::getCloudLocations(SERVER_PLATFORMS::EC2, false)
			),
			'module' => $this->response->template->fetchJs('tools/aws/ec2/ebs/volumes/view.js')
		));
	}
	
	public function xAttachAction()
	{
		$this->request->defineParams(array(
			'cloudLocation', 'serverId', 'volumeId', 'mount', 'mountPoint'
		));
		
		try {
			$dBEbsVolume = DBEBSVolume::loadByVolumeId($this->getParam('volumeId'));
			if ($DBEBSVolume->isManual == 0) {
				$errmsg = sprintf(_("This volume was automatically created for role '%s' on farm '%s' and cannot be re-attahced manually."),
					$this->db->GetOne("SELECT name FROM roles INNER JOIN farm_roles ON farm_roles.role_id = roles.id WHERE farm_roles.id=?", array($dBEbsVolume->farmRoleId)),
					$this->db->GetOne("SELECT name FROM farms WHERE id=?", array($dBEbsVolume->farmId))
				);
			}
		}
		catch(Exception $e) { }
		
		if ($errmsg)
			throw new Exception($errmsg);
		
		$amazonEC2Client = Scalr_Service_Cloud_Aws::newEc2(
			$this->getParam('cloudLocation'), 
			$this->session->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Ec2::PRIVATE_KEY), 
			$this->session->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Ec2::CERTIFICATE)
		);
		
		$r = $amazonEC2Client->DescribeVolumes($this->getParam('volumeId'));
		$info = $r->volumeSet->item;
		
		$dBServer = DBServer::LoadByID($this->getParam('serverId'));

		$attachVolumeType = new AttachVolumeType(
			$this->getParam('volumeId'), 
			$dBServer->GetProperty(EC2_SERVER_PROPERTIES::INSTANCE_ID), 
			$dBServer->GetFreeDeviceName()
		);
				
		$res = $amazonEC2Client->AttachVolume($attachVolumeType);
		
		if ($this->getParam('attachOnBoot') == 'on')
		{
			$dBEbsVolume = new DBEBSVolume();
			$dBEbsVolume->attachmentStatus = EC2_EBS_ATTACH_STATUS::ATTACHING;
			$dBEbsVolume->isManual = true;
			$dBEbsVolume->volumeId = $this->getParam('volumeId');
			$dBEbsVolume->ec2AvailZone = $info->availabilityZone;
			$dBEbsVolume->ec2Region = $this->getParam('cloudLocation');
			$dBEbsVolume->farmId = $dBServer->farmId;
			$dBEbsVolume->farmRoleId = $dBServer->farmRoleId;
			$dBEbsVolume->serverId = $dBServer->serverId;
			$dBEbsVolume->serverIndex = $dBServer->index;
			$dBEbsVolume->size = $info->size;
			$dBEbsVolume->snapId = $info->snapshotId;
			$dBEbsVolume->mount = ($this->getParam('mount') == 1) ? true: false;
			$dBEbsVolume->mountPoint = $this->getParam('mountPoint');
			$dBEbsVolume->mountStatus = ($this->getParam('mount') == 1) ? EC2_EBS_MOUNT_STATUS::AWAITING_ATTACHMENT : EC2_EBS_MOUNT_STATUS::NOT_MOUNTED;
			$dBEbsVolume->clientId = Scalr_Session::getInstance()->getClientId();
			$dBEbsVolume->envId = Scalr_Session::getInstance()->getEnvironmentId();
			
			$dBEbsVolume->Save();
		}
		
		$this->response->setJsonResponse(array('success' => true));
	}
	
	public function xDetachAction()
	{
		$this->request->defineParams(array(
			'cloudLocation', 'volumeId', 'forceDetach'
		));
		
		try {
			$dBEbsVolume = DBEBSVolume::loadByVolumeId($this->getParam('volumeId'));
		}
		catch(Exception $e){ }
		
		$amazonEC2Client = Scalr_Service_Cloud_Aws::newEc2(
			$this->getParam('cloudLocation'), 
			$this->session->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Ec2::PRIVATE_KEY), 
			$this->session->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Ec2::CERTIFICATE)
		);
		
		$isForce = ($this->getParam('forceDetach') == 1) ? true : false;
									
		$res = $amazonEC2Client->DetachVolume(new DetachVolumeType($this->getParam('volumeId'), null, null, $isforce));

		if ($res->volumeId && $res->status == AMAZON_EBS_STATE::DETACHING) {
			if ($dBEbsVolume) {
				if ($dBEbsVolume->isManual) {
					$dBEbsVolume->delete();
				}
				elseif (!$dBEbsVolume->isManual) {
					$dBEbsVolume->attachmentStatus = EC2_EBS_ATTACH_STATUS::AVAILABLE;
					$dBEbsVolume->mountStatus = EC2_EBS_MOUNT_STATUS::NOT_MOUNTED;
					$dBEbsVolume->serverId = '';
					$dBEbsVolume->deviceName = '';
					$dBEbsVolume->save();
				}
			}
		}
		
		$this->response->setJsonResponse(array('success' => true));
	}
	
	public function xCreateAction()
	{
		$this->request->defineParams(array(
			'cloudLocation', 'availabilityZone', 'size', 'snapshotId'
		));
		
		$amazonEC2Client = Scalr_Service_Cloud_Aws::newEc2(
			$this->getParam('cloudLocation'), 
			$this->session->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Ec2::PRIVATE_KEY), 
			$this->session->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Ec2::CERTIFICATE)
		);
		
		$CreateVolumeType = new CreateVolumeType();
    	if ($this->getParam('snapshotId'))
    		$CreateVolumeType->snapshotId = $this->getParam('snapshotId');
    	
    	$CreateVolumeType->size = $this->getParam('size');
    		 
    	$CreateVolumeType->availabilityZone = $this->getParam('availabilityZone');
    	
    	$res = $amazonEC2Client->CreateVolume($CreateVolumeType);

    	$this->response->setJsonResponse(array('success' => true, 'data' => array('volumeId' => $res->volumeId)));
	}
	
	public function xDeleteAction()
	{
		$this->request->defineParams(array(
			'volumeId' => array('type' => 'json'),
			'cloudLocation'
		));
		
		$amazonEC2Client = Scalr_Service_Cloud_Aws::newEc2(
			$this->getParam('cloudLocation'), 
			$this->session->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Ec2::PRIVATE_KEY), 
			$this->session->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Ec2::CERTIFICATE)
		);
		
		foreach ($this->getParam('volumeId') as $volumeId)
			$amazonEC2Client->DeleteVolume($volumeId);
		
		$this->response->setJsonResponse(array('success' => true));
	}
	
	public function xListVolumesAction()
	{
		$this->request->defineParams(array(
			'sort' => array('type' => 'string', 'default' => 'volumeId'),
			'dir' => array('type' => 'string', 'default' => 'ASC'),
			'volumeId'
		));
		
		$amazonEC2Client = Scalr_Service_Cloud_Aws::newEc2(
			$this->getParam('cloudLocation'), 
			$this->session->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Ec2::PRIVATE_KEY), 
			$this->session->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Ec2::CERTIFICATE)
		);
		
		// Rows
		$aws_response = $amazonEC2Client->DescribeVolumes();
		$rowz = $aws_response->volumeSet->item;
		if ($rowz instanceof stdClass) $rowz = array($rowz);

		$vols = array();
		foreach ($rowz as $pk=>$pv)
		{
			if ($pv->attachmentSet && $pv->attachmentSet->item)
				$pv->attachmentSet = $pv->attachmentSet->item;
			
			if ($this->getParam('volumeId') && $this->getParam('volumeId') != $pv->volumeId)
				continue;
				
			$item = array(
				'volumeId'	=> $pv->volumeId, 
				'size'	=> $pv->size, 
				'snapshotId' => $pv->snapshotId, 
				'availZone' => $pv->availabilityZone, 
				'status' => $pv->status, 
				'attachmentStatus' => $pv->attachmentSet->status,
				'device'	=> $pv->attachmentSet->device,
				'instanceId' => $pv->attachmentSet->instanceId,
			);
			
			$item['autoSnaps'] = ($this->db->GetOne("SELECT id FROM autosnap_settings WHERE objectid=? AND object_type=?",
					 array($pv->volumeId, AUTOSNAPSHOT_TYPE::EBSSnap))) ? true : false;
			
			try
			{
				$DBEBSVolume = DBEBSVolume::loadByVolumeId($pv->volumeId);
					
				$sort_key = "{$DBEBSVolume->farmId}_{$DBEBSVolume->farmRoleId}_{$pv->volumeId}";
				
				$item['farmId'] = $DBEBSVolume->farmId;
				$item['farmRoleId'] = $DBEBSVolume->farmRoleId;
				$item['serverIndex'] = $DBEBSVolume->serverIndex;
				$item['serverId'] = $DBEBSVolume->serverId;
				$item['mountStatus'] = $DBEBSVolume->mountStatus;
				$item['farmName'] = DBFarm::LoadByID($DBEBSVolume->farmId)->Name;
				$item['roleName'] = DBFarmRole::LoadByID($DBEBSVolume->farmRoleId)->GetRoleObject()->name;
			}
			catch(Exception $e) {}
			
			$vols[] = $item;
		}
		
		$response = $this->buildResponseFromData($vols, array('volumeId', 'snapshotId', 'farmId', 'farmRoleId', 'availZone'));
		
		$this->response->setJsonResponse($response);
	}
}
