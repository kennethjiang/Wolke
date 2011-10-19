<?php /* Smarty version 2.6.26, created on 2011-10-14 01:15:03
         compiled from event_messages/MySQLReplicationRecovered.tpl */ ?>
Mysql replication recovered on instance <?php echo $this->_tpl_vars['event']->DBServer->serverId; ?>
 Public IP: <?php echo $this->_tpl_vars['event']->DBServer->remoteIp; ?>
 Internal IP: <?php echo $this->_tpl_vars['event']->DBServer->localIp; ?>
 