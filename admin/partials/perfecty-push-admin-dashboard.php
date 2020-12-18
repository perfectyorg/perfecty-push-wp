<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.bundle.min.js" integrity="sha512-SuxO9djzjML6b9w9/I07IWnLnQhgyYVSpHZx0JV97kGBfTIsUYlWflyuW4ypnvhBrslz1yJ3R+S14fdCWmSmSA==" crossorigin="anonymous"></script>
<script>
	window.onload = function () {
		const colors = {
			red: 'rgb(255, 99, 132)',
			blue: 'rgb(54, 162, 235)'
		};
		const config = {
			type: 'line',
			data: {
				datasets: [{
					label: 'Failed',
					backgroundColor: colors.red,
					borderColor: colors.red,
					data: [
						<?php
						foreach ( $daily_stats as $item ) {
							echo "{ t: \"{$item->date}\",\n";
							echo "  y: {$item->failed} },\n";
						}
						?>
					],
					fill: false,
				}, {
					label: 'Succeeded',
					fill: false,
					backgroundColor: colors.blue,
					borderColor: colors.blue,
					data: [
						<?php
						foreach ( $daily_stats as $item ) {
							echo "{ t: \"{$item->date}\",\n";
							echo "  y: {$item->succeeded} },\n";
						}
						?>
					],
				}]
			},
			options: {
				responsive: true,
				title: {
					display: true,
					text: 'Daily Notifications'
				},
				tooltips: {
					mode: 'index',
					intersect: false,
				},
				hover: {
					mode: 'nearest',
					intersect: true
				},
				scales: {
					xAxes: [{
						type: 'time',
						display: true,
						scaleLabel: {
							display: true,
							labelString: 'Date'
						},
						time: {
							unit: 'day'
						}
					}],
					yAxes: [{
						display: true,
						scaleLabel: {
							display: true,
							labelString: 'Notifications'
						}
					}]
				}
			}
		};

		const ctx = document.getElementById('daily-notifications').getContext('2d');
		window.myLine = new Chart(ctx, config);
	}

</script>
<div class="wrap">
	<h1>Perfecty Push</h1>
	<div style="width: 800px;">
		<canvas id="daily-notifications"></canvas>
	</div>
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
