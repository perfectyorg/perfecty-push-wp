	'use strict';

	function checkFeatures() {
		return ('serviceWorker' in navigator) && ('PushManager' in window);
	}
	
	function registerServiceWorker(path, siteUrl, vapidPublicKey64, nonce) {
			navigator.serviceWorker.register(path + '/service-worker-loader.js.php', { scope: '/' }).then(() =>{
				return navigator.serviceWorker.ready
			}).then(async (registration) => {
				// we get the push subscription
				const subscription = await registration.pushManager.getSubscription();
				if (subscription) {
					return subscription;
				}
				const vapidPublicKey = urlBase64ToUint8Array(vapidPublicKey64);
				return registration.pushManager.subscribe({
					userVisibleOnly: true,
					applicationServerKey: vapidPublicKey
				});
			}).then((subscription) => {
				// we send the registration details to the server
				path = siteUrl + "/wp-json/perfecty-push/v1/register?_wpnonce=" + nonce
				const payload = {
					subscription: subscription
				}

				fetch(path, {
					method: 'post',
					headers: {
						'Content-type': 'application/json'
					},
					body: JSON.stringify(payload)
				})
				.then(resp => resp.json())
				.then(data => {
					if (data && data.result && data.result !== true){
						return Promise.reject("Unable to send the registration details")
					}
				})
				.catch(err => {
					console.log("Error when sending the registration details", err)
				})
			}).catch(err => {
				console.log('Unable to register the service worker', err)
			})
	}

	async function askForPermission() {
		let permission = window.Notification.permission
		if (permission !== 'denied') {
			permission = await window.Notification.requestPermission();
		}
		return permission;
	}
	
	async function drawFabControl(options) {
		let fabControl =
		'<div class="perfecty-fab-container" id="perfecty-fab-container">' +
		'  <div class="perfecty-fab-notify-box">' +
		'    <div class="perfecty-fab-title">' + options.title + '</div>' +
		'    <div>' + 
		'      <button id="perfecty-fab-cancel" type="button" class="secondary">' + options.cancel + '</button>' +
		'      <button id="perfecty-fab-subscribe" type="button" class="primary">' + options.submit + '</button> ' +
		'    </div>' +
		'  </div>' +
		'</div>';
		document.body.insertAdjacentHTML('beforeend', fabControl);
	}

	function showFabControl() {
		const fabContainer = document.getElementById('perfecty-fab-container');
		fabContainer.style.display = "block";
	}

	function hideFabControl() {
		const fabContainer = document.getElementById('perfecty-fab-container');
		fabContainer.style.display = "none";
	}

	// taken from https://github.com/mozilla/serviceworker-cookbook/blob/e912ace6e9566183e06a35ef28516af7bd1c53b2/tools.js
	function urlBase64ToUint8Array(base64String) {
		var padding = '='.repeat((4 - base64String.length % 4) % 4);
		var base64 = (base64String + padding)
			.replace(/\-/g, '+')
			.replace(/_/g, '/');
	 
		var rawData = window.atob(base64);
		var outputArray = new Uint8Array(rawData.length);
	 
		for (var i = 0; i < rawData.length; ++i) {
			outputArray[i] = rawData.charCodeAt(i);
		}
		return outputArray;
	}
	
	async function perfectyStart(options) {
		if (checkFeatures()) {
			// Draw bell
			drawFabControl(options.fabControl);

	
			// Notification permission
			let permission = Notification.permission;
			let askedForNotifications = localStorage.getItem("perfecty_asked_notifications") === "yes";
			if (permission === 'default' && !askedForNotifications) {
				showFabControl();
			}

			document.getElementById('perfecty-fab-subscribe').onclick = async () => {
				localStorage.setItem("perfecty_asked_notifications", "yes");
				hideFabControl();
				permission = await askForPermission();
				if (permission === 'granted'){
					// We only register the service worker and the push manager
					// when the user has granted permissions
					registerServiceWorker(options.path, options.siteUrl, options.vapidPublicKey, options.nonce);
				} else {
					console.log('Notification permission not granted')
				}
			};

			document.getElementById('perfecty-fab-cancel').onclick = async () => {
				localStorage.setItem("perfecty_asked_notifications", "yes");
				hideFabControl();
			}

		} else {
			console.log('Browser doesn\'t support notifications');
		}
	}

	window.onload = () => {
		// defined outside in the html body
		const options = window.PerfectyPushOptions;
		perfectyStart(options);
	};