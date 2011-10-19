<?php

class Scalr_Messaging_Service_LogQueueHandler implements Scalr_Messaging_Service_QueueHandler {
	
	private $db;
	private $logger;
	
	private static $severityCodes = array(
		'DEBUG' => 1,
		'INFO' => 2,
		'WARN' => 3,
		'WARNING' => 3,
		'ERROR' => 4
	);
	
	function __construct () {
		$this->db = Core::GetDBInstance();
		$this->logger = Logger::getLogger(__CLASS__);
	}
	
	function accept($queue) {
		return $queue == "log";
	}
	
	function handle($queue, Scalr_Messaging_Msg $message, $rawMessage) {
		$dbserver = DBServer::LoadByID($message->getServerId());
		
		if ($message instanceOf Scalr_Messaging_Msg_ExecScriptResult) {
			try {
				$this->db->Execute("INSERT INTO scripting_log SET 
					farmid = ?,
					server_id = ?, 
					event = ?,
					message = ?, 
					dtadded = NOW() 
				", array(
					$dbserver->farmId,
					$message->getServerId(),
					$message->eventName,
					sprintf("Script '%s' execution result (Execution time: %s seconds). %s %s", 
							$message->scriptName, $message->timeElapsed, 
							base64_decode($message->stderr), base64_decode($message->stdout))
				));
			} catch (Exception $e) {
				$this->logger->error($e->getMessage());
			}
			
		} elseif ($message instanceof Scalr_Messaging_Msg_Log) {
			foreach ($message->entries as $entry) {
				try {
					$this->db->Execute("INSERT INTO logentries SET 
						serverid = ?, 
						message = ?, 
						severity = ?, 
						time = ?, 
						source = ?, 
						farmid = ?
					", array(
						$message->getServerId(),
						$entry->msg,
						self::$severityCodes[$entry->level],
						time(),
						$entry->name,
						$dbserver->farmId
					));
				} catch (Exception $e) {
					$this->logger->error($e->getMessage());
				}
			}
		} elseif ($message instanceof Scalr_Messaging_Msg_RebundleLog) {
			try {
				$this->db->Execute("INSERT INTO bundle_task_log SET 
					bundle_task_id = ?,
					dtadded = NOW(),
					message = ?
				", array(
					$message->bundleTaskId,
					$message->message
				));
			} catch (Exception $e) {
				$this->logger->error($e->getMessage());
			}
		}
	}
}