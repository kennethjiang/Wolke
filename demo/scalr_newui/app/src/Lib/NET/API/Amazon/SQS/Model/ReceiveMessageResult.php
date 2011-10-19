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
 * Amazon_SQS_Model_ReceiveMessageResult
 * 
 * Properties:
 * <ul>
 * 
 * <li>Message: Amazon_SQS_Model_Message</li>
 *
 * </ul>
 */ 
class Amazon_SQS_Model_ReceiveMessageResult extends Amazon_SQS_Model
{


    /**
     * Construct new Amazon_SQS_Model_ReceiveMessageResult
     * 
     * @param mixed $data DOMElement or Associative Array to construct from. 
     * 
     * Valid properties:
     * <ul>
     * 
     * <li>Message: Amazon_SQS_Model_Message</li>
     *
     * </ul>
     */
    public function __construct($data = null)
    {
        $this->_fields = array (
        'Message' => array('FieldValue' => array(), 'FieldType' => array('Amazon_SQS_Model_Message')),
        );
        parent::__construct($data);
    }

        /**
     * Gets the value of the Message.
     * 
     * @return array of Message Message
     */
    public function getMessage() 
    {
        return $this->_fields['Message']['FieldValue'];
    }

    /**
     * Sets the value of the Message.
     * 
     * @param mixed Message or an array of Message Message
     * @return this instance
     */
    public function setMessage($message) 
    {
        if (!$this->_isNumericArray($message)) {
            $message =  array ($message);    
        }
        $this->_fields['Message']['FieldValue'] = $message;
        return $this;
    }


    /**
     * Sets single or multiple values of Message list via variable number of arguments. 
     * For example, to set the list with two elements, simply pass two values as arguments to this function
     * <code>withMessage($message1, $message2)</code>
     * 
     * @param Message  $messageArgs one or more Message
     * @return Amazon_SQS_Model_ReceiveMessageResult  instance
     */
    public function withMessage($messageArgs)
    {
        foreach (func_get_args() as $message) {
            $this->_fields['Message']['FieldValue'][] = $message;
        }
        return $this;
    }   



    /**
     * Checks if Message list is non-empty
     * 
     * @return bool true if Message list is non-empty
     */
    public function isSetMessage()
    {
        return count ($this->_fields['Message']['FieldValue']) > 0;
    }




}