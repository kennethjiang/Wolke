<?php

	class Scalr_Cronjob_MessagingQueue extends Scalr_System_Cronjob
    {
        static function getConfig () {
        	return array(
        		"description" => "Manage messages queues"
        	);
        }
    	
        private $logger;
        
        private $messageSerializer;
        
    	function __construct() {
        	$this->logger = Logger::getLogger(__CLASS__);
			$this->messageSerializer = new Scalr_Messaging_XmlSerializer();        	
        }
        
        function run ($options)
        {
            $db = Core::GetDBInstance();
            
            // Check undelivered messges and try to send them again.
            $messages = $db->Execute("SELECT server_id, message, id, handle_attempts FROM messages 
            		WHERE status=? AND UNIX_TIMESTAMP(dtlasthandleattempt)+handle_attempts*120 < UNIX_TIMESTAMP(NOW())", 
            		array(MESSAGE_STATUS::PENDING));
            
            while ($message = $messages->FetchRow())
            {
				try
				{
					if ($message['handle_attempts'] >= 3)
					{
						$db->Execute("UPDATE messages SET status=? WHERE id=?", array(MESSAGE_STATUS::FAILED, $message['id']));
					}
					else
					{
						$DBServer = DBServer::LoadByID($message['server_id']);						
						// Only 0.2-68 or greater version support this feature.
						if ($DBServer->IsSupported("0.2-68"))
						{					
							$msg = $this->messageSerializer->unserialize($message['message']);
							$DBServer->SendMessage($msg);
						}
						else
						{
							$db->Execute("UPDATE messages SET status=? WHERE id=?", array(MESSAGE_STATUS::UNSUPPORTED, $message['id']));
						}
					}
				}
				catch(Exception $e)
				{
					
				}
            }
        }
    }
