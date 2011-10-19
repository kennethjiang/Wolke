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
 * Amazon_SQS_Model_SendMessageResult
 * 
 * Properties:
 * <ul>
 * 
 * <li>MessageId: string</li>
 * <li>MD5OfMessageBody: string</li>
 *
 * </ul>
 */ 
class Amazon_SQS_Model_SendMessageResult extends Amazon_SQS_Model
{


    /**
     * Construct new Amazon_SQS_Model_SendMessageResult
     * 
     * @param mixed $data DOMElement or Associative Array to construct from. 
     * 
     * Valid properties:
     * <ul>
     * 
     * <li>MessageId: string</li>
     * <li>MD5OfMessageBody: string</li>
     *
     * </ul>
     */
    public function __construct($data = null)
    {
        $this->_fields = array (
        'MessageId' => array('FieldValue' => null, 'FieldType' => 'string'),
        'MD5OfMessageBody' => array('FieldValue' => null, 'FieldType' => 'string'),
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
     * @return Amazon_SQS_Model_SendMessageResult instance
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
     * Gets the value of the MD5OfMessageBody property.
     * 
     * @return string MD5OfMessageBody
     */
    public function getMD5OfMessageBody() 
    {
        return $this->_fields['MD5OfMessageBody']['FieldValue'];
    }

    /**
     * Sets the value of the MD5OfMessageBody property.
     * 
     * @param string MD5OfMessageBody
     * @return this instance
     */
    public function setMD5OfMessageBody($value) 
    {
        $this->_fields['MD5OfMessageBody']['FieldValue'] = $value;
        return $this;
    }

    /**
     * Sets the value of the MD5OfMessageBody and returns this instance
     * 
     * @param string $value MD5OfMessageBody
     * @return Amazon_SQS_Model_SendMessageResult instance
     */
    public function withMD5OfMessageBody($value)
    {
        $this->setMD5OfMessageBody($value);
        return $this;
    }


    /**
     * Checks if MD5OfMessageBody is set
     * 
     * @return bool true if MD5OfMessageBody  is set
     */
    public function isSetMD5OfMessageBody()
    {
        return !is_null($this->_fields['MD5OfMessageBody']['FieldValue']);
    }




}