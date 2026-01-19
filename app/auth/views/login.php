<?php
$errors = $errors ?? [];
$old = $old ?? [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login</title>
  <link rel="stylesheet" href="auth/css/forms.css" />
</head>
<body class="form-page">

  <div class="form-wrap">
    <h1>Login</h1>

    <?php if (!empty($errors)): ?>
      <div style="border:1px solid #cc0000; padding:10px; margin-bottom:15px;">
        <b>Please fix:</b>
        <ul>
          <?php foreach ($errors as $e): ?>
            <li><?php echo $e; ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <form method="post" action="index.php?url=auth/login">
      <div class="form-group">
        <label>Email or Phone</label>
        <input type="text" name="login"
               value="<?php echo $old['login'] ?? ''; ?>"
               required>
      </div>

      <div class="form-group">
        <label>Password</label>
        <input type="password" name="password" required>
      </div>

      <button type="submit">Login</button>
    </form>

    <a class="back-link" href="index.php">Back to Home</a>
    <a class="back-link" href="index.php?url=auth/register_seller">Register as Seller</a>
    <a class="back-link" href="index.php?url=auth/register_buyer">Register as Buyer</a>
  </div>

</body>
</html>
