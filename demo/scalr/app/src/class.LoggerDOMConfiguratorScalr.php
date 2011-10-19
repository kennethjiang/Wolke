<?php

   cl 
    /**
     * @param object $object
     * @param string $name
     * @param mixed $value
     */
    function setter(&$object, $name, $value)
    {
        if (empty($name)) {
            LoggerLog::debug("LoggerDOMConfiguratorScalr::setter() 'name' param cannot be empty");        
            return false;
        }
        
        if (preg_match("/\%appconfig\{([A-Za-z0-9_]+)\}\%/si", $value, $matches))
        {
        	if (property_exists("CONFIG", $matches[1]))
        		$value = CONFIG::$$matches[1];
        	else
        		trigger_error("Cannot parse '{$matches[0]}' string in log4php.xml. Class CONFIG doesn't have property '{$matches[1]}'", E_USER_WARNING);
        }
        
        $methodName = 'set'.ucfirst($name);
        if (method_exists($object, $methodName)) {
            LoggerLog::debug("LoggerDOMConfiguratorScalr::setter() Calling ".get_class($object)."::{$methodName}({$value})");
            return call_user_func(array(&$object, $methodName), $value);
        } else {
            LoggerLog::warn("LoggerDOMConfiguratorScalr::setter() ".get_class($object)."::{$methodName}() does not exists");
            return false;
        }
    }
    
    function subst($value)
    {
        return LoggerOptionConverter::substVars($value);
    }

}
?>