<?php
/**
 * Licensed to the Apache Software Foundation (ASF) under one or more
 * contributor license agreements.  See the NOTICE file distributed with
 * this work for additional information regarding copyright ownership.
 * The ASF licenses this file to You under the Apache License, Version 2.0
 * (the "License"); you may not use this file except in compliance with
 * the License.  You may obtain a copy of the License at
 * 
 *      http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * 
 * @category   tests   
 * @package    log4php
 * @subpackage or
 * @license    http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @version    SVN: $Id$
 * @link       http://logging.apache.org/log4php
 */

require_once dirname(__FILE__).'/../phpunit.php';

require_once LOG4PHP_DIR.'/or/LoggerRendererMap.php';
require_once LOG4PHP_DIR.'/LoggerHierarchy.php';

class LoggerRendererMapTest extends PHPUnit_Framework_TestCase {
        
        protected function setUp() {
        }
        
        protected function tearDown() {
        }
        
        public function testAddRenderer() {
                
                $hierarchy = LoggerHierarchy::singleton();
                
                //print_r($hierarchy);
                
                LoggerRendererMap::addRenderer($hierarchy, 'string', 'LoggerDefaultRenderer');
                
                //print_r($hierarchy);
                
                throw new PHPUnit_Framework_IncompleteTestError();
        }
        
        public function testFindAndRender() {
                throw new PHPUnit_Framework_IncompleteTestError();
        }
        
        public function testGetByObject() {
                throw new PHPUnit_Framework_IncompleteTestError();
        }
        
        public function testGetByClassName() {
                throw new PHPUnit_Framework_IncompleteTestError();
        }
        
        public function testGetDefaultRenderer() {
                throw new PHPUnit_Framework_IncompleteTestError();
        }
        
        public function testClear() {
                throw new PHPUnit_Framework_IncompleteTestError();
        }
        
        public function testPut() {
                throw new PHPUnit_Framework_IncompleteTestError();
        }
        
        public function testRendererExists() {
                throw new PHPUnit_Framework_IncompleteTestError();
        }

}
?>
