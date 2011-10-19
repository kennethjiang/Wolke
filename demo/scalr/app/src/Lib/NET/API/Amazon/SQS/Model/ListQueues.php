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
 * Amazon_SQS_Model_ListQueues
 * 
 * Properties:
 * <ul>
 * 
 * <li>QueueNamePrefix: string</li>
 *
 * </ul>
 */ 
class Amazon_SQS_Model_ListQueues extends Amazon_SQS_Model
{


    /**
     * Construct new Amazon_SQS_Model_ListQueues
     * 
     * @param mixed $data DOMElement or Associative Array to construct from. 
     * 
     * Valid properties:
     * <ul>
     * 
     * <li>QueueNamePrefix: string</li>
     *
     * </ul>
     */
    public function __construct($data = null)
    {
        $this->_fields = array (
        'QueueNamePrefix' => array('FieldValue' => null, 'FieldType' => 'string'),
        );
        parent::__construct($data);
    }

        /**
     * Gets the value of the QueueNamePrefix property.
     * 
     * @return string QueueNamePrefix
     */
    public function getQueueNamePrefix() 
    {
        return $this->_fields['QueueNamePrefix']['FieldValue'];
    }

    /**
     * Sets the value of the QueueNamePrefix property.
     * 
     * @param string QueueNamePrefix
     * @return this instance
     */
    public function setQueueNamePrefix($value) 
    {
        $this->_fields['QueueNamePrefix']['FieldValue'] = $value;
        return $this;
    }

    /**
     * Sets the value of the QueueNamePrefix and returns this instance
     * 
     * @param string $value QueueNamePrefix
     * @return Amazon_SQS_Model_ListQueues instance
     */
    public function withQueueNamePrefix($value)
    {
        $this->setQueueNamePrefix($value);
        return $this;
    }


    /**
     * Checks if QueueNamePrefix is set
     * 
     * @return bool true if QueueNamePrefix  is set
     */
    public function isSetQueueNamePrefix()
    {
        return !is_null($this->_fields['QueueNamePrefix']['FieldValue']);
    }




}