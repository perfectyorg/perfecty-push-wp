<div class="wrap">
	<h1>Perfecty Push</h1>
	<div class="perfecty-push-stats">
		<div>
			<h3>Users</h3>
			<div class="perfecty-push-stats-text">
				<span>Total users: </span><span><?php echo $users_stats['total']; ?></span>
			</div>
			<div class="perfecty-push-stats-text">
				<span>Active: </span><span><?php echo $users_stats['active']; ?></span>
			</div>
			<div class="perfecty-push-stats-text">
				<span>Inactive: </span><span><?php echo $users_stats['inactive']; ?></span>
			</div>
		</div>
		<div>
			<h3>Notifications</h3>
			<div class="perfecty-push-stats-text">
				<span>Total notifications: </span><span><?php echo $notifications_stats['total']; ?></span>
			</div>
			<div class="perfecty-push-stats-text">
				<span>Succeeded: </span><span><?php echo $notifications_stats['succeeded']; ?></span>
			</div>
			<div class="perfecty-push-stats-text">
				<span>Failed: </span><span><?php echo $notifications_stats['failed']; ?></span>
			</div>
		</div>
		<div>
			<h3>Jobs</h3>
			<div class="perfecty-push-stats-text">
				<span>Completed: </span><span><?php echo $jobs_stats['completed']; ?></span>
			</div>
			<div class="perfecty-push-stats-text">
				<span>Running: </span><span><?php echo $jobs_stats['running']; ?></span>
			</div>
			<div class="perfecty-push-stats-text">
				<span>Scheduled: </span><span><?php echo $jobs_stats['scheduled']; ?></span>
			</div>
			<div class="perfecty-push-stats-text">
				<span>Failed: </span><span><?php echo $jobs_stats['failed']; ?></span>
			</div>
		</div>
	</div>
</div>
