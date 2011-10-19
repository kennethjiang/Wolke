<?php

class Scalr_Messaging_Msg_ExecScriptResult extends Scalr_Messaging_Msg {
	public $eventName;
	public $stdout;
	public $stderr;
	public $timeElapsed;
	public $scriptName;
	public $scriptPath;
	
	function __construct ($eventName=null, $stdout=null, $stderr=null, $timeElapsed=null, $scriptName=null, $scriptPath=null) {
		parent::__construct();
		$this->eventName = $eventName;
		$this->stdout = $stdout;
		$this->stderr = $stderr;
		$this->timeElapsed = $timeElapsed;
		$this->scriptName = $scriptName;
		$this->scriptPath = $scriptPath;
	}	
}