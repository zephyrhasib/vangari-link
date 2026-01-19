<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Manage Prices</title>

  <link rel="stylesheet" href="dealer/css/style.css" />
  <link rel="stylesheet" href="dealer/css/forms.css" />
  <link rel="stylesheet" href="dealer/css/tables.css" />
</head>
<body>

  <header>
    <h1>Price Submission</h1>
  </header>

  <main class="container">
    
      <h2>Submit Your Prices for Today</h2>

      <?php if (!empty($flash)): ?>
        <p style="color: green; margin: 10px 0;">
          <?php echo $flash; ?>
        </p>
      <?php endif; ?>

      <?php if (!empty($errors) && is_array($errors)): ?>
        <div style="color: #b00020; margin: 10px 0;">
          <strong>Please fix:</strong>
          <ul>
            <?php foreach ($errors as $e): ?>
              <li><?php echo (string)$e; ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <?php if (!empty($alreadySubmittedToday)): ?>
        <p style="color:#666; margin:10px 0;">
          You already submitted prices today.
        </p>
      <?php endif; ?>

      <form method="post" action="index.php?url=dealer/save_prices">
        <table class="simple-table">
          <thead>
            <tr>
              <th>Item</th>
              <th class="num">Today Market</th>
              <th class="num">Yesterday Market</th>
              <th class="num">7-day Avg</th>
              <th class="num">30-day Avg</th>
              <th class="num">Your Today Price</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($rows)): ?>
              <?php foreach ($rows as $r): ?>
                <?php
                  $itemId = (int)($r['item_id'] ?? 0);
                  $name = $r['item_name'] ?? '';

                  $t = $r['today_market'];
                  $y = $r['yesterday_market'];
                  $a7 = $r['avg7_market'];
                  $a30 = $r['avg30_market'];

                  $mine = $r['your_today_price'];

                  $val = '';
                  if (isset($oldPrices[$itemId]) && $oldPrices[$itemId] !== '') {
                    $val = (string)$oldPrices[$itemId];
                  } elseif ($mine !== null) {
                    $val = (string)$mine;
                  }

                  $disabled = !empty($alreadySubmittedToday) ? 'disabled' : '';
                ?>
                <tr>
                  <td><?php echo $name; ?></td>
                  <td class="num"><?php echo ($t !== null) ? number_format((float)$t, 2) : 'N/A'; ?></td>
                  <td class="num"><?php echo ($y !== null) ? number_format((float)$y, 2) : 'N/A'; ?></td>
                  <td class="num"><?php echo ($a7 !== null) ? number_format((float)$a7, 2) : 'N/A'; ?></td>
                  <td class="num"><?php echo ($a30 !== null) ? number_format((float)$a30, 2) : 'N/A'; ?></td>
                  <td class="num">
                    <input
                      class="input input-sm"
                      type="number"
                      name="prices[<?php echo $itemId; ?>]"
                      value="<?php echo $val; ?>"
                      placeholder="e.g. 12.00"
                      <?php echo $disabled; ?>
                    />
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>

        <div class="center" style="margin-top: 15px;">
          <button class="btn" type="submit" <?php echo !empty($alreadySubmittedToday) ? 'disabled' : ''; ?>>
            Save
          </button>
          <a class="btn" href="index.php?url=dealer/dashboard">Back</a>
        </div>
      </form>

      <p style="margin-top: 10px; color: #666;">
        Rule: you can submit once per day. Input must be within ±5% of 7-day average.
      </p>
   
  </main>

</body>
</html>
