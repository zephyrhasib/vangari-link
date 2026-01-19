<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Create Pickup Request</title>

  <link rel="stylesheet" href="household/css/style.css" />
  <link rel="stylesheet" href="household/css/forms.css" />
  <link rel="stylesheet" href="household/css/tables.css" />

  <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
  
</head>
<body>

<header>
  <h1>Create Pickup Request</h1>
  <p>Submit your scrap pickup request</p>
</header>

<main class="container">
  <div class="box" style="width:90%; max-width:650px; margin:auto;">

    <div id="msg" style="margin:10px 0;"></div>

    <form id="pickupForm" method="post">

      <label>Scrap Item</label>
      <select name="scrap_item_id" required>
        <option value="">-- Select --</option>
        <?php foreach ($items as $it): ?>
          <option value="<?php echo $it['id']; ?>">
            <?php echo $it['name']; ?> (<?php echo $it['unit']; ?>)
          </option>
        <?php endforeach; ?>
      </select>

      <label>Estimated Weight (kg)</label>
      <input type="number" step="0.01" name="estimated_weight" required />

      <label>Phone</label>
      <input type="text" name="contact_phone"
             value="<?php echo $prefillPhone; ?>" required />

      <label>Your Area (from profile)</label>
      <input type="text" value="<?php echo $sellerArea; ?>"
             readonly
             style="max-width:260px; background:#f3f3f3;" />
      <p style="font-size:12px; color:#555;">
        To change area, update your profile.
      </p>

      <label>Address</label>
      <textarea name="address" rows="3" required></textarea>

      <label>Desired Pickup Date & Time</label>
      <input type="datetime-local" name="desired_datetime" required />

      <div class="center" style="margin-top:12px;">
        <button type="submit" class="btn">Submit Request</button>
      </div>

    </form>

    <div class="center" style="margin-top:12px;">
      <a class="btn" href="index.php?url=household/dashboard">Back</a>
    </div>

  </div>
</main>

<script>
$(function () {

  $('#pickupForm').on('submit', function (e) {
    e.preventDefault();
    $('#msg').html('');

    $.ajax({
      url: 'index.php?url=household/submit_request',
      type: 'POST',
      data: $('#pickupForm').serialize(),
      success: function (res) {

        if (typeof res === 'string') {
          try { res = JSON.parse(res); } catch (e) {}
        }

        if (res && res.success) {
          $('#msg').html('<p style="color:green;">' + res.message + '</p>');
          $('#pickupForm')[0].reset();
        } else {
          var out = '<div style="color:#b00020;"><ul>';
          if (res && res.errors) {
            for (var i = 0; i < res.errors.length; i++) {
              out += '<li>' + res.errors[i] + '</li>';
            }
          } else {
            out += '<li>Something went wrong.</li>';
          }
          out += '</ul></div>';
          $('#msg').html(out);
        }
      },
      error: function () {
        $('#msg').html('<p style="color:#b00020;">Server error.</p>');
      }
    });

  });

});
</script>

</body>
</html>