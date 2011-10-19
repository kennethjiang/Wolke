<?
	require(dirname(__FILE__) . "/../src/prepend.inc.php");

	$display['title'] = _("Self-Scaling Hosting Environment utilizing Amazon's EC2.");
	$display['meta_descr'] = _("Scalr is fully redundant, self-curing and self-scaling hosting environment utilizing Amazon's EC2.  It is open source, allowing you to create server farms through a web-based interface using pre-built AMI's.");
	$display['meta_keywords'] = _("Amazon EC2, scalability, AWS, hosting, scaling, self-scaling, hosting environment, cloud computing, open source, web-based interface");

	$isxmlhttp = ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest');

	session_start();
	Scalr_Session::restore();

	if ($req_action == "pwdrecovery")
	{
		if ($_POST)
		{
			$clientinfo = $db->GetRow("SELECT * FROM clients WHERE email=?", array($post_email));
			if ($clientinfo)
			{
				$password = $Crypto->Sault(10);
				$db->Execute("UPDATE clients SET password=? WHERE id=?",
					array($Crypto->Hash($password), $clientinfo["id"])
				);

				$clientinfo["password"] = $password;

				// Send welcome E-mail
				$Mailer->ClearAddresses();
				$res = $Mailer->Send("emails/welcome.eml",
					array("client" => $clientinfo, "site_url" => "https://{$_SERVER['HTTP_HOST']}"),
					$clientinfo['email'],
					$clientinfo['fullname']
				);

				if ($isxmlhttp)
				{
					print json_encode(array("result" => "ok", "message" => "Your password has been reset and emailed<br> to you"));
					exit();
				}
				else
				{
					$display["okmsg"] = "Your password has been reset and emailed<br> to you";
					$_POST = false;
					$template_name = "login.tpl";
				}
			}
			else
				$err[] = "Specified e-mail not found in our database";
		}

		if ($err && $isxmlhttp)
		{
			print json_encode(array("result" => "error", "message" => $err[0]));
			exit();
		}

		if (!$template_name)
			$template_name = "pwdrecovery.tpl";
	}

	$redirect = false;

	if (($req_login && $req_pass) || $req_isadmin == 1)
	{
	    if (($req_login == CONFIG::$ADMIN_LOGIN) && ($Crypto->Hash($req_pass) == CONFIG::$ADMIN_PASSWORD))
		{
			$rpath = ($_SESSION["REQUEST_URI"]) ? $_SESSION["REQUEST_URI"] : "/html/index.htm";
			unset($_SESSION["REQUEST_URI"]);

			Scalr_Session::create(0, 0, Scalr_AuthToken::SCALR_ADMIN);

			$redirect = $rpath;
		}
		else
		{
			$req_login = trim($req_login);

			if($req_isadmin)
			{
				if (Scalr_Session::getInstance()->getAuthToken()->hasAccess(Scalr_AuthToken::SCALR_ADMIN))
				{
					$user = $db->GetRow("SELECT * FROM clients WHERE id=?", array($req_id));
					$valid_admin = true;
				}
				else
					$err[] = "Your session expired. Please log in again";
			}
			else
				$user = $db->GetRow("SELECT * FROM clients WHERE email=?", array($req_login));

			if ($user)
			{
		    	$bruteforce = false;
		    	if ($user['login_attempts'] >= 3 && strtotime($user['dtlastloginattempt'])+180 > time())
				{
					$err[] = _("Bruteforce Protection!<br>You must wait 3 minutes before trying again.");
					$bruteforce = true;
				}
		    	elseif ($user['login_attempts'] >= 3)
		    	{
		    		$db->Execute("UPDATE clients SET login_attempts='0' WHERE id=?", array($user["id"]));
		    	}

		    	if (!$bruteforce)
		    	{
		    		if ($user["password"] == $Crypto->Hash($req_pass) || $valid_admin)
				    {
	        			$rpath = ($_SESSION["REQUEST_URI"]) ? $_SESSION["REQUEST_URI"] : "/html/index.htm";
	        			unset($_SESSION["REQUEST_URI"]);

	        			Scalr_Session::create($user['id'], $user['id'], Scalr_AuthToken::ACCOUNT_ADMIN);

	        			$errmsg = false;
	        			$err = false;

	        			$db->Execute("UPDATE clients SET `login_attempts`=0, dtlastloginattempt=NOW() WHERE id=?", array($user["id"]));

	        			if ($post_keep_session) {
							Scalr_Session::keepSession();
	        			}
						
	        			$_SESSION['errmsg'] = null;
	        			$_SESSION['err'] = null;

	        			$redirect = $rpath;

	        			$Client = Client::Load($user['id']);
	        			
	        			if (!$Client->GetSettingValue(CLIENT_SETTINGS::DATE_FIRST_LOGIN))
	        				$Client->SetSettingValue(CLIENT_SETTINGS::DATE_FIRST_LOGIN, time());

	        			$redirect = $rpath;
				    }
				    else
				    {
	                    $db->Execute("UPDATE clients SET `login_attempts`=`login_attempts` + 1, dtlastloginattempt=NOW() WHERE id=?", array($user["id"]));
				    	$err[] = _("Incorrect login or password");
				    }
		    	}
			}
			else
                $err[] = _("Incorrect login or password");
		}
	}

	if (!$err && $redirect)
	{
		if ($isxmlhttp)
		{
			print json_encode(array("result" => "ok", "redirect" => $redirect));
			exit();
		}
		else
		{
			UI::Redirect($redirect);
		}
	}
	else
	{
		if ($isxmlhttp)
		{
			print json_encode(array("result" => "error", "message" => $err[0]));
			exit();
		}
	}

	if (!$template_name)
		$template_name = "login.tpl";
	
	require("src/append.inc.php")
?>