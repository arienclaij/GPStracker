<!DOCTYPE html>
<html>
<head>
  <title>GPS Positie Verzenden</title>
  <style>
    /* Voeg CSS toe voor scrollbare lijst */
    #statusList {
      max-height: 200px;
      overflow-y: scroll;
      border: 1px solid #ccc;
      padding: 5px;
    }
  </style>
</head>
<body>
  <h1>GPS Positie Verzenden</h1>
  <button onclick="startTracking()">Start Tracking</button>
  <button onclick="stopTracking()">Stop Tracking</button>
  <button onclick="clearStatus()">Wis log</button>

  <ul id="statusList"></ul> <!-- Lijst voor statusberichten -->

  <script>
    let trackingInterval;

    function startTracking() {
      console.log('Start tracking aangeroepen');
      sendGPS();
      trackingInterval = setInterval(sendGPS, 15000); // Verzend elke 15 seconden
    }

    function stopTracking() {
      clearInterval(trackingInterval);
    }

    function sendGPS() {
      if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
          const latitude = position.coords.latitude;
          const longitude = position.coords.longitude;
          const timestamp = new Date().toISOString(); // Haal de huidige tijd op

          // Verzend de GPS-positie en tijd naar PHP-script
          sendDataToPHP(latitude, longitude, timestamp);
        });
      } else {
        console.log('Geolocation is niet ondersteund.');
      }
    }

    function sendDataToPHP(latitude, longitude, timestamp) {
      const xhr = new XMLHttpRequest();
      const url = 'verwerk_gps.php'; // Vervang 'verwerk_gps.php' met je PHP-script
      const statusList = document.getElementById('statusList');

      xhr.open('POST', url, true);
      xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
      xhr.onreadystatechange = function() {
        if (xhr.readyState === XMLHttpRequest.DONE) {
          if (xhr.status === 200) {
            addStatusToList(timestamp, 'GPS-positie succesvol verzonden.', statusList);
          } else {
            addStatusToList(timestamp, 'Fout bij verzenden van GPS-positie.', statusList);
          }
        }
      };

      const data = `latitude=${latitude}&longitude=${longitude}&timestamp=${timestamp}`;
      xhr.send(data);
    }

    function addStatusToList(timestamp, message, listElement) {
      const listItem = document.createElement('li');
      listItem.textContent = `${timestamp}: ${message}`;
      listElement.appendChild(listItem);

      // Scroll naar het laatste item in de lijst
      listElement.scrollTop = listElement.scrollHeight;
    }
    
    function clearStatus() {
      const statusList = document.getElementById('statusList');
      statusList.innerHTML = ''; // Verwijder alle inhoud van de lijst
    }
  </script>
</body>
</html>
