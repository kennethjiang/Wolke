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
 */
define('LOG4PHP_DIR', dirname(__FILE__).'/../../main/php');
define('LOG4PHP_CONFIGURATION', dirname(__FILE__).'/cache.properties');

require_once LOG4PHP_DIR.'/LoggerManager.php';

$cache = 'hierarchy.cache';

if(!file_exists($cache)) {
  $hierarchy = LoggerManager::getLoggerRepository();
  file_put_contents($cache, serialize($hierarchy));
}
$hierarchy = unserialize(file_get_contents($cache));

$logger = $hierarchy->getRootLogger();

$logger->debug('Debug message from cached logger');
?>
