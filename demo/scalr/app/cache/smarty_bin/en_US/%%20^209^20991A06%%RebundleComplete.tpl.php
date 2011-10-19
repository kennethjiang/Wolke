<?php /* Smarty version 2.6.26, created on 2011-09-21 01:50:03
         compiled from event_messages/RebundleComplete.tpl */ ?>
Rebundle started on instance <?php echo $this->_tpl_vars['event']->DBServer->remoteIp; ?>
 (<?php echo $this->_tpl_vars['event']->DBServer->serverId; ?>
) for farm #<?php echo $this->_tpl_vars['event']->DBServer->farmId; ?>
 successfully complete. New Snapshot ID: <?php echo $this->_tpl_vars['event']->SnapshotID; ?>
.