<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Upload Profile Picture</title>

  <link rel="stylesheet" href="dealer/css/style.css" />
  <link rel="stylesheet" href="dealer/css/forms.css" />
  
</head>
<body>

<header>
  <h1>Upload Profile Picture</h1>
</header>

<main class="container">
  <div class="box" style="width: 90%; max-width: 500px; margin: auto;">

    <?php if (!empty($flash)): ?>
      <p style="color: green; margin: 10px 0;"><?php echo $flash; ?></p>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
      <div style="color:#b00020; margin: 10px 0;">
        <ul style="margin:0; padding-left:18px;">
          <?php foreach ($errors as $e): ?>
            <li><?php echo $e; ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <form method="post" action="index.php?url=dealer/save_photo" enctype="multipart/form-data">
      <label>Select JPG/JPEG photo (max 5MB)</label>
      <input type="file" name="profile_pic" accept=".jpg,.jpeg,image/jpeg" required />

      <div class="center" style="margin-top: 12px;">
        <button type="submit" class="btn">Upload</button>
      </div>
    </form>

    <div style="margin-top:12px;">
      <a class="btn" href="index.php?url=dealer/dashboard">← Back</a>
    </div>

  </div>
</main>

</body>
</html>
