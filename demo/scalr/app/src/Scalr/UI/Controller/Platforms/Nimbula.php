<?php

class Scalr_UI_Controller_Platforms_Nimbula extends Scalr_UI_Controller
{
	public function xGetShapesAction()
	{
		$nimbula =  Scalr_Service_Cloud_Nimbula::newNimbula(
			Scalr_Session::getInstance()->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Nimbula::API_URL),
			Scalr_Session::getInstance()->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Nimbula::USERNAME),
			Scalr_Session::getInstance()->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Nimbula::PASSWORD)
		);
		
		$shapes = $nimbula->listShapes();
		$data = array();
		foreach ($shapes as $shape) {
			$data[] = array(
				$shape->name,
				"{$shape->name} (CPUs: {$shape->cpus} RAM: {$shape->ram})"
			);
		}
		
		$this->response->setJsonResponse(array(
			'success' => true,
			'data' => $data
		));
	}
}
