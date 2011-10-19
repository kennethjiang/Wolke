<?php
class Scalr_UI_Controller_Farms_Events extends Scalr_UI_Controller
{
	const CALL_PARAM_NAME = 'eventId';
	
	/**
	 * 
	 * @var DBFarm
	 */
	private $dbFarm;
	
	public function hasAccess()
	{
		$hasAccess = $this->session->getAuthToken()->hasAccess(Scalr_AuthToken::ACCOUNT_USER);

		try {
			$this->dbFarm = DBFarm::LoadByID($this->getParam(Scalr_UI_Controller_Farms::CALL_PARAM_NAME));
			if (!$this->session->getAuthToken()->hasAccessEnvironment($this->dbFarm->EnvID))
	    		throw new Exception("no access");
			
		} catch (Exception $e) {
			return false;
		}
		
		return $hasAccess;
	}

	public function defaultAction()
	{
		$this->viewAction();
	}

	public function viewAction()
	{
		$this->response->setJsonResponse(array(
			'success' => true,
			'moduleParams' => array(
				'farmName' => $this->dbFarm->Name, 
				'time' => date('r'),
				'timeOffset' => date('Z')
			),
			'module' => $this->response->template->fetchJs('farms/events/view.js')
		));	
	}
	
	public function xListViewEventsAction()
	{
		$this->request->defineParams(array(
			'farmId' => array('type' => 'int'),
			'query' => array('type' => 'string'),
			'sort' => array('type' => 'string', 'default' => 'id'),
			'dir' => array('type' => 'string', 'default' => 'DESC')
		));
		
		$sql = "SELECT farmid, message, type, dtadded FROM events WHERE farmid='{$this->dbFarm->ID}'";
	
		$response = $this->buildResponseFromSql($sql, array("message", "type", "dtadded"));
		
		foreach ($response['data'] as &$row) {			
			$row['message'] = nl2br($row['message']);	
			$row["dtadded"] = date("M j, Y H:i:s", strtotime($row["dtadded"]." ".SCALR_SERVER_TZ));
		}
		
		$this->response->setJsonResponse($response);
	}
}