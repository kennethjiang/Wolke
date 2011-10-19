<?php /* Smarty version 2.6.26, created on 2011-09-20 14:35:30
         compiled from clients_add.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'strtolower', 'clients_add.tpl', 93, false),)), $this); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "inc/header.tpl", 'smarty_include_vars' => array('upload_files' => 1)));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	<?php echo '
	<link rel="stylesheet" type="text/css" media="all" href="/css/calendar.css"  />
	<script type="text/javascript" src="/js/calendar/calendar.js"></script>
	<script type="text/javascript" src="/js/calendar/calendar-en.js"></script>
	<script type="text/javascript">
	
	// This function gets called when the end-user clicks on some date.
	function selected(cal, date) {
	  cal.sel.value = date; // just update the date in the input field.
	  if (cal.sel.id == "sel1" || cal.sel.id == "sel3")
		// if we add this call we close the calendar on single-click.
		// just to exemplify both cases, we are using this only for the 1st
		// and the 3rd field, while 2nd and 4th will still require double-click.
		cal.callCloseHandler();
	}
	
	function closeHandler(cal) {
	  cal.hide();                        // hide the calendar
	}
	function showCalendar(id, format) {
	  var el = document.getElementById(id);
	  if (calendar != null) {
		// we already have some calendar created
		calendar.hide();                 // so we hide it first.
	  } else {
		// first-time call, create the calendar.
		var cal = new Calendar(false, null, selected, closeHandler);
		// uncomment the following line to hide the week numbers
		// cal.weekNumbers = false;
		calendar = cal;                  // remember it in the global var
		cal.setRange(1900, 2070);        // min/max year allowed.
		cal.create();
	  }
	  calendar.setDateFormat(format);    // set the specified date format
	  calendar.parseDate(el.value);      // try to parse the text in field
	  calendar.sel = el;                 // inform it what input field we use
	  calendar.showAtElement(el);        // show the calendar below it
	
	  return false;
	}
	
	var MINUTE = 60 * 1000;
	var HOUR = 60 * MINUTE;
	var DAY = 24 * HOUR;
	var WEEK = 7 * DAY;
	
	function isDisabled(date) {
	  var today = new Date();
	  return (Math.abs(date.getTime() - today.getTime()) / DAY) > 10;
	}
	</script>
	'; ?>

	<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "inc/table_header.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "inc/intable_header.tpl", 'smarty_include_vars' => array('header' => 'Comments','color' => 'Gray')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
        <tr>
    		<td colspan="2">
    			<textarea class="text" rows="10" cols="80" name='comments' id='comments'><?php echo $this->_tpl_vars['comments']; ?>
</textarea>
    		</td>
    	</tr>
        <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "inc/intable_footer.tpl", 'smarty_include_vars' => array('color' => 'Gray')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
                
		<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "inc/intable_header.tpl", 'smarty_include_vars' => array('header' => 'Account information','color' => 'Gray')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
    	<tr>
    		<td width="20%">E-mail:</td>
    		<td><input type="text" class="text" name="email" value="<?php echo $this->_tpl_vars['email']; ?>
" /></td>
    	</tr>
    	<tr>
    		<td width="20%">Password:</td>
    		<td><input type="password" class="text" name="password" value="<?php if ($this->_tpl_vars['password']): ?>******<?php endif; ?>" /></td>
    	</tr>
    	<tr>
    		<td width="20%">Confirm password:</td>
    		<td><input type="password" class="text" name="password2" value="<?php if ($this->_tpl_vars['password']): ?>******<?php endif; ?>" /></td>
    	</tr>
    	<tr>
    		<td width="20%">Farms limit:</td>
    		<td><input type="text" class="text" name="farms_limit" value="<?php if ($this->_tpl_vars['farms_limit']): ?><?php echo $this->_tpl_vars['farms_limit']; ?>
<?php else: ?>0<?php endif; ?>" size="5" /> (0 for unlimited)</td>
    	</tr>
    	<tr>
    		<td width="20%">Full name:</td>
    		<td><input type="text" class="text" name="name" value="<?php echo $this->_tpl_vars['fullname']; ?>
" /></td>
    	</tr>
    	<tr>
    		<td width="20%">Organization:</td>
    		<td><input type="text" class="text" name="org" value="<?php echo $this->_tpl_vars['org']; ?>
" /></td>
    	</tr>
    	<tr>
    		<td width="20%">Country:</td>
    		<td>
    			<select  id="country" name="country" class="text">
				<?php unset($this->_sections['id']);
$this->_sections['id']['name'] = 'id';
$this->_sections['id']['loop'] = is_array($_loop=$this->_tpl_vars['countries']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['id']['show'] = true;
$this->_sections['id']['max'] = $this->_sections['id']['loop'];
$this->_sections['id']['step'] = 1;
$this->_sections['id']['start'] = $this->_sections['id']['step'] > 0 ? 0 : $this->_sections['id']['loop']-1;
if ($this->_sections['id']['show']) {
    $this->_sections['id']['total'] = $this->_sections['id']['loop'];
    if ($this->_sections['id']['total'] == 0)
        $this->_sections['id']['show'] = false;
} else
    $this->_sections['id']['total'] = 0;
if ($this->_sections['id']['show']):

            for ($this->_sections['id']['index'] = $this->_sections['id']['start'], $this->_sections['id']['iteration'] = 1;
                 $this->_sections['id']['iteration'] <= $this->_sections['id']['total'];
                 $this->_sections['id']['index'] += $this->_sections['id']['step'], $this->_sections['id']['iteration']++):
$this->_sections['id']['rownum'] = $this->_sections['id']['iteration'];
$this->_sections['id']['index_prev'] = $this->_sections['id']['index'] - $this->_sections['id']['step'];
$this->_sections['id']['index_next'] = $this->_sections['id']['index'] + $this->_sections['id']['step'];
$this->_sections['id']['first']      = ($this->_sections['id']['iteration'] == 1);
$this->_sections['id']['last']       = ($this->_sections['id']['iteration'] == $this->_sections['id']['total']);
?>
					<option value="<?php echo $this->_tpl_vars['countries'][$this->_sections['id']['index']]['code']; ?>
" <?php if (((is_array($_tmp=$this->_tpl_vars['country'])) ? $this->_run_mod_handler('strtolower', true, $_tmp) : strtolower($_tmp)) == ((is_array($_tmp=$this->_tpl_vars['countries'][$this->_sections['id']['index']]['code'])) ? $this->_run_mod_handler('strtolower', true, $_tmp) : strtolower($_tmp)) || ( ! ((is_array($_tmp=$this->_tpl_vars['country'])) ? $this->_run_mod_handler('strtolower', true, $_tmp) : strtolower($_tmp)) && ((is_array($_tmp=$this->_tpl_vars['countries'][$this->_sections['id']['index']]['code'])) ? $this->_run_mod_handler('strtolower', true, $_tmp) : strtolower($_tmp)) == 'us' )): ?>selected<?php endif; ?>><?php echo $this->_tpl_vars['countries'][$this->_sections['id']['index']]['name']; ?>
</option>
				<?php endfor; endif; ?>
				</select>
    		</td>
    	</tr>
    	<tr>
    		<td width="20%">State / Region:</td>
    		<td><input type="text" class="text" name="state" value="<?php echo $this->_tpl_vars['state']; ?>
" /></td>
    	</tr>
    	<tr>
    		<td width="20%">City:</td>
    		<td><input type="text" class="text" name="city" value="<?php echo $this->_tpl_vars['city']; ?>
" /></td>
    	</tr>
    	<tr>
    		<td width="20%">Postal code:</td>
    		<td><input type="text" class="text" name="zipcode" value="<?php echo $this->_tpl_vars['zipcode']; ?>
" /></td>
    	</tr>
    	<tr>
    		<td width="20%">Address 1:</td>
    		<td><input type="text" class="text" name="address1" value="<?php echo $this->_tpl_vars['address1']; ?>
" /></td>
    	</tr>
    	<tr>
    		<td width="20%">Address 2:</td>
    		<td><input type="text" class="text" name="address2" value="<?php echo $this->_tpl_vars['address2']; ?>
" /></td>
    	</tr>
    	<tr>
    		<td width="20%">Phone:</td>
    		<td><input type="text" class="text" name="phone" value="<?php echo $this->_tpl_vars['phone']; ?>
" /></td>
    	</tr>
    	<tr>
    		<td width="20%">Fax:</td>
    		<td><input type="text" class="text" name="fax" value="<?php echo $this->_tpl_vars['fax']; ?>
" /></td>
    	</tr>
        <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "inc/intable_footer.tpl", 'smarty_include_vars' => array('color' => 'Gray')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "inc/table_footer.tpl", 'smarty_include_vars' => array('edit_page' => 1)));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "inc/footer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>