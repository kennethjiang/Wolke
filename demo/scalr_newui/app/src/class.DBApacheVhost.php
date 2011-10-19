<?php
class DBApacheVhost
{
	public
		 $id,
		 $domainName,
		 $isSslEnabled,
		 $farmId,
		 $envId,
		 $farmRoleId,
		 $caCert,
		 $sslCert,
		 $sslKey,
		 $lastModified,
		 $clientId,
		 $advancedMode,
		 $options,
		 $httpdConf,
		 $httpdConfSsl;		 
		
	private 
		$db;
			
	private static $FieldPropertyMap = array(
		'id' 			=> 'id',
		'name'			=> 'domainName',
		'is_ssl_enabled'=> 'isSslEnabled',
		'farm_id'		=> 'farmId',
		'env_id'		=> 'envId',
		'farm_roleid'	=> 'farmRoleId',
		'ca_cert'		=> 'caCert',
		'ssl_cert'		=> 'sslCert',
		'ssl_key'		=> 'sslKey',						
		'client_id'		=> 'clientId',
		'advanced_mode'	=> 'advancedMode',
		'last_modified' => 'lastModified',
		'httpd_conf'	=> 'httpdConf',
		'httpd_conf_ssl'=> 'httpdConfSsl',
		'httpd_conf_vars' => 'options'
	);
			
	function __construct()
	{
		$this->db = Core::GetDBInstance();
	}
	
	/**
	 * @name   create
	 * @return DBApacheVhost
	 */
	static function create($domainName, $farmId, $farmRoleId,  $clientId,
	$advancedMode, $httpdConf, $httpdConfSsl, $options)
	{		
		$vhost = new DBApacheVhost();
		
		$vhost->domainName 		= $domainName;
		$vhost->farmId 			= (int)$farmId;
		$vhost->farmRoleId 		= (int)$farmRoleId;
		$vhost->clientId 		= (int)$clientId;
		$vhost->advancedMode	= $advancedMode;
		$vhost->httpdConf		= $httpdConf;
		$vhost->httpdConfSsl	= $httpdConfSsl;
		$vhost->options 		= $options;
		
		$vhost->disableSSL();
		
		return $vhost;
	}
		
	/**
	 * @name  save
	 * @param $domainName
	 * @param $farmId
	 * @param $farmRoleId
	 * @param $isSslEnabled
	 * @return unknown_type
	 */
	function save()
	{			
		// gets all class properties
		$row = $this->unBind();
			unset($row['id']);
			unset($row['last_modified']);
			
		$set  = array();
		$bind = array();
		
		// shapes query params by class field's name and value
		foreach ($row as $field => $value) 
		{
			$set[] = "`$field` = ?";			
			$bind[] = $value;					
		}
		$set = join(', ', $set);	

		try
		{
			if ($this->id) 
			{			
				//  Updates
				$bind[] = $this->id;
				$this->db->Execute("UPDATE apache_vhosts SET $set, `last_modified` = NOW() WHERE `id` = ?",$bind);				
			}
			else 
			{
				//  Inserts
				$this->db->Execute("INSERT INTO apache_vhosts SET $set, `last_modified` = NOW() ", $bind);
				$this->id = $this->db->Insert_ID();
			}
		}
		catch(Exception $e)
		{			
			if($e->getCode() == 1062)
				throw new Exception ("Can't save virtual host. Error: equal domain name was found");
			else
				throw new Exception ("Can't save virtual host. Error: " . $e->getMessage(), $e->getCode());
		}		
	}
	
	/**	 
	 * @name unBind
	 * @return multitype:
	 */
	private function unBind () 
	{
		$row = array();
		foreach (self::$FieldPropertyMap as $field => $property) {
			$row[$field] = $this->{$property};
		}
		
		// returns class fields		
		return $row;		
	}
	
	/**
	 * @name  loadById
	 * @param $id
	 * @param $clientId
	 * @return ApacheVhost object
	 */
	static function loadById($id)
	{				
		$vhost = new DBApacheVhost();		
   		
   		$hostInfo = $vhost->db->GetRow("SELECT * FROM apache_vhosts WHERE id = ?",
   			array((int)$id)
   		);  
   		
   		if(!$hostInfo)
   			throw new Exception(_("Host not found"));
   			
		foreach(self::$FieldPropertyMap as $k => $v)
		{
			if ($hostInfo[$k])
				$vhost->{$v} = $hostInfo[$k];
		}
			
   		return $vhost;
	}			
	
	/**
	 * @name  addCerificates
	 * @param $sslCertFile
	 * @param $sslKeyFile
	 * @param $caKeyFilePath
	 * @return unknown_type
	 */
	function enableSSL($sslCertContent, $sslKeyContent, $caCertContent = null)
	{			
		// adds SSL certificate
		if($sslCertContent != $this->sslCert)			
			$this->sslCert = $this->checkCertificate($sslCertContent, "SSL");
				
		$this->isSslEnabled = 1;			
		
		
		if (!$this->id || !$this->db->GetOne("SELECT id FROM apache_vhosts WHERE is_ssl_enabled='1' AND id=?", array($this->id)))
		{
			$ssl_vhost_id = $this->db->GetOne("SELECT id FROM apache_vhosts WHERE farm_roleid = ? AND is_ssl_enabled = 1",
				array($this->farmRoleId)
			);
					
			if($info && $this->id != $ssl_vhost_id)
				throw new Exception(_("Role cannot have more than one SSL virtual host."));
		}
		
		// adds CA certificate if it was set
		if($caCertContent && ($caCertContent != $this->caCert))
			$this->caCert = $this->checkCertificate($caCertContent, "CA");			

		if(!$sslKeyContent)
			throw new Exception(_("Empty SSL key file"));
			
		if($sslKeyContent != $this->sslKey)
			$this->sslKey = $sslKeyContent;				
	}
	
	function disableSSL()
	{
		$this->isSslEnabled = 0;
		$this->sslCert = null;
		$this->sslKey = null;
		$this->caCert = null;		
	}
	
	private function checkCertificate($certContent, $certName = null)
	{
		// parse certificate			
		$certParseInfo = @openssl_x509_parse($certContent);		
			
		if(!$certParseInfo['name'])					
			throw new Exception(trim(sprintf(_("Incorrect or corrupted certificate %s"), $certName)));
	
		return $certContent;
	}	
	
}