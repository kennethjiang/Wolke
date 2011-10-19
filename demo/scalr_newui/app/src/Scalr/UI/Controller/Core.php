<?php

class Scalr_UI_Controller_Core extends Scalr_UI_Controller
{
	public function defaultAction()
	{
		// HACK for old system
		$display = array();

		if ($_SESSION["okmsg"])
		{
			$display["okmsg"] = $_SESSION["okmsg"] ? $_SESSION["okmsg"] : $okmsg;
			$_SESSION["okmsg"] = null;
		}
		elseif ($_SESSION["errmsg"])
		{
			$display["errmsg"] = $_SESSION["errmsg"] ? $_SESSION["errmsg"] : $errmsg;
			$_SESSION["errmsg"] = null;
		}
		elseif ($_SESSION["mess"])
		{
			$display["mess"] = $_SESSION["mess"] ? $_SESSION["mess"] : $mess;
			$_SESSION["mess"] = null;
		}

		if ($_SESSION["warnmsg"])
		{
			$display["warnmsg"] = $_SESSION["warnmsg"] ? $_SESSION["warnmsg"] : $warnmsg;
			$_SESSION["warnmsg"] = null;
		}

		if ($_SESSION["err"])
		{
			$err = $_SESSION["err"];
			$_SESSION["err"] = null;
		}

		if (is_array($err))
		{
			$display["errmsg"] = $errmsg ? $errmsg : "The following errors occured:";
			$display["err"] = $err;
		}

		$display["errmsg"] = preg_replace("/[\n\r]+/", "<br />", $display["errmsg"]);

		foreach ($display as $k => $v)
			$this->response->template->assignParam($k, $v);

		// END of HACk

		$this->response->template->assignParam('newUING', '1');
	}
	
	public function profileSaveAction()
	{
		global $Crypto; //FIXME:
		
		$this->request->defineParams(array(
			'fullname' => array('type' => 'string'),
			'password' => array('type' => 'string'),
			'cpassword' => array('type' => 'string'),
			'org' => array('type' => 'string'),
			'phone' => array('type' => 'string'),
			'country' => array('type' => 'string')
		));
		
		if (!$this->getParam('password'))
            $err['password'] = "Password is required";
            
        if ($this->getParam('password') != $this->getParam('cpassword'))
            $err['cpassword'] = "Two passwords are not equal";
            
                  
        if (count($err) == 0)
        {  
        	if ($this->getParam('password') != '******')
				$password = "password = '".$Crypto->Hash($this->getParam('password'))."',";
                    
            	// Add user to database
           	$this->db->Execute("UPDATE clients SET
				{$password}
				fullname	= ?,
				org			= ?,
				country		= ?,
				phone		= ?
                	WHERE id = ?
                ", 
				array(
					$this->getParam("fullname"), 
					$this->getParam("org"), 
					$this->getParam("country"), 
					$this->getParam("phone"),
					Scalr_Session::getInstance()->getClientId()
			));
		
			Scalr::FireInternalEvent('updateClient', Client::Load(Scalr_Session::getInstance()->getClientId()));
			
			$this->response->setJsonResponse(array('success' => true), 'text');
        }
        else
        	$this->response->setJsonResponse(array('success' => false, 'errors' => $err), 'text');	
	}
	
	public function profileAction()
	{
		$params = $this->db->GetRow("SELECT * FROM `clients` WHERE id=?", array(Scalr_Session::getInstance()->getClientId()));;
		
		$this->response->setJsonResponse(array(
			'success' => true,
			'moduleParams' => $params,
			'module' => $this->response->template->fetchJs('core/profile.js')
		));
	}
	
	public function searchAction()
	{
		
	}
	
	public function logoutAction()
	{
		Scalr_Session::destroy();

		$this->response->setJsonResponse(array(
			'success' => true,
			'module' => $this->response->template->fetchJs('core/logout.js')
		));
	}
	
	public function loginAction()
	{
		
	}

	public function settingsAction()
	{
		$client = Client::Load($this->session->getClientId());
		$params = array(
			'rss_login' => $client->GetSettingValue(CLIENT_SETTINGS::RSS_LOGIN),
			'rss_pass' => $client->GetSettingValue(CLIENT_SETTINGS::RSS_PASSWORD),
			'system_auth_noip' => $client->GetSettingValue(CLIENT_SETTINGS::SYSTEM_AUTH_NOIP)
		);

		$this->response->setJsonResponse(array(
			'success' => true,
			'module' => $this->response->template->fetchJs('core/settings.js'),
			'moduleParams' => $params
		));
	}

	public function xSettingsSaveAction()
	{
		$this->request->defineParams(array(
			'rss_login', 'rss_pass', 'system_auth_noip'
		));

		$client = Client::Load($this->session->getClientId());

		$rssLogin = htmlspecialchars($this->getParam('rss_login'));
		$rssPass = htmlspecialchars($this->getParam('rss_pass'));
		$systemAuthNoIP = $this->getParam('system_auth_noip') == '1' ? 1 : 0;

		$this->response->setJsonDump($this->getParam('system_auth_noip'));

		if ($rssLogin != '' || $rssPass != '') {
			if (strlen($rssLogin) < 6)
				$err['rss_login'] = "RSS feed login must be 6 chars or more";

			if (strlen($rssPass) < 6)
				$err['rss_pass'] = "RSS feed password must be 6 chars or more";
		}

		if (count($err) == 0) {
			$client->SetSettingValue(CLIENT_SETTINGS::RSS_LOGIN, $rssLogin);
			$client->SetSettingValue(CLIENT_SETTINGS::RSS_PASSWORD, $rssPass);
			$client->SetSettingValue(CLIENT_SETTINGS::SYSTEM_AUTH_NOIP, $systemAuthNoIP);

			Scalr_Session::create($this->session->getClientId(), $this->session->getUserId(), $this->session->getUserGroup());

			$this->response->setJsonResponse(array('success' => true));
		} else
			$this->response->setJsonResponse(array('success' => false, 'errors' => $err));
	}

	public function changeEnvironmentAction()
	{
		$this->request->defineParams(array(
			'environmentId' => array('type' => 'int')
		));

		//$this->response->setJsonDump($this->getParam('environmentId'));
		//$this->response->setJsonDump($this->request->debugParams());

		$env = Scalr_Model::init(Scalr_Model::ENVIRONMENT)->loadById($this->getParam('environmentId'));
		// TODO: replace with authToken->hasAccessEnvironment()
		if ($env->clientId == $this->session->getClientId()) {
			$this->session->setEnvironmentId($env->id);
			$this->response->template->messageSuccess(_("Current environment successfully switched to \"{$env->name}\""));
			$this->response->setJsonResponse(array(
				'success' => true
			));
		} else
			$this->response->setJsonResponse(array(
				'success' => false,
				'error' => _("Error switching environment: \"Access denied\"")
			));
	}

	public function loadUIAction()
	{
		$this->response->setJsonResponse(array(
			'success' => true,
			'user' => array(
				'userId' => $this->session->getUserId(),
				'clientId' => $this->session->getClientId()
			)
		));
	}
}
