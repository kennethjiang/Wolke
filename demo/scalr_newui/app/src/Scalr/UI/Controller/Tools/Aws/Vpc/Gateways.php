<?php

class Scalr_UI_Controller_Tools_Aws_Vpc_Gateways extends Scalr_UI_Controller
{
	const CALL_PARAM_NAME = 'gatewayId';
	
	public function xSaveVpnConnectionAction()
	{
		$amazonVPCClient = Scalr_Service_Cloud_Aws::newVpc(
			$this->getParam('cloudLocation'), 
			$this->session->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Ec2::PRIVATE_KEY),
			$this->session->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Ec2::CERTIFICATE)
		);
		
		$amazonVPCClient->CreateVpnConnection(new CreateVpnConnection(
			$this->getParam('customerGatewayId'),
			$this->getParam('vpnGatewayId'),
			"ipsec.1"
		));
		
		$this->response->setJsonResponse(array('success' => true));
	}
	
	public function xSaveCustomAction()
	{
		$amazonVPCClient = Scalr_Service_Cloud_Aws::newVpc(
			$this->getParam('cloudLocation'), 
			$this->session->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Ec2::PRIVATE_KEY),
			$this->session->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Ec2::CERTIFICATE)
		);
		
		$amazonVPCClient->CreateCustomerGateway(new CreateCustomerGateway(
			$this->getParam('type'),
			$this->getParam('ip'),
			$this->getParam('bgp')
		));
		
		$this->response->setJsonResponse(array('success' => true));
	}
	
	public function xSaveVpnAction()
	{
		$amazonVPCClient = Scalr_Service_Cloud_Aws::newVpc(
			$this->getParam('cloudLocation'), 
			$this->session->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Ec2::PRIVATE_KEY),
			$this->session->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Ec2::CERTIFICATE)
		);
		
		$amazonVPCClient->CreateVpnGateway(new CreateCustomerGateway(
			$this->getParam('type'),
			$this->getParam('availZone')
		));
		
		$this->response->setJsonResponse(array('success' => true));
	}
	
	public function getVpnConnectionCreateDataAction()
	{
		$amazonVPCClient = Scalr_Service_Cloud_Aws::newVpc(
			$this->getParam('cloudLocation'), 
			$this->session->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Ec2::PRIVATE_KEY),
			$this->session->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Ec2::CERTIFICATE)
		);
		
		$vpnGatewayId = array();
		$customerGatewayId = array();
	
	
		// Get VPN gateways list
		$aws_response = $amazonVPCClient->DescribeVpnGateways();		
		$rows = (array)$aws_response->vpnGatewaySet;
		
		if ($rows["item"] instanceof stdClass)
			$rows["item"] = array($rows["item"]); // convert along  record to array		
						
		foreach ($rows['item'] as $row) {
    		if (stristr($row->state,'available'))    		
    			array_push($vpnGatewayId, array((string)$row->vpnGatewayId));
    	}
				
		// Get Customer gateways list
		$aws_response = $amazonVPCClient->DescribeCustomerGateways();		
		$rows = (array)$aws_response->customerGatewaySet;
		
		if ($rows["item"] instanceof stdClass)
			$rows["item"] = array($rows["item"]); 		
					
		foreach ($rows['item'] as $row) {
    		if (stristr($row->state,'available'))    		
    			array_push($customerGatewayId, array((string)$row->customerGatewayId));
    	}
    	
    	$this->response->setJsonResponse(array(
			'success' => true,
			'data' => array(
				'vpnGatewayIds' => $vpnGatewayId,
    			'customerGatewayIds' => $customerGatewayId
			)
		));
	}
	
	public function vpnConnectionCreateAction()
	{
		$this->response->setJsonResponse(array(
			'success' => true,
			'moduleParams' => array(
				'locations'			=> Scalr_UI_Controller_Tools_Aws::getAwsLocations()
			),
			'module' => $this->response->template->fetchJs('tools/aws/vpc/gateways/vpnConnectionCreate.js')
		));
	}
	
	public function vpnCreateAction()
	{
		$this->response->setJsonResponse(array(
			'success' => true,
			'moduleParams' => array(
				'locations'	=> Scalr_UI_Controller_Tools_Aws::getAwsLocations()
			),
			'module' => $this->response->template->fetchJs('tools/aws/vpc/gateways/vpnCreate.js')
		));
	}
	
	public function customCreateAction()
	{
		$this->response->setJsonResponse(array(
			'success' => true,
			'moduleParams' => array(
				'locations'	=> Scalr_UI_Controller_Tools_Aws::getAwsLocations()
			),
			'module' => $this->response->template->fetchJs('tools/aws/vpc/gateways/customCreate.js')
		));
	}
}