<?php
 
	//include("src/prepend.inc.php");
	require_once (dirname(__FILE__) . "/../../src/class.XmlMenu.php");
	$Menu = new XmlMenu();
    
    if (Scalr_Session::getInstance()->getAuthToken()->hasAccess(Scalr_AuthToken::SCALR_ADMIN))
    	$Menu->LoadFromFile(dirname(__FILE__)."/../../etc/admin_nav.xml");
    else
    {
    	$Menu->LoadFromFile(dirname(__FILE__)."/../../etc/client_nav.xml");  
    	   	    			
		// creates path for  user menu cash files in session		
		$menuDirectory = dirname(__FILE__)."/../../cache/menu";

		foreach ($GLOBALS["db"]->GetAll("SELECT name, id  FROM farms WHERE env_id=?", 
			array(Scalr_Session::getInstance()->getEnvironmentId())) as $row)
		{
		    $farm_info[] = array(
		    	'name' =>$row['name'], 
		    	'id' => $row['id']
		    );
		    // if farms list changes - file name also changes
		    $farmCrc32String .= $row['name'].$row['id'];
		}   			
		
		$farmCrc32String = crc32($farmCrc32String);		
		$xmlUserFileName = "menu_".Scalr_Session::getInstance()->getEnvironmentId()."_{$farmCrc32String}.xml";
					
		$filesArray = array();
	
		// system("rm -rf $menuDirectory");  	// delete menu directory if you need to update menu		
		
		if(!file_exists($menuDirectory))
			mkdir($menuDirectory, 0777);			
	    else  // get the list of directory files
	    {			    	
	    	$filesArray = glob("{$menuDirectory}/menu_*.xml");
	  	    	    
	   		for($i = 0; $i < count($filesArray); $i++)    		
    			$filesArray[$i] = basename($filesArray[$i]);    		    		
	    }	    	   
	    
	    $currentUserFileInfo = null;
	    
	    foreach($filesArray as $fileName)
	    {
	    	// $currentUserFileInfo[1] - user ID
	    	// $currentUserFileInfo[2] - $farmCrc32String code like  "123456789.xml"	    		   	
	    	$currentUserFileInfo = explode("_",$fileName);	

	    	// updates "menu" cache files for user
	    	if(($currentUserFileInfo[1] == Scalr_Session::getInstance()->getEnvironmentId()) &&  // current user...
	    	 	($currentUserFileInfo[2] != "{$farmCrc32String}.xml")) // has another(old) file    
	    	 	unlink("{$menuDirectory}/{$fileName}");  
	        	
		    if($fileName == $xmlUserFileName)	    	
	    		$userFileExists = true;
	    }
	    
	    if($userFileExists)
	    	$Menu->LoadFromFile("{$menuDirectory}/{$xmlUserFileName}");
	    else	
	    {    	
	    	$Menu->LoadFromFile(dirname(__FILE__)."/../../etc/client_nav.xml");		    		    	
	    	
	    	// get XML document to add new children as farms names
	    	$clientMenu = $Menu->GetXml();   
		    	
			// creates a list of farms for server farms in main menu
			$nodeServerFarms = $clientMenu->xpath("//node[@id='server_farms']");			
			
			if(count($farm_info) > 0)
				$nodeServerFarms[0]->addChild('separator');
			
			foreach($farm_info as $farm_row)
			{			
				$farmList = $nodeServerFarms[0]->addChild('node');			
				$farmList->addAttribute('title', $farm_row['name']);	
						
				$itemFarm = $farmList->addChild('item','管理');
					$itemFarm->addAttribute('href', "#/farms/{$farm_row['id']}/view");
				$itemFarm = $farmList->addChild('item','修改');
					$itemFarm->addAttribute('href', "#/farms/{$farm_row['id']}/edit");
				$itemFarm = $farmList->addChild('separator');			
				$itemFarm = $farmList->addChild('item',"所有服务对象");
					$itemFarm->addAttribute('href', "#/farms/{$farm_row['id']}/roles");								
				$itemFarm = $farmList->addChild('item',"所有服务器");
					$itemFarm->addAttribute('href', "#/servers/view?farmId={$farm_row['id']}");					
				$itemFarm = $farmList->addChild('item', "所有DNS区域");
					$itemFarm->addAttribute('href', "#/dnszones/view?farmId={$farm_row['id']}");
				$itemFarm = $farmList->addChild('item',"所有apache vhosts");
					$itemFarm->addAttribute('href', "/apache_vhosts_view.php?farm_id={$farm_row['id']}");
											
			}
	    }			

		$Menu->WtiteXmlToFile("{$menuDirectory}/{$xmlUserFileName}");			
    }

    $display["menuitems"] = json_encode($Menu->GetExtJSMenuItems());
    
?>