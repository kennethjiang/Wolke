<?
	final class SERVER_PLATFORMS
	{
		const EC2		= 'ec2';
		const RDS		= 'rds';
		const RACKSPACE = 'rackspace';
		const EUCALYPTUS= 'eucalyptus';
		const NIMBULA	= 'nimbula';
		
		//FOR FUTURE USE
		const VPS		= 'vps';
		const GOGRID	= 'gogrid';
		const CLOUDCOM	= 'cloud.com';
		const NOVACC	= 'novacc';
		
		
		public static function GetList()
		{
			return array(
				self::EC2 			=> 'Amazon EC2',
				self::RDS 			=> 'Amazon RDS',
				self::EUCALYPTUS 	=> 'Eucalyptus',
				self::RACKSPACE		=> 'Rackspace',
				self::NIMBULA		=> 'Nimbula'
			);
		}
		
		public static function GetName($const)
		{
			$list = self::GetList();
			
			return $list[$const];
		}
	}
?>