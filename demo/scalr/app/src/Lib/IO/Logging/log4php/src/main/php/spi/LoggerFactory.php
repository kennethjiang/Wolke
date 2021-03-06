<?php
/**
 * Licensed to the Apache Software Foundation (ASF) under one or more
 * contributor license agreements.  See the NOTICE file distributed with
 * this work for additional information regarding copyright ownership.
 * The ASF licenses this file to You under the Apache License, Version 2.0
 * (the "License"); you may not use this file except in compliance with
 * the License.  You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 *
 * @package log4php
 * @subpackage spi
 */

/**
 * @ignore
 */
if (!defined('LOG4PHP_DIR')) define('LOG4PHP_DIR', dirname(__FILE__));

require_once(LOG4PHP_DIR . '/LoggerLog.php');

/**
 * Extend and implement this abstract class to create new instances of 
 * {@link Logger} or a sub-class of {@link Logger}.
 *
 * @author  Marco Vassura
 * @version $Revision: 635069 $
 * @package log4php
 * @subpackage spi
 * @since 0.5 
 * @abstract
 */
abstract class LoggerFactory {

    /**
     * @abstract
     * @param string $name
     * @return Logger
     */
    abstract function makeNewLoggerInstance($name);

}
