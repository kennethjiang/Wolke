<?
	$base = dirname(__FILE__);
	require_once("{$base}/../prepend.inc.php");
	
	///
    /// LibWebta Tests
    ///
	require_once("{$base}/LibWebta/tests/tests.php");
	
		
	/*
    Applicaton Tests
    */
    
	$test_app = &new GroupTest('App tests');
	require_once(dirname(__FILE__)."/tests.php");
	$test_app->addTestCase(new App_Test());
	
	/*
	Reporting
	*/
	
	// Select reporter based on php_sapi_name()
	$sapi_type = php_sapi_name();
	if (substr($sapi_type, 0, 3) != 'cli')
	{
		$test_app->run(new NiceReporter());
	}
	else 
	{
		$test_app->run(new ShellReporter());
	}
?>