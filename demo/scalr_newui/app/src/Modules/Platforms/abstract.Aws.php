<?php

	class Modules_Platforms_Aws
	{		
		/**
		 * 
		 * @return array
		 */
		public function getLocations()
		{
			return array(
				'us-east-1'		 => 'AWS / US East 1',
				'us-west-1' 	 => 'AWS / US West 1',
				'eu-west-1'		 => 'AWS / EU West 1',
				'ap-southeast-1' => 'AWS / Asia Pacific East 1',
				'ap-northeast-1' => 'AWS / Asia Pacific North 1'
			);
		}
	}
?>