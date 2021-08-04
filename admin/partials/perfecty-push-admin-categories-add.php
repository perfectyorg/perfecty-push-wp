<div class="wrap">
	<div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
	<h1><?php printf( esc_html__( 'Add category', 'perfecty-push-notifications' ) ); ?></h1>

	<?php if ( ! empty( $notice ) ) : ?>
	<div id="notice" class="notice notice-warning"><p><?php echo esc_html( $notice ); ?></p></div>
	<?php endif; ?>
	<?php if ( ! empty( $message ) ) : ?>
	<div id="message" class="notice notice-success"><p><?php echo esc_html( $message ); ?></p></div>
	<?php endif; ?>

	<form id="form" method="POST">
		<input type="hidden" name="nonce" value="<?php echo esc_html( wp_create_nonce( 'perfecty_push_categories_add' ) ); ?>"/>

		<div class="metabox-holder" id="poststuff">
			<div id="post-body">
				<div id="post-body-content">
					<div class="formdata">
					<div>
						<p>
							<label for="perfecty-push-add-category-name"><?php printf( esc_html__( 'Title', 'perfecty-push-notifications' ) ); ?></label>
							<br>
							<input id="perfecty-push-add-category-name" name="perfecty-push-add-category-name" type="text" value="<?php echo esc_attr( $item['perfecty-push-add-category-name'] ); ?>" required>
						</p>
					</div>

					<input type="submit" value="<?php printf( esc_html__( 'Add category', 'perfecty-push-notifications' ) ); ?>" id="submit" class="button-primary" name="submit">
				</div>
			</div>
		</div>
	</form>
</div>
