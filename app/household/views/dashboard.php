<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Seller Dashboard</title>
  <link rel="stylesheet" href="household/css/style.css" />
</head>
<body>

   <header>
  <h1>Seller Dashboard</h1>

  <?php $pic = $_SESSION['profile_pic'] ?? ''; ?>

  <div class="center" style="margin: 15px 0;">
    <?php if ($pic): ?>
      <img src="<?php echo $pic; ?>"
           style="width:90px; height:90px; border-radius:50%; object-fit:cover; border:2px solid #999;">
    <?php else: ?>
      <div style="width:90px; height:90px; border-radius:50%; border:2px solid #999; display:inline-block;"></div>
    <?php endif; ?>
  </div>

  <p>Welcome, <?php echo $_SESSION['name'] ?? ''; ?></p>
</header>

  <main class="container">
    <div class="box" style="width: 90%; margin: auto;">
      <h2>Menu</h2>
      <ul>
        <li><a href="index.php?url=household/account">Manage your Account</a></li>
        <li><a href="index.php?url=household/check_prices">Check Current Market Prices</a></li>
        <li><a href="index.php?url=household/create_request">Create Pickup Request</a></li>
        <li><a href="index.php?url=household/order_tracking">Track Order Status</a></li>
        <li><a href="index.php?url=household/upload_photo">Upload Profile Picture</a></li>
      </ul>

      <div class="center">
        <a class="btn" href="index.php?url=auth/logout">Logout</a>
      </div>
    </div>
  </main>

</body>
</html>
