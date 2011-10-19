<?php
	require("src/prepend.inc.php"); 
		
	if (!Scalr_Session::getInstance()->getAuthToken()->hasAccess(Scalr_AuthToken::ACCOUNT_USER, Scalr_AuthToken::MODULE_VHOSTS))
	{
		$errmsg = _("You have no permissions for viewing requested page");
		UI::Redirect("/#/dashboard");
	}
	
	$display["title"] = _("Add virtual host");
	$display['task'] = 'create';

	if ($req_vhost_id)
	{
		try
		{
			//
			// Displays vhost values
			//
			$vhost = DBApacheVhost::loadById($req_vhost_id);  	
	   				
			if (!Scalr_Session::getInstance()->getAuthToken()->hasAccessEnvironment($vhost->envId))
				throw new Exception("Virtualhost not found");
			
	   		$display['vhost_id']		 	= $vhost->id;
	   		$display['domain_name']		 	= $vhost->domainName;
	   		$display['host_ssl_enabled'] 	= $vhost->isSslEnabled;   		
	   		$display['loadedFarmId'] 		= $vhost->farmId;
	   		$display['loadedFarmRoleId'] 	= $vhost->farmRoleId;
	   		$display['user_template']		= $vhost->httpdConf;
	   		$display['user_template_ssl']	= ($vhost->httpdConfSsl) ? $vhost->httpdConfSsl : @file_get_contents("../templates/services/apache/ssl.vhost.tpl");
	   		$display['userMode']			= $vhost->advancedMode;
	   		
	   		// displays SSL && CA cert names
	   		if($vhost->isSslEnabled)
	   		{ 
	   			$info = @openssl_x509_parse($vhost->sslCert, false);
				$display["ssl_cert_name"] = $info["name"];
	
				if($vhost->caCert)
				{				
					$info = @openssl_x509_parse($vhost->caCert, false);
					$display["ca_cert_name"] = $info["name"];				
				}
				
				unset($info);
	   		} 
	   	           
	   		
	   		$options 						= unserialize($vhost->options);
	   		$display['document_root_dir'] 	= $options['document_root'];
	   		$display['logs_dir'] 			= $options['logs_dir'];
	   		$display['server_admin'] 		= $options['server_admin'];   		
	   		$display['aliases'] 			= $options['server_alias'];   		
	   				
		}
		catch(Exception $e)
		{
	   		 $err[] = $e->getMessage();
	   		 UI::Redirect("/apache_vhosts_view.php");
		}
		
		$display['task'] = 'edit';
	}
	
	if($_POST)
	{
		try
		{
			$validator = new Validator();		
			
			if(!$validator->IsDomain($req_domain_name))
				$err[] = _("domain name is incorrect");
			
			$DBFarm = DBFarm::LoadByID($req_farm_target);

			if(!Scalr_Session::getInstance()->getAuthToken()->hasAccessEnvironment($DBFarm->EnvID))
				$err[] = _("farm not found");	

			if($req_role_target)
				$DBFarmRole = DBFarmRole::LoadByID($req_role_target);
			
			if($DBFarmRole->FarmID != $DBFarm->ID)
				$err[] = _("there is no roles with such ID for selected farm.");

			if(!$validator->IsEmail($req_server_admin))
				$err[] = _("server admin's email is incorrect or empty ");

			if(!$req_document_root_dir)
				$err[] = _("document root is empty");

			if(!$req_logs_dir)
				$err[] = _("logs directory is empty");

			//
			// Loads SSL and CA certificates
			//
			if($req_isSslEnabled)
			{
				if (!$vhost)
				{
					// validate SSL certificate 	
					if(!is_uploaded_file($_FILES['ssl_cert']['tmp_name']))
						$err[] = _("SSL certificate required");
	
					if(!is_uploaded_file($_FILES['ssl_key']['tmp_name']))
						$err[] = _("SSL key required");	
	
					// validate CA certificate
	
					if($_FILES['ca_cert']['tmp_name'])
					{
						if(!is_uploaded_file($_FILES['ca_cert']['tmp_name']))
							$err[] = _("CA certificate not uploaded");
					}
				}
				else
				{
					$sslFileUploaded = is_uploaded_file($_FILES['ssl_cert']['tmp_name']);
					$sslKeyFileUploaded = is_uploaded_file($_FILES['ssl_key']['tmp_name']);
					
					// required cert file existed
					if(!$sslFileUploaded && !$vhost->isSslEnabled) 				
						$err[] = _("SSL certificate required");		
					// new cert from file							
					elseif($sslFileUploaded)					    
						$sslCertContent	= @file_get_contents($_FILES['ssl_cert']['tmp_name']);
					 // old sert	
					else										 
						$sslCertContent = $vhost->sslCert;			
	
					if(!$sslKeyFileUploaded && !$vhost->isSslEnabled )
						$err[] = _("SSL key required");					
					elseif($sslKeyFileUploaded)
						$sslKeyContent 	= @file_get_contents($_FILES['ssl_key']['tmp_name']);
					else
						$sslKeyContent = $vhost->sslKey;
										
				   	if($_FILES['ca_cert']['tmp_name'])
					{						 	
						if(!is_uploaded_file($_FILES['ca_cert']['tmp_name']))
							$err[] = _("CA certificate not uploaded");
					}	
		 		
					if($err)
						throw new Exception();
	
					if($_FILES['ca_cert']['tmp_name'])
						$caCertContent 	= @file_get_contents($_FILES['ca_cert']['tmp_name']);
				}
			}
				
			//
			// Creates host
			//
			if (!$err)
			{
				// fills smarty host template	
				if (!$vhost || $vhost->isSslEnabled == 0)
				{
					if($req_isSslEnabled)	
					{	
						$httpConfigTemplateSSL = @file_get_contents("../templates/services/apache/ssl.vhost.tpl");
						
						$sslCertContent		= @file_get_contents($_FILES['ssl_cert']['tmp_name']);
						$sslKeyContent 		= @file_get_contents($_FILES['ssl_key']['tmp_name']);
						
						if($_FILES['ca_cert']['tmp_name'])
							$caCertContent 	= @file_get_contents($_FILES['ca_cert']['tmp_name']);
					}
					else
						$httpConfigTemplateSSL = '';
				}
				
				if($req_user_template_ssl && $req_isSslEnabled)
					$httpConfigTemplateSSL = $req_user_template_ssl;
				
				$httpConfigTemplate = @file_get_contents("../templates/services/apache/nonssl.vhost.tpl");

				if($req_user_template)
					$httpConfigTemplate = $req_user_template;
				
				$options = serialize(array(
					"document_root" 	=> trim($req_document_root_dir),
					"logs_dir"			=> trim($req_logs_dir),
					"server_admin"		=> trim($req_server_admin),
					"server_alias"		=> trim($req_aliases)		
				));	

				if (!$vhost)
				{
					$vhost = DBApacheVhost::create($req_domain_name,
						(int)$req_farm_target,
						(int)$req_role_target,
						(int)Scalr_Session::getInstance()->getClientId(),
						$advancedMode,
						$httpConfigTemplate,
						$httpConfigTemplateSSL,
						$options
					);
					
					$vhost->envId = (int)Scalr_Session::getInstance()->getEnvironmentId();
					
					$str_action = 'added';
				}
				else
				{
					$vhost->domainName 		= $req_domain_name;
					$vhost->farmId 			= (int)$req_farm_target;
					$vhost->farmRoleId 		= (int)$req_role_target;
					$vhost->advancedMode	= $advancedMode;
					$vhost->httpdConf		= $httpConfigTemplate;
					$vhost->httpdConfSsl		= $httpConfigTemplateSSL;
						
					$vhost->options = $options;	

					$str_action = 'updated';
				}
		
				// adds SSL certificate
				if($req_isSslEnabled)
				{
					$vhost->enableSSL($sslCertContent, $sslKeyContent, $caCertContent);
				}
				else
					$vhost->disableSSL();

			}
			else
				throw new Exception();

			$vhost->save();

			$servers = $DBFarm->GetServersByFilter(array('status' => array(SERVER_STATUS::INIT, SERVER_STATUS::RUNNING)));
			foreach ($servers as $DBServer)
			{
				if ($DBServer->GetFarmRoleObject()->GetRoleObject()->hasBehavior(ROLE_BEHAVIORS::NGINX) || 
					$DBServer->GetFarmRoleObject()->GetRoleObject()->hasBehavior(ROLE_BEHAVIORS::APACHE))
					$DBServer->SendMessage(new Scalr_Messaging_Msg_VhostReconfigure());
			}


			$okmsg = _("Virtual host successfully {$str_action}");
			UI::Redirect("/apache_vhosts_view.php");

		}
		catch(Exception $e)
		{
			if($e->getMessage())
				$err[] = $e->getMessage();

			$display['document_root_dir']	= $req_document_root_dir;
			$display['domain_name']			= $req_domain_name;
			$display['host_ssl_enabled']	= $req_isSslEnabled;
			$display['server_admin']		= $req_server_admin;
			$display['aliases']				= $req_aliases;
			$display['logs_dir']			= $req_logs_dir;
			$display['user_template']		= $req_user_template;
			$display['user_template_ssl']	= $req_user_template_ssl;
			$display['userMode']			= $advancedMode;
			$display['loadedFarmId']		= $req_farm_target;
			$display['loadedFarmRoleId']	= $req_role_target;
		}
	}	
	
	if (!$req_vhost_id)
	{
		$display['document_root_dir']	= CONFIG::$APACHE_DOCROOT_DIR;
		$display['server_admin']		= $Client->Email;
		$display['logs_dir']			= CONFIG::$APACHE_LOGS_DIR;
		$display['user_template']		= @file_get_contents("../templates/services/apache/nonssl.vhost.tpl");
		$display['user_template_ssl']	= @file_get_contents("../templates/services/apache/ssl.vhost.tpl");
	}
	
	$template_name = 'apache_vhost_add.tpl';
	
	require("src/append.inc.php"); 