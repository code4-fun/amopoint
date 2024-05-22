<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = $_POST['username'];
  $password = $_POST['password'];

  if ($username === 'admin' && $password === '1') {
    $_SESSION['loggedin'] = true;
    header('Location: index.php');
    exit();
  } else {
    $error = "Invalid username or password";
  }
}

if (isset($_GET['logout'])) {
  unset($_SESSION['loggedin']);
  header('Location: http://localhost');
  exit;
}

if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
  $dsn = 'sqlite:sqlite.db';
  $options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  ];

  try {
    $pdo = new PDO($dsn, null, null, $options);

    $hourlyVisits = $pdo->query("
        SELECT strftime('%H', visit_time) as hour, COUNT(DISTINCT ip) as count
        FROM visits
        GROUP BY hour
        ORDER BY hour
    ")->fetchAll();

    $cityVisits = $pdo->query("
        SELECT city, COUNT(DISTINCT ip) as count
        FROM visits
        GROUP BY city
        ORDER BY count DESC
    ")->fetchAll();
  } catch (Exception $e) {
    error_log($e->getMessage());
    exit('Error connecting to the database');
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Visitor Statistics</title>
  <link rel="stylesheet" href="style.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/moment@2.29.1"></script>
  <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-moment@1.0.0"></script>
</head>
<body>

<div class="wrapper">
  <header class="header">
    <div class="header_container container">
      <div class="header_left">
        <a href="http://localhost">Main</a>
      </div>
      <div class="header_right">
        <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true): ?>
          <a href="?logout=true">Logout</a>
        <?php endif; ?>
      </div>
    </div>
  </header>

  <main class="page">
    <div class="main_container container">

      <?php if (!isset($_SESSION['loggedin'])): ?>
        <form method="post" class="form_container auth-form">
          <?php if (isset($error)) echo "<div class='error-msg'>$error</div>"; ?>
          <input type="text" name="username" placeholder="Username">
          <input type="password" name="password" placeholder="Password">
          <input type="submit" value="Sign in" />
        </form>
      <?php else: ?>
        <div class="title_1">Visitor Statistics</div>
        <div class="title_2">Visits per Hour</div>
        <div class="hourly_chart_container">
          <canvas id="hourlyChart"></canvas>
        </div>
        <div class="title_2">Visits by City</div>
        <div class="city_chart_container">
          <canvas id="cityChart"></canvas>
        </div>

        <script>
          const hourlyLabels = <?= json_encode(array_map(function($visit) { return $visit['hour'] . ':00'; }, $hourlyVisits)) ?>;
          const hourlyData = <?= json_encode(array_column($hourlyVisits, 'count')) ?>;

          const cityLabels = <?= json_encode(array_column($cityVisits, 'city')) ?>;
          const cityData = <?= json_encode(array_column($cityVisits, 'count')) ?>;

          const ctxHourly = document.getElementById('hourlyChart').getContext('2d');
          const hourlyChart = new Chart(ctxHourly, {
            type: 'bar',
            data: {
              labels: hourlyLabels,
              datasets: [{
                label: 'Visits',
                data: hourlyData,
                backgroundColor: 'rgba(75, 192, 192, 0.3)',
              }]
            },
            options: {
              indexAxis: 'y',
              responsive: true,
              maintainAspectRatio: true,
              scales: {
                x: {
                  beginAtZero: true,
                  title: {
                    display: true,
                    text: 'Number of unique visits'
                  }
                },
                y: {
                  title: {
                    display: true,
                    text: 'Time (hours)'
                  }
                }
              }
            }
          });

          const ctxCity = document.getElementById('cityChart').getContext('2d');
          const cityChart = new Chart(ctxCity, {
            type: 'pie',
            data: {
              labels: cityLabels,
              datasets: [{
                label: 'Visits by City',
                data: cityData,
                backgroundColor: [
                  'rgba(255, 99, 132, 0.3)',
                  'rgba(54, 162, 235, 0.3)',
                  'rgba(255, 206, 86, 0.3)',
                  'rgba(75, 192, 192, 0.3)',
                  'rgba(153, 102, 255, 0.3)',
                  'rgba(255, 159, 64, 0.3)'
                ],
              }]
            }
          });

          window.addEventListener('resize', function() {
            hourlyChart.resize();
            cityChart.resize();
          });
        </script>
      <?php endif; ?>

    </div>
  </main>
  <footer class="footer">
    <div class="container">
      &copy; Statistics 2024
    </div>
  </footer>
</div>
</body>
</html>
