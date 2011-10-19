<?php

class Scalr_UI_Controller_Tools_Aws_Vpc_Subnets extends Scalr_UI_Controller
{
	const CALL_PARAM_NAME = 'subnetId';
	
	public function defaultAction()
	{
		$this->viewAction();
	}

	public function viewAction()
	{
		$this->response->setJsonResponse(array(
			'success' => true,
			'moduleParams' => array(
				'locations'	=> Scalr_UI_Controller_Tools_Aws::getAwsLocations()
			),
			'module' => $this->response->template->fetchJs('tools/aws/vpc/subnets/view.js')
		));
	}
	
	public function xRemoveAction()
	{
		$this->request->defineParams(array(
			'subnets' => array('type' => 'json')
		));
		
		$amazonVPCClient = Scalr_Service_Cloud_Aws::newVpc(
			$this->getParam('cloudLocation'), 
			$this->session->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Ec2::PRIVATE_KEY),
			$this->session->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Ec2::CERTIFICATE)
		);
		
		foreach ($this->getParam('subnets') as $dd) {		
				$amazonVPCClient->DeleteSubnet($dd);			
		}
		
		$this->response->setJsonResponse(array('success' => true));
	}
	
	public function xSaveSubnetAction()
	{
		$amazonVPCClient = Scalr_Service_Cloud_Aws::newVpc(
			$this->getParam('cloudLocation'), 
			$this->session->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Ec2::PRIVATE_KEY),
			$this->session->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Ec2::CERTIFICATE)
		);
		
		$amazonVPCClient->CreateSubnet(new CreateSubnet(
			$this->getParam(Scalr_UI_Controller_Tools_Aws_Vpc::CALL_PARAM_NAME),
			$this->getParam('cidr'),
			$this->getParam('availabilityZone')
		));
		
		$this->response->setJsonResponse(array('success' => true));
	}
	
	public function createAction()
	{
		$this->response->setJsonResponse(array(
			'success' => true,
			'moduleParams' => array(
				'locations'	=> Scalr_UI_Controller_Tools_Aws::getAwsLocations()
			),
			'module' => $this->response->template->fetchJs('tools/aws/vpc/subnets/create.js')
		));
	}
	
	public function xListViewSubnetsAction()
	{
		$this->request->defineParams(array(
			'vpcId' => array('type' => 'int'),
			'cloudLocation',
			'sort' => array('type' => 'string', 'default' => 'id'),
			'dir' => array('type' => 'string', 'default' => 'ASC')
		));
		
		$amazonVPCClient = Scalr_Service_Cloud_Aws::newVpc(
			$this->getParam('cloudLocation'), 
			$this->session->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Ec2::PRIVATE_KEY),
			$this->session->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Ec2::CERTIFICATE)
		);
		
		// Rows	
		$aws_response = $amazonVPCClient->DescribeSubnets();
		$rows = (array)$aws_response->subnetSet;
		
		if ($rows["item"] instanceof stdClass)
			$rows["item"] = array($rows["item"]); // convert along  record to array		
		
		$rowz = array();
		foreach ($rows["item"] as $row)
			$rowz[]=(array)$row;
		 		
		
		$response = $this->buildResponseFromData($rowz);
		
		// Rows. Create final rows array for script
		foreach ($response['data'] as &$row) { 	
			$r = array(
				"id"				=> (string)$row['subnetId'], // have to call only like "id" for correct script work in template
				"vpcId"				=> (string)$row['vpcId'],
				"state"				=> (string)$row['state'],					
				"cidrBlock"			=> (string)$row['cidrBlock'],
				"availableIpAddressCount"	=> (string)$row['availableIpAddressCount'],
				"availabilityZone"	=>(string)$row['availabilityZone']
			);	

			$row = $r;
		}
	
		$this->response->setJsonResponse($response);
	}
}