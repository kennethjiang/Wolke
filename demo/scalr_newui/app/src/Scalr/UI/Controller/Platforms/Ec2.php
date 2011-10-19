<?php

class Scalr_UI_Controller_Platforms_Ec2 extends Scalr_UI_Controller
{
	public function xGetAvailZonesAction()
	{
		$AmazonEC2Client = Scalr_Service_Cloud_Aws::newEc2(
    		$this->getParam('cloudLocation'),
    		$this->session->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Ec2::PRIVATE_KEY),
			$this->session->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Ec2::CERTIFICATE)
    	);
    		
    	// Get Avail zones
		$avail_zones_resp = $AmazonEC2Client->DescribeAvailabilityZones();
		    
	    $data = array();
		foreach ($avail_zones_resp->availabilityZoneInfo->item as $zone) {
			if (stristr($zone->zoneState,'available')) {
				$data[] = array(
					'id' => (string)$zone->zoneName,
					'name' => (string)$zone->zoneName
				);
			}
		}
		
		$this->response->setJsonResponse(array(
			'success' => true,
			'data' => $data
		));
	}
}
