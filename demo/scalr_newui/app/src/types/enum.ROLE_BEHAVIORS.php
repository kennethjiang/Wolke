<?
	final class ROLE_BEHAVIORS
	{
		const BASE 		= "base";
		const MYSQL 	= "mysql";
		const NGINX	 	= "www";
		const APACHE 	= "app";
		const MEMCACHED = "memcached";
		const CASSANDRA = "cassandra";
		
		static public function GetName($const = null, $all = false)
		{
			$types = array(
				self::BASE	 => _("Base"),
				self::MYSQL	 => _("MySQL"),
				self::APACHE => _("Apache"),
				self::NGINX	 => _("Nginx"),
				self::MEMCACHED  => _("Memcached"),
				self::CASSANDRA	 => _("Cassandra")
			);
			
			return ($all) ? $types : $types[$const];
		}
	}
?>