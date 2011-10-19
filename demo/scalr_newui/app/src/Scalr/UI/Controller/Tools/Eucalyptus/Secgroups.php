<?php

class Scalr_UI_Controller_Tools_Eucalyptus_Secgroups extends Scalr_UI_Controller
{
	const CALL_PARAM_NAME = 'groupName';

	private function getClient()
	{
		return Scalr_Service_Cloud_Eucalyptus::newCloud(
			$this->session->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Eucalyptus::SECRET_KEY, true, $this->getParam('cloudLocation')),
			$this->session->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Eucalyptus::ACCESS_KEY, true, $this->getParam('cloudLocation')),
			$this->session->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Eucalyptus::EC2_URL, true, $this->getParam('cloudLocation'))
		);
	}

	public function viewAction()
	{
		$this->response->setJsonResponse(array(
			'success' => true,
			'moduleName' => $this->getModuleName('ui/tools/secgroups.js'),
			'moduleParams' => array(
				'platform' => SERVER_PLATFORMS::EUCALYPTUS,
				'locations' => Scalr_UI_Controller_Platforms::getCloudLocations(SERVER_PLATFORMS::EUCALYPTUS, false),
				'title' => 'Tools &raquo; Eucalyptus &raquo; Security groups',
				'loadUrl' => '/tools/eucalyptus/secGroups/xListGroups',
				'editUrl' => '/tools/eucalyptus/secGroups',
				'removeUrl' => '/tools/eucalyptus/secGroups/xRemove'
			)
		));
	}

	public function xListGroupsAction()
	{
		$this->request->defineParams(array(
			'cloudLocation',
			'showAll' => array('type' => 'bool'),
			'sort' => array('type' => 'string', 'default' => 'id'),
			'dir' => array('type' => 'string', 'default' => 'ASC')
		));

		$platformClient = $this->getClient();

		$awsResponse = $platformClient->DescribeSecurityGroups();
		$rows = $awsResponse->securityGroupInfo->item;
		$rowz = array();

		if ($rows instanceof stdClass)
			$rows = array($rows);

		foreach ($rows as $row) {
			// Show only scalr security groups
			if (stristr($row->groupName, CONFIG::$SECGROUP_PREFIX) || stristr($row->groupName, "scalr-role.") || $this->getParam('showAll'))
				$rowz[] = array(
					"name" => $row->groupName,
					"description" => $row->groupDescription,
					"id" => $row->groupName
				);
		}

		$response = $this->buildResponseFromData($rowz, array('name'));
		$this->response->setJsonResponse($response);
	}

	public function editAction()
	{

	}

	public function xRemoveAction()
	{
		$this->request->defineParams(array(
			'cloudLocation',
			'groups' => array('type' => 'json')
		));

		$platformClient = $this->getClient();

		$i = 0;
		$errors = array();

		foreach ($this->getParam('groups') as $groupName) {
			try {
				//$platformClient->DeleteSecurityGroup($groupName);
				$i++;
			} catch (Exception $e) {
				$errors[] = sprintf(_("Cannot delete group %s: %s"), $groupName, $e->getMessage());
			}
		}

		$response = array();
		$response['success'] = true;
		$response['errorMessages'] = $errors;

		if ($i > 0)
			$response['successMessages'] = array(sprintf(_("%s security group(s) successfully removed"), $i));

		$this->response->setJsonResponse($response);
	}
}
