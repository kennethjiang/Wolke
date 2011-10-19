<?php

	class App_Test extends UnitTestCase 
	{
		
        function __construct() 
        {
        	$this->UnitTestCase('Application tests');
        }
		
        function _testTaskQueue()
        {
        	// Attach to queue
        	$Queue = TaskQueue::Attach(QUEUE_NAME::DEFERRED_EVENTS);

        	// Check queue size
        	$this->assertTrue($Queue->Size() == 0, "Queue is empty");
        	
        	// Put task to queue
        	$Queue->Put(new FireDeferredEventTask(1));
        	       	
        	// Put second task to queue
        	TaskQueue::Attach(QUEUE_NAME::DEFERRED_EVENTS)->AppendTask(new FireDeferredEventTask(2));
        	
        	// Check queue size
        	$this->assertTrue($Queue->Size() == 2, "Queue size = 1");
        	
        	// Pol queue (Retrieve and delete first element from queue)
        	$Task = $Queue->Poll();
        	
        	// Check queue length
        	$this->assertTrue($Queue->Size() == 1, "Queue contains 1 task");
        	
        	// Check returned task
        	$this->assertTrue($Task->EventID == 1, "Valid first task returned from queue");
        	
        	// Pol queue (Retrieve and delete first element from queue)
        	$Task = $Queue->Poll();
        	
        	// Check queue length
        	$this->assertTrue($Queue->Size() == 0, "Queue is empty");
        	
        	// Check returned task
        	$this->assertTrue($Task->EventID == 2, "Valid first task returned from queue");
        }
	}

?>