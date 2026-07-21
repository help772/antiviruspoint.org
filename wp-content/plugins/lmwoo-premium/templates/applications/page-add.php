<?php


defined('ABSPATH') || exit;
?>

<h1 class="wp-heading-inline"><?php echo esc_html__('Add new application', 'license-manager-for-woocommerce'); ?></h1>
<hr class="wp-header-end">

<div class="postbox">
	<div class="inside">
		<form method="post" action="<?php echo esc_url(admin_url('admin-post.php')) ; ?>">
			<input type="hidden" name="action" value="lmfwc_save_application">
			<?php wp_nonce_field('lmfwc_save_application'); ?>

			<table class="form-table">

				<tbody>
				<!-- NAME -->
				<tr scope="row">
					<th scope="row">
						<label for="name"><?php echo esc_html__('Name', 'license-manager-for-woocommerce'); ?>
							<span class="text-danger">*</span></label>
					</th>
					<td>
						<input name="name" id="name" class="regular-text" type="text" required>
						<p class="description" id="tagline-description">
							<b><?php echo esc_html__('Required.', 'license-manager-for-woocommerce'); ?></b>
							<span><?php echo esc_html__('A short name to describe the application.', 'license-manager-for-woocommerce'); ?></span>
						</p>
					</td>
				</tr>

				<!-- TYPE -->
				<tr scope="row">
					<th scope="row">
						<label for="type"><?php echo esc_html__('Type', 'license-manager-for-woocommerce'); ?></label>
						<span class="text-danger">*</span></label>
					</th>
					<td>
						<select id="type" name="type" class="regular-text">
							<?php foreach (  $applicationOptions as $key =>  $value ) : ?>
								<option value="<?php echo esc_attr($key); ?>"><?php echo esc_attr($value['name']); ?></option>
							<?php endforeach; ?>
						</select>
						<p class="description" id="tagline-description">
							<b><?php echo esc_html__('Required.', 'license-manager-for-woocommerce'); ?></b>
							<span><?php echo esc_html__('The type determines the metadata in the application release editor.', 'license-manager-for-woocommerce'); ?></span>
						</p>
					</td>
				</tr>

				</tbody>
			</table>
			<p class="submit">
				<input name="submit" id="submit" class="button button-primary" value="<?php echo esc_html__('Save' , 'license-manager-for-woocommerce'); ?>" type="submit">
			</p>
		</form>
	</div>
</div>
