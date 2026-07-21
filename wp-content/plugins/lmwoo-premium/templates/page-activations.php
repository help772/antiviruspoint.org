<?php defined( 'ABSPATH' ) || exit; ?>

<div class="wrap lmfwc">
	<?php
	if ( 'list' === $action  || 'activate' ===   $action || 'deactivate' === $action   || 'delete' === $action   ) {
		include_once 'activations/page-list.php' ;
	}

	?>
</div>