<?php

class Scalr_UI_Controller_Bundletasks extends Scalr_UI_Controller
{
	const CALL_PARAM_NAME = 'bundleTaskId';
	
	public function defaultAction()
	{
		$this->viewAction();
	}
	
	public function viewAction()
	{
		$this->response->setJsonResponse(array(
			'success' => true,
			'module' => $this->response->template->fetchJs('bundletasks/view.js')
		));	
	}
	
	public function xCancelAction()
	{
		$this->request->defineParams(array(
			'bundleTaskId' => array('type' => 'int')
		));
		
		try {
			$task = BundleTask::LoadById($this->getParam('bundleTaskId'));
			
			if (!Scalr_Session::getInstance()->getAuthToken()->hasAccessEnvironment($task->envId))
				throw new Exception(_("You have no permissions for viewing requested page"));
			
			if (in_array($task->status, array(
				SERVER_SNAPSHOT_CREATION_STATUS::CANCELLED, 
				SERVER_SNAPSHOT_CREATION_STATUS::FAILED, 
				SERVER_SNAPSHOT_CREATION_STATUS::SUCCESS)
			))
				throw new Exception("Selected task cannot be cancelled");
				
			$task->SnapshotCreationFailed("Cancelled by client");
			
			$this->response->setJsonResponse(array('success' => true, 'message' => _("Bundle task successfully cancelled.")));
				
		} catch (Exception $e) {
			$this->response->setJsonResponse(array('success' => false, 'error' => $e->getMessage()));
		}
	}
	
	public function logsAction()
	{
		$this->request->defineParams(array(
			'bundleTaskId' => array('type' => 'int')
		));
		
		try {
			$task = BundleTask::LoadById($this->getParam('bundleTaskId'));
			
			if (!Scalr_Session::getInstance()->getAuthToken()->hasAccessEnvironment($task->envId))
				throw new Exception(_("You have no permissions for viewing requested page"));

			$this->response->setJsonResponse(array(
				'success' => true,
				'module' => $this->response->template->fetchJs('bundletasks/logs.js')
			));

		} catch (Exception $e) {
			$this->response->setJsonResponse(array('success' => false, 'error' => $e->getMessage()));
		}
	}
	
	public function xListViewLogsAction()
	{
		$this->request->defineParams(array(
			'bundleTaskId' => array('type' => 'int'),
			'sort' => array('type' => 'string', 'default' => 'id'),
			'dir' => array('type' => 'string', 'default' => 'DESC')
		));
		
		try {
			$task = BundleTask::LoadById($this->getParam('bundleTaskId'));
			
			if (!Scalr_Session::getInstance()->getAuthToken()->hasAccessEnvironment($task->envId))
				throw new Exception(_("You have no permissions for viewing requested page"));

			
			$sql = "SELECT * FROM bundle_task_log WHERE bundle_task_id = '{$this->getParam('bundleTaskId')}'";
						
			$response = $this->buildResponseFromSql($sql, array("message"));

			$this->response->setJsonResponse($response);

		} catch (Exception $e) {
			$this->response->setJsonResponse(array('success' => false, 'error' => $e->getMessage()));
		}
	}
	
	public function failureDetailsAction()
	{
		$this->request->defineParams(array(
			'bundleTaskId' => array('type' => 'int')
		));
		
		try {
			$task = BundleTask::LoadById($this->getParam('bundleTaskId'));
			
			if (!Scalr_Session::getInstance()->getAuthToken()->hasAccessEnvironment($task->envId))
				throw new Exception(_("You have no permissions for viewing requested page"));

			$this->response->setJsonResponse(array(
				'success' => true,
				'module' => $this->response->template->fetchJs('bundletasks/failuredetails.js'),
				'moduleParams' => array(
					'failure_reason' => $task->failureReason
				)
			));

		} catch (Exception $e) {
			$this->response->setJsonResponse(array('success' => false, 'error' => $e->getMessage()));
		}
	}
	
	public function xListViewTasksAction()
	{
		$this->request->defineParams(array(
			'bundleTaskId' => array('type' => 'int'),
			'sort' => array('type' => 'string', 'default' => 'id'),
			'dir' => array('type' => 'string', 'default' => 'DESC')
		));
		
		try {
			$sql = "SELECT * FROM bundle_tasks WHERE env_id = '".$this->session->getEnvironmentId()."'";
		
			if ($this->getParam('id') > 0)
				$sql .= " AND id = ".$this->db->qstr($this->getParam('bundleTaskId'));
			
			$response = $this->buildResponseFromSql($sql, array("server_id", "rolename", "failure_reason", "snapshot_id", "id"));
			
			foreach ($response["data"] as &$row) {
			    $row['server_exists'] = DBServer::IsExists($row['server_id']);
			}
	
			$this->response->setJsonResponse($response);
		} catch (Exception $e) {
			$this->response->setJsonResponse(array('success' => false, 'error' => $e->getMessage()));
		}
	}
}
