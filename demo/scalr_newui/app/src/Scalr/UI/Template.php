<?php

class Scalr_UI_Template
{
	public
		$enabled = true,
		$name = null;

	protected
		$params = array(),
		$jsonParams = array();

	const RESPONSE_MESSAGES = 'messages';

	public function fetch($name)
	{
		$smarty = Core::GetSmartyInstance();

		if ($name == '' && $this->name != '') {
			$this->assignJson('scalrMessages', $_SESSION[__CLASS__][self::RESPONSE_MESSAGES]);
			$_SESSION[__CLASS__][self::RESPONSE_MESSAGES] = array();
			$name = $this->name;
		}
		$this->assignParam('scalrJsonParams', json_encode($this->jsonParams));
		$smarty->assign($this->params);

		return $smarty->fetch($name);
	}

	public function fetchJs($name)
	{
		return file_get_contents(APPPATH . "/www/js/ui/{$name}");
	}

	public function messageError($message)
	{
		$_SESSION[__CLASS__][self::RESPONSE_MESSAGES][] = array('type' => 'error', 'message' => $message);
	}

	public function messageSuccess($message)
	{
		$_SESSION[__CLASS__][self::RESPONSE_MESSAGES][] = array('type' => 'success', 'message' => $message);
	}

	public function assignTitle($title)
	{
		$this->assignParam('title', $title);
	}

	public function assignParam($name, $value)
	{
		if (is_array($name))
			$this->params = array_merge($this->params, $name);
		else
			$this->params[$name] = $value;
	}

	public function assignJson($name, $value)
	{
		$this->jsonParams[$name] = $value;
	}
}
