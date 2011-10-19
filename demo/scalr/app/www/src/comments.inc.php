<?php
	if ($req_task == "delete" && Scalr_Session::getInstance()->getAuthToken()->hasAccess(Scalr_AuthToken::SCALR_ADMIN))
	{
		$commentid = (int)$req_commentid; 
		$db->Execute("DELETE FROM comments WHERE id=?", array($commentid));
		
		$okmsg = _("Comment successfully removed");
		UI::Redirect($redir_link);
	}
	
	if ($req_task == "post_comment")
	{
		$HTMLPurifier_Config = HTMLPurifier_Config::createDefault();
	    $HTMLPurifier_Config->set('HTML', 'Allowed', '');
	    $HTMLPurifier_Config->set('Cache', 'DefinitionImpl', null);	    
		$HTMLPurifier_Config->set('Core', 'CollectErrors', true);
		    	
		$purifier = new HTMLPurifier($HTMLPurifier_Config);
		$comment = $purifier->purify($post_comment);
		$okmsg = "";
		
		if (Scalr_Session::getInstance()->getAuthToken()->hasAccess(Scalr_AuthToken::SCALR_ADMIN) && $display["allow_moderation"])
		{
			if ($post_approval_state != $display['approval_state'])
			{
				switch($entity_name)
				{
					case COMMENTS_OBJECT_TYPE::ROLE:
						
						$db->Execute("UPDATE roles SET approval_state=? WHERE id=?", array($post_approval_state, $role_info['id']));
						
						break;
						
					case COMMENTS_OBJECT_TYPE::SCRIPT:
						
						// Save script
						$db->Execute("UPDATE script_revisions SET approval_state = ? WHERE scriptid = ? AND revision = ?
						", array(
							$post_approval_state, $script_info['id'], $post_script_version
						));
						
						if ($display['approval_state'] == APPROVAL_STATE::PENDING || 
							$db->GetOne("SELECT * FROM script_revisions WHERE approval_state != ? AND scriptid=?", 
							array($post_approval_state, $script_info['id'])) == 0
						) {
							$db->Execute("UPDATE scripts SET approval_state = ? WHERE id = ?
							", array(
								$post_approval_state, $script_info['id']
							));
						}
						
						break;
				}
				
				//Send mail
				$client = $db->GetRow("SELECT * FROM clients WHERE id=?", array($object_owner));
				// Send mail
				$Mailer->ClearAddresses();
				$res = $Mailer->Send("emails/contributed_moderated.eml", 
					array(
						"declined"		=> ($post_approval_state == APPROVAL_STATE::DECLINED) ? true : false,
						"client" 		=> $client, 
						"entity_name" 	=> $entity_name, 
						"comment" 		=> $comment,
						"name"			=> $object_name,
						"submitted_by"	=> $submitted_by,
						"link"			=> $link
					), 
					$client['email'], 
					$client['fullname']
				);
			
				$Logger->info("Sending 'emails/contrinbuted_moderated.eml' email to '{$client['email']}'. Result: {$res}");
				if (!$res)
					$Logger->error($Mailer->ErrorInfo);
				
				
				$moderation_phase_changed = true;
				
				$okmsg = sprintf(_("%s succssfully moderated. "), $entity_name);
			}
		}
		
		if (strlen($comment) == 0 && !$moderation_phase_changed)
			$err[] = _("Comment cannot be empty");
		
		if (count($err) == 0 && strlen($comment) > 0)
		{
			$db->Execute("INSERT INTO comments SET 
				clientid		= ?,
				object_owner	= ?,
				dtcreated		= NOW(),
				object_type		= ?,
				comment			= ?,
				objectid		= ?,
				isprivate		= ?
			", array(
				Scalr_Session::getInstance()->getClientId(),
				$object_owner,
				$entity_name,
				$comment,
				$id,
				($post_isprivate == 1) ? 1 : 0
			));
			
			$submitted_by = (Scalr_Session::getInstance()->getClientId() == 0) ? _("Scalr Team") : $db->GetOne("SELECT fullname FROM clients WHERE id=?", array(Scalr_Session::getInstance()->getClientId()));
			$link = "http://{$_SERVER['HTTP_HOST']}/{$redir_link}";
			
			if (Scalr_Session::getInstance()->getClientId() != $object_owner && !$moderation_phase_changed)
			{
				if ($object_owner == 0)
				{
					$emails = explode("\n", CONFIG::$TEAM_EMAILS);
					if (count($emails) > 0)
					{
						foreach ($emails as $email)
						{
							$email = trim($email);
							
							$Mailer->ClearAddresses();
							$res = $Mailer->Send("emails/new_comment.eml", 
								array(
									"client" 		=> array("fullname" => _("Scalr Team Member")), 
									"entity_name" 	=> $entity_name, 
									"comment" 		=> $comment,
									"name"			=> $object_name,
									"submitted_by"	=> $submitted_by,
									"link"			=> $link
								), 
								$email, 
								""
							);
						
							$Logger->info("Sending 'emails/new_comment.eml' email to '{$email}'. Result: {$res}");
							if (!$res)
								$Logger->error($Mailer->ErrorInfo);
						}
					}
				}
				else
				{
					$client = $db->GetRow("SELECT * FROM clients WHERE id=?", array($object_owner));
					// Send mail
					$Mailer->ClearAddresses();
					$res = $Mailer->Send("emails/new_comment.eml", 
						array(
							"client" 		=> $client, 
							"entity_name" 	=> $entity_name, 
							"comment" 		=> $comment,
							"name"			=> $object_name,
							"submitted_by"	=> $submitted_by,
							"link"			=> $link
						), 
						$client['email'], 
						$client['fullname']
					);
				
					$Logger->info("Sending 'emails/new_comment.eml' email to '{$client['email']}'. Result: {$res}");
					if (!$res)
						$Logger->error($Mailer->ErrorInfo);
				}
			}
			
			$okmsg .= _("Comment successfully added.");
		}
		
		if (count($err) == 0)
			UI::Redirect($redir_link);
	}
	
	$display['comments'] = $db->GetAll("SELECT * FROM comments WHERE object_type=? AND objectid=? ORDER BY id DESC", 
		array($entity_name, $id)
	);
	
	foreach ($display['comments'] as &$comment)
	{
		if ($comment['clientid'] != 0)
		{
			$comment['client'] = $db->GetRow("SELECT fullname FROM clients WHERE id=?", array($comment['clientid']));
			if (!$comment['client']['fullname'])
				$comment['client']['fullname'] = _("Scalr User");
		}
		
		// Scalr team comments
		if ($comment['clientid'] == 0)
		{
			$comment['color_schema'] = 'comment_red';
			$comment['client']['fullname'] = _("Scalr Team");
		}
		elseif ($comment['clientid'] != 0 && $comment['clientid'] == Scalr_Session::getInstance()->getClientId())
			$comment['color_schema'] = 'comment_green';
		else
			$comment['color_schema'] = 'comment_purple';
	}
?>