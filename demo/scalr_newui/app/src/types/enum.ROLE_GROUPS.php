<?
	final class ROLE_GROUPS
	{
		const BASE			= "base";
		const DB  			= "database";
		const APP			= "app";
		const LB			= "lb";
		const CACHE			= "cache";
		const MIXED			= "mixed";
		
		static public function GetConstByBehavior($behavior)
		{
			if (is_array($behavior))
				$behavior  = implode(",", $behavior);
			
			switch($behavior)
			{
				case ROLE_BEHAVIORS::APACHE:
					return self::APP;
				break;
				
				case ROLE_BEHAVIORS::BASE:
					return self::BASE;
				break;
				
				case ROLE_BEHAVIORS::MYSQL:
					return self::DB;
				break;
				
				case ROLE_BEHAVIORS::NGINX:
					return self::LB;
				break;
				
				case ROLE_BEHAVIORS::MEMCACHED:
					return self::CACHE;
				break;
				
				case ROLE_BEHAVIORS::CASSANDRA:
					return self::DB;
				break;
				
				default:
					return self::MIXED;
				break;
			}
		}
		
		static public function GetNameByBehavior($behavior)
		{
			return self::GetName(self::GetConstByBehavior($behavior));
		}
		
		static public function GetName($const = null, $all = false)
		{
			$types = array(
				self::BASE	 => _("Base images"),
				self::DB	 => _("Database servers"),
				self::APP	 => _("Application servers"),
				self::LB	 => _("Load balancers"),
				self::CACHE  => _("Caching servers"),
				self::MIXED	 => _("Mixed images")
			);
						
			return ($all) ? $types : $types[$const];
		}
	}
?>