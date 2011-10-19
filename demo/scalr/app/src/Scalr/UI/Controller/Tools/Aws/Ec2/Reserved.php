<?php

class Scalr_UI_Controller_Tools_Aws_Ec2_Reserved extends Scalr_UI_Controller
{
	public function hasAccess()
	{
		return $this->session->getAuthToken()->hasAccess(Scalr_AuthToken::ACCOUNT_USER);
	}

	public function instancesAction()
	{
		$this->response->setJsonResponse(array(
			'success' => true,
			'module' => $this->response->template->fetchJs('tools/aws/ec2/reserved/instances.js'),
			'moduleParams' => array(
				'locations' => Scalr_UI_Controller_Platforms::getCloudLocations(SERVER_PLATFORMS::EC2, false)
			)
		));
	}

	public function offeringsAction()
	{
		$this->response->setJsonResponse(array(
			'success' => true,
			'module' => $this->response->template->fetchJs('tools/aws/ec2/reserved/offerings.js'),
			'moduleParams' => array(
				'locations' => Scalr_UI_Controller_Platforms::getCloudLocations(SERVER_PLATFORMS::EC2, false)
			)
		));
	}

	public function xListInstancesAction()
	{
		$this->request->defineParams(array(
			'cloudLocation',
			'sort' => array('type' => 'string', 'default' => 'id'),
			'dir' => array('type' => 'string', 'default' => 'ASC')
		));

		$AmazonEC2Client = Scalr_Service_Cloud_Aws::newEc2(
			$this->getParam('cloudLocation'),
			$this->session->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Ec2::PRIVATE_KEY),
			$this->session->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Ec2::CERTIFICATE)
		);

		$awsResponse = $AmazonEC2Client->DescribeReservedInstances();
		$rows = $awsResponse->reservedInstancesSet->item;
		$rowz = array();

		if ($rows instanceof stdClass)
			$rows = array($rows);

		foreach ($rows as $pv)
			$rowz[] = (array)$pv;

		$response = $this->buildResponseFromData($rowz);

		foreach ($response['data'] as &$row) {
			$row['duration'] = $row['duration']/86400/365;
		}

		$this->response->setJsonResponse($response);
	}

	public function xListOfferingsAction()
	{
		$this->request->defineParams(array(
			'cloudLocation',
			'sort' => array('type' => 'string', 'default' => 'id'),
			'dir' => array('type' => 'string', 'default' => 'ASC')
		));

		$AmazonEC2Client = Scalr_Service_Cloud_Aws::newEc2(
			$this->getParam('cloudLocation'),
			$this->session->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Ec2::PRIVATE_KEY),
			$this->session->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Ec2::CERTIFICATE)
		);

		$awsResponse = $AmazonEC2Client->DescribeReservedInstancesOfferings();
		$rows = $awsResponse->reservedInstancesOfferingsSet->item;
		$rowz = array();

		if ($rows instanceof stdClass)
			$rows = array($rows);

		foreach ($rows as $pv)
			$rowz[] = (array)$pv;

		$response = $this->buildResponseFromData($rowz);

		foreach ($response['data'] as &$row) {
			$row['duration'] = $row['duration']/86400/365;
		}

		$this->response->setJsonResponse($response);
	}

	public function xPurchaseReservedOfferingAction()
	{
		$this->request->defineParams(array(
			'cloudLocation', 'offeringId'
		));

		try {
			$amazonEC2Client = Scalr_Service_Cloud_Aws::newEc2(
				$this->getParam('cloudLocation'),
				$this->session->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Ec2::PRIVATE_KEY),
				$this->session->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Ec2::CERTIFICATE)
			);

			$amazonEC2Client->PurchaseReservedInstancesOffering($this->getParam('offeringId'));
			$this->response->setJsonResponse(array('success' => true));
		}
		catch(Exception $e) {
			throw new Exception(sprintf(_("Cannot purchase reserved instances offering: %s"), $e->getMessage()));
		}
	}
}
