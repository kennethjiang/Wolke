<?php
		
	interface IScalingAlgo
	{
		public function MakeDecision(DBFarmRole $DBFarmRole);
		
		/**
		 * Must return a DataForm object that will be used to draw a configuration form for this scaling algo.
		 * @return DataForm object
		 */
		public static function GetConfigurationForm(Scalr_Environment $environment);
		
		public static function ValidateConfiguration(array &$config, DBRole $DBRole);
		
		public static function GetAlgoDescription();
	}
?>