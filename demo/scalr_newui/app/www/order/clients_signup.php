<?
/*
 * Created on 2011-7-27
 *
 * Show signup form
 */
 
 require(dirname(__FILE__) . "/../../src/prepend.inc.php");

 
 if ($_SESSION["uid"] != 0)
	   UI::Redirect("/#/dashboard");
	
 $display["title"] = _("Clients&nbsp;&raquo;&nbsp;Sign Up");
 
 $Validator = new Validator();
 
 if ($_POST){
 	// Validate input data
	if (!$Validator->IsEmail($post_email))
        $err[] = _("Invalid E-mail address");
    else {
    	$clientinfo = $db->GetRow("SELECT * FROM clients WHERE email=?", $post_email);
    	if ($clientinfo) {
    		$err[] = _("E-mail exists");
    	}
    }
    		  
    if (!$Validator->IsNotEmpty($post_password))
        $err[] = _("Password required");
            
    if (!$Validator->AreEqual($post_password, $post_password2))
        $err[] = _("Two passwords are not equal");
        
	// Normal client's farm limit is 1	              
    $post_farms_limit = 1;

	if (count($err) == 0){
		try{
		    // Add user to database
		    $db->Execute("INSERT INTO clients SET
				email           = ?,
				password        = ?,
				farms_limit     = ?,
				fullname	= ?,
				org			= ?,
				country		= ?,
				state		= ?,
				city		= ?,
				zipcode		= ?,
				address1	= ?,
				address2	= ?,
				phone		= ?,
				fax			= ?,
				comments	= ?,
				dtadded		= NOW(),
				isactive	= '1'
			 ", array(
		    	$post_email, 
		    	$Crypto->Hash($post_password),  
		    	$post_farms_limit,
		    	$post_name, 
				$post_org, 
				$post_country, 
				$post_state, 
				$post_city, 
				$post_zipcode, 
				$post_address1, 
				$post_address2,
				$post_phone,
				$post_fax,
				$post_comments
		    ));
		    
		    $clientid = $db->Insert_Id();
		    
		    $keys = Scalr::GenerateAPIKeys();
		
            /*
            Create environment
            */
			$db->Execute("INSERT INTO client_environments SET
				name		= ?,
				client_id	= ?,
				dt_added	= NOW(),
				is_system	= '1'
			", array("default", $clientid));
			$env_id = $db->Insert_Id();

			$config = array();

			$config_n[ENVIRONMENT_SETTINGS::MAX_INSTANCES_LIMIT] = 20;
			$config_n[ENVIRONMENT_SETTINGS::MAX_EIPS_LIMIT] = 5;
			$config_n[ENVIRONMENT_SETTINGS::SYNC_TIMEOUT] = 86400;
			$config_n[ENVIRONMENT_SETTINGS::TIMEZONE] = "America/Adak";
			$config_n[ENVIRONMENT_SETTINGS::API_KEYID] = $keys['id'];
			$config_n[ENVIRONMENT_SETTINGS::API_ACCESS_KEY] = $keys['key'];
		
			foreach ($config_n as $key => $value) {
				$db->Execute("INSERT INTO client_environment_properties SET env_id = ?, name = ?, value = ? ON DUPLICATE KEY UPDATE value = ?", 
				array($env_id, $key, $value, $value));
			}
		}
        catch (Exception $e)
        {
            throw new ApplicationException($e->getMessage(), E_ERROR);
        }
        
        if (count($err) == 0) {
            //$okmsg = _("Client successfully added!");
            // TODO: Show this message in another page, since clients_views.php is only available for Admin
            //UI::Redirect("clients_signup_ok.php");
            $okmsg=_("Client successfully added!");
            $template_name = "clients_signup_ok.tpl";
        } else { 
            $db->Execute("DELETE FROM clients WHERE id='{$clientid}'");
        }		
	}            
 }
 
 $display["countries"] = $db->GetAll("SELECT * FROM countries");
	
 $display = array_merge($_POST, $display);

 if (!$template_name)
	$template_name = "clients_signup.tpl";
		
 require("../src/append.inc.php"); 
?>
