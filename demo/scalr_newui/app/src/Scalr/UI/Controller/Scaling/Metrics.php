<?php

class Scalr_UI_Controller_Scaling_Metrics extends Scalr_UI_Controller
{
	const CALL_PARAM_NAME = 'metricId';

	public function defaultAction()
	{
		$this->viewAction();
	}

	public function getList()
	{
		$dbmetrics = $this->db->Execute("SELECT * FROM scaling_metrics WHERE env_id=0 OR env_id=?",
			array($this->session->getEnvironmentId())
		);

		$metrics = array();
		while ($metric = $dbmetrics->FetchRow())
		{
			$metrics[$metric['id']] = array(
				'id'	=> $metric['id'],
				'name'	=> $metric['name'],
				'alias'	=> $metric['alias']
			);
		}

		return $metrics;
	}

	public function getListAction()
	{
		$this->response->setJsonResponse(array(
			'success' => true,
			'metrics' => $this->getList()
		));
	}

	public function xSaveAction()
	{
		$this->request->defineParams(array(
			'metricId' => array('type' => 'int'),
			'name', 'filePath', 'retrieveMethod', 'calcFunction'
		));

		$metric = Scalr_Model::init(Scalr_Model::SCALING_METRIC);

		if ($this->getParam('metricId')) {
			$metric->loadById($this->getParam('metricId'));
			if ($metric->clientId != $this->session->getClientId())
				throw new Exception("Metric not found");
		} else {
			$metric->clientId = $this->session->getClientId();
			$metric->envId = $this->session->getEnvironmentId();
			$metric->alias = 'custom';
			$metric->algorithm = Scalr_Scaling_Algorithm::SENSOR_ALGO;
		}

		if (!preg_match("/^[A-Za-z0-9]{6,}/", $this->getParam('name')))
			throw new Exception("Metric name should me alphanumeric and greater than 5 chars");

		$metric->name = $this->getParam('name');
		$metric->filePath = $this->getParam('filePath');
		$metric->retrieveMethod = $this->getParam('retrieveMethod');
		$metric->calcFunction = $this->getParam('calcFunction');

		$metric->save();
		$this->response->setJsonResponse(array('success' => true));
	}

	public function xRemoveAction()
	{
		$this->request->defineParams(array(
			'metrics' => array('type' => 'json')
		));

		foreach ($this->getParam('metrics') as $metricId) {
			
			if (!$this->db->GetOne("SELECT id FROM farm_role_scaling_metrics WHERE metric_id=?", array($metricId)))			
				$this->db->Execute("DELETE FROM scaling_metrics WHERE id=? AND env_id=?", array($metricId, $this->session->getEnvironmentId()));
			else
				$err[] = sprintf(_("Metric #%s is used and cannot be removed"), $metricId);
		}
		
		if (count($err) == 0)
			$this->response->setJsonResponse(array('success' => true));
		else
			$this->response->setJsonResponse(array('success' => false, 'errors' => $err));
	}
	
	public function createAction()
	{
		$this->response->setJsonResponse(array(
			'success' => true,
			'moduleParams' => array(
				'name' => '',
				'filePath' => '',
				'retrieveMethod' => '',
				'calcFunction' => ''
			),
			'moduleName' => $this->getModuleName('ui/scaling/metrics/create.js')
		));
	}

	public function editAction()
	{
		$this->request->defineParams(array(
			'metricId' => array('type' => 'int')
		));

		$metric = Scalr_Model::init(Scalr_Model::SCALING_METRIC);
		$metric->loadById($this->getParam('metricId'));
		if ($metric->clientId != $this->session->getClientId())
			throw new Exception ("Metric not found");

		$this->response->setJsonResponse(array(
			'success' => true,
			'moduleParams' => array(
				'name' => $metric->name,
				'filePath' => $metric->filePath,
				'retrieveMethod' => $metric->retrieveMethod,
				'calcFunction' => $metric->calcFunction
			),
			'moduleName' => $this->getModuleName('ui/scaling/metrics/create.js')
		));
	}

	public function viewAction()
	{
		$this->response->setJsonResponse(array(
			'success' => true,
			'moduleName' => $this->getModuleName('ui/scaling/metrics/view.js')
		));
	}

	public function xListViewMetricsAction()
	{
		$this->request->defineParams(array(
			'metricId' => array('type' => 'int'),
			'sort' => array('type' => 'string', 'default' => 'id'),
			'dir' => array('type' => 'string', 'default' => 'ASC')
		));

		$sql = "select * FROM scaling_metrics WHERE 1=1";
		$sql .= " AND (env_id='". $this->session->getEnvironmentId()."' OR env_id='0')";

		$response = $this->buildResponseFromSql($sql, array("name", "file_path"));
		$this->response->setJsonResponse($response);
	}
}
