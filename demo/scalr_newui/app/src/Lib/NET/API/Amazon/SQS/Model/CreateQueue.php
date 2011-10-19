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
 * Amazon_SQS_Model_CreateQueue
 * 
 * Properties:
 * <ul>
 * 
 * <li>QueueName: string</li>
 * <li>DefaultVisibilityTimeout: int</li>
 *
 * </ul>
 */ 
class Amazon_SQS_Model_CreateQueue extends Amazon_SQS_Model
{


    /**
     * Construct new Amazon_SQS_Model_CreateQueue
     * 
     * @param mixed $data DOMElement or Associative Array to construct from. 
     * 
     * Valid properties:
     * <ul>
     * 
     * <li>QueueName: string</li>
     * <li>DefaultVisibilityTimeout: int</li>
     *
     * </ul>
     */
    public function __construct($data = null)
    {
        $this->_fields = array (
        'QueueName' => array('FieldValue' => null, 'FieldType' => 'string'),
        'DefaultVisibilityTimeout' => array('FieldValue' => null, 'FieldType' => 'int'),
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
     * @return Amazon_SQS_Model_CreateQueue instance
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
     * Gets the value of the DefaultVisibilityTimeout property.
     * 
     * @return int DefaultVisibilityTimeout
     */
    public function getDefaultVisibilityTimeout() 
    {
        return $this->_fields['DefaultVisibilityTimeout']['FieldValue'];
    }

    /**
     * Sets the value of the DefaultVisibilityTimeout property.
     * 
     * @param int DefaultVisibilityTimeout
     * @return this instance
     */
    public function setDefaultVisibilityTimeout($value) 
    {
        $this->_fields['DefaultVisibilityTimeout']['FieldValue'] = $value;
        return $this;
    }

    /**
     * Sets the value of the DefaultVisibilityTimeout and returns this instance
     * 
     * @param int $value DefaultVisibilityTimeout
     * @return Amazon_SQS_Model_CreateQueue instance
     */
    public function withDefaultVisibilityTimeout($value)
    {
        $this->setDefaultVisibilityTimeout($value);
        return $this;
    }


    /**
     * Checks if DefaultVisibilityTimeout is set
     * 
     * @return bool true if DefaultVisibilityTimeout  is set
     */
    public function isSetDefaultVisibilityTimeout()
    {
        return !is_null($this->_fields['DefaultVisibilityTimeout']['FieldValue']);
    }




}