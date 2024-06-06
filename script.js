<!-- Add this code to the HTML file of your website -->

async function getVisitorInfo() {
  try {
    const response = await fetch('https://ipinfo.io?token=6f65bcf1d105e8');
    const data = await response.json();
    return {
      ip: data.ip,
      city: data.city,
      device: navigator.userAgent
    };
  } catch (error) {
    console.error('Error retrieving visitor data:', error);
    return null;
  }
}

async function sendVisitorData() {
  const visitorInfo = await getVisitorInfo();
  if (visitorInfo) {
    try {
      await fetch('http://localhost/track.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(visitorInfo)
      });
    } catch (error) {
      console.error('Error sending data to the server:', error);
    }
  }
}

window.onload = sendVisitorData;
