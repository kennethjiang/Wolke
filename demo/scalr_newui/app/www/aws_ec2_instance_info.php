<?php
	require_once('src/prepend.inc.php');

	if (!Scalr_Session::getInstance()->getAuthToken()->hasAccess(Scalr_AuthToken::ACCOUNT_USER))
	{
		$errmsg = _("You have no permissions for viewing requested page");
		UI::Redirect("/#/dashboard");
	}
	
	if (!$req_iid)
		UI::Redirect("aws_ec2_instances_view.php");
		
	$display['title'] = 'Spot instance details';
	
	$AmazonEC2Client = Scalr_Service_Cloud_Aws::newEc2(
		$_SESSION['aws_region'],
		Scalr_Session::getInstance()->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Ec2::PRIVATE_KEY),
		Scalr_Session::getInstance()->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Ec2::CERTIFICATE)
	);

	$instainceInfo = $AmazonEC2Client->DescribeInstances($req_iid);

	if ($instainceInfo->reservationSet->item->instancesSet->item === null)
	{
		$err[] = ("The requested instance not found");
		UI::Redirect("aws_ec2_instances_view.php");
	}

	$display['instance'] = $instainceInfo->reservationSet->item->instancesSet->item;

	require_once ("src/append.inc.php");
?>