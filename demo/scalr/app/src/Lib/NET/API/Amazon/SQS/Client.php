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
 *  @see Amazon_SQS_Interface
 */
require_once ('Amazon/SQS/Interface.php'); 

/**
 * 
 * Amazon Simple Queue Service (Amazon SQS) offers a reliable, highly scalable hosted queue for storing messages as they travel between computers. By using Amazon SQS, developers can simply move data between distributed application components performing different tasks, without losing messages or requiring each component to be always available.  Amazon SQS works by exposing Amazon's web-scale messaging infrastructure as a web service. Any computer on the Internet can add or read messages without any installed software or special firewall configurations. Components of applications using Amazon SQS can run independently, and do not need to be on the same network, developed with the same technologies, or running at the same time.
 * 
 * Amazon_SQS_Client is an implementation of Amazon_SQS
 *
 */
class Amazon_SQS_Client implements Amazon_SQS_Interface
{

    const SERVICE_VERSION = '2008-01-01';

    /** @var string */
    private  $_awsAccessKeyId = null;
    
    /** @var string */
    private  $_awsSecretAccessKey = null;
    
    /** @var array */
    private  $_config = array (
    	'ServiceURL' => 'http://queue.amazonaws.com', 
        'UserAgent' => 'Amazon SQS PHP5 Library',
		'SignatureVersion' => 1,
		'ProxyHost' => null,
		'ProxyPort' => -1,
		'MaxErrorRetry' => 3,
		'Debug' => false       
	);
   
    /**
     * Construct new Client
     * 
     * @param string $awsAccessKeyId AWS Access Key ID
     * @param string $awsSecretAccessKey AWS Secret Access Key
     * @param array $config configuration options. 
     * Valid configuration options are:
     * <ul>
     * <li>ServiceURL</li>
     * <li>UserAgent</li>
     * <li>SignatureVersion</li>
     * <li>TimesRetryOnError</li>
     * <li>ProxyHost</li>
     * <li>ProxyPort</li>
     * <li>MaxErrorRetry</li>
     * </ul>
     */
    public function __construct($awsAccessKeyId, $awsSecretAccessKey, $config = null)
    {
        iconv_set_encoding('output_encoding', 'UTF-8');
        iconv_set_encoding('input_encoding', 'UTF-8');
        iconv_set_encoding('internal_encoding', 'UTF-8');

        $this->_awsAccessKeyId = $awsAccessKeyId;
        $this->_awsSecretAccessKey = $awsSecretAccessKey;
        if (!is_null($config)) $this->_config = array_merge($this->_config, $config);
    }

    // Public API ------------------------------------------------------------//


            
    /**
     * Create Queue 
     * 
     * The CreateQueue action creates a new queue. You must provide a queue name that is unique within the scope of the queues you own. The queue is assigned a queue URL; you must use this URL when performing actions on the queue.  When you create a queue, if a queue with the same name already exists, CreateQueue returns the queue URL with an error indicating that the queue already exists.
     *   
     * @see http://docs.amazonwebservices.com/AWSSimpleQueueService/2008-01-01/SQSDeveloperGuide/Query_QueryCreateQueue.html      
     * @param mixed $request array of parameters for Amazon_SQS_Model_CreateQueue request or Amazon_SQS_Model_CreateQueue object itself
     * @see Amazon_SQS_Model_CreateQueue
     * @return Amazon_SQS_Model_CreateQueueResponse Amazon_SQS_Model_CreateQueueResponse
     *
     * @throws Amazon_SQS_Exception
     */
    public function createQueue($request) 
    {
        if (!$request instanceof Amazon_SQS_Model_CreateQueue) {
            require_once ('Amazon/SQS/Model/CreateQueue.php');
            $request = new Amazon_SQS_Model_CreateQueue($request);
        }
        require_once ('Amazon/SQS/Model/CreateQueueResponse.php');
        return Amazon_SQS_Model_CreateQueueResponse::fromXML($this->_invoke($this->_convertCreateQueue($request)));
    }


            
    /**
     * List Queues 
     * 
     * The ListQueues action returns a list of your queues.
     *   
     * @see http://docs.amazonwebservices.com/AWSSimpleQueueService/2008-01-01/SQSDeveloperGuide/Query_QueryListQueues.html      
     * @param mixed $request array of parameters for Amazon_SQS_Model_ListQueues request or Amazon_SQS_Model_ListQueues object itself
     * @see Amazon_SQS_Model_ListQueues
     * @return Amazon_SQS_Model_ListQueuesResponse Amazon_SQS_Model_ListQueuesResponse
     *
     * @throws Amazon_SQS_Exception
     */
    public function listQueues($request) 
    {
        if (!$request instanceof Amazon_SQS_Model_ListQueues) {
            require_once ('Amazon/SQS/Model/ListQueues.php');
            $request = new Amazon_SQS_Model_ListQueues($request);
        }
        require_once ('Amazon/SQS/Model/ListQueuesResponse.php');
        return Amazon_SQS_Model_ListQueuesResponse::fromXML($this->_invoke($this->_convertListQueues($request)));
    }


            
    /**
     * Delete Message 
     * The DeleteMessage action unconditionally removes the specified message from the specified queue. Even if the message is locked by another reader due to the visibility timeout setting, it is still deleted from the queue.
     *   
     * @see http://docs.amazonwebservices.com/AWSSimpleQueueService/2008-01-01/SQSDeveloperGuide/Query_QueryDeleteMessage.html      
     * @param mixed $request array of parameters for Amazon_SQS_Model_DeleteMessage request or Amazon_SQS_Model_DeleteMessage object itself
     * @see Amazon_SQS_Model_DeleteMessage
     * @return Amazon_SQS_Model_DeleteMessageResponse Amazon_SQS_Model_DeleteMessageResponse
     *
     * @throws Amazon_SQS_Exception
     */
    public function deleteMessage($request) 
    {
        if (!$request instanceof Amazon_SQS_Model_DeleteMessage) {
            require_once ('Amazon/SQS/Model/DeleteMessage.php');
            $request = new Amazon_SQS_Model_DeleteMessage($request);
        }
        require_once ('Amazon/SQS/Model/DeleteMessageResponse.php');
        return Amazon_SQS_Model_DeleteMessageResponse::fromXML($this->_invoke($this->_convertDeleteMessage($request)));
    }


            
    /**
     * Delete Queue 
     * 
     * This action unconditionally deletes the queue specified by the queue URL. Use this operation WITH CARE!  The queue is deleted even if it is NOT empty.
     *   
     * @see http://docs.amazonwebservices.com/AWSSimpleQueueService/2008-01-01/SQSDeveloperGuide/Query_QueryDeleteQueue.html      
     * @param mixed $request array of parameters for Amazon_SQS_Model_DeleteQueue request or Amazon_SQS_Model_DeleteQueue object itself
     * @see Amazon_SQS_Model_DeleteQueue
     * @return Amazon_SQS_Model_DeleteQueueResponse Amazon_SQS_Model_DeleteQueueResponse
     *
     * @throws Amazon_SQS_Exception
     */
    public function deleteQueue($request) 
    {
        if (!$request instanceof Amazon_SQS_Model_DeleteQueue) {
            require_once ('Amazon/SQS/Model/DeleteQueue.php');
            $request = new Amazon_SQS_Model_DeleteQueue($request);
        }
        require_once ('Amazon/SQS/Model/DeleteQueueResponse.php');
        return Amazon_SQS_Model_DeleteQueueResponse::fromXML($this->_invoke($this->_convertDeleteQueue($request)));
    }


            
    /**
     * Get Queue Attributes 
     * 
     * Gets one or all attributes of a queue. Queues currently have two attributes you can get: ApproximateNumberOfMessages and VisibilityTimeout.
     *   
     * @see http://docs.amazonwebservices.com/AWSSimpleQueueService/2008-01-01/SQSDeveloperGuide/Query_QueryGetQueueAttributes.html      
     * @param mixed $request array of parameters for Amazon_SQS_Model_GetQueueAttributes request or Amazon_SQS_Model_GetQueueAttributes object itself
     * @see Amazon_SQS_Model_GetQueueAttributes
     * @return Amazon_SQS_Model_GetQueueAttributesResponse Amazon_SQS_Model_GetQueueAttributesResponse
     *
     * @throws Amazon_SQS_Exception
     */
    public function getQueueAttributes($request) 
    {
        if (!$request instanceof Amazon_SQS_Model_GetQueueAttributes) {
            require_once ('Amazon/SQS/Model/GetQueueAttributes.php');
            $request = new Amazon_SQS_Model_GetQueueAttributes($request);
        }
        require_once ('Amazon/SQS/Model/GetQueueAttributesResponse.php');
        return Amazon_SQS_Model_GetQueueAttributesResponse::fromXML($this->_invoke($this->_convertGetQueueAttributes($request)));
    }


            
    /**
     * Receive Message 
     * 
     * Retrieves one or more messages from the specified queue, including the message body and message ID of each message. Messages returned by this action stay in the queue until you delete them. However, once a message is returned to a ReceiveMessage request, it is not returned on subsequent ReceiveMessage requests for the duration of the VisibilityTimeout. If you do not specify a VisibilityTimeout in the request, the overall visibility timeout for the queue is used for the returned messages.
     *   
     * @see http://docs.amazonwebservices.com/AWSSimpleQueueService/2008-01-01/SQSDeveloperGuide/Query_QueryReceiveMessage.html      
     * @param mixed $request array of parameters for Amazon_SQS_Model_ReceiveMessage request or Amazon_SQS_Model_ReceiveMessage object itself
     * @see Amazon_SQS_Model_ReceiveMessage
     * @return Amazon_SQS_Model_ReceiveMessageResponse Amazon_SQS_Model_ReceiveMessageResponse
     *
     * @throws Amazon_SQS_Exception
     */
    public function receiveMessage($request) 
    {
        if (!$request instanceof Amazon_SQS_Model_ReceiveMessage) {
            require_once ('Amazon/SQS/Model/ReceiveMessage.php');
            $request = new Amazon_SQS_Model_ReceiveMessage($request);
        }
        require_once ('Amazon/SQS/Model/ReceiveMessageResponse.php');
        return Amazon_SQS_Model_ReceiveMessageResponse::fromXML($this->_invoke($this->_convertReceiveMessage($request)));
    }


            
    /**
     * Send Message 
     * The SendMessage action delivers a message to the specified queue.
     *   
     * @see http://docs.amazonwebservices.com/AWSSimpleQueueService/2008-01-01/SQSDeveloperGuide/Query_QuerySendMessage.html      
     * @param mixed $request array of parameters for Amazon_SQS_Model_SendMessage request or Amazon_SQS_Model_SendMessage object itself
     * @see Amazon_SQS_Model_SendMessage
     * @return Amazon_SQS_Model_SendMessageResponse Amazon_SQS_Model_SendMessageResponse
     *
     * @throws Amazon_SQS_Exception
     */
    public function sendMessage($request) 
    {
        if (!$request instanceof Amazon_SQS_Model_SendMessage) {
            require_once ('Amazon/SQS/Model/SendMessage.php');
            $request = new Amazon_SQS_Model_SendMessage($request);
        }
        require_once ('Amazon/SQS/Model/SendMessageResponse.php');
        return Amazon_SQS_Model_SendMessageResponse::fromXML($this->_invoke($this->_convertSendMessage($request)));
    }


            
    /**
     * Set Queue Attributes 
     * 
     * Sets an attribute of a queue. Currently, you can set only the VisibilityTimeout attribute for a queue.
     *   
     * @see http://docs.amazonwebservices.com/AWSSimpleQueueService/2008-01-01/SQSDeveloperGuide/Query_QuerySetQueueAttributes.html      
     * @param mixed $request array of parameters for Amazon_SQS_Model_SetQueueAttributes request or Amazon_SQS_Model_SetQueueAttributes object itself
     * @see Amazon_SQS_Model_SetQueueAttributes
     * @return Amazon_SQS_Model_SetQueueAttributesResponse Amazon_SQS_Model_SetQueueAttributesResponse
     *
     * @throws Amazon_SQS_Exception
     */
    public function setQueueAttributes($request) 
    {
        if (!$request instanceof Amazon_SQS_Model_SetQueueAttributes) {
            require_once ('Amazon/SQS/Model/SetQueueAttributes.php');
            $request = new Amazon_SQS_Model_SetQueueAttributes($request);
        }
        require_once ('Amazon/SQS/Model/SetQueueAttributesResponse.php');
        return Amazon_SQS_Model_SetQueueAttributesResponse::fromXML($this->_invoke($this->_convertSetQueueAttributes($request)));
    }

        // Private API ------------------------------------------------------------//

    /**
     * Invoke request and return response
     */
    private function _invoke(array $parameters)
    {
        $actionName = $parameters["Action"];
        $queueName = $parameters["QueueName"];
        $removeQueueNameFromParameters = !is_null($queueName) && "CreateQueue" !== $actionName;
        $queuepath = $removeQueueNameFromParameters ? "/" . $queueName : "";
        if ($removeQueueNameFromParameters) {
             unset($parameters["QueueName"]);
        }

        $response = array();
        $responseBody = null;
        $statusCode = 200;

        /* Submit the request and read response body */
        try {
        
            /* Add required request parameters */
            $parameters = $this->_addRequiredParameters($parameters);

            $shouldRetry = true;
            $retries = 0;
            do {
                try {
                        $response = $this->_httpPost($parameters, $queuepath);
                        if ($response['Status'] === 200) {
                            $shouldRetry = false;
                        } else {
                            if ($response['Status'] === 500 || $response['Status'] === 503) {
                                $shouldRetry = true;
                                $this->_pauseOnRetry(++$retries, $response['Status']);
                            } else {    
                                throw $this->_reportAnyErrors($response['ResponseBody'], $response['Status']);
                            }
                       }     
                /* Rethrow on deserializer error */
                } catch (Exception $e) {
                    require_once ('Amazon/SQS/Exception.php');
                    if ($e instanceof Amazon_SQS_Exception) {
                        throw $e;
                    } else {
                        require_once ('Amazon/SQS/Exception.php');
                        throw new Amazon_SQS_Exception(array('Exception' => $e, 'Message' => $e->getMessage()));   
                    }
                }

            } while ($shouldRetry);

        } catch (Amazon_SQS_Exception $se) {
            throw $se;
        } catch (Exception $t) {
            throw new Amazon_SQS_Exception(array('Exception' => $t, 'Message' => $t->getMessage()));
        }

        return $response['ResponseBody'];
    }

    /**
     * Look for additional error strings in the response and return formatted exception
     */
    private function _reportAnyErrors($responseBody, $status, Exception $e =  null)
    {
        $ex = null;
        if (!is_null($responseBody) && strpos($responseBody, '<') === 0) {
            if (preg_match('@<RequestId>(.*)</RequestId>.*<Error><Code>(.*)</Code><Message>(.*)</Message></Error>.*(<Error>)?@mi',
                $responseBody, $errorMatcherOne)) {
                                
                $requestId = $errorMatcherOne[1];
                $code = $errorMatcherOne[2];
                $message = $errorMatcherOne[3];

                require_once ('Amazon/SQS/Exception.php');
                $ex = new Amazon_SQS_Exception(array ('Message' => $message, 'StatusCode' => $status, 'ErrorCode' => $code, 
                                                           'ErrorType' => 'Unknown', 'RequestId' => $requestId, 'XML' => $responseBody));

            } elseif (preg_match('@<Error><Code>(.*)</Code><Message>(.*)</Message></Error>.*(<Error>)?.*<RequestID>(.*)</RequestID>@mi',
                $responseBody, $errorMatcherTwo)) {
                                
                $code = $errorMatcherTwo[1];  
                $message = $errorMatcherTwo[2];  
                $requestId = $errorMatcherTwo[4];   
                require_once ('Amazon/SQS/Exception.php');
                $ex = new Amazon_SQS_Exception(array ('Message' => $message, 'StatusCode' => $status, 'ErrorCode' => $code, 
                                                              'ErrorType' => 'Unknown', 'RequestId' => $requestId, 'XML' => $responseBody));
            } elseif (preg_match('@<Error><Type>(.*)</Type><Code>(.*)</Code><Message>(.*)</Message>.*</Error>.*(<Error>)?.*<RequestId>(.*)</RequestId>@mi',
                $responseBody, $errorMatcherThree)) {
                
                $type = $errorMatcherTwo[1];
                $code = $errorMatcherTwo[2];  
                $message = $errorMatcherTwo[3];  
                $requestId = $errorMatcherTwo[5];   
                require_once ('Amazon/SQS/Exception.php');
                $ex = new Amazon_SQS_Exception(array ('Message' => $message, 'StatusCode' => $status, 'ErrorCode' => $code, 
                                                              'ErrorType' => $type, 'RequestId' => $requestId, 'XML' => $responseBody));
            
            } else {
                require_once ('Amazon/SQS/Exception.php');
                $ex = new Amazon_SQS_Exception(array('Message' => 'Internal Error', 'StatusCode' => $status));
            }
        } else {
            require_once ('Amazon/SQS/Exception.php');
            $ex = new Amazon_SQS_Exception(array('Message' => 'Internal Error', 'StatusCode' => $status));
        }
        return $ex;
    }



    /**
     * Perform HTTP post with exponential retries on error 500 and 503
     * 
     */
    private function _httpPost(array $parameters, $queuepath) 
    {
        $query = $this->_getParametersAsString($parameters);
        $url = parse_url ($this->_config['ServiceURL']);
        $post  = "POST " . $queuepath . " HTTP/1.0\r\n";
        $post .= "Host: " . $url['host'] . "\r\n";
        $post .= "Content-Type: application/x-www-form-urlencoded; charset=utf-8\r\n";
        $post .= "Content-Length: " . strlen($query) . "\r\n";
        $post .= "User-Agent: " . $this->_config['UserAgent'] . "\r\n";
        $post .= "\r\n";
        $post .= $query;
        
        $port = $url['port'];
        $scheme = '';
        
        switch ($url['scheme']) {
            case 'https':
                $scheme = 'ssl://';
                $port = $port === null ? 443 : $port;
                break;
            default:
                $scheme = '';
                $port = 80;   
        }

        if ($this->_config['Debug']) {
    		print ">> \n" . $post . "\n";
        }
        
        $response = '';
        if ($socket = @fsockopen($scheme . $url['host'], $port, $errno, $errstr, 10)) {
  
            fwrite($socket, $post);

            while (!feof($socket)) {
                $response .= fgets($socket, 1160);
            }
            fclose($socket);
        
            list($other, $responseBody) = explode("\r\n\r\n", $response, 2);
            $other = preg_split("/\r\n|\n|\r/", $other);
            list($protocol, $code, $text) = explode(' ', trim(array_shift($other)), 3);
        } else {
            throw new Exception ("Unable to establish connection to host " . $url['host'] . " $errstr");
        }
        
        if ($this->_config['Debug']) {
        	print "<< \n" . $response . "\n";
        }
        
        return array ('Status' => (int)$code, 'ResponseBody' => $responseBody);
    }

    /**
     * Exponential sleep on failed request
     * @param retries current retry
     * @throws Amazon_SQS_Exception if maximum number of retries has been reached
     */
    private function _pauseOnRetry($retries, $status)
    {
        if ($retries <= $this->_config['MaxErrorRetry']) {
            $delay = (int) (pow(4, $retries) * 100000) ;
            usleep($delay);
        } else {
            require_once ('Amazon/SQS/Exception.php');
            throw new Amazon_SQS_Exception (array ('Message' => "Maximum number of retry attempts reached :  $retries", 'StatusCode' => $status));
        }
    }

    /**
     * Add authentication related and version parameters
     */
    private function _addRequiredParameters(array $parameters)
    {
        $parameters['AWSAccessKeyId'] = $this->_awsAccessKeyId;
        $parameters['Timestamp'] = $this->_getFormattedTimestamp();
        $parameters['Version'] = self::SERVICE_VERSION;      
        $parameters['SignatureVersion'] = $this->_config['SignatureVersion']; 
        $parameters['Signature'] = $this->_signParameters($parameters, $this->_awsSecretAccessKey); 
        
        return $parameters;
    }

    /**
     * Convert paremeters to Url encoded query string
     */
    private function _getParametersAsString(array $parameters)
    {
        $queryParameters = array();
        foreach ($parameters as $key => $value) {
            $queryParameters[] = $key . '=' . urlencode($value);
        }
        return implode('&', $queryParameters);
    }  


    /**
      * Computes RFC 2104-compliant HMAC signature for request parameters
      * Implements AWS Signature, as per following spec:
      *
      * If Signature Version is 0, it signs concatenated Action and Timestamp
      *
      * If Signature Version is 1, it performs the following:
      *
      * Sorts all  parameters (including SignatureVersion and excluding Signature,
      * the value of which is being created), ignoring case.
      *
      * Iterate over the sorted list and append the parameter name (in original case)
      * and then its value. It will not URL-encode the parameter values before
      * constructing this string. There are no separators.
      */
    private function _signParameters(array $parameters, $key)
    {
        $signatureVersion = $parameters['SignatureVersion'];
        $data = '';

        if (0 === $signatureVersion) {
            $data .=  $parameters['Action'] .  $parameters['Timestamp'];
        } elseif (1 === $signatureVersion) {
            uksort($parameters, 'strcasecmp');
            unset ($parameters['Signature']);
                
            foreach ($parameters as $parameterName => $parameterValue) {
                $data .= $parameterName . $parameterValue;
            }
        } else {
            throw new Exception("Invalid Signature Version specified");
        }
        return $this->_sign($data, $key);
    }


    /**
     * Computes RFC 2104-compliant HMAC signature.
     */
    private function _sign($data, $key)
    {
        return base64_encode (
            pack("H*", sha1((str_pad($key, 64, chr(0x00))
            ^(str_repeat(chr(0x5c), 64))) .
            pack("H*", sha1((str_pad($key, 64, chr(0x00))
            ^(str_repeat(chr(0x36), 64))) . $data))))
        );
    }


    /**
     * Formats date as ISO 8601 timestamp
     */
    private function _getFormattedTimestamp()
    {
        return gmdate("Y-m-d\TH:i:s.\\0\\0\\0\\Z", time());
    }


        
    /**
     * Convert CreateQueue to name value pairs
     */
    private function _convertCreateQueue($request) {
        
        $parameters = array();
        $parameters['Action'] = 'CreateQueue';
        if ($request->isSetQueueName()) {
            $parameters['QueueName'] =  $request->getQueueName();
        }
        if ($request->isSetDefaultVisibilityTimeout()) {
            $parameters['DefaultVisibilityTimeout'] =  $request->getDefaultVisibilityTimeout();
        }

        return $parameters;
    }
        
                        
    /**
     * Convert ListQueues to name value pairs
     */
    private function _convertListQueues($request) {
        
        $parameters = array();
        $parameters['Action'] = 'ListQueues';
        if ($request->isSetQueueNamePrefix()) {
            $parameters['QueueNamePrefix'] =  $request->getQueueNamePrefix();
        }

        return $parameters;
    }
        
                        
    /**
     * Convert DeleteMessage to name value pairs
     */
    private function _convertDeleteMessage($request) {
        
        $parameters = array();
        $parameters['Action'] = 'DeleteMessage';
        if ($request->isSetQueueName()) {
            $parameters['QueueName'] =  $request->getQueueName();
        }
        if ($request->isSetReceiptHandle()) {
            $parameters['ReceiptHandle'] =  $request->getReceiptHandle();
        }

        return $parameters;
    }
        
                        
    /**
     * Convert DeleteQueue to name value pairs
     */
    private function _convertDeleteQueue($request) {
        
        $parameters = array();
        $parameters['Action'] = 'DeleteQueue';
        if ($request->isSetQueueName()) {
            $parameters['QueueName'] =  $request->getQueueName();
        }

        return $parameters;
    }
        
                        
    /**
     * Convert GetQueueAttributes to name value pairs
     */
    private function _convertGetQueueAttributes($request) {
        
        $parameters = array();
        $parameters['Action'] = 'GetQueueAttributes';
        if ($request->isSetQueueName()) {
            $parameters['QueueName'] =  $request->getQueueName();
        }
        foreach  ($request->getAttributeName() as $attributeNameIndex => $attributeName) {
            $parameters["GetQueueAttributes" . "." . "AttributeName" . "."  . ($attributeNameIndex + 1)] =  $attributeName;
        }	

        return $parameters;
    }
        
                        
    /**
     * Convert ReceiveMessage to name value pairs
     */
    private function _convertReceiveMessage($request) {
        
        $parameters = array();
        $parameters['Action'] = 'ReceiveMessage';
        if ($request->isSetQueueName()) {
            $parameters['QueueName'] =  $request->getQueueName();
        }
        if ($request->isSetMaxNumberOfMessages()) {
            $parameters['MaxNumberOfMessages'] =  $request->getMaxNumberOfMessages();
        }
        if ($request->isSetVisibilityTimeout()) {
            $parameters['VisibilityTimeout'] =  $request->getVisibilityTimeout();
        }

        return $parameters;
    }
        
                        
    /**
     * Convert SendMessage to name value pairs
     */
    private function _convertSendMessage($request) {
        
        $parameters = array();
        $parameters['Action'] = 'SendMessage';
        if ($request->isSetQueueName()) {
            $parameters['QueueName'] =  $request->getQueueName();
        }
        if ($request->isSetMessageBody()) {
            $parameters['MessageBody'] =  $request->getMessageBody();
        }

        return $parameters;
    }
        
                        
    /**
     * Convert SetQueueAttributes to name value pairs
     */
    private function _convertSetQueueAttributes($request) {
        
        $parameters = array();
        $parameters['Action'] = 'SetQueueAttributes';
        if ($request->isSetQueueName()) {
            $parameters['QueueName'] =  $request->getQueueName();
        }
        foreach ($request->getAttribute() as $attributeIndex => $attribute) {
            if ($attribute->isSetName()) {
                $parameters['Attribute' . '.'  . ($attributeIndex + 1) . '.' . 'Name'] =  $attribute->getName();
            }
            if ($attribute->isSetValue()) {
                $parameters['Attribute' . '.'  . ($attributeIndex + 1) . '.' . 'Value'] =  $attribute->getValue();
            }

        }

        return $parameters;
    }
        
                                                                                                

}