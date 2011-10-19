<?php

	class Scalr_UI_Request
	{
		protected
			$params = array(),
			$definitions = array(),
			$requestParams = array();

		private static $_instance = null;

		public static function getInstance()
		{
			if (self::$_instance === null)
				self::$_instance = new Scalr_UI_Request();

			return self::$_instance;
		}

		public function defineParams($defs)
		{
			foreach ($defs as $key => $value) {
				if (is_array($value))
					$this->definitions[$key] = $value;

				if (is_string($value))
					$this->definitions[$value] = array();
			}

			$this->params = array();
		}

		public function getRequestParam($key)
		{
			if (isset($this->requestParams[$key]))
				return $this->requestParams[$key];
			else
				return NULL;
		}

		public function hasParam($key)
		{
			return isset($this->requestParams[$key]);
		}

		public function getRemoteAddr()
		{
			return $_SERVER['REMOTE_ADDR'];
		}

		public function setParams($params)
		{
			$this->requestParams = array_merge($this->requestParams, $params);
		}

		public function getParam($key)
		{
			if (isset($this->params[$key]))
				return $this->params[$key];

			if (isset($this->definitions[$key])) {
				$value = $this->getRequestParam($key);
				$rule = $this->definitions[$key];
				
				if ($value == NULL && isset($rule['default']))
					$value = $rule['default'];

				switch ($rule['type']) {
					case 'int':
						$value = intval($value);
						break;

					case 'bool':
						$value = ($value == 'true' || $value == 'false') ? ($value == 'true' ? true : false) : (bool) $value;
						break;

					case 'json':
						$value = json_decode($value, true);
						break;

					case 'array':
						settype($value, 'array');
						break;

					case 'string': default:
						$value = strval($value);
						break;
				}

				$this->params[$key] = $value;
				return $value;
			}

			$this->params[$key] = $this->getRequestParam($key);
			return $this->params[$key];
		}

		public function debugParams($phpVarDump = true)
		{
			foreach ($this->definitions as $key => $value) {
				$this->getParam($key);
			}

			return $phpVarDump ? print_r($this->params, true) : $this->params;
		}
	}
