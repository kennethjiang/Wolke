<?php

	class Scalr_Environment extends Scalr_Model
	{
		protected $dbTableName = "client_environments";
		protected $dbPropertyMap = array(
			'id'		=> 'id',
			'name'		=> array('property' => 'name', 'is_filter' => true),
			'client_id'	=> array('property' => 'clientId', 'is_filter' => true),
			'dt_added'	=> array('property' => 'dtAdded', 'createSql' => 'NOW()', 'type' => 'datetime', 'update' => false),
			'is_system'	=> array('property' => 'isSystem', 'type' => 'bool', 'update' => false)
		);

		private $plainTextSettings = array(
			ENVIRONMENT_SETTINGS::MAX_INSTANCES_LIMIT,
			ENVIRONMENT_SETTINGS::MAX_EIPS_LIMIT,
			ENVIRONMENT_SETTINGS::API_ACCESS_KEY,
			ENVIRONMENT_SETTINGS::API_ALLOWED_IPS,
			ENVIRONMENT_SETTINGS::API_ENABLED,
			ENVIRONMENT_SETTINGS::API_KEYID,
			ENVIRONMENT_SETTINGS::SYNC_TIMEOUT,
			ENVIRONMENT_SETTINGS::TIMEZONE
		);

		public
			$id,
			$name,
			$clientId,
			$dtAdded,
			$isSystem;

		private $cache = array();
		private $crypto = null, $cryptoKey;

		public function create($name, $clientId)
		{
			$this->id = 0;
			$this->name = $name;
			$this->clientId = $clientId;
			$this->save();
			return $this;
		}

		protected function getCrypto()
		{
			if (! $this->crypto) {
				$this->crypto = new Scalr_Util_CryptoTool(MCRYPT_TRIPLEDES, MCRYPT_MODE_CFB, 24, 8);
				$this->cryptoKey = @file_get_contents(dirname(__FILE__)."/../../etc/.cryptokey");
			}

			return $this->crypto;
		}

		protected function encryptValue($value)
		{
			return $this->getCrypto()->encrypt($value, $this->cryptoKey);
		}

		protected function decryptValue($value)
		{
			return $this->getCrypto()->decrypt($value, $this->cryptoKey);
		}

		public function loadByApiKeyId($keyId)
		{
			$id = $this->db->GetOne("SELECT env_id FROM client_environment_properties WHERE name = ? AND value = ?", array(
				ENVIRONMENT_SETTINGS::API_KEYID, $keyId
			));

			if ($id)
				return $this->loadById($id);
			else
				throw new Exception(sprintf(_("API KeyID '%s' not found in database"), $keyId));
		}

		public function loadDefault($clientId)
		{
			$info = $this->db->GetRow("SELECT * FROM client_environments WHERE client_id = ? AND is_system = 1", array($clientId));
			if (! $info)
				throw new Exception(sprintf(_('Default environment for clientId #%s not found'), $clientId));

			return $this->loadBy($info);
		}

		public function getPlatformConfigValue($key, $encrypted = true, $group = '')
		{
			if (in_array($key, $this->plainTextSettings))
				$encrypted = false;

			if (! isset($this->cache[$group][$key])) {
				$value = $this->db->GetOne("SELECT value FROM client_environment_properties WHERE env_id = ? AND name = ? AND `group` = ?", array($this->id, $key, $group));
				if ($encrypted)
					$value = $this->decryptValue($value);
				$this->cache[$group][$key] = $value ? $value : null;
			}

			return $this->cache[$group][$key];
		}

		public function setSystem()
		{
			$this->db->Execute("UPDATE client_environments SET is_system = 0 WHERE client_id = ?", array($this->clientId));
			$this->db->Execute("UPDATE client_environments SET is_system = 1 WHERE id = ?", array($this->id));
		}

		public function isPlatformEnabled($platform) // constant from SERVER_PLATFORMS class
		{
			return $this->getPlatformConfigValue($platform . '.is_enabled', false);
		}

		public function getEnabledPlatforms()
		{
			$enabled = array();
			foreach (array_keys(SERVER_PLATFORMS::getList()) as $value) {
				if ($this->isPlatformEnabled($value))
					$enabled[] = $value;
			}
			return $enabled;
		}

		public function getLocations()
		{
			if (!$this->cache['locations']) {
				$this->cache['locations'] = array();
				foreach ($this->getEnabledPlatforms() as $platform) {
					$locs = call_user_func(array("Modules_Platforms_".ucfirst($platform), "getLocations"));
					foreach ($locs as $k => $v)
						$this->cache['locations'][$k] = $v;
		    	}
			}

			krsort($this->cache['locations']);
			
			return $this->cache['locations'];
		}

		public function enablePlatform($platform, $enabled = true)
		{
			$props = array($platform . '.is_enabled' => $enabled ? 1 : 0);
			if (! $enabled) {
				foreach (array_keys(call_user_func(array("Modules_Platforms_".ucfirst($platform), "getPropsList"))) as $key) {
					$props[$key] = null;
				}
			}

			$this->setPlatformConfig($props, false);
			$this->cache['locations'] = null;
		}

		public function setPlatformConfig($props, $encrypt = true, $group = '')
		{
			$updates = array();

			foreach ($props as $key => $value) {
				if (is_array($value)) {
					foreach ($value as $key2 => $value2) {
						$updates[$key2] = $value2;
					}
				} else {
					$updates[$key] = $value;
				}
			}

			foreach ($updates as $key => $value) {

				if (in_array($key, $this->plainTextSettings))
					$e = false;
				else
					$e = $encrypt;

				if ($e && $value)
					$value = $this->encryptValue($value);

				try {
					if (! $value)
						$this->db->Execute("DELETE FROM client_environment_properties WHERE env_id = ? AND name = ? AND `group` = ?", array($this->id, $key, $group));
					else
						$this->db->Execute("INSERT INTO client_environment_properties SET env_id = ?, name = ?, value = ?, `group` = ? ON DUPLICATE KEY UPDATE value = ?", array($this->id, $key, $value, $group, $value));
				} catch (Exception $e) {
					throw new Exception (sprintf(_("Cannot update record. Error: %s"), $e->getMessage()), $e->getCode());
				}
			}
		}

		public function delete()
		{
			parent::delete();

			try {
				$this->db->Execute("DELETE FROM client_environment_properties WHERE env_id=?", array($this->id));
			} catch (Exception $e) {
				throw new Exception (sprintf(_("Cannot delete record. Error: %s"), $e->getMessage()), $e->getCode());
			}
		}
	}
