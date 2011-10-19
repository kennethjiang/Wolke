<?php

class Scalr_UI_Controller_Tools_Aws extends Scalr_UI_Controller
{
	static public function getAwsLocations()
	{
		$locations = array();

		foreach (PlatformFactory::NewPlatform(SERVER_PLATFORMS::EC2)->getLocations() as $key => $loc)
			$locations[] = array($key, $loc);

		return $locations;
	}
}
