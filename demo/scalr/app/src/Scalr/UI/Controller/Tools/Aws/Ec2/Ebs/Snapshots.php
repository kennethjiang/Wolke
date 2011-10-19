<?php

class Scalr_UI_Controller_Tools_Aws_Ec2_Ebs_Snapshots extends Scalr_UI_Controller
{
	const CALL_PARAM_NAME = 'snapshotId';
	
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
			'moduleParams' => array(
				'locations'	=> Scalr_UI_Controller_Platforms::getCloudLocations(SERVER_PLATFORMS::EC2, false)
			),
			'module' => $this->response->template->fetchJs('tools/aws/ec2/ebs/snapshots/view.js')
		));
	}
	
	public function xCreateAction()
	{
		$this->request->defineParams(array(
			'volumeId',
			'cloudLocation'
		));
		
		$amazonEC2Client = Scalr_Service_Cloud_Aws::newEc2(
			$this->getParam('cloudLocation'), 
			$this->session->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Ec2::PRIVATE_KEY), 
			$this->session->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Ec2::CERTIFICATE)
		);
		
		$res =$amazonEC2Client->CreateSnapshot($this->getParam('volumeId'));
		
		if ($res->snapshotId)
		{
			$r = $amazonEC2Client->DescribeVolumes($res->volumeId);
			$info = $r->volumeSet->item;
			
			if ($info->attachmentSet->item->instanceId)
			{
				try {
					$dBServer = DBServer::LoadByPropertyValue(
						EC2_SERVER_PROPERTIES::INSTANCE_ID, 
						(string)$info->attachmentSet->item->instanceId
					);
					
					$dBFarm = $DBServer->GetFarmObject();
				}
				catch(Exception $e){}
				
				if ($dBServer && $dBFarm)
				{
					$comment = sprintf(_("Created on farm '%s', server '%s' (Instance ID: %s)"), 
						$dBFarm->Name, $dBServer->serverId, (string)$info->attachmentSet->item->instanceId
					);
				}
			}
			else
				$comment = "";
			
			$this->db->Execute("INSERT INTO ebs_snaps_info SET snapid=?, comment=?, dtcreated=NOW(), region=?",
				array($res->snapshotId, $comment, $this->getParam('cloudLocation'))
			);
			
			$this->response->setJsonResponse(array('success' => true, 'data' => array('snapshotId' => $res->snapshotId)));
		}
		else
			throw new Exception("Scalr unable to create snapshot. Please try again later.");
	}
	
	public function xDeleteAction()
	{
		$this->request->defineParams(array(
			'snapshotId' => array('type' => 'json'),
			'cloudLocation'
		));
		
		$amazonEC2Client = Scalr_Service_Cloud_Aws::newEc2(
			$this->getParam('cloudLocation'), 
			$this->session->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Ec2::PRIVATE_KEY), 
			$this->session->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Ec2::CERTIFICATE)
		);
		
		foreach ($this->getParam('snapshotId') as $snapshotId)
			$amazonEC2Client->DeleteSnapshot($snapshotId);
		
		$this->response->setJsonResponse(array('success' => true));
	}
	
	public function xListSnapshotsAction()
	{
		$this->request->defineParams(array(
			'sort' => array('type' => 'string', 'default' => 'snapshotId'),
			'dir' => array('type' => 'string', 'default' => 'ASC'),
			'showPublicSnapshots',
			'cloudLocation', 'volumeId', 'snapshotId'
		));
		
		$amazonEC2Client = Scalr_Service_Cloud_Aws::newEc2(
			$this->getParam('cloudLocation'), 
			$this->session->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Ec2::PRIVATE_KEY), 
			$this->session->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Ec2::CERTIFICATE)
		);
		
		// Rows
		$aws_response = $amazonEC2Client->DescribeSnapshots();
		$rowz = $aws_response->snapshotSet->item;
		if ($rowz instanceof stdClass) $rowz = array($rowz);

		$snaps = array();
		foreach ($rowz as $pk=>$pv)
		{
			if ($this->getParam('volumeId') && $pv->volumeId != $this->getParam('volumeId'))
				continue;
			
			if ($this->getParam('snapshotId') && $pv->snapshotId != $this->getParam('snapshotId'))
				continue;
				
			$pv->startTime = date("Y-m-d H:i:s", strtotime($pv->startTime));
			$item = (array)$pv;	
						
			if ($pv->ownerId != $this->session->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Ec2::ACCOUNT_ID))
			{
				$item['comment'] = $pv->description;
				$item['owner'] = $pv->ownerId;
				
				if (!$this->getParam('showPublicSnapshots'))
					continue;
			}
			else
			{
				$info = $this->db->GetRow("SELECT * FROM ebs_snaps_info WHERE snapid=?", array(
					$item->snapshotId
				));	
				
				$item['comment'] = $info['comment'] ? $info['comment'] : $pv->description;
				
				$item['owner'] = 'Me';
			}
			
			$item['progress'] = (int)preg_replace("/[^0-9]+/", "", $item['progress']);
			
			unset($item['description']);
			$snaps[] = $item;
		}
		
		$response = $this->buildResponseFromData($snaps, array('snapshotId', 'volumeId', 'comment', 'owner'));
		
		$this->response->setJsonResponse($response);
	}
}
