<?php

class Scalr_UI_Response
{
	public
		$body = '',
		$headers = array(),
		$httpResponseCode = 200;

	/**
	 * @var Scalr_UI_Template
	 */
	public $template;

	private static $_instance = null;

	public static function getInstance()
	{
		if (self::$_instance === null)
			self::$_instance = new Scalr_UI_Response();

		return self::$_instance;
	}

	public function __construct()
	{
		$this->template = new Scalr_UI_Template();
	}

	public function pageNotFound()
	{
		throw new Exception('Requested page not found');
	}

	public function pageAccessDenied()
	{
		throw new Exception('Access denied');
	}

	/*
		*Normalizes a header name to X-Capitalized-Names
		*/
	protected function normalizeHeader($name)
	{
		$filtered = str_replace(array('-', '_'), ' ', (string) $name);
		$filtered = ucwords(strtolower($filtered));
		$filtered = str_replace(' ', '-', $filtered);
		return $filtered;
	}

	public function setHeader($name, $value, $replace = false)
	{
		$name = $this->normalizeHeader($name);
		$value = (string) $value;

		if ($replace) {
			foreach ($this->headers as $key => $header) {
				if ($name == $header['name'])
					unset($this->headers[$key]);
			}
		}

		$this->headers[] = array(
			'name' => $name,
			'value' => $value,
			'replace' => $replace
		);
	}

	public function setRedirect($url, $code = 302)
	{
		$this->setHeader('Location', $url, true);
		$this->setHttpResponseCode($code);
		$this->template->enabled = false;
	}

	public function setHttpResponseCode($code)
	{
		$this->httpResponseCode = $code;
	}

	public function setResponse($value)
	{
		$this->body = $value;
	}

	public function setJsonResponse($value, $type = "javascript")
	{
		$this->template->enabled = false;
		$this->setResponse(json_encode($value));

		if ($type == "javascript")
			$this->setHeader('content-type', 'text/javascript', true);
		elseif ($type == "text")
			$this->setHeader('content-type', 'text/html'); // hack for ajax file uploads
	}

	public function setJsonDump($value, $name = 'var')
	{
		$this->setHeader('X-Scalr-Debug', json_encode($value));
	}

	public function sendResponse()
	{
		foreach ($this->headers as $header) {
			header($header['name'] . ': ' . $header['value'], $header['replace']);
		}

		if ($this->template->enabled) {
			$this->body = $this->template->fetch();
		}

		header("HTTP/1.0 {$this->httpResponseCode}");
		echo $this->body;
	}
}
