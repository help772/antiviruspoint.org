<?php

use LicenseManagerForWooCommerce\Models\Resources\Generator as GeneratorResourceModel;
use LicenseManagerForWooCommerce\Repositories\Resources\Generator as GeneratorResourceRepository;

defined( 'ABSPATH' ) || exit;

function lmfwc_add_generator( $name, $character_map, $chunks, $chunk_length, $separator = null, $prefix = null, $suffix = null, $expires_in = null, $times_activated_max = null ) {
	$name          = sanitize_text_field( $name );
	$character_map = sanitize_text_field( $character_map );
	$chunks        = (int) $chunks;
	$chunk_length  = (int) $chunk_length;

	if ( strlen( $name ) > 255 ) {
		return new WP_Error( 'The generator\'s name cannot exceed 255 characters.' );
	}

	if ( strlen( $character_map ) > 255 ) {
		return new WP_Error( 'The generator\'s character map cannot exceed 255 characters.' );
	}

	if ( $chunks > 4294967295 ) {
		return new WP_Error( 'The generator\'s number of chunks cannot be larger than 4294967295.' );
	}

	if ( $chunk_length > 4294967295 ) {
		return new WP_Error( 'The generator\'s chunk length cannot be larger than 4294967295.' );
	}

	if ( null !== $separator ) {
		$separator = (string) $separator;

		if ( strlen( $separator ) > 255 ) {
			return new WP_Error( 'The generator\'s separator cannot cannot exceed 255 characters.' );
		}
	}

	if ( null !== $prefix ) {
		$prefix = (string) $prefix;

		if ( strlen( $prefix ) > 255 ) {
			return new WP_Error( 'The generator\'s prefix cannot cannot exceed 255 characters.' );
		}
	}

	if ( null !== $suffix ) {
		$suffix = (string) $suffix;

		if ( strlen( $suffix ) > 255 ) {
			return new WP_Error( 'The generator\'s suffix cannot cannot exceed 255 characters.' );
		}
	}

	if ( null !== $expires_in ) {
		$expires_in = (int) $expires_in;

		if ( $expires_in > 4294967295 ) {
			return new WP_Error( 'The generator\'s expires_in cannot be larger than 4294967295.' );
		}
	}

	if ( null !== $times_activated_max ) {
		$times_activated_max = (int) $times_activated_max;

		if ( $times_activated_max > 4294967295 ) {
			return new WP_Error( 'The generator\'s timesActivatedMax cannot be larger than 4294967295.' );
		}
	}

	$generator = GeneratorResourceRepository::instance()->insert(
		array(
			'name'                => $name,
			'charset'             => $character_map,
			'chunks'              => $chunks,
			'chunk_length'        => $chunk_length,
			'times_activated_max' => $times_activated_max,
			'separator'           => $separator,
			'prefix'              => $prefix,
			'suffix'              => $suffix,
			'expires_in'          => $expires_in,
		)
	);

	if ( ! $generator ) {
		return new WP_Error( 'The generator could not be created.' );
	}

	return $generator;
}

function lmfwc_get_generator( $generator_id ) {
	$generator = GeneratorResourceRepository::instance()->find( (int) $generator_id );

	if ( ! $generator ) {
		return new WP_Error( 'The generator could not be found.' );
	}

	return $generator;
}


function lmfwc_get_generators( $query ) {
	$generators = GeneratorResourceRepository::instance()->findAllBy( $query );

	if ( ! $generators ) {
		return array();
	}

	return $generators;
}

function lmfwc_update_generator( $generator_id, $generator_data ) {
	$update_data = array();

	$old_generator = GeneratorResourceRepository::instance()->find( (int) $generator_id );

	if ( ! $old_generator ) {
		return new WP_Error( 'The generator could not be found.' );
	}

	// Name
	if ( isset( $generator_data['name'] ) ) {
		$name = sanitize_text_field( $generator_data['name'] );

		if ( strlen( $name ) > 255 ) {
			return new WP_Error( 'The generator\'s name cannot exceed 255 characters.' );
		}

		$update_data['name'] = $name;
	}

	// Character map
	if ( isset( $generator_data['charset'] ) ) {
		$charset = sanitize_text_field( $generator_data['charset'] );

		if ( strlen( $charset ) > 255 ) {
			return new WP_Error( 'The generator\'s character map cannot exceed 255 characters.' );
		}

		$update_data['charset'] = $charset;
	}

	// Chunks
	if ( isset( $generator_data['chunks'] ) ) {
		$chunks = (int) $generator_data['chunks'];

		if ( $chunks > 4294967295 ) {
			return new WP_Error( 'The generator\'s chunks cannot be larger than 4294967295.' );
		}

		$update_data['chunks'] = $chunks;
	}

	// Chunk length
	if ( isset( $generator_data['chunk_length'] ) ) {
		$expires_in = (int) $generator_data['chunk_length'];

		if ( $expires_in > 4294967295 ) {
			return new WP_Error( 'The generator\'s chunk_length cannot be larger than 4294967295.' );
		}

		$update_data['chunk_length'] = $expires_in;
	}

	// Times activated max
	if ( isset( $generator_data['times_activated_max'] ) ) {
		$expires_in = (int) $generator_data['times_activated_max'];

		if ( $expires_in > 4294967295 ) {
			return new WP_Error( 'The generator\'s times_activated_max cannot be larger than 4294967295.' );
		}

		$update_data['times_activated_max'] = $expires_in;
	}

	// Separator
	if ( isset( $generator_data['separator'] ) ) {
		$separator = sanitize_text_field( $generator_data['separator'] );

		if ( strlen( $separator ) > 255 ) {
			return new WP_Error( 'The generator\'s separator cannot exceed 255 characters.' );
		}

		$update_data['separator'] = $separator;
	}

	// Prefix
	if ( isset( $generator_data['prefix'] ) ) {
		$prefix = sanitize_text_field( $generator_data['prefix'] );

		if ( strlen( $prefix ) > 255 ) {
			return new WP_Error( 'The generator\'s prefix cannot exceed 255 characters.' );
		}

		$update_data['prefix'] = $prefix;
	}

	// Suffix
	if ( isset( $generator_data['suffix'] ) ) {
		$suffix = sanitize_text_field( $generator_data['suffix'] );

		if ( strlen( $suffix ) > 255 ) {
			return new WP_Error( 'The generator\'s suffix cannot exceed 255 characters.' );
		}

		$update_data['suffix'] = $suffix;
	}

	// Expires in
	if ( isset( $generator_data['expires_in'] ) ) {
		$expires_in = (int) $generator_data['expires_in'];

		if ( $expires_in > 4294967295 ) {
			return new WP_Error( 'The generator\'s expires_in cannot be larger than 4294967295.' );
		}

		$update_data['expires_in'] = $expires_in;
	}

	$generator = GeneratorResourceRepository::instance()->update( $generator_id, $update_data );

	if ( ! $generator ) {
		return new WP_Error( 'The generator could not be created.' );
	}

	return $generator;
}

function lmfwc_delete_generator( $generator_id ) {
	if ( ! is_array( $generator_id ) ) {
		$generator_id = (array) $generator_id;
	}

	$generator = GeneratorResourceRepository::instance()->delete( $generator_id );

	if ( ! $generator ) {
		return new WP_Error( 'The generator(s) could not be deleted.' );
	}

	return true;
}

function lmfwc_use_generator( $generator_id, $amount ) {
	$generator = GeneratorResourceRepository::instance()->find( (int) $generator_id );

	if ( ! $generator ) {
		return new WP_Error( 'The generator could not be found.' );
	}
	/**
	 * Filter lmfwc_generate_license_keys
	 *
	 * @since 1.0.0
	 */
	return apply_filters( 'lmfwc_generate_license_keys', (int) $amount, $generator );
}
