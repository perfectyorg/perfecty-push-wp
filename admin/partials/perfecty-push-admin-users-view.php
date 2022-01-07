<div class="wrap">
	<div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
	<h1><?php printf( esc_html__( 'User details', 'perfecty-push-notifications' ) ); ?></h1>

	<tbody>
	<div class="formdata perfecty-push-view-form">
		<form>
			<div>
				<p>
					<label for="uuid"><?php printf( esc_html__( 'UUID:', 'perfecty-push-notifications' ) ); ?> </label>
					<br>
				<div><?php echo esc_html( $item->uuid ); ?></div>
				</p>
				<?php if ( $segmentation_enabled ) { ?>
				<p>
					<label for="ip"><?php printf( esc_html__( 'IP Address:', 'perfecty-push-notifications' ) ); ?> </label>
					<br>
				<div><?php echo esc_html( $item->remote_ip ); ?></div>
				</p>
				<?php } ?>
				<p>
					<label for="endpoint"><?php printf( esc_html__( 'Registered endpoint:', 'perfecty-push-notifications' ) ); ?> </label>
					<br>
				<div><?php echo esc_html( $item->endpoint ); ?></div>
				</p>
				<p>
					<label for="created_at"><?php printf( esc_html__( 'Registered at:', 'perfecty-push-notifications' ) ); ?> </label>
					<br>
				<div><?php echo get_date_from_gmt( esc_html( $item->created_at ) ); ?></div>
				</p>
				<p>
					<label for="key_auth"><?php printf( esc_html__( 'Key auth:', 'perfecty-push-notifications' ) ); ?> </label>
					<br>
				<div><?php echo esc_html( $item->key_auth ); ?></div>
				</p>
				<p>
					<label for="key_p256dh"><?php printf( esc_html__( 'Key p256dh:', 'perfecty-push-notifications' ) ); ?> </label>
					<br>
				<div><?php echo esc_html( $item->key_p256dh ); ?></div>
				</p>
			</div>
		</form>
	</div>
	<a href="?page=<?php echo esc_html( $page ); ?>"><?php printf( esc_html__( 'Back', 'perfecty-push-notifications' ) ); ?></a>
</div>
