<?php

/* @var string $action */

defined( 'ABSPATH' ) || exit;

?>

<div class="wrap lmfwc">
	<?php
	if ( in_array( $action, array( 'list', 'delete' ) ) ) {
		include_once 'applications/page-list.php' ;
	} elseif ( 'add' === $action ) {
		include_once 'applications/page-add.php' ;
	} elseif ( 'edit' === $action ) {
		include_once 'applications/page-edit.php' ;
	}
	?>
</div>
