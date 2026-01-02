<?php
$areas = ["Select Area", "Dhaka", "Chattogram", "Khulna", "Rajshahi", "Sylhet", "Barishal", "Rangpur", "Mymensingh"];

$errors = $errors ?? [];
$old = $old ?? [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Register as Buyer</title>
  <link rel="stylesheet" href="css/forms.css" />
</head>
<body class="form-page">

  <div class="form-wrap">
    <h1>Buyer Registration</h1>

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

    <form method="post" action="index.php?url=auth/register_buyer">

      <div class="form-group">
        <label>Owner Name</label>
        <input
          type="text"
          name="owner_name"
          placeholder="Enter owner name"
          value="<?php echo $old['owner_name'] ?? ''; ?>"
          required
        >
      </div>

      <div class="form-group">
        <label>Shop Name</label>
        <input
          type="text"
          name="shop_name"
          placeholder="Enter shop name"
          value="<?php echo $old['shop_name'] ?? ''; ?>"
          required
        >
      </div>

      <div class="form-group">
        <label>Area</label>
        <select name="area" required>
          <?php foreach ($areas as $a): ?>
            <option
              value="<?php echo $a; ?>"
              <?php echo (($old['area'] ?? '') === $a) ? 'selected' : ''; ?>
            >
              <?php echo $a; ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="form-group">
        <label>Phone</label>
        <input
          type="text"
          name="phone"
          placeholder="01XXXXXXXXX"
          value="<?php echo $old['phone'] ?? ''; ?>"
          required
        >
      </div>

      <div class="form-group">
        <label>Email</label>
        <input
          type="email"
          name="email"
          placeholder="example@mail.com"
          value="<?php echo $old['email'] ?? ''; ?>"
          required
        >
      </div>

      <div class="form-group">
        <label>Password</label>
        <input type="password" name="password" required>
      </div>

      <div class="form-group">
        <label>Confirm Password</label>
        <input type="password" name="confirm_password" required>
      </div>

      <button type="submit">Register</button>
    </form>

    <a class="back-link" href="index.php">Back to Home</a>
    <a class="back-link" href="index.php?url=auth/login">Already have account? Login</a>
  </div>

</body>
</html>
