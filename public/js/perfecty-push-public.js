(function ($) {

	function checkFeatures() {
		return ('serviceWorker' in navigator) && ('PushManager' in window);
	}
	
	async function registerServiceWorker(path) {
		try {
			const registration = await navigator.serviceWorker.register(path + 'service-worker.js.php', { scope: '/' });
			console.log('Successful');
			return registration;
		} catch (err) {
			console.log('Unable to register the service worker', err);
		}
	}
	
	async function askForPermission() {
		const permission = await window.Notification.requestPermission();
		if (permission !== 'granted') {
			console.log('Notification permission not granted')
		}
		return permission;
	}
	
	async function drawFabControl(title) {
		$("body").append('<input type="button" id="perfecty-fab-control" value="' + title + '"></input>');
	}
	
	async function perfectyStart(options) {
		if (checkFeatures()) {
			// Service worker
			const swRegistration = await registerServiceWorker(options.path);
	
			// Notification permission
			let permission = Notification.permission;
			if (permission === 'default') {
				permission = await askForPermission();
			}
	
			// Draw bell
			drawFabControl(options.fabTitle);
		} else {
			console.log('Browser doesn\'t support sw or web push');
		}
	}

	$(window).load(function() {
		// window.PerfectyPushOptions is defined outside
		const options = window.PerfectyPushOptions;
		perfectyStart(options);
	});

})(jQuery);