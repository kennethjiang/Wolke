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
 *  @see Amazon_SQS_Model
 */
require_once ('Amazon/SQS/Model.php');  

    

/**
 * Amazon_SQS_Model_SendMessageResponse
 * 
 * Properties:
 * <ul>
 * 
 * <li>SendMessageResult: Amazon_SQS_Model_SendMessageResult</li>
 * <li>ResponseMetadata: Amazon_SQS_Model_ResponseMetadata</li>
 *
 * </ul>
 */ 
class Amazon_SQS_Model_SendMessageResponse extends Amazon_SQS_Model
{


    /**
     * Construct new Amazon_SQS_Model_SendMessageResponse
     * 
     * @param mixed $data DOMElement or Associative Array to construct from. 
     * 
     * Valid properties:
     * <ul>
     * 
     * <li>SendMessageResult: Amazon_SQS_Model_SendMessageResult</li>
     * <li>ResponseMetadata: Amazon_SQS_Model_ResponseMetadata</li>
     *
     * </ul>
     */
    public function __construct($data = null)
    {
        $this->_fields = array (
        'SendMessageResult' => array('FieldValue' => null, 'FieldType' => 'Amazon_SQS_Model_SendMessageResult'),
        'ResponseMetadata' => array('FieldValue' => null, 'FieldType' => 'Amazon_SQS_Model_ResponseMetadata'),
        );
        parent::__construct($data);
    }

       
    /**
     * Construct Amazon_SQS_Model_SendMessageResponse from XML string
     * 
     * @param string $xml XML string to construct from
     * @return Amazon_SQS_Model_SendMessageResponse 
     */
    public static function fromXML($xml)
    {
        $dom = new DOMDocument();
        $dom->loadXML($xml);
        $xpath = new DOMXPath($dom);
    	$xpath->registerNamespace('a', 'http://queue.amazonaws.com/doc/2008-01-01/');
        $response = $xpath->query('//a:SendMessageResponse');
        if ($response->length == 1) {
            return new Amazon_SQS_Model_SendMessageResponse(($response->item(0))); 
        } else {
            throw new Exception ("Unable to construct Amazon_SQS_Model_SendMessageResponse from provided XML. 
                                  Make sure that SendMessageResponse is a root element");
        }
          
    }
    
    /**
     * Gets the value of the SendMessageResult.
     * 
     * @return SendMessageResult SendMessageResult
     */
    public function getSendMessageResult() 
    {
        return $this->_fields['SendMessageResult']['FieldValue'];
    }

    /**
     * Sets the value of the SendMessageResult.
     * 
     * @param SendMessageResult SendMessageResult
     * @return void
     */
    public function setSendMessageResult($value) 
    {
        $this->_fields['SendMessageResult']['FieldValue'] = $value;
        return;
    }

    /**
     * Sets the value of the SendMessageResult  and returns this instance
     * 
     * @param SendMessageResult $value SendMessageResult
     * @return Amazon_SQS_Model_SendMessageResponse instance
     */
    public function withSendMessageResult($value)
    {
        $this->setSendMessageResult($value);
        return $this;
    }


    /**
     * Checks if SendMessageResult  is set
     * 
     * @return bool true if SendMessageResult property is set
     */
    public function isSetSendMessageResult()
    {
        return !is_null($this->_fields['SendMessageResult']['FieldValue']);

    }

    /**
     * Gets the value of the ResponseMetadata.
     * 
     * @return ResponseMetadata ResponseMetadata
     */
    public function getResponseMetadata() 
    {
        return $this->_fields['ResponseMetadata']['FieldValue'];
    }

    /**
     * Sets the value of the ResponseMetadata.
     * 
     * @param ResponseMetadata ResponseMetadata
     * @return void
     */
    public function setResponseMetadata($value) 
    {
        $this->_fields['ResponseMetadata']['FieldValue'] = $value;
        return;
    }

    /**
     * Sets the value of the ResponseMetadata  and returns this instance
     * 
     * @param ResponseMetadata $value ResponseMetadata
     * @return Amazon_SQS_Model_SendMessageResponse instance
     */
    public function withResponseMetadata($value)
    {
        $this->setResponseMetadata($value);
        return $this;
    }


    /**
     * Checks if ResponseMetadata  is set
     * 
     * @return bool true if ResponseMetadata property is set
     */
    public function isSetResponseMetadata()
    {
        return !is_null($this->_fields['ResponseMetadata']['FieldValue']);

    }



    /**
     * XML Representation for this object
     * 
     * @return string XML for this object
     */
    public function toXML() 
    {
        $xml = "";
        $xml .= "<SendMessageResponse xmlns=\"http://queue.amazonaws.com/doc/2008-01-01/\">";
        $xml .= $this->_toXMLFragment();
        $xml .= "</SendMessageResponse>";
        return $xml;
    }

}