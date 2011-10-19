<?php

class Scalr_Messaging_Msg_HostInitResponse extends Scalr_Messaging_Msg {
	public $farmCryptoKey;
		
	function __construct ($farmCryptoKey) {
		parent::__construct();
		$this->farmCryptoKey = $farmCryptoKey;
	}
}