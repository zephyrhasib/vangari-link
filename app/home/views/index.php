<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Vangari-Link</title>


  <link rel="stylesheet" href="home/css/style.css" />
</head>
<body>

  <header>
    <h1>Web-Based Scrap Management System</h1>
    <p>Vangari-Link</p>
  </header>

  <main>

    <section class="two-columns">

      <div class="box">
        <h2>For Households (Seller)</h2>
        <ul>
          <li>Post Scrap Pickup Request</li>
          <li>Check Scrap Prices</li>
          <li>Track Your Orders</li>
        </ul>

        <a class="btn" href="index.php?url=auth/register_seller">
          Register as Seller
        </a>
      </div>

      <div class="box">
        <h2>For Scrap Dealers (Buyer)</h2>
        <ul>
          <li>View Pickup Requests</li>
          <li>Update Scrap Rates</li>
          <li>Complete Orders</li>
        </ul>

        <a class="btn" href="index.php?url=auth/register_buyer">
          Register as Buyer
        </a>
      </div>

    </section>

    <section class="center">
        <a class="btn" href="index.php?url=auth/login">
          Login
        </a>
    </section>
  </main>

  <footer>
    <nav class="footer-links">
      <a href="index.php?url=home/about">About Us</a>
      <a href="index.php?url=home/howitworks">How It Works</a>
      <a href="index.php?url=home/contact">Contact Us</a>
    </nav>
  </footer>

</body>
</html>
