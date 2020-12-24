self.addEventListener('push', async (event) => {
  event.waitUntil(getContent(event).then(data => {
    const title = data.title
    const options = {
      body: data.body,
      icon: data.icon,
      image: data.image,
      data: {
        url: data.extra.url_to_open
      }
    }

    self.registration.showNotification(title, options)
    return Promise.resolve()
  }).catch(err => {
    console.log("Error receiving push notification", err);
  }))
})

self.addEventListener('notificationclick', event => {
  const url = event.notification.data.url

  event.notification.close()

  event.waitUntil(openWindow(url))
})

self.addEventListener('install', event => {
  // This is required when there is a third-party installation already
  self.skipWaiting()
})

const getContent = async (event) => {
  if (!event.data) {
    return Promise.reject("No payload was sent in the push message")
  }
  const data = event.data.json()
  return Promise.resolve(data)
}

const openWindow = (url) => {
  clients.openWindow(url)

  return Promise.resolve()
}
