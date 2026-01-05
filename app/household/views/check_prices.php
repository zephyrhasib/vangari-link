<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Market Prices</title>

  <link rel="stylesheet" href="css/style.css" />
  <link rel="stylesheet" href="css/forms.css" />
  <link rel="stylesheet" href="css/tables.css" />
</head>
<body>

  <header>
    <h1>Current Market Prices</h1>
  </header>

  <main class="container">
    
      <h2>Today & Yesterday</h2>

      <table class="simple-table">
        <thead>
          <tr>
            <th>Item</th>
            <th class="num">Today (Tk/kg)</th>
            <th class="num">Yesterday (Tk/kg)</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($rows)): ?>
            <?php foreach ($rows as $r): ?>
              <tr>
                <td><?php echo $r['item_name'] ?? ''; ?></td>
                <td class="num">
                  <?php echo ($r['today_price'] !== null) ? number_format((float)$r['today_price'], 2) : 'N/A'; ?>
                </td>
                <td class="num">
                  <?php echo ($r['yesterday_price'] !== null) ? number_format((float)$r['yesterday_price'], 2) : 'N/A'; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr><td colspan="3" class="empty">No price data available yet.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>

      <div class="center" style="margin-top: 15px;">
        <a class="btn" href="index.php?url=household/dashboard">Back to Dashboard</a>
      </div>
    
  </main>

</body>
</html>
