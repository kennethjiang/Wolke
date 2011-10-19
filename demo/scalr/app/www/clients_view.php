<?
	require_once('src/prepend.inc.php');
	$display['load_extjs'] = true;
	
	if (!Scalr_Session::getInstance()->getAuthToken()->hasAccess(Scalr_AuthToken::SCALR_ADMIN))
	{
		$errmsg = _("You have no permissions for viewing requested page");
		UI::Redirect("index.php");
	}
	
	// Post actions
	if ($_POST && $post_with_selected)
	{
		switch($post_action)
		{
			case "activate":
			case "deactivate":
				
				$flag = ($post_action == "activate") ? '1' : '0';
				
				$i = 0;			
				foreach ((array)$post_id as $clientid)
				{
					$db->Execute("UPDATE clients SET isactive=? WHERE id=?", array($flag, $clientid));
					$i++;
				}
				
				$okmsg = "{$i} clients updated";
				UI::Redirect("clients_view.php");
				
				break;
			
			case "cleanup":
				
				$i = 0;			
				foreach ((array)$post_id as $clientid)
				{
					$i++;
					
					$Client = Client::Load($clientid);
					
					$db->Execute("DELETE FROM servers WHERE client_id=?", array($clientid));
					$db->Execute("DELETE FROM ec2_ebs WHERE client_id=?", array($clientid));
					$db->Execute("DELETE FROM apache_vhosts WHERE client_id=?", array($clientid));
					
					$farms = $db->GetAll("SELECT id FROM farms WHERE clientid='{$clientid}'");
				    foreach ($farms as $farm)
				    {
					    $db->Execute("DELETE FROM farms WHERE id=?", array($farm["id"]));
					    $db->Execute("DELETE FROM farm_roles WHERE farmid=?", array($farm["id"]));
					    $db->Execute("DELETE FROM farm_role_options WHERE farmid=?", array($farm["id"]));
                        $db->Execute("DELETE FROM farm_role_scripts WHERE farmid=?", array($farm["id"]));
                        $db->Execute("DELETE FROM farm_event_observers WHERE farmid=?", array($farm["id"]));
                        $db->Execute("DELETE FROM elastic_ips WHERE farmid=?", array($farm["id"]));
				    }
				    
				    $roles = $db->GetAll("SELECT id FROM roles WHERE client_id='{$clientid}'");
				    foreach ($roles as $role)
				    {
				    	$db->Execute("DELETE FROM roles WHERE id = ?", array($role['id']));
			
						$db->Execute("DELETE FROM role_behaviors WHERE role_id = ?", array($role['id']));
						$db->Execute("DELETE FROM role_images WHERE role_id = ?", array($role['id']));
						$db->Execute("DELETE FROM role_parameters WHERE role_id = ?", array($role['id']));
						$db->Execute("DELETE FROM role_properties WHERE role_id = ?", array($role['id']));
						$db->Execute("DELETE FROM role_security_rules WHERE role_id = ?", array($role['id']));
						$db->Execute("DELETE FROM role_software WHERE role_id = ?", array($role['id']));
				    }
				}
				
				$okmsg = sprintf(_("%s clients cleanuped"), $i);
				UI::Redirect("clients_view.php");
				
				break;
				
			case "delete":
				
				$i = 0;			
				foreach ((array)$post_id as $clientid)
				{
					$i++;
					
					$Client = Client::Load($clientid);
					
					$db->Execute("DELETE FROM clients WHERE id='{$clientid}'");
					$db->Execute("DELETE FROM client_settings WHERE clientid='{$clientid}'");
					$db->Execute("DELETE FROM servers WHERE client_id=?", array($clientid));
					$db->Execute("DELETE FROM ec2_ebs WHERE client_id=?", array($clientid));
					
					$farms = $db->GetAll("SELECT * FROM farms WHERE clientid='{$clientid}'");
				    foreach ($farms as $farm)
				    {
					    $db->Execute("DELETE FROM farms WHERE id=?", array($farm["id"]));
					    $db->Execute("DELETE FROM farm_roles WHERE farmid=?", array($farm["id"]));
					    $db->Execute("DELETE FROM farm_role_options WHERE farmid=?", array($farm["id"]));
                        $db->Execute("DELETE FROM farm_role_scripts WHERE farmid=?", array($farm["id"]));
                        $db->Execute("DELETE FROM farm_event_observers WHERE farmid=?", array($farm["id"]));
                        $db->Execute("DELETE FROM elastic_ips WHERE farmid=?", array($farm["id"]));
				    }
				    
				    $db->Execute("DELETE FROM roles WHERE client_id='{$clientid}'");
				}
				
				$okmsg = sprintf(_("%s clients deleted"), $i);
				UI::Redirect("clients_view.php");
				
				break;
		}
	}

	if ($get_clientid)
	{
		$clientid = (int)$get_clientid;
		$display['grid_query_string'] .= "&clientid={$clientid}";
	}

	if (isset($req_isactive))
	{
		$isactive = (int)$req_isactive;
		$display['grid_query_string'] .= "&isactive={$isactive}";
	}
	
	if ($req_cancelled)
	{
		$display['grid_query_string'] .= "&cancelled=1";
	}
	
	$display["title"] = _("Clients > Manage");
	require_once ("src/append.inc.php");
?>