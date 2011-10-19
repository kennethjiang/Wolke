<?php
	class Modules_Platforms_Ec2 extends Modules_Platforms_Aws implements IPlatformModule
	{
		private $db;
		
		/** Properties **/
		const ACCOUNT_ID 	= 'ec2.account_id';
		const ACCESS_KEY	= 'ec2.access_key';
		const SECRET_KEY	= 'ec2.secret_key';
		const PRIVATE_KEY	= 'ec2.private_key';
		const CERTIFICATE	= 'ec2.certificate';
		
		/**
		 * 
		 * @var AmazonEC2
		 */
		private $instancesListCache = array();
		
		public function __construct()
		{
			$this->db = Core::GetDBInstance();
		}
		
		public function getRoleBuilderBaseImages()
		{
			return array(
				// Ubuntu 10.04 LTS
				'ami-22423c70'	=> array('name' => 'Ubuntu 10.04', 'os_dist' => 'ubuntu', 'location' => 'ap-southeast-1', 'architecture' => 'x86_64'),
				'ami-3c423c6e'	=> array('name' => 'Ubuntu 10.04', 'os_dist' => 'ubuntu', 'location' => 'ap-southeast-1', 'architecture' => 'i386'),
				
				'ami-5e0fa45f'	=> array('name' => 'Ubuntu 10.04', 'os_dist' => 'ubuntu', 'location' => 'ap-northeast-1', 'architecture' => 'x86_64'),
				'ami-5c0fa45d'	=> array('name' => 'Ubuntu 10.04', 'os_dist' => 'ubuntu', 'location' => 'ap-northeast-1', 'architecture' => 'i386'),
			
				'ami-d19ca9a5'	=> array('name' => 'Ubuntu 10.04', 'os_dist' => 'ubuntu', 'location' => 'eu-west-1', 'architecture' => 'x86_64'),
				'ami-d79ca9a3'	=> array('name' => 'Ubuntu 10.04', 'os_dist' => 'ubuntu', 'location' => 'eu-west-1', 'architecture' => 'i386'),
				
				'ami-b8f405d1'	=> array('name' => 'Ubuntu 10.04', 'os_dist' => 'ubuntu', 'location' => 'us-east-1', 'architecture' => 'x86_64'),
				'ami-a2f405cb'	=> array('name' => 'Ubuntu 10.04', 'os_dist' => 'ubuntu', 'location' => 'us-east-1', 'architecture' => 'i386'),
				
				'ami-b37e2ef6'	=> array('name' => 'Ubuntu 10.04', 'os_dist' => 'ubuntu', 'location' => 'us-west-1', 'architecture' => 'x86_64'),
				'ami-b17e2ef4'	=> array('name' => 'Ubuntu 10.04', 'os_dist' => 'ubuntu', 'location' => 'us-west-1', 'architecture' => 'i386'),
				
				// Ubuntu 10.10
				'ami-32423c60'	=> array('name' => 'Ubuntu 10.10', 'os_dist' => 'ubuntu', 'location' => 'ap-southeast-1', 'architecture' => 'x86_64'),
				'ami-0c423c5e'	=> array('name' => 'Ubuntu 10.10', 'os_dist' => 'ubuntu', 'location' => 'ap-southeast-1', 'architecture' => 'i386'),
				
				'ami-460fa447'	=> array('name' => 'Ubuntu 10.10', 'os_dist' => 'ubuntu', 'location' => 'ap-northeast-1', 'architecture' => 'x86_64'),
				'ami-440fa445'	=> array('name' => 'Ubuntu 10.10', 'os_dist' => 'ubuntu', 'location' => 'ap-northeast-1', 'architecture' => 'i386'),
			
				'ami-e59ca991'	=> array('name' => 'Ubuntu 10.10', 'os_dist' => 'ubuntu', 'location' => 'eu-west-1', 'architecture' => 'x86_64'),
				'ami-fb9ca98f'	=> array('name' => 'Ubuntu 10.10', 'os_dist' => 'ubuntu', 'location' => 'eu-west-1', 'architecture' => 'i386'),
				
				'ami-cef405a7'	=> array('name' => 'Ubuntu 10.10', 'os_dist' => 'ubuntu', 'location' => 'us-east-1', 'architecture' => 'x86_64'),
				'ami-ccf405a5'	=> array('name' => 'Ubuntu 10.10', 'os_dist' => 'ubuntu', 'location' => 'us-east-1', 'architecture' => 'i386'),
				
				'ami-af7e2eea'	=> array('name' => 'Ubuntu 10.10', 'os_dist' => 'ubuntu', 'location' => 'us-west-1', 'architecture' => 'x86_64'),
				'ami-ad7e2ee8'	=> array('name' => 'Ubuntu 10.10', 'os_dist' => 'ubuntu', 'location' => 'us-west-1', 'architecture' => 'i386'),
			
				// CentOS 5.5
				'ami-ee01aaef'	=> array('name' => 'CentOS 5.5', 'os_dist' => 'centos', 'location' => 'ap-southeast-1', 'architecture' => 'x86_64'),
				'ami-e001aae1'	=> array('name' => 'CentOS 5.5', 'os_dist' => 'centos', 'location' => 'ap-southeast-1', 'architecture' => 'i386'),	
			
				'ami-0292ec50'	=> array('name' => 'CentOS 5.5', 'os_dist' => 'centos', 'location' => 'ap-southeast-1', 'architecture' => 'x86_64'),
				'ami-1c92ec4e'	=> array('name' => 'CentOS 5.5', 'os_dist' => 'centos', 'location' => 'ap-southeast-1', 'architecture' => 'i386'),
				
				'ami-9f4377eb'	=> array('name' => 'CentOS 5.5', 'os_dist' => 'centos', 'location' => 'eu-west-1', 'architecture' => 'x86_64'),
				'ami-1b1e2a6f'	=> array('name' => 'CentOS 5.5', 'os_dist' => 'centos', 'location' => 'eu-west-1', 'architecture' => 'i386'),
				
				'ami-34a6565d'	=> array('name' => 'CentOS 5.5', 'os_dist' => 'centos', 'location' => 'us-east-1', 'architecture' => 'x86_64'),
				'ami-38a65651'	=> array('name' => 'CentOS 5.5', 'os_dist' => 'centos', 'location' => 'us-east-1', 'architecture' => 'i386'),
				
				'ami-bbbeeefe'	=> array('name' => 'CentOS 5.5', 'os_dist' => 'centos', 'location' => 'us-west-1', 'architecture' => 'x86_64'),
				'ami-bfbeeefa'	=> array('name' => 'CentOS 5.5', 'os_dist' => 'centos', 'location' => 'us-west-1', 'architecture' => 'i386'),
			);
		}
		
		public function getPropsList()
		{
			return array(
				self::ACCOUNT_ID	=> 'AWS Account ID',
				self::ACCESS_KEY	=> 'AWS Access Key',
				self::SECRET_KEY	=> 'AWS Secret Key',
				self::CERTIFICATE	=> 'AWS x.509 Certificate',
				self::PRIVATE_KEY	=> 'AWS x.509 Private Key'
			);
		}
		
		public function GetServerCloudLocation(DBServer $DBServer)
		{
			return $DBServer->GetProperty(EC2_SERVER_PROPERTIES::REGION);
		}
		
		public function GetServerID(DBServer $DBServer)
		{
			return $DBServer->GetProperty(EC2_SERVER_PROPERTIES::INSTANCE_ID);
		}
		
		public function IsServerExists(DBServer $DBServer, $debug = false)
		{
			return in_array(
				$DBServer->GetProperty(EC2_SERVER_PROPERTIES::INSTANCE_ID), 
				array_keys($this->GetServersList(
					$DBServer->GetEnvironmentObject(), 
					$DBServer->GetProperty(EC2_SERVER_PROPERTIES::REGION)
				))
			);
		}
		
		public function GetServerIPAddresses(DBServer $DBServer)
		{
			$EC2Client = Scalr_Service_Cloud_Aws::newEc2(
				$DBServer->GetProperty(EC2_SERVER_PROPERTIES::REGION), 
				$DBServer->GetEnvironmentObject()->getPlatformConfigValue(self::PRIVATE_KEY),
				$DBServer->GetEnvironmentObject()->getPlatformConfigValue(self::CERTIFICATE)
			);
	        
	        $iinfo = $EC2Client->DescribeInstances($DBServer->GetProperty(EC2_SERVER_PROPERTIES::INSTANCE_ID));
		    $iinfo = $iinfo->reservationSet->item->instancesSet->item;
		    
		    return array(
		    	'localIp'	=> $iinfo->privateIpAddress,
		    	'remoteIp'	=> $iinfo->ipAddress
		    );
		}
		
		public function GetServersList(Scalr_Environment $environment, $region, $skipCache = false)
		{
			if (!$region)
				return array();
			
			if (!$this->instancesListCache[$environment->id][$region] || $skipCache)
			{
				$EC2Client = Scalr_Service_Cloud_Aws::newEc2(
					$region, 
					$environment->getPlatformConfigValue(self::PRIVATE_KEY),
					$environment->getPlatformConfigValue(self::CERTIFICATE)
				);
		        
		        try
				{
		            $results = $EC2Client->DescribeInstances();
		            $results = $results->reservationSet;
				}
				catch(Exception $e)
				{
					throw new Exception(sprintf("Cannot get list of servers for platfrom ec2: %s", $e->getMessage()));
				}


				if ($results->item)
				{					
					if ($results->item->reservationId)
						$this->instancesListCache[$environment->id][$region][(string)$results->item->instancesSet->item->instanceId] = (string)$results->item->instancesSet->item->instanceState->name;
					else
					{
						foreach ($results->item as $item)
							$this->instancesListCache[$environment->id][$region][(string)$item->instancesSet->item->instanceId] = (string)$item->instancesSet->item->instanceState->name;
					}
				}
			}
	        
			return $this->instancesListCache[$environment->id][$region];
		}
		
		public function GetServerRealStatus(DBServer $DBServer)
		{
			$region = $DBServer->GetProperty(EC2_SERVER_PROPERTIES::REGION);
			
			$iid = $DBServer->GetProperty(EC2_SERVER_PROPERTIES::INSTANCE_ID);
			if (!$iid || !$region)
			{
				$status = 'not-found';
			}
			elseif (!$this->instancesListCache[$DBServer->GetEnvironmentObject()->id][$region][$iid])
			{
		        $EC2Client = Scalr_Service_Cloud_Aws::newEc2(
					$region, 
					$DBServer->GetEnvironmentObject()->getPlatformConfigValue(self::PRIVATE_KEY),
					$DBServer->GetEnvironmentObject()->getPlatformConfigValue(self::CERTIFICATE)
				);
		        
		        try {
		        	$iinfo = $EC2Client->DescribeInstances($iid);
			        $iinfo = $iinfo->reservationSet->item;
			        
			        if ($iinfo)
			        	$status = (string)$iinfo->instancesSet->item->instanceState->name;
			        else
			        	$status = 'not-found';
		        }
		        catch(Exception $e)
		        {
		        	if (stristr($e->getMessage(), "does not exist"))
		        		$status = 'not-found';
		        	else
		        		throw $e;
		        }
			}
			else
			{
				$status = $this->instancesListCache[$DBServer->GetEnvironmentObject()->id][$region][$DBServer->GetProperty(EC2_SERVER_PROPERTIES::INSTANCE_ID)];
			}
			
			return Modules_Platforms_Ec2_Adapters_Status::load($status);
		}
		
		public function TerminateServer(DBServer $DBServer)
		{
			$EC2Client = Scalr_Service_Cloud_Aws::newEc2(
				$DBServer->GetProperty(EC2_SERVER_PROPERTIES::REGION), 
				$DBServer->GetEnvironmentObject()->getPlatformConfigValue(self::PRIVATE_KEY),
				$DBServer->GetEnvironmentObject()->getPlatformConfigValue(self::CERTIFICATE)
			);
	        
	        $EC2Client->TerminateInstances(array($DBServer->GetProperty(EC2_SERVER_PROPERTIES::INSTANCE_ID)));
	        
	        return true;
		}
		
		public function RebootServer(DBServer $DBServer)
		{
			$EC2Client = Scalr_Service_Cloud_Aws::newEc2(
				$DBServer->GetProperty(EC2_SERVER_PROPERTIES::REGION), 
				$DBServer->GetEnvironmentObject()->getPlatformConfigValue(self::PRIVATE_KEY),
				$DBServer->GetEnvironmentObject()->getPlatformConfigValue(self::CERTIFICATE)
			);
	        
	        $EC2Client->RebootInstances(array($DBServer->GetProperty(EC2_SERVER_PROPERTIES::INSTANCE_ID)));
	        
	        return true;
		}
		
		public function RemoveServerSnapshot(DBRole $DBRole)
		{
			foreach ($DBRole->getImageId(SERVER_PLATFORMS::EC2) as $location => $imageId) {
				try {
					$EC2Client = Scalr_Service_Cloud_Aws::newEc2(
						$location, 
						$DBRole->getEnvironmentObject()->getPlatformConfigValue(self::PRIVATE_KEY),
						$DBRole->getEnvironmentObject()->getPlatformConfigValue(self::CERTIFICATE)
					);	
					
					try {
						$DescribeImagesType = new DescribeImagesType();
						$DescribeImagesType->imagesSet->item[] = array("imageId" => $imageId);
			        	$ami_info = $EC2Client->DescribeImages($DescribeImagesType);
					}
					catch(Exception $e)
					{
						if (stristr($e->getMessage(), "is no longer available") || stristr($e->getMessage(), "does not exist"))
							return true;
						else
							throw $e;
					}
			        
			        $platfrom = (string)$ami_info->imagesSet->item->platform;
			        $rootDeviceType = (string)$ami_info->imagesSet->item->rootDeviceType;
			        
			        if ($rootDeviceType == 'ebs') {
			        	$EC2Client->DeregisterImage($imageId);
			        }
			        else {
			       		$image_path = (string)$ami_info->imagesSet->item->imageLocation;
	    		    	
	    		    	$chunks = explode("/", $image_path);
	    		    	
	    		    	$bucket_name = $chunks[0];
	    		    	if (count($chunks) == 3)
	    		    		$prefix = $chunks[1];
	    		    	else
	    		    		$prefix = str_replace(".manifest.xml", "", $chunks[1]);
	    		    	
	    		    	try {
	    		    		$bucket_not_exists = false;
	    		    		$S3Client = new AmazonS3(
	    		    			$DBRole->getEnvironmentObject()->getPlatformConfigValue(self::ACCESS_KEY), 
	    		    			$DBRole->getEnvironmentObject()->getPlatformConfigValue(self::SECRET_KEY)
	    		    		);
	    		    		$objects = $S3Client->ListBucket($bucket_name, $prefix);
	    		    	}
	    		    	catch(Exception $e) {
	    		    		if (stristr($e->getMessage(), "The specified bucket does not exist"))
	    		    			$bucket_not_exists = true;
	    		    	}	
	    		    			    			    	
	    		    	if ($ami_info) {
	    		    		if (!$bucket_not_exists) {
	    			    		foreach ($objects as $object)
	    			    			$S3Client->DeleteObject($object->Key, $bucket_name);
	    			    			
	    			    		$bucket_not_exists = true;
	    			    	}
	    		    		
	    		    		if ($bucket_not_exists)
	    			    		$EC2Client->DeregisterImage($imageId);
	    		    	}
			        }
				} catch(Exception $e) {
					if (stristr($e->getMessage(), "is no longer available"))
						continue;
					else
						throw $e;
				}
			}
		}
		
		public function CheckServerSnapshotStatus(BundleTask $BundleTask)
		{
			if ($BundleTask->bundleType == SERVER_SNAPSHOT_CREATION_TYPE::EC2_EBS_HVM)
			{
				try
				{
					$DBServer = DBServer::LoadByID($BundleTask->serverId);
					
			        $EC2Client = Scalr_Service_Cloud_Aws::newEc2(
						$DBServer->GetProperty(EC2_SERVER_PROPERTIES::REGION), 
						$DBServer->GetEnvironmentObject()->getPlatformConfigValue(self::PRIVATE_KEY),
						$DBServer->GetEnvironmentObject()->getPlatformConfigValue(self::CERTIFICATE)
					);
			        
			        $DescribeImagesType = new DescribeImagesType();
					$DescribeImagesType->imagesSet->item[] = array("imageId" => $BundleTask->snapshotId);
			        $ami_info = $EC2Client->DescribeImages($DescribeImagesType);
			        $ami_info = $ami_info->imagesSet->item;
			        
			        $BundleTask->Log(sprintf("Checking snapshot creation status: %s", $ami_info->imageState));
			        
			        if ($ami_info->imageState == 'available') {
			        	$metaData = array(
			        		'tags' 			=> array(ROLE_TAGS::EC2_EBS, ROLE_TAGS::EC2_HVM),
			        		'szr_version'	=> $DBServer->GetProperty(SERVER_PROPERTIES::SZR_VESION)
			        	);
			        	
			        	$BundleTask->SnapshotCreationComplete($BundleTask->snapshotId, $metaData);
			        }
			        else {
			        	$BundleTask->Log("CheckServerSnapshotStatus: AMI status = {$ami_info->imageState}. Waiting...");
			        }
				}
				catch(Exception $e) {
					Logger::getLogger(__CLASS__)->fatal("CheckServerSnapshotStatus ({$BundleTask->id}): {$e->getMessage()}");
				}
			}
		}
		
		public function CreateServerSnapshot(BundleTask $BundleTask)
		{
			$DBServer = DBServer::LoadByID($BundleTask->serverId);
			
			$EC2Client = Scalr_Service_Cloud_Aws::newEc2(
				$DBServer->GetProperty(EC2_SERVER_PROPERTIES::REGION), 
				$DBServer->GetEnvironmentObject()->getPlatformConfigValue(self::PRIVATE_KEY),
				$DBServer->GetEnvironmentObject()->getPlatformConfigValue(self::CERTIFICATE)
			);
	        
	        if (!$BundleTask->prototypeRoleId)
	        {
	        	$proto_image_id = $DBServer->GetProperty(EC2_SERVER_PROPERTIES::AMIID);
	        }
	        else
	        {
	        	$proto_image_id = DBRole::loadById($BundleTask->prototypeRoleId)->getImageId(
	        		SERVER_PLATFORMS::EC2, 
	        		$DBServer->GetProperty(EC2_SERVER_PROPERTIES::REGION)
	        	);
	        }	        
	        
	        $DescribeImagesType = new DescribeImagesType();
			$DescribeImagesType->imagesSet->item[] = array("imageId" => $proto_image_id);
	        $ami_info = $EC2Client->DescribeImages($DescribeImagesType);
	        
	        $platfrom = (string)$ami_info->imagesSet->item->platform;
	        
	        if ($platfrom == 'windows')
	        {
        
	        	//TODO: Windows platfrom is not supported yet.
	        	
	        	$BundleTask->bundleType = SERVER_SNAPSHOT_CREATION_TYPE::EC2_WIN;
	        	
	        	$BundleTask->Log(sprintf(_("Selected platfrom snapshoting type: %s"), $BundleTask->bundleType));
	        	
	        	$BundleTask->SnapshotCreationFailed("Not supported yet");
	        	return;
	        }
	        else
	        {
	        	$BundleTask->status = SERVER_SNAPSHOT_CREATION_STATUS::IN_PROGRESS;
	        	
	        	if ((string)$ami_info->imagesSet->item->rootDeviceType == 'ebs') {
	        		if ((string)$ami_info->imagesSet->item->virtualizationType == 'hvm')
	        			$BundleTask->bundleType = SERVER_SNAPSHOT_CREATION_TYPE::EC2_EBS_HVM;
	        		else
	        			$BundleTask->bundleType = SERVER_SNAPSHOT_CREATION_TYPE::EC2_EBS;
	        	} else {
	        		$BundleTask->bundleType = SERVER_SNAPSHOT_CREATION_TYPE::EC2_S3I;
	        	}
	        	
	        	$BundleTask->Save();
	        	
	        	$BundleTask->Log(sprintf(_("Selected platfrom snapshoting type: %s"), $BundleTask->bundleType));
	        	
	        	if ($BundleTask->bundleType == SERVER_SNAPSHOT_CREATION_TYPE::EC2_EBS_HVM)
	        	{
		        	try
		        	{
			        	$CreateImageType = new CreateImageType(
			        		$DBServer->GetProperty(EC2_SERVER_PROPERTIES::INSTANCE_ID),
			        		$BundleTask->roleName,
			        		$BundleTask->roleName,
			        		false
			        	);
			        	
			        	$result = $EC2Client->CreateImage($CreateImageType);
			        			        	
			        	$BundleTask->status = SERVER_SNAPSHOT_CREATION_STATUS::IN_PROGRESS;
			        	$BundleTask->snapshotId = $result->imageId;
			        	
			        	$BundleTask->Log(sprintf(_("Snapshot creating initialized (AMIID: %s). Bundle task status changed to: %s"), 
			        		$BundleTask->snapshotId, $BundleTask->status
			        	));
		        	}
		        	catch(Exception $e)
		        	{
		        		$BundleTask->SnapshotCreationFailed($e->getMessage());
		        		return;
		        	}
	        	}
	        	else {
		        	$msg = new Scalr_Messaging_Msg_Rebundle(
		        		$BundleTask->id,
						$BundleTask->roleName,
						array()
		        	);
	
		        	$metaData = $BundleTask->getSnapshotDetails();
		        	if ($metaData['rootVolumeSize'])
		        		$msg->volumeSize = $metaData['rootVolumeSize']; 
	
	        		if (!$DBServer->SendMessage($msg))
	        		{
	        			$BundleTask->SnapshotCreationFailed("Cannot send rebundle message to server. Please check event log for more details.");
	        			return;
	        		}
		        	else
		        	{
			        	$BundleTask->Log(sprintf(_("Snapshot creation started (MessageID: %s). Bundle task status changed to: %s"), 
			        		$msg->messageId, $BundleTask->status
			        	));
		        	}
	        	}
	        }
	        
	        $BundleTask->setDate('started');
	        $BundleTask->Save();
		}
		
		private function ApplyAccessData(Scalr_Messaging_Msg $msg)
		{
			
			
		}
		
		public function GetServerConsoleOutput(DBServer $DBServer)
		{
			$EC2Client = Scalr_Service_Cloud_Aws::newEc2(
				$DBServer->GetProperty(EC2_SERVER_PROPERTIES::REGION), 
				$DBServer->GetEnvironmentObject()->getPlatformConfigValue(self::PRIVATE_KEY),
				$DBServer->GetEnvironmentObject()->getPlatformConfigValue(self::CERTIFICATE)
			);
	        
	        $c = $EC2Client->GetConsoleOutput($DBServer->GetProperty(EC2_SERVER_PROPERTIES::INSTANCE_ID));
	        
	        if ($c->output)
	        	return $c->output;
	        else
	        	return false;
		}
		
		public function GetServerExtendedInformation(DBServer $DBServer)
		{
			try
			{
				try {
		        	$EC2Client = Scalr_Service_Cloud_Aws::newEc2(
						$DBServer->GetProperty(EC2_SERVER_PROPERTIES::REGION), 
						$DBServer->GetEnvironmentObject()->getPlatformConfigValue(self::PRIVATE_KEY),
						$DBServer->GetEnvironmentObject()->getPlatformConfigValue(self::CERTIFICATE)
					);
		        
		        	$iinfo = $EC2Client->DescribeInstances($DBServer->GetProperty(EC2_SERVER_PROPERTIES::INSTANCE_ID));
		        	$iinfo = $iinfo->reservationSet->item;
				}
				catch(Exception $e) {}
		        
		        if ($iinfo && $iinfo->instancesSet->item)
		        {
			        $groups = array();
			        if ($iinfo->groupSet->item->groupId)
			        	$groups[] = $iinfo->groupSet->item->groupId;
			        else
			        {
			        	foreach ($iinfo->groupSet->item as $item)
			        		$groups[] = $item->groupId;
			        }
			        
			        $monitoring = $iinfo->instancesSet->item->monitoring->state;
			        if ($monitoring == 'disabled')
			        {
			        	$monitoring = "Disabled
							&nbsp;(<a href='aws_ec2_cw_manage.php?action=Enable&server_id={$DBServer->serverId}'>Enable</a>)";
			        }
			        else 
			        {
			        	$monitoring = "<a href='/aws_cw_monitor.php?ObjectId=".$DBServer->GetProperty(EC2_SERVER_PROPERTIES::INSTANCE_ID)."&Object=InstanceId&NameSpace=AWS/EC2'>Enabled</a>
							&nbsp;(<a href='aws_ec2_cw_manage.php?action=Disable&server_id={$DBServer->serverId}'>Disable</a>)";
			        }
			        
			        
			        return array(
			        	'Instance ID'			=> $DBServer->GetProperty(EC2_SERVER_PROPERTIES::INSTANCE_ID),
			        	'Owner ID'				=> $iinfo->ownerId,
			        	'Image ID (AMI)'		=> $iinfo->instancesSet->item->imageId,
			        	'Public DNS name'		=> $iinfo->instancesSet->item->dnsName,			        
			        	'Private DNS name'		=> $iinfo->instancesSet->item->privateDnsName,
			        	'Public IP'				=> $iinfo->instancesSet->item->ipAddress,
			        	'Private IP'			=> $iinfo->instancesSet->item->privateIpAddress,			        
			        	'Key name'				=> $iinfo->instancesSet->item->keyName,
			        	'AMI launch index'		=> $iinfo->instancesSet->item->amiLaunchIndex,
			        	'Instance type'			=> $iinfo->instancesSet->item->instanceType,
			        	'Launch time'			=> $iinfo->instancesSet->item->launchTime,
			        	'Architecture'			=> $iinfo->instancesSet->item->architecture,
			        	'Root device type'		=> $iinfo->instancesSet->item->rootDeviceType,
			        	'Instance state'		=> $iinfo->instancesSet->item->instanceState->name." ({$iinfo->instancesSet->item->instanceState->code})",
			        	'Placement'				=> $iinfo->instancesSet->item->placement->availabilityZone,
			        	'Monitoring (CloudWatch)'	=> $monitoring,
			        	'Security groups'		=> implode(', ', $groups)
			        );
		        }
			}
			catch(Excpetion $e)
			{
				
			}
			
			return false;
		}
		
		public function LaunchServer(DBServer $DBServer, Scalr_Server_LaunchOptions $launchOptions = null)
		{	        
			$RunInstancesType = new RunInstancesType();
	        
	        $RunInstancesType->ConfigureRootPartition();
			
			if (!$launchOptions)
			{
				$launchOptions = new Scalr_Server_LaunchOptions();
				$DBRole = DBRole::loadById($DBServer->roleId);
				
				// Set Cloudwatch monitoring
		        $RunInstancesType->SetCloudWatchMonitoring(
		        	$DBServer->GetFarmRoleObject()->GetSetting(DBFarmRole::SETTING_AWS_ENABLE_CW_MONITORING)
		        );

		        $launchOptions->architecture = $DBRole->architecture;
		        
		        $launchOptions->imageId = $DBRole->getImageId(
		        	SERVER_PLATFORMS::EC2, 
		        	$DBServer->GetFarmRoleObject()->GetSetting(DBFarmRole::SETTING_CLOUD_LOCATION)
		        );
		        
		        $launchOptions->cloudLocation = $DBServer->GetFarmRoleObject()->GetSetting(DBFarmRole::SETTING_CLOUD_LOCATION);
		        
		        $akiId = $DBServer->GetProperty(EC2_SERVER_PROPERTIES::AKIID);
		        if (!$akiId)
		        	$akiId = $DBServer->GetFarmRoleObject()->GetSetting(DBFarmRole::SETTING_AWS_AKI_ID);
		        		
		        if ($akiId)
		        	$RunInstancesType->kernelId = $akiId;
		        	        
		        $ariId = $DBServer->GetProperty(EC2_SERVER_PROPERTIES::ARIID);
		        if (!$ariId)
		        	$ariId = $DBServer->GetFarmRoleObject()->GetSetting(DBFarmRole::SETTING_AWS_ARI_ID);
		        		
		        if ($ariId)
		        	$RunInstancesType->ramdiskId = $ariId;
		        	
				$i_type = $DBServer->GetFarmRoleObject()->GetSetting(DBFarmRole::SETTING_AWS_INSTANCE_TYPE);
		        if (!$i_type)
		        {
		        	$DBRole = DBRole::loadById($DBServer->roleId);
		        	$i_type = $DBRole->getProperty(EC2_SERVER_PROPERTIES::INSTANCE_TYPE);
		        }
		        
		        $launchOptions->serverType = $i_type;
		        
		        foreach ($DBServer->GetCloudUserData() as $k=>$v)
	        		$u_data .= "{$k}={$v};";
	        	
	        	$RunInstancesType->SetUserData(trim($u_data, ";"));
	        	
				$vpcPrivateIp = $DBServer->GetFarmRoleObject()->GetSetting(DBFarmRole::SETTING_AWS_VPC_PRIVATE_IP);
		        $vpcSubnetId = $DBServer->GetFarmRoleObject()->GetSetting(DBFarmRole::SETTING_AWS_VPC_SUBNET_ID);
		        if ($vpcSubnetId)
		        {
		        	$RunInstancesType->subnetId = $vpcSubnetId;
		        	
		        	if ($vpcPrivateIp)
		        		$RunInstancesType->privateIpAddress = $vpcPrivateIp;
		        }
			}
			else 
				$RunInstancesType->SetUserData(trim($launchOptions->userData));

			$DBServer->SetProperty(SERVER_PROPERTIES::ARCHITECTURE, $launchOptions->architecture);
				
			$EC2Client = Scalr_Service_Cloud_Aws::newEc2(
				$launchOptions->cloudLocation, 
				$DBServer->GetEnvironmentObject()->getPlatformConfigValue(self::PRIVATE_KEY),
				$DBServer->GetEnvironmentObject()->getPlatformConfigValue(self::CERTIFICATE)
			);
			
	        // Set AMI, AKI and ARI ids
	        $RunInstancesType->imageId = $launchOptions->imageId;
	        
	        if (!$RunInstancesType->subnetId) {
	        	// Set Security groups
				foreach ($this->GetServerSecurityGroupsList($DBServer, $EC2Client) as $sgroup)
	        		$RunInstancesType->AddSecurityGroup($sgroup);
	        }
	        	
	        $RunInstancesType->minCount = 1;
	        $RunInstancesType->maxCount = 1;
	        	
	        // Set availability zone
	        $avail_zone = $this->GetServerAvailZone($DBServer, $EC2Client, $launchOptions);
	        if ($avail_zone)
	        	$RunInstancesType->SetAvailabilityZone($avail_zone);
	        
	        // Set instance type
	        $RunInstancesType->instanceType = $launchOptions->serverType;
	        
	        if (in_array($RunInstancesType->instanceType, array('cc1.4xlarge', 'cg1.4xlarge')))
	        {
	        	$placementGroup = $DBServer->GetFarmRoleObject()->GetSetting(DBFarmRole::SETTING_AWS_CLUSTER_PG);
	        	if (!$placementGroup)
	        	{
	        		$placementGroup = "scalr-role-{$DBServer->farmRoleId}";
	        		if (!$EC2Client->CreatePlacementGroup($placementGroup))
	        			throw new Exception(sprintf(_("Cannot launch new instance. Unable to create placement group: %s"), $result->faultstring));
	        			
	        		$DBServer->GetFarmRoleObject()->SetSetting(DBFarmRole::SETTING_AWS_CLUSTER_PG, $placementGroup);
	        	}
	        	
	        	$RunInstancesType->SetPlacementGroup($placementGroup);
	        }
	        
	        // Set additional info
	       	$RunInstancesType->additionalInfo = "";
	       	
	       	
	       	/////
	       	if ($DBServer->status == SERVER_STATUS::TEMPORARY) {
				$keyName = "SCALR-ROLESBUILDER";
				
				$sshKey = Scalr_Model::init(Scalr_Model::SSH_KEY);
				if (!$sshKey->loadGlobalByName($keyName, $launchOptions->cloudLocation, $DBServer->envId)) {
					$result = $EC2Client->CreateKeyPair($keyName);
					if ($result->keyMaterial) {	
						$sshKey->farmId = 0;
						$sshKey->clientId = $DBServer->clientId;
						$sshKey->envId = $DBServer->envId;
						$sshKey->type = Scalr_SshKey::TYPE_GLOBAL;
						$sshKey->cloudLocation = $launchOptions->cloudLocation;
						$sshKey->cloudKeyName = $keyName;
						$sshKey->platform = SERVER_PLATFORMS::EC2;
						
						$sshKey->setPrivate($result->keyMaterial);
						
						$sshKey->setPublic($sshKey->generatePublicKey());
						
						$sshKey->save();
		            }
				}
	       	}
	       	else {
	       		$keyName = Scalr_Model::init(Scalr_Model::SSH_KEY)->loadGlobalByFarmId(
		        	$DBServer->farmId, 
		        	$DBServer->GetProperty(EC2_SERVER_PROPERTIES::REGION)
		        )->cloudKeyName;
	       	}
	       	/////
	       	
	        $RunInstancesType->keyName = $keyName;
	        
			$result = $EC2Client->RunInstances($RunInstancesType);
	        
	        if ($result->instancesSet) {
	        	$DBServer->SetProperty(EC2_SERVER_PROPERTIES::AVAIL_ZONE, (string)$result->instancesSet->item->placement->availabilityZone);
	        	$DBServer->SetProperty(EC2_SERVER_PROPERTIES::INSTANCE_ID, (string)$result->instancesSet->item->instanceId);
	        	$DBServer->SetProperty(EC2_SERVER_PROPERTIES::INSTANCE_TYPE, $RunInstancesType->instanceType);
	        	$DBServer->SetProperty(EC2_SERVER_PROPERTIES::AMIID, $RunInstancesType->imageId);
	        	$DBServer->SetProperty(EC2_SERVER_PROPERTIES::REGION, $launchOptions->cloudLocation);
	        	
	        	try {
	        		if ($DBServer->farmId != 0) {
		        		$CreateTagsType = new CreateTagsType(
		        			array((string)$result->instancesSet->item->instanceId),
		        			array(
		        				"scalr-farm-id"			=> $DBServer->farmId,
		        				"scalr-farm-name"		=> $DBServer->GetFarmObject()->Name,
		        				"scalr-farm-role-id"	=> $DBServer->farmRoleId,
		        				"scalr-role-name"		=> $DBServer->GetFarmRoleObject()->GetRoleObject()->name,
		        				"scalr-server-id"		=> $DBServer->serverId
		        			)
		        		);
		        		
		        		$EC2Client->CreateTags($CreateTagsType);
	        		}
	        	}
	        	catch(Exception $e){
	        		Logger::getLogger('EC2')->fatal("Cannot add tags to server: {$e->getMessage()}");
	        	}
	        	
		        return $DBServer;
	        }
	        else 
	            throw new Exception(sprintf(_("Cannot launch new instance. %s"), $result->faultstring));
		}
		
		/*********************************************************************/
		/*********************************************************************/
		/*********************************************************************/
		/*********************************************************************/
		/*********************************************************************/
		
		private function GetServerSecurityGroupsList(DBServer $DBServer, $EC2Client)
		{
			// Add default security group
			$retval = array('default');
			
			try {
				$aws_sgroups_list_t = $EC2Client->DescribeSecurityGroups();
				$aws_sgroups_list_t = $aws_sgroups_list_t->securityGroupInfo->item;
		        if ($aws_sgroups_list_t instanceof stdClass)
		        	$aws_sgroups_list_t = array($aws_sgroups_list_t);
	
		        $aws_sgroups = array();
		        foreach ($aws_sgroups_list_t as $sg)
		        	$aws_sgroups[strtolower($sg->groupName)] = $sg;
		        	
		        unset($aws_sgroups_list_t);
			}
			catch(Exception $e) {
				throw new Exception("GetServerSecurityGroupsList failed: {$e->getMessage()}");
			}
			
			if ($DBServer->status == SERVER_STATUS::TEMPORARY) {
				if (!$aws_sgroups['scalr-rb-system']) {
					try {
						$EC2Client->CreateSecurityGroup('scalr-rb-system', _("Security group for Roles Builder"));
					}
					catch(Exception $e) {
						throw new Exception("GetServerSecurityGroupsList failed: {$e->getMessage()}");
					}					
				
			    	$IpPermissionSet = new IpPermissionSetType();
					
			    	$group_rules = array(
						array('rule' => 'tcp:22:22:0.0.0.0/0'),
						array('rule' => 'tcp:8013:8013:0.0.0.0/0'), // For Scalarizr
						array('rule' => 'udp:8014:8014:0.0.0.0/0'), // For Scalarizr
						array('rule' => 'udp:161:162:0.0.0.0/0'),
						array('rule' => 'icmp:-1:-1:0.0.0.0/0')
					); 
					
					foreach ($group_rules as $rule) {
		            	$group_rule = explode(":", $rule["rule"]);
		                $IpPermissionSet->AddItem($group_rule[0], $group_rule[1], $group_rule[2], null, array($group_rule[3]));
		            }
		
		            // Create security group
		            $EC2Client->AuthorizeSecurityGroupIngress(
		            	$DBServer->GetEnvironmentObject()->getPlatformConfigValue(self::ACCOUNT_ID), 
		            	'scalr-rb-system', 
		            	$IpPermissionSet
		            );
				}

				array_push($retval, 'scalr-rb-system');
				
				return $retval;
			}
			
			// Add Role security group
			$role_sec_group = CONFIG::$SECGROUP_PREFIX.$DBServer->GetFarmRoleObject()->GetRoleObject()->name;
			$partent_sec_group = CONFIG::$SECGROUP_PREFIX.$DBServer->GetFarmRoleObject()->GetRoleObject()->getRoleHistory();
			
			$new_role_sec_group = "scalr-role.".$DBServer->GetFarmRoleObject()->ID;
			
			if ($aws_sgroups[strtolower($role_sec_group)]) {
				// OLD System. scalr.%ROLENAME% . Nothing to do
				array_push($retval, $role_sec_group);
			}
			else 
			{
				if ($aws_sgroups[strtolower($new_role_sec_group)]) {
					// NEW System. scalr-role.%FARM_ROLE_ID% . Nothing to do
					array_push($retval, $new_role_sec_group);
				}
				else 
				{
					try {
						$EC2Client->CreateSecurityGroup($new_role_sec_group, sprintf("Security group for FarmRoleID #%s on FarmID #%s", 
							$DBServer->GetFarmRoleObject()->ID, $DBServer->farmId
						));
					}
					catch(Exception $e) {
						throw new Exception("GetServerSecurityGroupsList failed: {$e->getMessage()}");
					}					
				
			    	$IpPermissionSet = new IpPermissionSetType();
					
			    	$group_rules = $DBServer->GetFarmRoleObject()->GetRoleObject()->getSecurityRules();
			    	
			    	//
					// Check parent security group
					//
					if (count($group_rules) == 0) {
						$group_rules = array(
							array('rule' => 'tcp:22:22:0.0.0.0/0'),
							array('rule' => 'tcp:8013:8013:0.0.0.0/0'), // For Scalarizr
							array('rule' => 'udp:8014:8014:0.0.0.0/0'), // For Scalarizr
							array('rule' => 'udp:161:162:0.0.0.0/0'),
							array('rule' => 'icmp:-1:-1:0.0.0.0/0')
						); 
	
						if ($DBServer->GetFarmRoleObject()->GetRoleObject()->hasBehavior(ROLE_BEHAVIORS::APACHE) ||
							$DBServer->GetFarmRoleObject()->GetRoleObject()->hasBehavior(ROLE_BEHAVIORS::NGINX)
						) {
							$group_rules[] = array('rule' => 'tcp:80:80:0.0.0.0/0');
							$group_rules[] = array('rule' => 'tcp:443:443:0.0.0.0/0');
						}
						
						if ($DBServer->GetFarmRoleObject()->GetRoleObject()->hasBehavior(ROLE_BEHAVIORS::MYSQL)) {
							$group_rules[] = array('rule' => 'tcp:3306:3306:0.0.0.0/0');
						}
						
						if ($DBServer->GetFarmRoleObject()->GetRoleObject()->hasBehavior(ROLE_BEHAVIORS::CASSANDRA)) {
							$group_rules[] = array('rule' => 'tcp:7000:7000:0.0.0.0/0');
							$group_rules[] = array('rule' => 'tcp:9160:9160:0.0.0.0/0');
						} 
					}
					
		            foreach ($group_rules as $rule) {
		            	$group_rule = explode(":", $rule["rule"]);
		                $IpPermissionSet->AddItem($group_rule[0], $group_rule[1], $group_rule[2], null, array($group_rule[3]));
		            }
		
		            // Create security group
		            $EC2Client->AuthorizeSecurityGroupIngress(
		            	$DBServer->GetEnvironmentObject()->getPlatformConfigValue(self::ACCOUNT_ID), 
		            	$new_role_sec_group, 
		            	$IpPermissionSet
		            );	
		            
		            $DBServer->GetFarmRoleObject()->SetSetting(DBFarmRole::SETTING_AWS_SECURITY_GROUP, $new_role_sec_group);
		            
		            array_push($retval, $new_role_sec_group);
				}
			}
			
			
			// Add MySQL Security group
			if ($DBServer->GetFarmRoleObject()->GetRoleObject()->hasBehavior(ROLE_BEHAVIORS::MYSQL))
			{
				array_push($retval, CONFIG::$MYSQL_STAT_SEC_GROUP);
				if (!$aws_sgroups[CONFIG::$MYSQL_STAT_SEC_GROUP])
				{
					try {
						$EC2Client->CreateSecurityGroup(strtolower(CONFIG::$MYSQL_STAT_SEC_GROUP), "Security group for access to mysql replication status from Scalr app");
					}
					catch(Exception $e) {
						throw new Exception("GetServerSecurityGroupsList failed: {$e->getMessage()}");
					}
					
					// Get permission rules for group
		            $IpPermissionSet = new IpPermissionSetType();
		            //$ipProtocol, $fromPort, $toPort, $groups, $ipRanges
		            $ips = explode(",", CONFIG::$APP_SYS_IPADDRESS);
		            
		            foreach ($ips as $ip)
		            {
		            	if ($ip != '')
		            		$IpPermissionSet->AddItem("tcp", "3306", "3306", null, array(trim($ip)."/32"));
		            }
		
		            // Create security group
		            $EC2Client->AuthorizeSecurityGroupIngress($DBServer->GetEnvironmentObject()->getPlatformConfigValue(self::ACCOUNT_ID), CONFIG::$MYSQL_STAT_SEC_GROUP, $IpPermissionSet);
				}
			}
	         
			return $retval;
		}
		
		private function GetServerAvailZone(DBServer $DBServer, $EC2Client, Scalr_Server_LaunchOptions $launchOptions)
		{
			if ($DBServer->status == SERVER_STATUS::TEMPORARY)
				return false;
			
			$server_avail_zone = $DBServer->GetProperty(EC2_SERVER_PROPERTIES::AVAIL_ZONE);
			
			if ($server_avail_zone && $server_avail_zone != 'x-scalr-diff')
				return $server_avail_zone; 
			
			$role_avail_zone = $this->db->GetOne("SELECT ec2_avail_zone FROM ec2_ebs WHERE server_index=? AND farm_roleid=?",
        		array($DBServer->index, $DBServer->farmRoleId)
        	);
        	
        	if (!$role_avail_zone)
        		$role_avail_zone = $DBServer->GetFarmRoleObject()->GetSetting(DBFarmRole::SETTING_AWS_AVAIL_ZONE);
        		
        	if (!$role_avail_zone || $role_avail_zone == "x-scalr-diff")
        	{
        		//TODO: Elastic Load Balancer
        		
        		// Get list of all available zones
        		$avail_zones_resp = $EC2Client->DescribeAvailabilityZones();
			    $avail_zones = array();
			    foreach ($avail_zones_resp->availabilityZoneInfo->item as $zone)
			    {
			    	if (strstr($zone->zoneState,'available')) //TODO:
			    		array_push($avail_zones, (string)$zone->zoneName);
			    }
        		
			    if (!$role_avail_zone)
			    	return false;
			    else
			    {
				    // Get count of curently running instances
	        		$instance_count = $this->db->GetOne("SELECT COUNT(*) FROM servers WHERE farm_roleid=? AND status NOT IN (?,?)", 
	        			array($DBServer->farmRoleId, SERVER_STATUS::PENDING_TERMINATE, SERVER_STATUS::TERMINATED)
	        		);
	        		
	        		// Get zone index.
	        		$zone_index = ($instance_count-1) % count($avail_zones);
			    }
        		
        		return $avail_zones[$zone_index];
        	}
        	else
        		return $role_avail_zone;
		}
		
		public function PutAccessData(DBServer $DBServer, Scalr_Messaging_Msg $message)
		{
			$put = false;
			$put |= $message instanceof Scalr_Messaging_Msg_Rebundle;
			$put |= $message instanceof Scalr_Messaging_Msg_HostInitResponse && $DBServer->GetFarmRoleObject()->GetRoleObject()->hasBehavior(ROLE_BEHAVIORS::MYSQL);
			$put |= $message instanceof Scalr_Messaging_Msg_Mysql_PromoteToMaster;
			$put |= $message instanceof Scalr_Messaging_Msg_Mysql_CreateDataBundle;
			$put |= $message instanceof Scalr_Messaging_Msg_Mysql_CreateBackup;
			
			
			if ($put) {
				$environment = $DBServer->GetEnvironmentObject();
	        	$accessData = new stdClass();
	        	$accessData->accountId = $environment->getPlatformConfigValue(self::ACCOUNT_ID);
	        	$accessData->keyId = $environment->getPlatformConfigValue(self::ACCESS_KEY);
	        	$accessData->key = $environment->getPlatformConfigValue(self::SECRET_KEY);
	        	$accessData->cert = $environment->getPlatformConfigValue(self::CERTIFICATE);
	        	$accessData->pk = $environment->getPlatformConfigValue(self::PRIVATE_KEY);
	        	
	        	$message->platformAccessData = $accessData;
			}
		}
		
		public function ClearCache ()
		{
			$this->instancesListCache = array();
		}
	}

	
	
?>