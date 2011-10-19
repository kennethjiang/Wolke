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
 * Amazon_SQS_Model_ListQueuesResult
 * 
 * Properties:
 * <ul>
 * 
 * <li>QueueUrl: string</li>
 *
 * </ul>
 */ 
class Amazon_SQS_Model_ListQueuesResult extends Amazon_SQS_Model
{


    /**
     * Construct new Amazon_SQS_Model_ListQueuesResult
     * 
     * @param mixed $data DOMElement or Associative Array to construct from. 
     * 
     * Valid properties:
     * <ul>
     * 
     * <li>QueueUrl: string</li>
     *
     * </ul>
     */
    public function __construct($data = null)
    {
        $this->_fields = array (
        'QueueUrl' => array('FieldValue' => array(), 'FieldType' => array('string')),
        );
        parent::__construct($data);
    }

        /**
     * Gets the value of the QueueUrl .
     * 
     * @return array of string QueueUrl
     */
    public function getQueueUrl() 
    {
        return $this->_fields['QueueUrl']['FieldValue'];
    }

    /**
     * Sets the value of the QueueUrl.
     * 
     * @param string or an array of string QueueUrl
     * @return this instance
     */
    public function setQueueUrl($queueUrl) 
    {
        if (!$this->_isNumericArray($queueUrl)) {
            $queueUrl =  array ($queueUrl);    
        }
        $this->_fields['QueueUrl']['FieldValue'] = $queueUrl;
        return $this;
    }
  

    /**
     * Sets single or multiple values of QueueUrl list via variable number of arguments. 
     * For example, to set the list with two elements, simply pass two values as arguments to this function
     * <code>withQueueUrl($queueUrl1, $queueUrl2)</code>
     * 
     * @param string  $stringArgs one or more QueueUrl
     * @return Amazon_SQS_Model_ListQueuesResult  instance
     */
    public function withQueueUrl($stringArgs)
    {
        foreach (func_get_args() as $queueUrl) {
            $this->_fields['QueueUrl']['FieldValue'][] = $queueUrl;
        }
        return $this;
    }  
      

    /**
     * Checks if QueueUrl list is non-empty
     * 
     * @return bool true if QueueUrl list is non-empty
     */
    public function isSetQueueUrl()
    {
        return count ($this->_fields['QueueUrl']['FieldValue']) > 0;
    }




}