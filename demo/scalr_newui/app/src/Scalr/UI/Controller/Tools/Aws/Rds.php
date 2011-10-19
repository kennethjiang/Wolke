<?php

class Scalr_UI_Controller_Tools_Aws_Rds extends Scalr_UI_Controller
{
	public function hasAccess()
	{
		$enabledPlatforms = $this->session->getEnvironment()->getEnabledPlatforms();
		if (!in_array(SERVER_PLATFORMS::RDS, $enabledPlatforms))
			throw new Exception("You need to enable RDS platform for current environment");

		return true;
	}
}
