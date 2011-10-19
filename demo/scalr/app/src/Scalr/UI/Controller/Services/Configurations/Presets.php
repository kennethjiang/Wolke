<?php
class Scalr_UI_Controller_Services_Configurations_Presets extends Scalr_UI_Controller
{	
	const CALL_PARAM_NAME = 'presetId';
	
	public function hasAccess()
	{
		return $this->session->getAuthToken()->hasAccess(Scalr_AuthToken::ACCOUNT_USER);
	}
	
	public function defaultAction()
	{
		$this->viewAction();
	}
	
	public function xSaveAction()
	{
		$this->request->defineParams(array(
			'presetId' => array('type' => 'int'),
			'config'	=> array('type' => 'array')
		));
		
		if (!$this->getParam('presetId'))
		{
			if (!in_array($this->getParam('roleBehavior'), array('mysql','app','memcached','cassandra','www')))
				$err['roleBehavior'] = _("Please select service name");
			
			if (!$this->getParam('presetName'))
				$err['presetName'] = _("Preset name required");
			else
			{	
				if (strlen($this->getParam('presetName')) < 5)
					$err['presetName'] = _("Preset name should be 5 chars or longer");
				elseif (!preg_match("/^[A-Za-z0-9-]+$/", $this->getParam('presetName')))
					$err['presetName'] = _("Preset name should be alpha-numeric");
				elseif (strtolower($this->getParam('presetName')) == "default")
					$err['presetName'] = _("default is reserverd name");
				elseif ($this->getParam('roleBehavior') && $this->db->GetOne("SELECT id FROM service_config_presets WHERE name = ? AND role_behavior = ? AND id != ? AND env_id = ?", array(
					$this->getParam('presetName'), $this->getParam('roleBehavior'), (int)$this->getParam('presetId'), $this->session->getEnvironmentId()
				)))
					$err['presetName'] = _("Preset with selected name already exists");
			}
		}
		
		if (count($err) == 0)
		{
			try {
				$serviceConfiguration = Scalr_Model::init(Scalr_Model::SERVICE_CONFIGURATION);
				
				if ($this->getParam('presetId')) {
					$serviceConfiguration->loadById($this->getParam('presetId'));
					
					if (!Scalr_Session::getInstance()->getAuthToken()->hasAccessEnvironment($serviceConfiguration->envId))
						throw new Exception("Preset not found in database");
				}
				else {
					$serviceConfiguration->loadBy(array(
						'client_id'		=> $this->session->getClientId(),
						'env_id'		=> $this->session->getEnvironmentId(),
						'name'			=> $this->getParam('presetName'),
						'role_behavior'	=> $this->getParam('roleBehavior')
					));
				}
				
				$config = $this->getParam('config');
				
				foreach ($config as $k=>$v) {
					if ($v)
						$serviceConfiguration->setParameterValue($k, $v);
				}
				
				foreach ($serviceConfiguration->getParameters() as $param) {
					if ($param->getType() == 'boolean') {
						if (!$config[$param->getName()])
							$serviceConfiguration->setParameterValue($param->getName(), '0');
					}
				}
				
				
				$serviceConfiguration->name = $this->getParam('presetName');
				$serviceConfiguration->save();
				
				//TODO:
				$resetToDefaults = false;
				Scalr::FireEvent(null, new ServiceConfigurationPresetChangedEvent($serviceConfiguration, $resetToDefaults));
				
				$this->response->setJsonResponse(array('success' => true));
			} catch(Exception $e) {
				$this->response->setJsonResponse(array('success' => false, 'error' => $e->getMessage()));
			}
		}
		else
			$this->response->setJsonResponse(array('success' => false, 'errors' => $err));
	}
	
	public function editAction()
	{
		$this->request->defineParams(array(
			'presetId' => array('type' => 'int')
		));
		
		$this->buildAction();
	}
	
	public function buildAction()
	{
		try {
			$moduleParams = array();
			if ($this->getParam('presetId')) {
				$serviceConfiguration = Scalr_Model::init(Scalr_Model::SERVICE_CONFIGURATION)->loadById($this->getParam('presetId'));
				if (!Scalr_Session::getInstance()->getAuthToken()->hasAccessEnvironment($serviceConfiguration->envId))
					throw new Exception("Preset not found in database");
					
				$moduleParams = array(
					'presetId'		=> $serviceConfiguration->id,
					'presetName'	=> $serviceConfiguration->name,
					'roleBehavior'	=> $serviceConfiguration->roleBehavior
				);
			}
			else {
				$moduleParams = array(
					'presetId'		=> 0,
					'presetName'	=> '',
					'roleBehavior'	=> ''
				);
			}
			
			$this->response->setJsonResponse(array(
				'success' 		=> true,
				'moduleParams'	=> $moduleParams,
				'module' => $this->response->template->fetchJs('services/configurations/presets/build.js')
			));
		} catch (Exception $e) {
			$this->response->setJsonResponse(array('success' => false, 'error' => $e->getMessage()));
		}
	}
	
	public function xGetPresetOptionsAction()
	{
		$this->request->defineParams(array(
			'presetId' => array('type' => 'int')
		));
		
		try {
			$serviceConfiguration = Scalr_Model::init(Scalr_Model::SERVICE_CONFIGURATION);
			
			if ($this->getParam('presetId')) {
				$serviceConfiguration->loadById($this->getParam('presetId'));
				
				if (!Scalr_Session::getInstance()->getAuthToken()->hasAccessEnvironment($serviceConfiguration->envId))
					throw new Exception("Preset not found in database");
			}
			else {
				$serviceConfiguration->loadBy(array(
					'client_id'		=> $this->session->getClientId(),
					'env_id'		=> $this->session->getEnvironmentId(),
					'name'			=> $this->getParam('presetName'),
					'role_behavior'	=> $this->getParam('roleBehavior')
				));
			}
			
			$items = $serviceConfiguration->getJsParameters();
			
		
			$this->response->setJsonResponse(array(
				'success' => true,
				'presetOptions' => $items
			));
		}
		catch (Exception $e) {
			$this->response->setJsonResponse(array('success' => false, 'error' => $e->getMessage()));
		}
	}
	
	public function xRemoveAction()
	{
		$this->request->defineParams(array(
			'presets' => array('type' => 'json')
		));

		foreach ($this->getParam('presets') as $presetId) {
			if (!$this->db->GetOne("SELECT id FROM farm_role_service_config_presets WHERE preset_id=?", array($presetId)))
			{
				try {
					$serviceConfiguration = Scalr_Model::init(Scalr_Model::SERVICE_CONFIGURATION)->loadById($presetId);
					if (!Scalr_Session::getInstance()->getAuthToken()->hasAccessEnvironment($serviceConfiguration->envId))
						throw new Exception("No perms");
						
					$serviceConfiguration->delete();
				}
				catch (Exception $e) {}
			}
			else
				$err[] = sprintf(_("Preset id #%s assigned to role and cannot be removed."), $presetId);
		}
		
		if (count($err) == 0)
			$this->response->setJsonResponse(array('success' => true));
		else
			$this->response->setJsonResponse(array('success' => false, 'errors' => $err));
	}
	
	public function viewAction()
	{
		try {
			$this->response->setJsonResponse(array(
				'success' => true,
				'module' => $this->response->template->fetchJs('services/configurations/presets/view.js')
			));
		} catch (Exception $e) {
			$this->response->setJsonResponse(array('success' => false, 'error' => $e->getMessage()));
		}
	}
	
	public function xListViewPresetsAction()
	{
		$this->request->defineParams(array(
			'presetId' => array('type' => 'int'),
			'sort' => array('type' => 'string', 'default' => 'id'),
			'dir' => array('type' => 'string', 'default' => 'ASC')
		));
		
		try {
			$sql = "select * FROM service_config_presets WHERE 1=1";
			$sql .= " AND env_id='".Scalr_Session::getInstance()->getEnvironmentId()."'";
			
			if ($this->getParam('presetId'))
			    $sql .= " AND id=".$this->db->qstr($this->getParam('presetId'));
			
			$response = $this->buildResponseFromSql($sql, array("name", "role_behavior"));
		    
			$this->response->setJsonResponse($response);
		} catch (Exception $e) {
			$this->response->setJsonResponse(array('success' => false, 'error' => $e->getMessage()));
		}
	}
}