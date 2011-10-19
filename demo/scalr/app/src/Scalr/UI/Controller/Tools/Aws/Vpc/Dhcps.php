<?php

class Scalr_UI_Controller_Tools_Aws_Vpc_Dhcps extends Scalr_UI_Controller
{
	const CALL_PARAM_NAME = 'dhcpId';
	
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
			'module' => $this->response->template->fetchJs('tools/aws/vpc/dhcps/view.js')
		));
	}
	
	public function attachAction()
	{
		$amazonVPCClient = Scalr_Service_Cloud_Aws::newVpc(
			$this->getParam('cloudLocation'), 
			$this->session->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Ec2::PRIVATE_KEY),
			$this->session->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Ec2::CERTIFICATE)
		);
	
		$options = array('default');
		
		// Get VPN gateways list
		$aws_response = $amazonVPCClient->DescribeDhcpOptions();		
		$rows = (array)$aws_response->dhcpOptionsSet;
	
		if ($rows["item"] instanceof stdClass)
			$rows["item"] = array($rows["item"]); // convert along  record to array		
		
		foreach ($rows['item'] as $row)	
	    	array_push($options, (string)$row->dhcpOptionsId);
		
		
		$this->response->setJsonResponse(array(
			'success' => true,
			'moduleParams' => array(
				'options'	=> $options,
				'value'		=> 'default'
			),
			'module' => $this->response->template->fetchJs('tools/aws/vpc/dhcps/attach.js')
		));
	}
	
	public function xAttachAction()
	{
		$amazonVPCClient = Scalr_Service_Cloud_Aws::newVpc(
			$this->getParam('cloudLocation'), 
			$this->session->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Ec2::PRIVATE_KEY),
			$this->session->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Ec2::CERTIFICATE)
		);
		
		$amazonVPCClient->AssociateDhcpOptions(new AssociateDhcpOptions(
			$this->getParam('vpcId'), 
			$this->getParam(self::CALL_PARAM_NAME))
		);
		
		$this->response->setJsonResponse(array('success' => true));
	}
	
	public function xRemoveAction()
	{
		$this->request->defineParams(array(
			'dhcps' => array('type' => 'json')
		));
		
		$amazonVPCClient = Scalr_Service_Cloud_Aws::newVpc(
			$this->getParam('cloudLocation'), 
			$this->session->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Ec2::PRIVATE_KEY),
			$this->session->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Ec2::CERTIFICATE)
		);
		
		foreach ($this->getParam('dhcps') as $dd) {		
			$amazonVPCClient->DeleteDhcpOptions($dd);			
		}
		
		$this->response->setJsonResponse(array('success' => true));
	}
	
	public function xSaveDhcpAction()
	{
		$amazonVPCClient = Scalr_Service_Cloud_Aws::newVpc(
			$this->getParam('cloudLocation'), 
			$this->session->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Ec2::PRIVATE_KEY),
			$this->session->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Ec2::CERTIFICATE)
		);
		
		$options = array();
		if($this->getParam('domainName')) {
			$req_domainName= trim($this->getParam('domainName'));
			$dhcpConfigurationDomainName = new DhcpConfigurationItemType("domain-name",array($req_domainName));
			$options[] = $dhcpConfigurationDomainName;
		}
		if($this->getParam('nameServers')) {
			$valueSetDomainNameServers = $this->formValueSetFromString($this->getParam('nameServers'));
			$dhcpConfigurationDomainNameServers = new DhcpConfigurationItemType("domain-name-servers",$valueSetDomainNameServers);
			$options[] = $dhcpConfigurationDomainNameServers;
		}
		if($this->getParam('ntpServers')) {		
			$valueSetNtpServers = $this->formValueSetFromString($this->getParam('ntpServers'));
			$dhcpConfigurationNtpServers = new DhcpConfigurationItemType("ntp-servers",$valueSetNtpServers);
			$options[] = $dhcpConfigurationNtpServers;
		}
		if($this->getParam('netbiosServers')) {	
			$valueSetNetBiosNameServers = $this->formValueSetFromString($this->getParam('netbiosServers'));
			$dhcpConfigurationNetBiosNameServers = new DhcpConfigurationItemType("netbios-name-servers",$valueSetNetBiosNameServers);
			$options[] = $dhcpConfigurationNetBiosNameServers;
		}
		if($this->getParam('netbiosType')) {
			$valueSetNetBiosType = $this->formValueSetFromString($this->getParam('netbiosType'));
			$dhcpConfigurationNetBiosType = new DhcpConfigurationItemType("netbios-node-type",$valueSetNetBiosType);
			$options[] = $dhcpConfigurationNetBiosType;
		}
	
		$amazonVPCClient->CreateDhcpOptions(new CreateDhcpOptions($options));	
		
		$this->response->setJsonResponse(array('success' => true));
	}
	
	public function createAction()
	{
		$this->response->setJsonResponse(array(
			'success' => true,
			'moduleParams' => array(
				'locations'	=> Scalr_UI_Controller_Tools_Aws::getAwsLocations()
			),
			'module' => $this->response->template->fetchJs('tools/aws/vpc/dhcps/create.js')
		));
	}
	
	private function formValueSetFromString($string)
	{	
		$valueSet = array();
		$valueSet = explode(',', $string);
		for($i = 0; $i<count($valueSet); $i++) {									
			$valueSet[$i] = trim($valueSet[$i]);					
		}
		return $valueSet;
	}
	
	public function xListViewDhcpsAction()
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
		
		
		$aws_response = $amazonVPCClient->DescribeDhcpOptions();	
		$rows = (array)$aws_response->dhcpOptionsSet;
		
		if ($rows["item"] instanceof stdClass)
			$rows["item"] = array($rows["item"]); // convert along subnet record to array
		
		$rowz = array();				
		foreach ($rows['item'] as $row)						
			$rowz[]=(array)$row;
		
		// conver soap response to structured data array (stdClass is preseneted)
		for($i = 0; $i<count($rowz); $i++) {										
			if ($rowz[$i]["dhcpConfigurationSet"]->item instanceof stdClass) {			
				$rowz[$i]["dhcpConfigurationSet"]->item = array($rowz[$i]["dhcpConfigurationSet"]->item); // convert along  record to array
			}	
			$rowz[$i]["dhcpConfigurationSet"] = $rowz[$i]["dhcpConfigurationSet"]->item;  // item object to array
		
			foreach($rowz[$i]["dhcpConfigurationSet"] as $j => $item) {
				if ($item->valueSet->item instanceof stdClass)					
					$item->valueSet->item = array($item->valueSet->item);  // along element to array
									
				$rowz[$i]["dhcpConfigurationSet"][$j] = $item;					
	    	}	
		}		
		
		$response = $this->buildResponseFromData($rowz);
		
		// Rows. Create final rows array for script
		foreach ($response['data'] as &$row) { 	
			$options = "";
			foreach($row['dhcpConfigurationSet'] as $set)
			{
				$options = $options."{$set->key} = ";				
			
				for($i = 0; $i<count($set->valueSet->item); $i++)
				{					
					$options = $options.$set->valueSet->item[$i]->value;					
					
					if(count($set->valueSet->item) == 1)
						continue;					
					elseif($i<count($set->valueSet->item)-1)									
						$options = $options.",";						
					
				}				
				$options = $options."; "; 				
			}
			$r = array(
				"id"		=> (string)$row['dhcpOptionsId'],
				"options"	=> $options				
			);

			$row = $r;
		}
	
		$this->response->setJsonResponse($response);
	}
}