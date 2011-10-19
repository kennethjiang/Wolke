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
 * Amazon_SQS_Model_Message
 * 
 * Properties:
 * <ul>
 * 
 * <li>MessageId: string</li>
 * <li>ReceiptHandle: string</li>
 * <li>MD5OfBody: string</li>
 * <li>Body: string</li>
 *
 * </ul>
 */ 
class Amazon_SQS_Model_Message extends Amazon_SQS_Model
{


    /**
     * Construct new Amazon_SQS_Model_Message
     * 
     * @param mixed $data DOMElement or Associative Array to construct from. 
     * 
     * Valid properties:
     * <ul>
     * 
     * <li>MessageId: string</li>
     * <li>ReceiptHandle: string</li>
     * <li>MD5OfBody: string</li>
     * <li>Body: string</li>
     *
     * </ul>
     */
    public function __construct($data = null)
    {
        $this->_fields = array (
        'MessageId' => array('FieldValue' => null, 'FieldType' => 'string'),
        'ReceiptHandle' => array('FieldValue' => null, 'FieldType' => 'string'),
        'MD5OfBody' => array('FieldValue' => null, 'FieldType' => 'string'),
        'Body' => array('FieldValue' => null, 'FieldType' => 'string'),
        );
        parent::__construct($data);
    }

        /**
     * Gets the value of the MessageId property.
     * 
     * @return string MessageId
     */
    public function getMessageId() 
    {
        return $this->_fields['MessageId']['FieldValue'];
    }

    /**
     * Sets the value of the MessageId property.
     * 
     * @param string MessageId
     * @return this instance
     */
    public function setMessageId($value) 
    {
        $this->_fields['MessageId']['FieldValue'] = $value;
        return $this;
    }

    /**
     * Sets the value of the MessageId and returns this instance
     * 
     * @param string $value MessageId
     * @return Amazon_SQS_Model_Message instance
     */
    public function withMessageId($value)
    {
        $this->setMessageId($value);
        return $this;
    }


    /**
     * Checks if MessageId is set
     * 
     * @return bool true if MessageId  is set
     */
    public function isSetMessageId()
    {
        return !is_null($this->_fields['MessageId']['FieldValue']);
    }

    /**
     * Gets the value of the ReceiptHandle property.
     * 
     * @return string ReceiptHandle
     */
    public function getReceiptHandle() 
    {
        return $this->_fields['ReceiptHandle']['FieldValue'];
    }

    /**
     * Sets the value of the ReceiptHandle property.
     * 
     * @param string ReceiptHandle
     * @return this instance
     */
    public function setReceiptHandle($value) 
    {
        $this->_fields['ReceiptHandle']['FieldValue'] = $value;
        return $this;
    }

    /**
     * Sets the value of the ReceiptHandle and returns this instance
     * 
     * @param string $value ReceiptHandle
     * @return Amazon_SQS_Model_Message instance
     */
    public function withReceiptHandle($value)
    {
        $this->setReceiptHandle($value);
        return $this;
    }


    /**
     * Checks if ReceiptHandle is set
     * 
     * @return bool true if ReceiptHandle  is set
     */
    public function isSetReceiptHandle()
    {
        return !is_null($this->_fields['ReceiptHandle']['FieldValue']);
    }

    /**
     * Gets the value of the MD5OfBody property.
     * 
     * @return string MD5OfBody
     */
    public function getMD5OfBody() 
    {
        return $this->_fields['MD5OfBody']['FieldValue'];
    }

    /**
     * Sets the value of the MD5OfBody property.
     * 
     * @param string MD5OfBody
     * @return this instance
     */
    public function setMD5OfBody($value) 
    {
        $this->_fields['MD5OfBody']['FieldValue'] = $value;
        return $this;
    }

    /**
     * Sets the value of the MD5OfBody and returns this instance
     * 
     * @param string $value MD5OfBody
     * @return Amazon_SQS_Model_Message instance
     */
    public function withMD5OfBody($value)
    {
        $this->setMD5OfBody($value);
        return $this;
    }


    /**
     * Checks if MD5OfBody is set
     * 
     * @return bool true if MD5OfBody  is set
     */
    public function isSetMD5OfBody()
    {
        return !is_null($this->_fields['MD5OfBody']['FieldValue']);
    }

    /**
     * Gets the value of the Body property.
     * 
     * @return string Body
     */
    public function getBody() 
    {
        return $this->_fields['Body']['FieldValue'];
    }

    /**
     * Sets the value of the Body property.
     * 
     * @param string Body
     * @return this instance
     */
    public function setBody($value) 
    {
        $this->_fields['Body']['FieldValue'] = $value;
        return $this;
    }

    /**
     * Sets the value of the Body and returns this instance
     * 
     * @param string $value Body
     * @return Amazon_SQS_Model_Message instance
     */
    public function withBody($value)
    {
        $this->setBody($value);
        return $this;
    }


    /**
     * Checks if Body is set
     * 
     * @return bool true if Body  is set
     */
    public function isSetBody()
    {
        return !is_null($this->_fields['Body']['FieldValue']);
    }




}