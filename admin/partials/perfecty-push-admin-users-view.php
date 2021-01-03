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
				<div><?php echo $item->uuid; ?></div>
				</p>
				<p>
					<label for="ip"><?php printf( esc_html__( 'IP Address:', 'perfecty-push-notifications' ) ); ?> </label>
					<br>
				<div><?php echo $item->remote_ip; ?></div>
				</p>
				<p>
					<label for="endpoint"><?php printf( esc_html__( 'Registered endpoint:', 'perfecty-push-notifications' ) ); ?> </label>
					<br>
				<div><?php echo $item->endpoint; ?></div>
				</p>
				<p>
					<label for="created_at"><?php printf( esc_html__( 'Registered at:', 'perfecty-push-notifications' ) ); ?> </label>
					<br>
				<div><?php echo $item->created_at; ?></div>
				</p>
				<p>
					<label for="key_auth"><?php printf( esc_html__( 'Key auth:', 'perfecty-push-notifications' ) ); ?> </label>
					<br>
				<div><?php echo $item->key_auth; ?></div>
				</p>
				<p>
					<label for="key_p256dh"><?php printf( esc_html__( 'Key p256dh:', 'perfecty-push-notifications' ) ); ?> </label>
					<br>
				<div><?php echo $item->key_p256dh; ?></div>
				</p>
				<p>
					<label for="is_active"><?php printf( esc_html__( 'Is active:', 'perfecty-push-notifications' ) ); ?> </label>
					<br>
				<div><?php echo $item->is_active == 1 ? esc_html__( 'Yes', 'perfecty-push-notifications' ) : esc_html__( 'No', 'perfecty-push-notifications' ); ?></div>					
				</p>
				<p>
					<label for="disabled"><?php printf( esc_html__( 'Disabled: ', 'perfecty-push-notifications' ) ); ?></label>
					<br>
				<div><?php echo $item->disabled == 1 ? esc_html__( 'Yes', 'perfecty-push-notifications' ) : esc_html__( 'No', 'perfecty-push-notifications' ); ?></div>
				</p>
			</div>
		</form>
	</div>
	<a href="?page=<?php echo $page; ?>"><?php printf( esc_html__( 'Back', 'perfecty-push-notifications' ) ); ?></a>
</div>
