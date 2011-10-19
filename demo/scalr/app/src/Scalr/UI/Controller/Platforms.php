<?php

class Scalr_UI_Controller_Platforms extends Scalr_UI_Controller
{
	static public function getCloudLocations($platforms, $allowAll = true)
	{
		$ePlatforms = array();
		$locations = array();

		if (is_string($platforms))
			$platforms = explode(',', $platforms);

		if ($allowAll)
			$locations[''] = 'All';

		if (Scalr_Session::getInstance()->getEnvironment())
			$ePlatforms = Scalr_Session::getInstance()->getEnvironment()->getEnabledPlatforms();
		else
			$ePlatforms = array_keys(SERVER_PLATFORMS::GetList());

		if (implode('', $platforms) != 'all')
			$ePlatforms = array_intersect($ePlatforms, $platforms);

		foreach ($ePlatforms as $platform) {
			foreach (PlatformFactory::NewPlatform($platform)->getLocations() as $key => $loc)
				$locations[$key] = $loc;
		}
		
		return $locations;
	}

	static public function getEnabledPlatforms()
	{
		$ePlatforms = Scalr_Session::getInstance()->getEnvironment()->getEnabledPlatforms();
		$lPlatforms = SERVER_PLATFORMS::GetList();
		$platforms = array();

		foreach ($ePlatforms as $platform)
			$platforms[$platform] = $lPlatforms[$platform];

		return $platforms;
	}
}
