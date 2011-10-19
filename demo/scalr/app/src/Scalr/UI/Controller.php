<?php

	class Scalr_UI_Controller
	{
		/**
		 * @var Scalr_UI_Request
		 */
		public $request;

		/**
		 * @var Scalr_UI_Response
		 */
		public $response;

		/**
		 * @var Scalr_Session
		 */
		public $session;

		public $db;

		public $uiCacheKeyPattern;

		public function __construct()
		{
			$this->request = Scalr_UI_Request::getInstance();
			$this->response = Scalr_UI_Response::getInstance();
			$this->db = Core::getDBInstance();
			$this->session = Scalr_Session::getInstance();
		}

		public function getParam($key)
		{
			return $this->request->getParam($key);
		}

		public function hasAccess()
		{
			return true;
		}

		public function getModuleName($name, $onlyPath = false)
		{
			$tm = filemtime(APPPATH . "/www/js/{$name}");
			$nameTm = str_replace('.js', "-{$tm}.js", $name);

			if ($onlyPath)
				return "/js/{$nameTm}";
			else
				return array(
					'page' => "Scalr." . str_replace('/', '.', str_replace(".js", "", $name)),
					'file' => "/js/{$nameTm}"
				);
		}

		protected function sort($item1, $item2)
		{
			$f1 = $item1[$this->getParam('sort')];
			$f2 = $item2[$this->getParam('sort')];
			
			return strnatcmp($f1,$f2);
		}

		protected function buildResponseFromData(array $data, $filterFields = array())
		{
			$this->request->defineParams(array(
				'start' => array('type' => 'int', 'default' => 0),
				'limit' => array('type' => 'int', 'default' => 20)
			));
			
			if ($this->getParam('query') && count($filterFields) > 0) {
				foreach ($data as $k=>$v) {
					$found = false;
					foreach ($filterFields as $field)
					{
						if (stristr($v[$field], $this->getParam('query'))) {
							$found = true;
							break;
						}
					}
					
					if (!$found)
						unset($data[$k]);
				}
			}
			
			$response['total'] = count($data);

			if ($this->getParam('sort')) {
				uasort($data, array($this, 'sort'));

				if ($this->getParam('dir') == 'DESC')
					$data = array_reverse($data);
			}

			$data = (count($data) > $this->getParam('limit')) ? array_slice($data, $this->getParam('start'), $this->getParam('limit')) : $data;

			$response["success"] = true;
			$response['data'] = array_values($data);

			return $response;
		}
		
		protected function buildResponseFromSql($sql, $filterFields = array(), $groupSQL = "", $simpleQuery = true, $noLimit = false)
		{
			$this->request->defineParams(array(
				'start' => array('type' => 'int'),
				'limit' => array('type' => 'int')
			));

			if ($this->getParam('query') && count($filterFields) > 0) {
				$filter = $this->db->qstr('%' . $this->getParam('query') . '%');
				foreach($filterFields as $field) {
					if ($simpleQuery)
						$likes[] = "`{$field}` LIKE {$filter}";
					else
						$likes[] = "{$field} LIKE {$filter}";
				}
				$sql .= " AND (";
				$sql .= join(" OR ", $likes);
				$sql .= ")";
			}

			if ($groupSQL)
				$sql .= "{$groupSQL}";

			if (! $noLimit) {
				$response["total"] = $this->db->Execute($sql)->RecordCount();
			}

			$sort = preg_replace("/[^A-Za-z0-9_]+/", "", $this->getParam('sort'));
			$dir = $this->getParam('dir');
			$dir = (in_array(strtolower($dir), array('asc', 'desc'))) ? $dir : 'ASC';

			if ($noLimit) {
				$sql .= " ORDER BY `{$sort}` $dir";
			} else {
				$start = $this->getParam('start');
				$limit = $this->getParam('limit');
				$sql .= " ORDER BY `{$sort}` $dir LIMIT $start, $limit";
			}

			$response["success"] = true;
			$response["data"] = $this->db->GetAll($sql);

			return $response;
		}

		public function call($pathChunks)
		{
			$arg = array_shift($pathChunks);

			if (($subController = self::loadController($arg, get_class($this))) != null) {
				$this->addUiCacheKeyPatternChunk($arg);
				$subController->uiCacheKeyPattern = $this->uiCacheKeyPattern;
				$subController->call($pathChunks);

			} else if (($action = $arg . 'Action') && method_exists($this, $action)) {
				if ($this->response->template->name == '')
					$this->response->template->name = './../ui/' . strtolower((array_pop(explode('_', get_class($this)))) . '_' . $arg . '.tpl');

				$this->addUiCacheKeyPatternChunk($arg);
				$this->response->setHeader('X-Scalr-Cache-Id', $this->uiCacheKeyPattern);
				$this->{$action}();

			} else if (count($pathChunks) > 0) {
				$const = constant(get_class($this) . '::CALL_PARAM_NAME');
				if ($const) {
					$this->request->setParams(array($const => $arg));
					$this->addUiCacheKeyPatternChunk('{' . $const . '}');
				} else {
					// TODO notice
				}

				$this->call($pathChunks);

			} else if (method_exists($this, 'defaultAction') && $arg == '') {
				if ($this->response->template->name == '')
					$this->response->template->name = './../ui/' . strtolower((array_pop(explode('_', get_class($this)))) . '_' . 'default' . '.tpl');

				$this->response->setHeader('X-Scalr-Cache-Id', $this->uiCacheKeyPattern);
				$this->defaultAction();

			} else {
				// JS page not found
				Scalr_UI_Response::getInstance()->pageNotFound();
			}
		}

		public function addUiCacheKeyPatternChunk($chunk)
		{
			$this->uiCacheKeyPattern .= "/{$chunk}";
		}

		static public function handleRequest($pathChunks, $params)
		{
			if ($pathChunks[0] == '')
				$pathChunks = array('core');

			Scalr_UI_Request::getInstance()->setParams($params);
			$controller = self::loadController(array_shift($pathChunks));

			if ($controller) {
				$controller->uiCacheKeyPattern = '';
				
				if (isset($_SERVER['HTTP_X_AJAX_SCALR'])) {
					try {
						$controller->addUiCacheKeyPatternChunk(strtolower((array_pop(explode('_', get_class($controller))))));
						$controller->call($pathChunks);
					} catch (Exception $e) {
						Scalr_UI_Response::getInstance()->setJsonResponse(array('success' => false, 'error' => $e->getMessage()));
					}
				} else {
					$controller->addUiCacheKeyPatternChunk(strtolower((array_pop(explode('_', get_class($controller))))));
					$controller->call($pathChunks);
				}
			} else {
				Scalr_UI_Response::getInstance()->pageNotFound();
			}

			Scalr_UI_Response::getInstance()->sendResponse();
		}

		static public function loadController($controller, $prefix = 'Scalr_UI_Controller')
		{
			if (preg_match("/^[a-z0-9]+$/i", $controller)) {
				$controller = ucwords(strtolower($controller));
				$className = "{$prefix}_{$controller}";
				if (file_exists(SRCPATH . '/' . str_replace('_', '/', $prefix) . '/' . $controller . '.php') && class_exists($className)) {
					$o = new $className();
					if ($o->hasAccess())
						return $o;
					else {
						Scalr_UI_Response::getInstance()->pageAccessDenied();
					}
				}
			}

			return null;
		}
	}
