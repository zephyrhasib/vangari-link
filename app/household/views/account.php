<?php
$areas = ["Select Area", "Dhaka", "Chattogram", "Khulna", "Rajshahi", "Sylhet", "Barishal", "Rangpur", "Mymensingh"];
$flash  = $flash ?? '';
$user   = $user ?? [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Seller Account</title>

  <link rel="stylesheet" href="household/css/style.css" />
  <link rel="stylesheet" href="household/css/forms.css" />

  <script src="household/js/AccountValidation.js" defer></script>
</head>
<body>

  <header>
    <h1>Seller - Account Management</h1>
  </header>

  <main class="container">

      <?php if (!empty($flash)): ?>
        <p style="color: green; margin: 10px 0;">
          <?php echo $flash; ?>
        </p>
      <?php endif; ?>

      <div style="display:flex; gap:20px; flex-wrap:wrap;">

        <div class="box" style="flex:1; min-width:320px;">
          <h2>Edit Profile</h2>

          <form id="profileForm" method="post" action="index.php?url=household/update_profile">
            <div class="form-group">
              <label>Name</label>
              <input type="text" name="name" value="<?php echo $user['name'] ?? ''; ?>" required>
            </div>

            <div class="form-group">
              <label>Area</label>
              <select name="area" required>
                <?php foreach ($areas as $a): ?>
                  <option
                    value="<?php echo $a; ?>"
                    <?php echo (($user['area'] ?? '') === $a) ? 'selected' : ''; ?>
                    <?php echo ($a === "Select Area") ? 'disabled' : ''; ?>
                  >
                    <?php echo $a; ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="form-group">
              <label>Phone</label>
              <input type="text" name="phone" value="<?php echo $user['phone'] ?? ''; ?>" required>
            </div>

            <div class="form-group">
              <label>Email</label>
              <input type="email" name="email" value="<?php echo $user['email'] ?? ''; ?>" required>
            </div>

            <button type="submit">Save Changes</button>
          </form>
        </div>

        <div class="box" style="flex:1; min-width:320px;">
          <h2>Security</h2>

          <h3>Change Password</h3>
          <form id="passwordForm" method="post" action="index.php?url=household/change_password">
            <div class="form-group">
              <label>Current Password</label>
              <input type="password" name="current_password" required>
            </div>

            <div class="form-group">
              <label>New Password</label>
              <input type="password" name="new_password" required>
            </div>

            <div class="form-group">
              <label>Confirm New Password</label>
              <input type="password" name="confirm_password" required>
            </div>

            <button type="submit">Update Password</button>
          </form>


          <h3 style="color:#b00020;">Delete Account</h3>
          <form id="deleteForm" method="post" action="index.php?url=household/delete_account">
            <p style="font-size:14px;">
              This will permanently delete your account.
            </p>

            <div class="form-group">
              <label>Type YES to confirm</label>
              <input type="text" name="confirm_delete" placeholder="YES" required>
            </div>

            <div class="form-group">
              <label>Your Password</label>
              <input type="password" name="delete_password" required>
            </div>

            <button type="submit">Delete My Account</button>
          </form>
        </div>

      </div>

      <div class="center" style="margin-top:15px;">
        <a class="btn" href="index.php?url=household/dashboard">← Back to Dashboard</a>
      </div>

  </main>
</body>
</html>
