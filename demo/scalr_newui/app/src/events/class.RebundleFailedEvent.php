<?php
	
	class RebundleFailedEvent extends Event
	{
		/**
		 * 
		 * @var DBServer
		 */
		public $DBServer;
		public $BundleTaskID;
		
		public function __construct(DBServer $DBServer, $BundleTaskID)
		{
			parent::__construct();
			
			$this->DBServer = $DBServer;
			$this->BundleTaskID = $BundleTaskID;
		}
	}
?>