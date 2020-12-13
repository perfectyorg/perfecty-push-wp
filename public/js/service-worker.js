self.addEventListener('push', async (event) => {
  // see: https://developer.mozilla.org/en-US/docs/Web/API/ExtendableEvent/waitUntil
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
