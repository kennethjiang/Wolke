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
 * Amazon_SQS_Model_GetQueueAttributes
 * 
 * Properties:
 * <ul>
 * 
 * <li>QueueName: string</li>
 * <li>AttributeName: string</li>
 *
 * </ul>
 */ 
class Amazon_SQS_Model_GetQueueAttributes extends Amazon_SQS_Model
{


    /**
     * Construct new Amazon_SQS_Model_GetQueueAttributes
     * 
     * @param mixed $data DOMElement or Associative Array to construct from. 
     * 
     * Valid properties:
     * <ul>
     * 
     * <li>QueueName: string</li>
     * <li>AttributeName: string</li>
     *
     * </ul>
     */
    public function __construct($data = null)
    {
        $this->_fields = array (
        'QueueName' => array('FieldValue' => null, 'FieldType' => 'string'),
        'AttributeName' => array('FieldValue' => array(), 'FieldType' => array('string')),
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
     * @return Amazon_SQS_Model_GetQueueAttributes instance
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
     * Gets the value of the AttributeName .
     * 
     * @return array of string AttributeName
     */
    public function getAttributeName() 
    {
        return $this->_fields['AttributeName']['FieldValue'];
    }

    /**
     * Sets the value of the AttributeName.
     * 
     * @param string or an array of string AttributeName
     * @return this instance
     */
    public function setAttributeName($attributeName) 
    {
        if (!$this->_isNumericArray($attributeName)) {
            $attributeName =  array ($attributeName);    
        }
        $this->_fields['AttributeName']['FieldValue'] = $attributeName;
        return $this;
    }
  

    /**
     * Sets single or multiple values of AttributeName list via variable number of arguments. 
     * For example, to set the list with two elements, simply pass two values as arguments to this function
     * <code>withAttributeName($attributeName1, $attributeName2)</code>
     * 
     * @param string  $stringArgs one or more AttributeName
     * @return Amazon_SQS_Model_GetQueueAttributes  instance
     */
    public function withAttributeName($stringArgs)
    {
        foreach (func_get_args() as $attributeName) {
            $this->_fields['AttributeName']['FieldValue'][] = $attributeName;
        }
        return $this;
    }  
      

    /**
     * Checks if AttributeName list is non-empty
     * 
     * @return bool true if AttributeName list is non-empty
     */
    public function isSetAttributeName()
    {
        return count ($this->_fields['AttributeName']['FieldValue']) > 0;
    }




}