<?php /* Smarty version 2.6.26, created on 2011-10-12 23:15:02
         compiled from event_messages/NewMysqlMasterUp.tpl */ ?>
New MySQL master UP: <?php echo $this->_tpl_vars['event']->DBServer->serverId; ?>
 Public IP: <?php echo $this->_tpl_vars['event']->DBServer->remoteIp; ?>
 Internal IP: <?php echo $this->_tpl_vars['event']->DBServer->localIp; ?>