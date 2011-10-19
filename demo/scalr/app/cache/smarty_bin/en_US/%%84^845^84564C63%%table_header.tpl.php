<?php /* Smarty version 2.6.26, created on 2011-09-18 17:33:48
         compiled from inc/table_header.tpl */ ?>
<?php if (! $this->_tpl_vars['nofilter']): ?>
<table border="0" width="100%" cellspacing="0" cellpadding="0" height="40">
	<tr>
		<td align="center" nowrap width="10">&nbsp;</td>
		<td align="left" valign="bottom" width="500px">
			<div style="padding:0px;">
			<?php if ($this->_tpl_vars['show_region_filter']): ?>
				<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "inc/region_filter.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
			<?php endif; ?>
			<?php if ($this->_tpl_vars['filter']): ?><?php echo $this->_tpl_vars['filter']; ?>
<?php endif; ?>
			</div>
		</td>
		<td colspan="4" align="left" valign="bottom"><?php echo $this->_tpl_vars['paging']; ?>
</td>
		<td align="center" nowrap>&nbsp;</td>
	</tr>
</table>
<?php endif; ?>

<?php if ($this->_tpl_vars['tabs']): ?>
<?php echo '
<script language="Javascript">
	function SetActiveTab(id, itable_tabs)
	{
		//
		// Unselect current active tab
		//
		var container = Ext.get(\'tabs_container\');
		
		var elems = container.select(\'.TableHeaderLeft\');
		elems.each(function(item){    
			item.dom.className = \'TableHeaderLeft_LGray\';
		});
	
		var elems = container.select(\'.TableHeaderCenter\');
		elems.each(function(item){    
			item.dom.className = \'TableHeaderCenter_LGray\';
		});
		
		var elems = container.select(\'.TableHeaderRight\');
		elems.each(function(item){    
			item.dom.className = \'TableHeaderRight_LGray\';
		});
		
		var elems = container.select(\'.TableHeaderContent\');
		elems.each(function(item){    
			item.dom.bgColor = \'#f4f4f4\';
			item.dom.className = \'TableHeaderContent_LGray\';
		});
		
		//
		// Select active tab
		//
		var ctab = Ext.get(\'tab_\'+id)
		
		var elems = ctab.select(\'[class="TableHeaderLeft_LGray"]\');
		elems.each(function(item){    
			item.className = \'TableHeaderLeft\';
		});
		
		var elems = ctab.select(\'[class="TableHeaderCenter_LGray"]\');
		elems.each(function(item){    
			item.className = \'TableHeaderCenter\';
		});
		
		var elems = ctab.select(\'[class="TableHeaderRight_LGray"]\');
		elems.each(function(item){    
			item.className = \'TableHeaderRight\';
		});
		
		var elems = ctab.select(\'[class="TableHeaderContent_LGray"]\');
		elems.each(function(item){    
			item.bgColor = \'#C3D9FF\';
			item.className = \'TableHeaderContent\';
		});
		
		
		var elems = Ext.select(\'div.tab_contents\');
		elems.each(function(item){    
			item.dom.style.display = "none";
		});

		if (Ext.get(\'tab_contents_\'+id))
		{
			Ext.get(\'tab_contents_\'+id).dom.style.display = "";
		}
			
		try
		{
			OnTabChanged(id);
		}
		catch(e){}
	}
</script>
'; ?>

<div id="tabs_container">
	<div style="margin-left:10px;">
		<?php $_from = $this->_tpl_vars['tabs_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['id'] => $this->_tpl_vars['tab_name']):
?>
		<?php if ($this->_tpl_vars['selected_tab'] == $this->_tpl_vars['id']): ?>
			<?php $this->assign('is_active_tab', '1'); ?>
		<?php else: ?>
			<?php $this->assign('is_active_tab', '0'); ?>
		<?php endif; ?>
	  	<div class="table_tab" id="tab_<?php echo $this->_tpl_vars['id']; ?>
" onClick="SetActiveTab('<?php echo $this->_tpl_vars['id']; ?>
');">
           	<table border="0" cellpadding="0" cellspacing="0" width="120">
           		<tr>
           			<td width="7"><div class="TableHeaderLeft<?php if (! $this->_tpl_vars['is_active_tab']): ?>_LGray<?php endif; ?>"></div></td>
           			<td><div class="TableHeaderCenter<?php if (! $this->_tpl_vars['is_active_tab']): ?>_LGray<?php endif; ?>"></div></td>
           			<td><div class="TableHeaderCenter<?php if (! $this->_tpl_vars['is_active_tab']): ?>_LGray<?php endif; ?>"></div></td>
           			<td width="7"><div class="TableHeaderRight<?php if (! $this->_tpl_vars['is_active_tab']): ?>_LGray<?php endif; ?>"></div></td>
           		</tr>
           		<tr id="tab_content_<?php echo $this->_tpl_vars['id']; ?>
" class="TableHeaderContent<?php if (! $this->_tpl_vars['is_active_tab']): ?>_LGray<?php endif; ?>" bgcolor="<?php if ($this->_tpl_vars['is_active_tab']): ?>#C3D9FF<?php else: ?>#f4f4f4<?php endif; ?>">
           			<td width="7" class="TableHeaderCenter<?php if (! $this->_tpl_vars['is_active_tab']): ?>_LGray<?php endif; ?>"></td>
           			<td id="tab_name_<?php echo $this->_tpl_vars['id']; ?>
" nowrap style="padding-bottom:5px;" align="center">
						<?php echo $this->_tpl_vars['tab_name']; ?>

           			</td>
           			<td align="left" nowrap></td>
           			<td width="7" class="TableHeaderCenter<?php if (! $this->_tpl_vars['is_active_tab']): ?>_LGray<?php endif; ?>"></td>
           		</tr>
           	</table>
		</div>
		<?php endforeach; endif; unset($_from); ?>
		<div style="clear:both;"></div>
	</div>
</div>
<?php endif; ?>

<?php if ($this->_tpl_vars['table_header_text']): ?>
<div>
	<div style="margin-left:10px;">
      	<table border="0" cellpadding="0" cellspacing="0">
      		<tr>
      			<td width="7"><div class="TableHeaderLeft"></div></td>
      			<td><div class="TableHeaderCenter"></div></td>
      			<td><div class="TableHeaderCenter"></div></td>
      			<td width="7"><div class="TableHeaderRight"></div></td>
      		</tr>
      		<tr bgcolor="#C3D9FF">
      			<td width="7" class="TableHeaderCenter"></td>
      			<td nowrap style="padding-bottom:5px;">
      			 <?php echo $this->_tpl_vars['table_header_text']; ?>

      			</td>
      			<td align="left" nowrap></td>
      			<td width="7" class="TableHeaderCenter"></td>
      		</tr>
      	</table>
	</div>
<?php endif; ?>
<div class="Webta_Table">
	<div style="width:100%;">
		<div style="padding-left:7px; height:7px; background-image: url(/images/tl.gif); background-repeat: no-repeat;">
			<div style="padding-right:7px; height:7px; background-image: url(/images/tr.gif); background-position: right top; background-repeat: no-repeat;">
				<div style="background-color: #C3D9FF; height:7px;"></div>
			</div>
		</div>
	</div>
	<div>
		<div style="border-left:7px solid #C3D9FF; border-right:7px solid #C3D9FF;">
		
		