<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Buyer Dashboard</title>
  <link rel="stylesheet" href="css/style.css" />
</head>
<body>

  <header>
    <h1>Buyer Dashboard</h1>
    <p>Welcome, <?php echo $_SESSION['name'] ?? ''; ?></p>
  </header>

  <main class="container">
    <div class="box" style="width: 90%; margin: auto;">
      <h2>Menu</h2>
      <ul>
        <li><a href="index.php?url=dealer/account">Manage your Account</a></li>
        <li><a href="index.php?url=dealer/manage_prices">Update Scrap Price</a></li>
        <li><a href="index.php?url=dealer/manage_requests">Manage Pickup Requests</a></li>
      </ul>

      <div class="center">
        <a class="btn" href="index.php?url=auth/logout">Logout</a>
      </div>
    </div>
  </main>

</body>
</html>
