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
 * Amazon_SQS_Model_ListQueuesResponse
 * 
 * Properties:
 * <ul>
 * 
 * <li>ListQueuesResult: Amazon_SQS_Model_ListQueuesResult</li>
 * <li>ResponseMetadata: Amazon_SQS_Model_ResponseMetadata</li>
 *
 * </ul>
 */ 
class Amazon_SQS_Model_ListQueuesResponse extends Amazon_SQS_Model
{


    /**
     * Construct new Amazon_SQS_Model_ListQueuesResponse
     * 
     * @param mixed $data DOMElement or Associative Array to construct from. 
     * 
     * Valid properties:
     * <ul>
     * 
     * <li>ListQueuesResult: Amazon_SQS_Model_ListQueuesResult</li>
     * <li>ResponseMetadata: Amazon_SQS_Model_ResponseMetadata</li>
     *
     * </ul>
     */
    public function __construct($data = null)
    {
        $this->_fields = array (
        'ListQueuesResult' => array('FieldValue' => null, 'FieldType' => 'Amazon_SQS_Model_ListQueuesResult'),
        'ResponseMetadata' => array('FieldValue' => null, 'FieldType' => 'Amazon_SQS_Model_ResponseMetadata'),
        );
        parent::__construct($data);
    }

       
    /**
     * Construct Amazon_SQS_Model_ListQueuesResponse from XML string
     * 
     * @param string $xml XML string to construct from
     * @return Amazon_SQS_Model_ListQueuesResponse 
     */
    public static function fromXML($xml)
    {
        $dom = new DOMDocument();
        $dom->loadXML($xml);
        $xpath = new DOMXPath($dom);
    	$xpath->registerNamespace('a', 'http://queue.amazonaws.com/doc/2008-01-01/');
        $response = $xpath->query('//a:ListQueuesResponse');
        if ($response->length == 1) {
            return new Amazon_SQS_Model_ListQueuesResponse(($response->item(0))); 
        } else {
            throw new Exception ("Unable to construct Amazon_SQS_Model_ListQueuesResponse from provided XML. 
                                  Make sure that ListQueuesResponse is a root element");
        }
          
    }
    
    /**
     * Gets the value of the ListQueuesResult.
     * 
     * @return ListQueuesResult ListQueuesResult
     */
    public function getListQueuesResult() 
    {
        return $this->_fields['ListQueuesResult']['FieldValue'];
    }

    /**
     * Sets the value of the ListQueuesResult.
     * 
     * @param ListQueuesResult ListQueuesResult
     * @return void
     */
    public function setListQueuesResult($value) 
    {
        $this->_fields['ListQueuesResult']['FieldValue'] = $value;
        return;
    }

    /**
     * Sets the value of the ListQueuesResult  and returns this instance
     * 
     * @param ListQueuesResult $value ListQueuesResult
     * @return Amazon_SQS_Model_ListQueuesResponse instance
     */
    public function withListQueuesResult($value)
    {
        $this->setListQueuesResult($value);
        return $this;
    }


    /**
     * Checks if ListQueuesResult  is set
     * 
     * @return bool true if ListQueuesResult property is set
     */
    public function isSetListQueuesResult()
    {
        return !is_null($this->_fields['ListQueuesResult']['FieldValue']);

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
     * @return Amazon_SQS_Model_ListQueuesResponse instance
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
        $xml .= "<ListQueuesResponse xmlns=\"http://queue.amazonaws.com/doc/2008-01-01/\">";
        $xml .= $this->_toXMLFragment();
        $xml .= "</ListQueuesResponse>";
        return $xml;
    }

}