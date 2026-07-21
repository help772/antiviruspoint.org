<?php defined('ABSPATH') || exit; ?>

<div class="wrap lmfwc">
	<?php
	if ( 'list' === $action  || 'activate' === $action  || 'deactivate' === $action  || 'delete' === $action ) {
		include_once 'licenses/page-list.php';
	} elseif ( 'add' === $action  ) {
		include_once 'licenses/page-add.php';
	} elseif (  'import' === $action) {
		include_once 'licenses/page-import.php';
	} elseif ( 'edit'  === $action ) {
		include_once 'licenses/page-edit.php';
	}
	?>
</div>