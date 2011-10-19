<?php
/** 
 *  PHP Version 5
 *
 *  @category    Amazon
 *  @package     Amazon_SQS
 *  @copyright   Copyright 2007 Amazon Technologies, Inc.
 *  @link        http://aws.amazon.com
 *  @license     http://aws.amazon.com/apache2.0  Apache License, Version 2.0
 *  @version     2008-01-01
 */
/******************************************************************************* 
 *    __  _    _  ___ 
 *   (  )( \/\/ )/ __)
 *   /__\ \    / \__ \
 *  (_)(_) \/\/  (___/
 * 
 *  Amazon SQS PHP5 Library
 *  Generated: Wed Mar 19 15:10:06 PDT 2008
 * 
 */

/**
 * Send Message  Sample
 */

include_once ('.config.inc.php'); 

/************************************************************************
 * Instantiate Implementation of Amazon SQS
 * 
 * AWS_ACCESS_KEY_ID and AWS_SECRET_ACCESS_KEY constants 
 * are defined in the .config.inc.php located in the same 
 * directory as this sample
 ***********************************************************************/
 $service = new Amazon_SQS_Client(AWS_ACCESS_KEY_ID, 
                                       AWS_SECRET_ACCESS_KEY);
 
/************************************************************************
 * Uncomment to try out Mock Service that simulates Amazon_SQS
 * responses without calling Amazon_SQS service.
 *
 * Responses are loaded from local XML files. You can tweak XML files to
 * experiment with various outputs during development
 *
 * XML files available under Amazon/SQS/Mock tree
 *
 ***********************************************************************/
 // $service = new Amazon_SQS_Mock();

/************************************************************************
 * Setup request parameters and uncomment invoke to try out 
 * sample for Send Message Action
 ***********************************************************************/
 // @TODO: set request. Action can be passed as Amazon_SQS_Model_SendMessage 
 // object or array of parameters
 // invokeSendMessage($service, $request);

                                                
/**
  * Send Message Action Sample
  * The SendMessage action delivers a message to the specified queue.
  *   
  * @param Amazon_SQS_Interface $service instance of Amazon_SQS_Interface
  * @param mixed $request Amazon_SQS_Model_SendMessage or array of parameters
  */
  function invokeSendMessage(Amazon_SQS_Interface $service, $request) 
  {
      try {
              $response = $service->sendMessage($request);
              
                echo ("Service Response\n");
                echo ("=============================================================================\n");

                echo("        SendMessageResponse\n");
                if ($response->isSetSendMessageResult()) { 
                    echo("            SendMessageResult\n");
                    $sendMessageResult = $response->getSendMessageResult();
                    if ($sendMessageResult->isSetMessageId()) 
                    {
                        echo("                MessageId\n");
                        echo("                    " . $sendMessageResult->getMessageId() . "\n");
                    }
                    if ($sendMessageResult->isSetMD5OfMessageBody()) 
                    {
                        echo("                MD5OfMessageBody\n");
                        echo("                    " . $sendMessageResult->getMD5OfMessageBody() . "\n");
                    }
                } 
                if ($response->isSetResponseMetadata()) { 
                    echo("            ResponseMetadata\n");
                    $responseMetadata = $response->getResponseMetadata();
                    if ($responseMetadata->isSetRequestId()) 
                    {
                        echo("                RequestId\n");
                        echo("                    " . $responseMetadata->getRequestId() . "\n");
                    }
                } 

     } catch (Amazon_SQS_Exception $ex) {
         echo("Caught Exception: " . $ex->getMessage() . "\n");
         echo("Response Status Code: " . $ex->getStatusCode() . "\n");
         echo("Error Code: " . $ex->getErrorCode() . "\n");
         echo("Error Type: " . $ex->getErrorType() . "\n");
         echo("Request ID: " . $ex->getRequestId() . "\n");
         echo("XML: " . $ex->getXML() . "\n");
     }
 }
        