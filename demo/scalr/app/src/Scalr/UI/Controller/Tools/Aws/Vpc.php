<?php

class Scalr_UI_Controller_Tools_Aws_Vpc extends Scalr_UI_Controller
{
	const CALL_PARAM_NAME = 'vpcId';

	public function hasAccess()
	{
		$enabledPlatforms = $this->session->getEnvironment()->getEnabledPlatforms();
		if (!in_array(SERVER_PLATFORMS::EC2, $enabledPlatforms))
			throw new Exception("You need to enable EC2 platform for current environment");

		return true;
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
				'locations'	=> Scalr_UI_Controller_Tools_Aws::getAwsLocations()
			),
			'module' => $this->response->template->fetchJs('tools/aws/vpc/view.js')
		));
	}
	
	public function xRemoveAction()
	{
		$this->request->defineParams(array(
			'vpcs' => array('type' => 'json')
		));
		
		$amazonVPCClient = Scalr_Service_Cloud_Aws::newVpc(
			$this->getParam('cloudLocation'), 
			$this->session->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Ec2::PRIVATE_KEY),
			$this->session->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Ec2::CERTIFICATE)
		);
		
		foreach ($this->getParam('vpcs') as $dd) {		
				$amazonVPCClient->DeleteVpc($dd);			
		}
		
		$this->response->setJsonResponse(array('success' => true));
	}
	
	public function xSaveVpcAction()
	{
		$amazonVPCClient = Scalr_Service_Cloud_Aws::newVpc(
			$this->getParam('cloudLocation'), 
			$this->session->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Ec2::PRIVATE_KEY),
			$this->session->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Ec2::CERTIFICATE)
		);
		
		$amazonVPCClient->CreateVpc($this->getParam('cidr'));
		
		$this->response->setJsonResponse(array('success' => true));
	}
	
	public function createAction()
	{
		$this->response->setJsonResponse(array(
			'success' => true,
			'moduleParams' => array(
				'locations'	=> Scalr_UI_Controller_Tools_Aws::getAwsLocations()
			),
			'module' => $this->response->template->fetchJs('tools/aws/vpc/create.js')
		));
	}
	
	public function xListViewVpcAction()
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
		$aws_response = $amazonVPCClient->DescribeVpcs();		
		$rows = (array)$aws_response->vpcSet;	
		
		$rowz = array();
		foreach ($rows as $row)
			$rowz[]=(array)$row;
		 		
		
		$response = $this->buildResponseFromData($rowz);
		
		// Rows. Create final rows array for script
		foreach ($response['data'] as &$row) { 	
			$r = array(
				"id"			=> (string)$row['vpcId'],
				"state"			=> (string)$row['state'],
				"cidrBlock"		=> (string)$row['cidrBlock'],
				"dhcpOptionsId"	=> (string)$row['dhcpOptionsId']
			);	

			$row = $r;
		}
	
		$this->response->setJsonResponse($response);
	}
}