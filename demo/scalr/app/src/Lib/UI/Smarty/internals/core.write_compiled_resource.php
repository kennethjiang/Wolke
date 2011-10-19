<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/**
 * write the compiled resource
 *
 * @param string $compile_path
 * @param string $compiled_content
 * @return true
 */
function smarty_core_write_compiled_resource($params, &$smarty)
{
    if (!@file_exists($smarty->compile_dir))
    	@mkdir($smarty->compile_dir, 0777);
	
	if(!@is_writable($smarty->compile_dir)) 
	{
        $smarty->trigger_error("Insuficient permissions on folder {$smarty->compile_dir}. Please chmod 0777", E_USER_ERROR);
        return false;
    }

    $_params = array('filename' => $params['compile_path'], 'contents' => $params['compiled_content'], 'create_dirs' => true);
    require_once(SMARTY_CORE_DIR . 'core.write_file.php');
    smarty_core_write_file($_params, $smarty);
    return true;
}

/* vim: set expandtab: */

?>
