<?php
class Scalr_UI_Controller_Farms_Roles extends Scalr_UI_Controller
{
	const CALL_PARAM_NAME = 'farmRoleId';
	
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

	public function getList()
	{
		$retval = array();
		$s = $this->db->execute("SELECT id, platform, role_id FROM farm_roles WHERE farmid = ?", array($this->dbFarm->ID));
		while ($farmRole = $s->fetchRow()) {
			try {
				$dbRole = DBRole::loadById($farmRole['role_id']);
				$farmRole['name'] = $dbRole->name;
			} catch (Exception $e) {
				$farmRole['name'] = '*removed*';
			}

			$retval[$farmRole['id']] = $farmRole;
		}

		return $retval;
	}

	public function viewAction()
	{
		$this->response->setJsonResponse(array(
			'success' => true,
			'moduleParams' => array('farmName' => $this->dbFarm->Name),
			'module' => $this->response->template->fetchJs('farms/roles/view.js')
		));	
	}
	
	public function extendedInfoAction()
	{
		try {
			$dbFarmRole = DBFarmRole::LoadByID($this->getParam('farmRoleId'));
			
			$scalingManager = new Scalr_Scaling_Manager($dbFarmRole);
			$scaling_algos = array();
	        foreach ($scalingManager->getFarmRoleMetrics() as $farmRoleMetric)
	        	$scaling_algos[] = array(
	        		'name' => $farmRoleMetric->getMetric()->name, 
	        		'last_value' => $farmRoleMetric->lastValue ? $farmRoleMetric->lastValue : 'Unknown',
	        		'date'		=> date("Y-m-d H:i:s", $farmRoleMetric->dtLastPolled)
	        	);

			$form = array(
				array(
					'xtype' => 'fieldset',
					'title' => '基本信息',
					'labelWidth' => 220,
					'items' => array(
						array(
							'xtype' => 'displayfield',
							'fieldLabel' => '服务器组ID',
							'value' => $dbFarmRole->ID
						),
						array(
							'xtype' => 'displayfield',
							'fieldLabel' => '服务角色ID',
							'value' => $dbFarmRole->RoleID
						),
						array(
							'xtype' => 'displayfield',
							'fieldLabel' => '服务角色名称',
							'value' => $dbFarmRole->GetRoleObject()->name
						),
						array(
							'xtype' => 'displayfield',
							'fieldLabel' => '平台',
							'value' => $dbFarmRole->Platform
						)
					)
				)
			);

			$it = array();
			foreach ($scaling_algos as $algo) {
				$it[] = array(
					'xtype' => 'displayfield',
					'fieldLabel' => $algo['name'],
					'value' => ($algo['date']) ? "Checked at {$algo['date']}. Value: {$algo['last_value']}" : "Never checked"
				);
			}

			$form[] = array(
				'xtype' => 'fieldset',
				'labelWidth' => 220,
				'title' => '系统信息',
				'items' => $it
			);
			
			
			$it = array();
			foreach ($dbFarmRole->GetAllSettings() as $name => $value) {
				$it[] = array(
					'xtype' => 'displayfield',
					'fieldLabel' => $name,
					'value' => $value
				);
			}

			$form[] = array(
				'xtype' => 'fieldset',
				'labelWidth' => 220,
				'title' => '信息内部参数',
				'items' => $it
			);


			$this->response->setJsonResponse(array(
				'success' => true,
				'moduleParams' => array('form' => $form, 'farmName' => $this->dbFarm->Name, 'roleName' => $dbFarmRole->GetRoleObject()->name),
				'module' => $this->response->template->fetchJs('farms/roles/extendedinfo.js')
			));

		} catch (Exception $e) {
			$this->response->setJsonResponse(array('success' => false, 'error' => $e->getMessage()));
		}
	}
	
	public function xGetRoleSshPrivateKeyAction()
	{
		try
		{
			$dbFarmRole = DBFarmRole::LoadByID($this->getParam('farmRoleId'));
			$dbFarm = $dbFarmRole->GetFarmObject();
			
			$sshKey = Scalr_Model::init(Scalr_Model::SSH_KEY)->loadGlobalByFarmId(
				$dbFarm->ID, 
				$dbFarmRole->GetSetting(DBFarmRole::SETTING_CLOUD_LOCATION)
			);
			
			if (!$sshKey)
				throw new Exception("Key not found");
			
			$retval = $sshKey->getPrivate();
			
			$this->response->setHeader('Pragma', 'private');
			$this->response->setHeader('Cache-control', 'private, must-revalidate');
			$this->response->setHeader('Content-type', 'plain/text');
			$this->response->setHeader('Content-Disposition', 'attachment; filename="'.$dbFarm->Name.'-'.$dbFarmRole->GetRoleObject()->name.'.pem"');
			$this->response->setHeader('Content-Length', strlen($retval));
			
			$this->response->setResponse($retval);
			$this->response->template->enabled = false;		
		}
		catch(Exception $e)
		{
			//TODO: handle errors
		}
	}
	
	public function xLaunchNewServerAction()
	{
		try
		{
			$dbFarmRole = DBFarmRole::LoadByID($this->getParam('farmRoleId'));
			$dbFarm = $dbFarmRole->GetFarmObject();
			
			if ($dbFarm->Status != FARM_STATUS::RUNNING)
				throw new Exception("You can launch servers only on running farms");
				
			$dbRole = $dbFarmRole->GetRoleObject();
								
			$pendingInstancesCount = $dbFarmRole->GetPendingInstancesCount(); 
			if ($pendingInstancesCount >= 5)
				throw new Exception("There are {$pendingInstancesCount} pending instances. You cannot launch new instances while you have 5 pending ones.");
				
			$maxInstances = $dbFarmRole->GetSetting(DBFarmRole::SETTING_SCALING_MAX_INSTANCES);
			$minInstances = $dbFarmRole->GetSetting(DBFarmRole::SETTING_SCALING_MIN_INSTANCES);
				
        	if ($maxInstances < $minInstances+1) {
        		$dbFarmRole->SetSetting(DBFarmRole::SETTING_SCALING_MAX_INSTANCES, $maxInstances+1);
        		
        		$warnmsg = sprintf(_("The number of running %s instances is equal to maximum instances setting for this role. Maximum Instances setting for role %s has been increased automatically"), 
        			$dbRole->name, $dbRole->name
        		);
        	}
	
        	$runningInstancesCount = $dbFarmRole->GetRunningInstancesCount();
        	
        	if ($runningInstancesCount+$pendingInstancesCount > $minInstances)
	        	$dbFarmRole->SetSetting(DBFarmRole::SETTING_SCALING_MIN_INSTANCES, $minInstances+1);
	        	
	        $serverCreateInfo = new ServerCreateInfo($dbFarmRole->Platform, $dbFarmRole);
                
			Scalr::LaunchServer($serverCreateInfo);
			
			$this->response->setJsonResponse(array('success' => true, 'warnMsg' => $warnmsg));
			
		} catch(Exception $e)
		{
			$this->response->setJsonResponse(array('success' => false, 'error' => $e->getMessage()));
		}
	}
	
	public function xListViewFarmRolesAction()
	{
		$this->request->defineParams(array(
			'farmId' => array('type' => 'int'),
			'farmRoleId' => array('type' => 'int'),
			'roleId' => array('type' => 'int'),
			'id' => array('type' => 'int'),
			'sort' => array('type' => 'string', 'default' => 'id'),
			'dir' => array('type' => 'string', 'default' => 'ASC')
		));
		
		try {
			$sql = "SELECT * from farm_roles WHERE farmid=".$this->db->qstr($this->getParam('farmId'));
			
			if ($this->getParam('roleId'))
				$sql .= " AND role_id=".$this->db->qstr($this->getParam('roleId'));
		
			if ($this->getParam('farmRoleId'))
				$sql .= " AND id=".$this->db->qstr($this->getParam('farmRoleId'));
				
			$response = $this->buildResponseFromSql($sql, array("role_id", "platform"));
			foreach ($response['data'] as &$row)
			{
				$row["servers"] = $this->db->GetOne("SELECT COUNT(*) FROM servers WHERE farm_roleid=?", array($row['id']));
			
				$row['farm_status'] = $this->db->GetOne("SELECT status FROM farms WHERE id=?", array($row['farmid']));
				
				$row["domains"] = $this->db->GetOne("SELECT COUNT(*) FROM dns_zones WHERE farm_roleid=? AND status != ? AND farm_id=?", 
					array($row["id"], DNS_ZONE_STATUS::PENDING_DELETE, $row['farmid'])
				);
				
				$DBFarmRole = DBFarmRole::LoadByID($row['id']);
				
				$row['min_count'] = $DBFarmRole->GetSetting(DBFarmRole::SETTING_SCALING_MIN_INSTANCES);
				$row['max_count'] = $DBFarmRole->GetSetting(DBFarmRole::SETTING_SCALING_MAX_INSTANCES);
				
				$row['location'] = $DBFarmRole->GetSetting(DBFarmRole::SETTING_CLOUD_LOCATION);
				
				$DBRole = DBRole::loadById($row['role_id']);
				$row["name"] = $DBRole->name;
				$row['image_id'] = $DBRole->getImageId(
					$DBFarmRole->Platform,
					$DBFarmRole->GetSetting(DBFarmRole::SETTING_CLOUD_LOCATION)
				);
				
				$row['shortcuts'] = $this->db->GetAll("SELECT * FROM farm_role_scripts WHERE farm_roleid=? AND ismenuitem='1'",
					array($row['id'])
				);
				foreach ($row['shortcuts'] as &$shortcut)
					$shortcut['name'] = $this->db->GetOne("SELECT name FROM scripts WHERE id=?", array($shortcut['scriptid']));
				
					
				$scalingManager = new Scalr_Scaling_Manager($DBFarmRole);
				$scaling_algos = array();
	        	foreach ($scalingManager->getFarmRoleMetrics() as $farmRoleMetric)
	        		$scaling_algos[] = $farmRoleMetric->getMetric()->name;
					
	        	if (count($scaling_algos) == 0)
	        		$row['scaling_algos'] = _("Scaling disabled");
	        	else
					$row['scaling_algos'] = implode(', ', $scaling_algos);
			}
			
			$this->response->setJsonResponse($response);
		} catch (Exception $e) {
			$this->response->setJsonResponse(array('success' => false, 'error' => $e->getMessage()));
		}
	}
}