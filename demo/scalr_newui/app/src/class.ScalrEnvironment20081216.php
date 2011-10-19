<?

	class ScalrEnvironment20081216 extends ScalrEnvironment20081125
    {    	
    	protected function GetLatestVersion()
    	{
    		$ResponseDOMDocument = $this->CreateResponse();
    		$VersionDOMNode = $ResponseDOMDocument->createElement("version", ScalrEnvironment::LATEST_VERSION);
    		$ResponseDOMDocument->documentElement->appendChild($VersionDOMNode);
    		
    		return $ResponseDOMDocument;
    	}

    	protected function ListEBSMountpoints()
    	{
    		$ResponseDOMDocument = $this->CreateResponse();
    		
    		$MountpointsDOMNode = $ResponseDOMDocument->createElement("mountpoints");
    		
    		//
    		// List EBS Arrays
    		//
    		/*
    		$arrays = $this->DB->GetAll("SELECT * FROM ebs_arrays WHERE status IN (?,?,?) AND server_id=?",
    		array(
    			EBS_ARRAY_STATUS::MOUNTING,
    			EBS_ARRAY_STATUS::IN_USE,
    			EBS_ARRAY_STATUS::CREATING_FS,
    			$this->DBServer->serverId
    		));
    		*/
    		foreach ($arrays as $array)
    		{
    			/*
    			$mountpoints[] = array(
					'name'		=> $array['name'],
					'dir'		=> $array['mountpoint'],
    				'createfs' 	=> $array['isfscreated'] ? 0 : 1,
    				'volumes'	=> $this->DB->GetAll("SELECT * FROM farm_ebs WHERE ebs_arrayid=?", array($array['id'])),
    				'isarray'	=> 1
				);
				*/
    			//TODO:
    		}
    		
    		//
    		// List EBS Volumes
    		//
    		$volumes = $this->DB->GetAll("SELECT id FROM ec2_ebs WHERE server_id=? AND attachment_status = ? AND mount_status IN (?,?)",
    		array(
    			$this->DBServer->serverId,
    			EC2_EBS_ATTACH_STATUS::ATTACHED,
    			EC2_EBS_MOUNT_STATUS::MOUNTED,
    			EC2_EBS_MOUNT_STATUS::MOUNTING
    		));
    		
    		$DBFarmRole = $this->DBServer->GetFarmRoleObject();
    		
    		foreach ($volumes as $volume)
    		{
    			$DBEBSVolume = DBEBSVolume::loadById($volume['id']);
    			
    			$mountpoint = $DBEBSVolume->mountPoint;
    			
    			if (!$DBEBSVolume->isManual)
					$createfs = $DBEBSVolume->isFsExists ? 0 : 1;
				else
					$createfs = 0;
    			
				if ($mountpoint)
				{
	    			$mountpoints[] = array(
						'name'		=> $DBEBSVolume->volumeId,
						'dir'		=> $mountpoint,
	    				'createfs' 	=> $createfs,
	    				'volumes'	=> array($DBEBSVolume),
	    				'isarray'	=> 0
					);
				}
    		}
    		
    		//
    		// Create response
    		//
    		
    		foreach ($mountpoints as $mountpoint)
    		{
    			$MountpointDOMNode = $ResponseDOMDocument->createElement("mountpoint");
				
				$MountpointDOMNode->setAttribute("name", $mountpoint['name']);
				$MountpointDOMNode->setAttribute("dir", $mountpoint['dir']);
				$MountpointDOMNode->setAttribute("createfs", $mountpoint['createfs']);
				$MountpointDOMNode->setAttribute("isarray", $mountpoint['isarray']);
				
				$VolumesDOMNode = $ResponseDOMDocument->createElement("volumes");
				
				foreach ($mountpoint['volumes'] as $DBEBSVolume)
				{
					$VolumeDOMNode = $ResponseDOMDocument->createElement("volume");
					$VolumeDOMNode->setAttribute("device", $DBEBSVolume->deviceName);
					$VolumeDOMNode->setAttribute("volume-id", $DBEBSVolume->volumeId);
					
					$VolumesDOMNode->appendChild($VolumeDOMNode);
				}
				
				$MountpointDOMNode->appendChild($VolumesDOMNode);
				$MountpointsDOMNode->appendChild($MountpointDOMNode);
    		}
    		
    		$ResponseDOMDocument->documentElement->appendChild($MountpointsDOMNode);
    		
    		return $ResponseDOMDocument;
    	}
    }
?>