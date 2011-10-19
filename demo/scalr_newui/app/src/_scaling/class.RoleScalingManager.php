<?
	class RoleScalingManager
	{
		public static $ScalingAlgos = array(); 
		
		private $DB;
		private $EnabledAlgos;
		private $FarmRoleAlgos;
		
		/**
		 * Register new scaling sensor
		 * @param $sensor
		 * @return void
		 */
		public function RegisterScalingAlgo(ScalignAlgo $algo)
		{
			if (array_search($algo, self::$ScalingAlgos) !== false)
				throw new Exception(sprintf(_('Scaling algoritm %s already attached to class <Scalr>'), get_class($algo)));
				
			self::$ScalingAlgos[] = $algo;
		}
		
		/**
		 * Constructor
		 * @param $DBFarmRole
		 * @return void
		 */
		function __construct(DBFarmRole $DBFarmRole)
		{
			$this->DB = Core::GetDBInstance();
			$this->EnabledAlgos = array();
			
			foreach (self::$ScalingAlgos as $Algo)
			{
				$algo_name = strtolower(str_replace("ScalingAlgo", "", get_class($Algo)));
				
				if ($algo_name == 'base')
					$is_enabled = true;
				else
					$is_enabled = $this->DB->GetOne("SELECT value FROM farm_role_settings WHERE farm_roleid = ? AND name = ?",
						array($DBFarmRole->ID, "scaling.{$algo_name}.enabled")
					);
				
				$props = array();
				$config = $Algo->GetConfigurationForm();
				foreach ((array)$config->ListFields() as $field)
				{
					if ($field->FieldType != FORM_FIELD_TYPE::MIN_MAX_SLIDER)
					{
						if ($is_enabled == 1)
						{
							$props[$field->Name] = $this->DB->GetOne("SELECT value FROM farm_role_settings WHERE farm_roleid = ? AND name = ?",
								array($DBFarmRole->ID, $field->Name)
							);
						}
						else
							$props[$field->Name] = $field->DefaultValue;
					}
					else
					{
						if ($is_enabled == 1)
						{
							$props["{$field->Name}.min"] = $this->DB->GetOne("SELECT value FROM farm_role_settings WHERE farm_roleid = ? AND name = ?",
								array($DBFarmRole->ID, "{$field->Name}.min")
							);
							
							$props["{$field->Name}.max"] = $this->DB->GetOne("SELECT value FROM farm_role_settings WHERE farm_roleid = ? AND name = ?",
								array($DBFarmRole->ID, "{$field->Name}.max")
							);
						}
						else
						{
							$s = explode(",", $field->DefaultValue);
							$props["{$field->Name}.min"] = $s[0];
							$props["{$field->Name}.max"] = $s[1];
						}
					}
				}
				
				$Algo->SetProperties($props);
				
				$this->FarmRoleAlgos[$algo_name] = $Algo;
				
				if ($is_enabled == 1)
					$this->EnabledAlgos[$algo_name] = $Algo;
			}
		}
		
		/**
		 * Return all registered algos
		 * @return array
		 */
		function GetRegisteredAlgos()
		{
			return $this->FarmRoleAlgos;
		}
		
		/**
		 * Return is specified scaling algo enabled for role
		 * @param string $name
		 * @return bool
		 */
		function IsAlgoEnabled($name)
		{
			return array_key_exists($name, $this->EnabledAlgos);
		}
		
		/**
		 * Returns list of enabled scaling algos for role
		 * @return array
		 */
		function GetEnabledAlgos()
		{
			return array_values($this->EnabledAlgos);
		}
	}
?>