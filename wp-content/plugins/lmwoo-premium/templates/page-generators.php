<?php defined('ABSPATH') || exit; ?>

<div class="wrap lmfwc">
	<?php
	if ( 'list' === $action || 'delete' === $action   ) {
		include_once 'generators/page-list.php';
	} elseif ( 'add' === $action  ) {
		include_once 'generators/page-add.php';
	} elseif ( 'edit'  ===  $action ) {
		include_once 'generators/page-edit.php';
	} elseif ( 'generate' === $action  ) {
		include_once 'generators/page-generate.php';
	}
	?>
</div>