<?php

class Scalr_UI_Controller_AwsIam extends Scalr_UI_Controller
{
	
	public function hasAccess()
	{
		return $this->session->getAuthToken()->hasAccess(Scalr_AuthToken::ACCOUNT_USER);
	}
	
	public function serverCertificatesAddAction()
	{
		$this->response->setJsonResponse(array(
			'success' => true,
			'module' => $this->response->template->fetchJs('awsiam/server_certificates_add.js')
		));
	}
	
	public function serverCertificatesListAction()
	{
		$this->response->setJsonResponse(array(
			'success' => true,
			'module' => $this->response->template->fetchJs('awsiam/server_certificates_view.js')
		));	
	}
	
	public function serverCertificatesSaveAction()
	{
		$this->request->defineParams(array(
			'name' => array('type' => 'string')
		));
		
		try {	
			
			$iamClient = Scalr_Service_Cloud_Aws::newIam(
				Scalr_Session::getInstance()->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Ec2::ACCESS_KEY),
				Scalr_Session::getInstance()->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Ec2::SECRET_KEY)
			);
			
			$iamClient->uploadServerCertificate(
				@file_get_contents($_FILES['certificate']['tmp_name']),
				@file_get_contents($_FILES['privateKey']['tmp_name']),
				$this->getParam('name'),
				($_FILES['certificateChain']['tmp_name']) ? @file_get_contents($_FILES['certificateChain']['tmp_name']) : null 
			);
			
			$this->response->setJsonResponse(array('success' => true), 'text');
		} catch (Exception $e) {
			$this->response->setJsonResponse(array('success' => false, 'error' => $e->getMessage()), 'text');
		}
	} 
	
	/**
	 * Get list of roles for listView
	 */
	public function xListViewServerCertificatesAction()
	{
		$this->request->defineParams(array(
			'id' => array('type' => 'int'),
			'start' => array('type' => 'int'),
			'limit' => array('type' => 'int')
		));

		try {
			
			$response["data"] = array();
			$response["start"] = $start;

			$iamClient = Scalr_Service_Cloud_Aws::newIam(
				Scalr_Session::getInstance()->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Ec2::ACCESS_KEY),
				Scalr_Session::getInstance()->getEnvironment()->getPlatformConfigValue(Modules_Platforms_Ec2::SECRET_KEY)
			);
			
			$certs = $iamClient->listServerCertificates();
			
			foreach ($certs->ServerCertificateMetadataList as $item) {
				
				$response["data"][] = array(
					'id'			=> $item->ServerCertificateId,
					'name'			=> $item->ServerCertificateName,
					'path'			=> $item->Path,
					'arn'			=> $item->Arn,
					'upload_date'	=> $item->UploadDate
				);
			}

			$this->response->setJsonResponse($response);
		} catch (Exception $e) {
			$this->response->setJsonResponse(array('success' => false, 'error' => $e->getMessage()));
		}
	}
}
