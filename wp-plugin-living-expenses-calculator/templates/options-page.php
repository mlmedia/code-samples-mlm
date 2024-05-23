<?php

/* set active tab to switch between tabs using same form and template below */
$active_tab = filter_input(INPUT_GET, 'tab', FILTER_SANITIZE_STRING);
$active_tab = strlen($active_tab) > 0 ? $active_tab : 'general';
?><div id="living-expenses-lec-settings">
	<h1><?php echo __('Living Expense Calculator Settings');?></h1>
	<h2 class="nav-tab-wrapper">
		<a class="nav-tab<?php echo $active_tab == 'general' ? ' nav-tab-active':'';?>" href="?page=living_expense_calculator&tab=general">
			<?php echo __('General Settings');?>
		</a>
		<a class="nav-tab<?php echo $active_tab == 'housing' ? ' nav-tab-active':'';?>" href="?page=living_expense_calculator&tab=housing">
			<?php echo __('Housing Expenses');?>
		</a>
		<a class="nav-tab<?php echo $active_tab == 'upkeep' ? ' nav-tab-active':'';?>" href="?page=living_expense_calculator&tab=upkeep">
			<?php echo __('Upkeep Expenses');?>
		</a>
		<a class="nav-tab<?php echo $active_tab == 'lifestyle' ? ' nav-tab-active':'';?>" href="?page=living_expense_calculator&tab=lifestyle">
			<?php echo __('Lifestyle Expenses');?>
		</a>
		<a class="nav-tab<?php echo $active_tab == 'form' ? ' nav-tab-active':'';?>" href="?page=living_expense_calculator&tab=form">
			<?php echo __('Form');?>
		</a>
	</h2>
	<form action="options.php" method="post">
		<?php
		if ($active_tab == 'general') {
			settings_fields( 'lec_general' );
			do_settings_sections( 'lec_general' );
		} elseif ($active_tab == 'housing') {
			settings_fields( 'lec_housing' );
			do_settings_sections( 'lec_housing' );
		} elseif ($active_tab == 'upkeep') {
			settings_fields( 'lec_upkeep' );
			do_settings_sections( 'lec_upkeep' );
		} elseif ($active_tab == 'lifestyle') {
			settings_fields( 'lec_lifestyle' );
			do_settings_sections( 'lec_lifestyle' );
		} elseif ($active_tab == 'form') {
			settings_fields( 'lec_form' );
			do_settings_sections( 'lec_form' );
		}
		submit_button();
		?>
	</form>
</div>
