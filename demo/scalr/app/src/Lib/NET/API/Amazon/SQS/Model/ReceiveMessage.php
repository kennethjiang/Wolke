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
 * Amazon_SQS_Model_ReceiveMessage
 * 
 * Properties:
 * <ul>
 * 
 * <li>QueueName: string</li>
 * <li>MaxNumberOfMessages: int</li>
 * <li>VisibilityTimeout: int</li>
 *
 * </ul>
 */ 
class Amazon_SQS_Model_ReceiveMessage extends Amazon_SQS_Model
{


    /**
     * Construct new Amazon_SQS_Model_ReceiveMessage
     * 
     * @param mixed $data DOMElement or Associative Array to construct from. 
     * 
     * Valid properties:
     * <ul>
     * 
     * <li>QueueName: string</li>
     * <li>MaxNumberOfMessages: int</li>
     * <li>VisibilityTimeout: int</li>
     *
     * </ul>
     */
    public function __construct($data = null)
    {
        $this->_fields = array (
        'QueueName' => array('FieldValue' => null, 'FieldType' => 'string'),
        'MaxNumberOfMessages' => array('FieldValue' => null, 'FieldType' => 'int'),
        'VisibilityTimeout' => array('FieldValue' => null, 'FieldType' => 'int'),
        );
        parent::__construct($data);
    }

        /**
     * Gets the value of the QueueName property.
     * 
     * @return string QueueName
     */
    public function getQueueName() 
    {
        return $this->_fields['QueueName']['FieldValue'];
    }

    /**
     * Sets the value of the QueueName property.
     * 
     * @param string QueueName
     * @return this instance
     */
    public function setQueueName($value) 
    {
        $this->_fields['QueueName']['FieldValue'] = $value;
        return $this;
    }

    /**
     * Sets the value of the QueueName and returns this instance
     * 
     * @param string $value QueueName
     * @return Amazon_SQS_Model_ReceiveMessage instance
     */
    public function withQueueName($value)
    {
        $this->setQueueName($value);
        return $this;
    }


    /**
     * Checks if QueueName is set
     * 
     * @return bool true if QueueName  is set
     */
    public function isSetQueueName()
    {
        return !is_null($this->_fields['QueueName']['FieldValue']);
    }

    /**
     * Gets the value of the MaxNumberOfMessages property.
     * 
     * @return int MaxNumberOfMessages
     */
    public function getMaxNumberOfMessages() 
    {
        return $this->_fields['MaxNumberOfMessages']['FieldValue'];
    }

    /**
     * Sets the value of the MaxNumberOfMessages property.
     * 
     * @param int MaxNumberOfMessages
     * @return this instance
     */
    public function setMaxNumberOfMessages($value) 
    {
        $this->_fields['MaxNumberOfMessages']['FieldValue'] = $value;
        return $this;
    }

    /**
     * Sets the value of the MaxNumberOfMessages and returns this instance
     * 
     * @param int $value MaxNumberOfMessages
     * @return Amazon_SQS_Model_ReceiveMessage instance
     */
    public function withMaxNumberOfMessages($value)
    {
        $this->setMaxNumberOfMessages($value);
        return $this;
    }


    /**
     * Checks if MaxNumberOfMessages is set
     * 
     * @return bool true if MaxNumberOfMessages  is set
     */
    public function isSetMaxNumberOfMessages()
    {
        return !is_null($this->_fields['MaxNumberOfMessages']['FieldValue']);
    }

    /**
     * Gets the value of the VisibilityTimeout property.
     * 
     * @return int VisibilityTimeout
     */
    public function getVisibilityTimeout() 
    {
        return $this->_fields['VisibilityTimeout']['FieldValue'];
    }

    /**
     * Sets the value of the VisibilityTimeout property.
     * 
     * @param int VisibilityTimeout
     * @return this instance
     */
    public function setVisibilityTimeout($value) 
    {
        $this->_fields['VisibilityTimeout']['FieldValue'] = $value;
        return $this;
    }

    /**
     * Sets the value of the VisibilityTimeout and returns this instance
     * 
     * @param int $value VisibilityTimeout
     * @return Amazon_SQS_Model_ReceiveMessage instance
     */
    public function withVisibilityTimeout($value)
    {
        $this->setVisibilityTimeout($value);
        return $this;
    }


    /**
     * Checks if VisibilityTimeout is set
     * 
     * @return bool true if VisibilityTimeout  is set
     */
    public function isSetVisibilityTimeout()
    {
        return !is_null($this->_fields['VisibilityTimeout']['FieldValue']);
    }




}