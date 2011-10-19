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
 * 
 * Amazon Simple Queue Service (Amazon SQS) offers a reliable, highly scalable hosted queue for storing messages as they travel between computers. By using Amazon SQS, developers can simply move data between distributed application components performing different tasks, without losing messages or requiring each component to be always available.  Amazon SQS works by exposing Amazon's web-scale messaging infrastructure as a web service. Any computer on the Internet can add or read messages without any installed software or special firewall configurations. Components of applications using Amazon SQS can run independently, and do not need to be on the same network, developed with the same technologies, or running at the same time.
 * 
 */

interface  Amazon_SQS_Interface 
{
    

            
    /**
     * Create Queue 
     * 
     * The CreateQueue action creates a new queue. You must provide a queue name that is unique within the scope of the queues you own. The queue is assigned a queue URL; you must use this URL when performing actions on the queue.  When you create a queue, if a queue with the same name already exists, CreateQueue returns the queue URL with an error indicating that the queue already exists.
     *   
     * @see http://docs.amazonwebservices.com/AWSSimpleQueueService/2008-01-01/SQSDeveloperGuide/Query_QueryCreateQueue.html      
     * @param mixed $request array of parameters for Amazon_SQS_Model_CreateQueue request or Amazon_SQS_Model_CreateQueue object itself
     * @see Amazon_SQS_Model_CreateQueue
     * @return Amazon_SQS_Model_CreateQueueResponse Amazon_SQS_Model_CreateQueueResponse
     *
     * @throws Amazon_SQS_Exception
     */
    public function createQueue($request);


            
    /**
     * List Queues 
     * 
     * The ListQueues action returns a list of your queues.
     *   
     * @see http://docs.amazonwebservices.com/AWSSimpleQueueService/2008-01-01/SQSDeveloperGuide/Query_QueryListQueues.html      
     * @param mixed $request array of parameters for Amazon_SQS_Model_ListQueues request or Amazon_SQS_Model_ListQueues object itself
     * @see Amazon_SQS_Model_ListQueues
     * @return Amazon_SQS_Model_ListQueuesResponse Amazon_SQS_Model_ListQueuesResponse
     *
     * @throws Amazon_SQS_Exception
     */
    public function listQueues($request);


            
    /**
     * Delete Message 
     * The DeleteMessage action unconditionally removes the specified message from the specified queue. Even if the message is locked by another reader due to the visibility timeout setting, it is still deleted from the queue.
     *   
     * @see http://docs.amazonwebservices.com/AWSSimpleQueueService/2008-01-01/SQSDeveloperGuide/Query_QueryDeleteMessage.html      
     * @param mixed $request array of parameters for Amazon_SQS_Model_DeleteMessage request or Amazon_SQS_Model_DeleteMessage object itself
     * @see Amazon_SQS_Model_DeleteMessage
     * @return Amazon_SQS_Model_DeleteMessageResponse Amazon_SQS_Model_DeleteMessageResponse
     *
     * @throws Amazon_SQS_Exception
     */
    public function deleteMessage($request);


            
    /**
     * Delete Queue 
     * 
     * This action unconditionally deletes the queue specified by the queue URL. Use this operation WITH CARE!  The queue is deleted even if it is NOT empty.
     *   
     * @see http://docs.amazonwebservices.com/AWSSimpleQueueService/2008-01-01/SQSDeveloperGuide/Query_QueryDeleteQueue.html      
     * @param mixed $request array of parameters for Amazon_SQS_Model_DeleteQueue request or Amazon_SQS_Model_DeleteQueue object itself
     * @see Amazon_SQS_Model_DeleteQueue
     * @return Amazon_SQS_Model_DeleteQueueResponse Amazon_SQS_Model_DeleteQueueResponse
     *
     * @throws Amazon_SQS_Exception
     */
    public function deleteQueue($request);


            
    /**
     * Get Queue Attributes 
     * 
     * Gets one or all attributes of a queue. Queues currently have two attributes you can get: ApproximateNumberOfMessages and VisibilityTimeout.
     *   
     * @see http://docs.amazonwebservices.com/AWSSimpleQueueService/2008-01-01/SQSDeveloperGuide/Query_QueryGetQueueAttributes.html      
     * @param mixed $request array of parameters for Amazon_SQS_Model_GetQueueAttributes request or Amazon_SQS_Model_GetQueueAttributes object itself
     * @see Amazon_SQS_Model_GetQueueAttributes
     * @return Amazon_SQS_Model_GetQueueAttributesResponse Amazon_SQS_Model_GetQueueAttributesResponse
     *
     * @throws Amazon_SQS_Exception
     */
    public function getQueueAttributes($request);


            
    /**
     * Receive Message 
     * 
     * Retrieves one or more messages from the specified queue, including the message body and message ID of each message. Messages returned by this action stay in the queue until you delete them. However, once a message is returned to a ReceiveMessage request, it is not returned on subsequent ReceiveMessage requests for the duration of the VisibilityTimeout. If you do not specify a VisibilityTimeout in the request, the overall visibility timeout for the queue is used for the returned messages.
     *   
     * @see http://docs.amazonwebservices.com/AWSSimpleQueueService/2008-01-01/SQSDeveloperGuide/Query_QueryReceiveMessage.html      
     * @param mixed $request array of parameters for Amazon_SQS_Model_ReceiveMessage request or Amazon_SQS_Model_ReceiveMessage object itself
     * @see Amazon_SQS_Model_ReceiveMessage
     * @return Amazon_SQS_Model_ReceiveMessageResponse Amazon_SQS_Model_ReceiveMessageResponse
     *
     * @throws Amazon_SQS_Exception
     */
    public function receiveMessage($request);


            
    /**
     * Send Message 
     * The SendMessage action delivers a message to the specified queue.
     *   
     * @see http://docs.amazonwebservices.com/AWSSimpleQueueService/2008-01-01/SQSDeveloperGuide/Query_QuerySendMessage.html      
     * @param mixed $request array of parameters for Amazon_SQS_Model_SendMessage request or Amazon_SQS_Model_SendMessage object itself
     * @see Amazon_SQS_Model_SendMessage
     * @return Amazon_SQS_Model_SendMessageResponse Amazon_SQS_Model_SendMessageResponse
     *
     * @throws Amazon_SQS_Exception
     */
    public function sendMessage($request);


            
    /**
     * Set Queue Attributes 
     * 
     * Sets an attribute of a queue. Currently, you can set only the VisibilityTimeout attribute for a queue.
     *   
     * @see http://docs.amazonwebservices.com/AWSSimpleQueueService/2008-01-01/SQSDeveloperGuide/Query_QuerySetQueueAttributes.html      
     * @param mixed $request array of parameters for Amazon_SQS_Model_SetQueueAttributes request or Amazon_SQS_Model_SetQueueAttributes object itself
     * @see Amazon_SQS_Model_SetQueueAttributes
     * @return Amazon_SQS_Model_SetQueueAttributesResponse Amazon_SQS_Model_SetQueueAttributesResponse
     *
     * @throws Amazon_SQS_Exception
     */
    public function setQueueAttributes($request);

}