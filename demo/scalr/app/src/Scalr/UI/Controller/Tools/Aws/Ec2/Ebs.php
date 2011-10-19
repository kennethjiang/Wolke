<?php

class Scalr_UI_Controller_Tools_Aws_Ec2_Ebs extends Scalr_UI_Controller
{
	public function hasAccess()
	{
		$enabledPlatforms = $this->session->getEnvironment()->getEnabledPlatforms();
		if (!in_array(SERVER_PLATFORMS::EC2, $enabledPlatforms))
			throw new Exception("You need to enable EC2 platform for current environment");

		return true;
	}
}
