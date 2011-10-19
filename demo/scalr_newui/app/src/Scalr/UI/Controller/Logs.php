<?php

class Scalr_UI_Controller_Logs extends Scalr_UI_Controller
{
	public function hasAccess()
	{
		return $this->session->getAuthToken()->hasAccess(Scalr_AuthToken::ACCOUNT_USER);
	}

	public function systemAction()
	{
		$farmsController = self::loadController('Farms');
		if (is_null($farmsController))
			throw new Exception('Controller Farms not created');
		else
			$farms = $farmsController->getList();

		$farms[0] = '所有云平台';

		$this->response->setJsonResponse(array(
			'success' => true,
			'module' => $this->response->template->fetchJs('logs/system.js'),
			'moduleParams' => array(
				'farms' => $farms,
				'params' => array(
					'severity[1]' => 0,
					'severity[2]' => 1,
					'severity[3]' => 1,
					'severity[4]' => 1,
					'severity[5]' => 1
				),
				'time' => date('r'),
				'timeOffset' => date('Z')
			)
		));
	}

	public function scriptingAction()
	{
		$farmsController = self::loadController('Farms');
		if (is_null($farmsController))
			throw new Exception('Controller Farms not created');
		else
			$farms = $farmsController->getList();

		$farms[0] = 'All farms';

		$this->response->setJsonResponse(array(
			'success' => true,
			'module' => $this->response->template->fetchJs('logs/scripting.js'),
			'moduleParams' => array(
				'farms' => $farms,
				'time' => date('r'),
				'timeOffset' => date('Z')
			)
		));
	}

	public function apiAction()
	{
		$this->response->setJsonResponse(array(
			'success' => true,
			'module' => $this->response->template->fetchJs('logs/api.js'),
			'moduleParams' => array(
				'time' => date('r'),
				'timeOffset' => date('Z')
			)
		));
	}

	public function xGetLogsAction()
	{
		$this->request->defineParams(array(
			'serverId' => array('type' => 'string'),
			'farmId' => array('type' => 'int'),
			'severity' => array('type' => 'array'),
			'query' => array('type' => 'string'),
			'sort' => array('type' => 'string', 'default' => 'id'),
			'dir' => array('type' => 'string', 'default' => 'DESC')
		));

		$farms = $this->db->GetAll("SELECT id FROM farms WHERE env_id=?", array($this->session->getEnvironmentId()));
		$frms = array();
		foreach ($farms as $f)
			array_push($frms, $f['id']);

		$frms = implode(',', array_values($frms));

		if ($frms == '')
			$frms = '0';

		$authSql = " AND farmid IN ({$frms}) AND farmid > 0";
		$sql = "SELECT * from logentries WHERE id > 0 {$authSql}";

		if ($this->getParam('serverId'))
		{
			$serverId = preg_replace("/[^A-Za-z0-9-]+/si", "", $this->getParam('serverId'));
			$sql  .= " AND serverid = '{$serverId}'";
		}

		if ($this->getParam('farmId'))
		{
			$sql  .= " AND farmid = '{$this->getParam('farmId')}'";
		}

		if ($this->getParam('severity'))
		{
			$severities = array();
			foreach ($this->getParam('severity') as $key => $value) {
				if ($value == 1)
					$severities[] = $key;
			}
			if (count($severities)) {
				$severities = implode(",", $severities);
				$sql  .= " AND severity IN ($severities)";
			} else {
				$sql .= " AND 0"; // is it right ?
			}
		}

		$severities = array(1 => "Debug", 2 => "Info", 3 => "Warning", 4 => "Error", 5 => "Fatal");
		if ($this->getParam('action') == "download") {
			$fileContent = array();
			$farmNames = array();
			$fileContent[] = "Type;Time;Farm;Caller;Message\r\n";

			$response = $this->buildResponseFromSql($sql, array("message", "serverid", "source"), "", true, true);

			foreach($response["data"] as &$data) {
				$data["time"] = date("M j, Y H:i:s", $data["time"]);
				$data["s_severity"] = $severities[$data["severity"]];

				if (!$farmNames[$data['farmid']])
					$farmNames[$data['farmid']] = $this->db->GetOne("SELECT name FROM farms WHERE id=?", array($data['farmid']));

				$data['farm_name'] = $farmNames[$data['farmid']];

				$data['message'] = str_replace("<br />","",$data['message']);
				$data['message'] = str_replace("\n","",$data['message']);

				$fileContent[] = "{$data['s_severity']};{$data['time']};{$data['farm_name']};{$data['source']};{$data['message']}";
			}

			$this->response->setHeader('Content-Encoding', 'utf-8');
			$this->response->setHeader('Content-Type', 'text/csv', true);
			$this->response->setHeader('Expires', 'Mon, 10 Jan 1997 08:00:00 GMT');
			$this->response->setHeader('Pragma', 'no-cache');
			$this->response->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate');
			$this->response->setHeader('Cache-Control', 'post-check=0, pre-check=0');
			$this->response->setHeader('Content-Disposition', 'attachment; filename=' . "EventLog_" . date("M_j_Y_H:i:s") . ".csv");
			$this->response->setResponse(implode("\n", $fileContent));
			$this->response->template->enabled = false;
		} else {
			$farmNames = array();

			$response = $this->buildResponseFromSql($sql, array("message", "serverid", "source"));

			foreach ($response["data"] as &$row) {
				$row["time"] = date("M j, Y H:i:s", $row["time"]);

				$row["servername"] = $row["serverid"];
				$row["s_severity"] = $severities[$row["severity"]];
				$row["severity"] = (int)$row["severity"];

				if (!$farmNames[$row['farmid']])
					$farmNames[$row['farmid']] = $this->db->GetOne("SELECT name FROM farms WHERE id=?", array($row['farmid']));

				$row['farm_name'] = $farmNames[$row['farmid']];

				$row['message'] = nl2br(htmlspecialchars($row['message']));
			}

			$this->response->setJsonResponse($response);
		}
	}

	public function xGetScriptingLogsAction()
	{
		$this->request->defineParams(array(
			'farmId' => array('type' => 'int'),
			'query' => array('type' => 'string'),
			'sort' => array('type' => 'string', 'default' => 'id'),
			'dir' => array('type' => 'string', 'default' => 'DESC')
		));

		$farms = array();
		foreach ($this->db->getAll('SELECT id FROM farms WHERE clientid = ?', array($this->session->getClientId())) as $value)
			$farms[] = $value['id'];

		$sql = "SELECT id, farmid, event, server_id, dtadded, SUBSTRING(message, 1, 70) AS message FROM scripting_log ";

		if ($this->getParam('farmId'))
			$sql .= "WHERE farmid = '{$this->getParam('farmId')}'";
		else
			$sql .= count($farms) ? "WHERE farmid IN (" . implode(',', $farms) . ")" : "WHERE 0";

		$response = $this->buildResponseFromSql($sql, array("message", "server_id"));
		foreach ($response["data"] as &$row) {
			$row["farm_name"] = $this->db->GetOne("SELECT name FROM farms WHERE id=?", array($row['farmid']));
			$row["dtadded"] = date("M j, Y H:i:s", strtotime($row["dtadded"]." ".SCALR_SERVER_TZ));
			$row['message'] = htmlspecialchars($row['message']);
		}

		$this->response->setJsonResponse($response);
	}

	public function xGetApiLogsAction()
	{
		$this->request->defineParams(array(
			'sort' => array('type' => 'string', 'default' => 'id'),
			'dir' => array('type' => 'string', 'default' => 'DESC')
		));

		$sql = "SELECT * from api_log WHERE id > 0 AND clientid='{$this->session->getClientId()}'";

		$response = $this->buildResponseFromSql($sql, array("transaction_id"));
		foreach ($response["data"] as &$row) {
			$row["dtadded"] = date("M j, Y H:i:s", $row["dtadded"]);
		}

		$this->response->setJsonResponse($response);
	}

	public function scriptingMessageAction()
	{
		$entry = $this->db->GetRow("SELECT * FROM scripting_log WHERE id = ?", array($this->getParam('eventId')));
		if (empty($entry))
			throw new Exception ('Unknown event');

		$farm = DBFarm::LoadByID($entry['farmid']);
		if ($farm->ClientID != $this->session->getClientId())
			throw new Exception('Unknown event');

		$form = array(
			array(
				'xtype' => 'fieldset',
				'title' => 'Message',
				'layout' => 'fit',
				'items' => array(
					array(
						'xtype' => 'textarea',
						'readOnly' => true,
						'hideLabel' => true,
						'height' => 400,
						'value' => $entry['message']
					)
				)
			)
		);

		$this->response->setJsonResponse(array(
			'success' => true,
			'module' => $this->response->template->fetchJs('logs/scriptingmessage.js'),
			'moduleParams' => $form
		));
	}

	public function apiLogEntryDetailsAction()
	{
		$entry = $this->db->GetRow("SELECT * FROM api_log WHERE transaction_id = ? AND clientid = ?", array($this->getParam('transactionId'), $this->session->getClientId()));
		if (empty($entry))
			throw new Exception ('Unknown transaction');

		$entry['dtadded'] = date("M j, Y H:i:s", $entry['dtadded']);

		$form = array(
			array(
				'xtype' => 'fieldset',
				'title' => 'General information',
				'labelWidth' => 120,
				'items' => array(
					array(
						'xtype' => 'displayfield',
						'fieldLabel' => 'Transaction ID',
						'value' => $entry['transaction_id']
					),
					array(
						'xtype' => 'displayfield',
						'fieldLabel' => 'Action',
						'value' => $entry['action']
					),
					array(
						'xtype' => 'displayfield',
						'fieldLabel' => 'IP address',
						'value' => $entry['ipaddress']
					),
					array(
						'xtype' => 'displayfield',
						'fieldLabel' => 'Time',
						'value' => $entry['dtadded']
					)
				)
			),
			array(
				'xtype' => 'fieldset',
				'title' => 'Request',
				'layout' => 'fit',
				'items' => array(
					array(
						'xtype' => 'textarea',
						'readOnly' => true,
						'hideLabel' => true,
						'height' => 100,
						'value' => $entry['request']
					)
				)
			),
			array(
				'xtype' => 'fieldset',
				'title' => 'Response',
				'layout' => 'fit',
				'items' => array(
					array(
						'xtype' => 'textarea',
						'readOnly' => true,
						'hideLabel' => true,
						'height' => 100,
						'value' => $entry['response']
					)
				)
			)
		);

		$this->response->setJsonResponse(array(
			'success' => true,
			'module' => $this->response->template->fetchJs('logs/apilogentrydetails.js'),
			'moduleParams' => $form
		));
	}
}
