<?php /* Smarty version 2.6.26, created on 2011-09-29 04:38:43
         compiled from inc/intable_header.tpl */ ?>

<div <?php if ($this->_tpl_vars['intableid']): ?>id="<?php echo $this->_tpl_vars['intableid']; ?>
"<?php endif; ?> <?php if ($this->_tpl_vars['intable_classname']): ?>class="<?php echo $this->_tpl_vars['intable_classname']; ?>
"<?php endif; ?> style="display:<?php echo $this->_tpl_vars['visible']; ?>
;padding: <?php if ($this->_tpl_vars['intablepadding']): ?><?php echo $this->_tpl_vars['intablepadding']; ?>
<?php else: ?>7<?php endif; ?>px;">
	<table border="0" cellpadding="0" cellspacing="0" width="100%">
	<?php if (! $this->_tpl_vars['noheaderline']): ?>
	<tr>
		<td width="7"><div class="TableHeaderLeft_<?php echo $this->_tpl_vars['color']; ?>
"></div></td>
		<td>
		<a name="<?php echo $this->_tpl_vars['ancor_name']; ?>
"></a>
		<div id="webta_table_header<?php echo $this->_tpl_vars['header_id']; ?>
" class="SettingsHeader_<?php echo $this->_tpl_vars['color']; ?>
" style="padding-left:10px;">
			<?php if ($this->_tpl_vars['header']): ?><strong><?php echo $this->_tpl_vars['header']; ?>
</strong><?php endif; ?>
		</div>
		</td>
		<td width="7"><div class="TableHeaderRight_<?php echo $this->_tpl_vars['color']; ?>
"></div></td>
	</tr>
	<?php endif; ?>
	<tr>
		<td width="7" class="TableHeaderCenter_<?php echo $this->_tpl_vars['color']; ?>
"></td>
		<td class="Inner_<?php echo $this->_tpl_vars['color']; ?>
">
			<table width="100%" cellspacing="0" cellpadding="2" id="Webta_InnerTable_<?php echo $this->_tpl_vars['header']; ?>
" <?php if ($this->_tpl_vars['section_closed']): ?>style="display: none;"<?php endif; ?>>
			<?php if (! $this->_tpl_vars['no_first_row']): ?>
			<tr>
				<td width="<?php if ($this->_tpl_vars['intable_first_column_width']): ?><?php echo $this->_tpl_vars['intable_first_column_width']; ?>
<?php else: ?>20%<?php endif; ?>"></td>
				<td colspan="<?php if ($this->_tpl_vars['intable_colspan']): ?><?php echo $this->_tpl_vars['intable_colspan']; ?>
<?php else: ?>1<?php endif; ?>" style="height:15px;"></td>
			</tr>
			<?php endif; ?>
			