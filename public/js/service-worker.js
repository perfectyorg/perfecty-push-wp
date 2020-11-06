self.addEventListener('push', async (event) => {
  // It's important that we use waitUntil to tell the
  // browser we are doing some work
  // see: https://developer.mozilla.org/en-US/docs/Web/API/ExtendableEvent/waitUntil
  event.waitUntil(getContent(event).then(data => {
    const title = data.title
    const options = {
      body: data.body,
      icon: data.icon
    }

    self.registration.showNotification(title, options)
    return Promise.resolve()
  }).catch(err => {
    console.log("Error receiving push notification", err);
  }));
})

const getContent = async (event) => {
  if (!event.data) {
    return Promise.reject("No payload was sent in the push message")
  }
  const data = event.data.json()
  return Promise.resolve(data)
}