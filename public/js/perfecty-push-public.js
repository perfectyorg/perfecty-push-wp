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
	
	async function drawFabControl(options) {
		let fabControl = '<div class="perfecty-fab-container"><div class="perfecty-fab-title">' + options.title + '</div><div>' + 
		'<button id="perfecty-fab-cancel" type="button" class="secondary">' + options.cancel + '</button>' +
		'<button id="perfecty-fab-subscribe" type="button" class="primary">' + options.submit + '</button> ' +
		'</div></div>';
		$("body").append(fabControl);
	}

	function showFabControl() {
		$(".perfecty-fab-container").show();
	}

	function hideFabControl() {
		$(".perfecty-fab-container").hide();
	}
	
	async function perfectyStart(options) {
		if (checkFeatures()) {
			// Draw bell
			drawFabControl(options.fabControl);

			// Service worker
			const swRegistration = await registerServiceWorker(options.path);
	
			// Notification permission
			let permission = Notification.permission;
			let askedForNotifications = localStorage.getItem("perfecty_asked_notifications") === "yes";
			if (permission === 'default' && !askedForNotifications) {
				showFabControl();
			}

			$("#perfecty-fab-subscribe").click(async function() {
				localStorage.setItem("perfecty_asked_notifications", "yes");
				hideFabControl();
				permission = await askForPermission();
			});

			$("#perfecty-fab-cancel").click(async function(){
				localStorage.setItem("perfecty_asked_notifications", "yes");
				hideFabControl();
			})

		} else {
			console.log('Browser doesn\'t support notifications');
		}
	}

	$(window).load(function() {
		// window.PerfectyPushOptions is defined outside
		const options = window.PerfectyPushOptions;
		perfectyStart(options);
	});

})(jQuery);