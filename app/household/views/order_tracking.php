<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Order Tracking</title>

  <link rel="stylesheet" href="household/css/style.css" />
  <link rel="stylesheet" href="household/css/forms.css" />
  <link rel="stylesheet" href="household/css/tables.css" />
  <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

</head>
<body>

<header>
  <h1>Order Tracking</h1>
  <p>Track your pickup request status</p>
</header>

<main class="container">
  
    <div class="center" style="margin:20px;">
      <span id="lastUpdated" style="margin-left:10px; font-size:14px;"></span>
    </div>

    <table class="simple-table">
      
      <thead>
          <tr>
            <th>Scrap Item</th>
            <th class="num">Weight</th>
            <th>Pickup Time</th>
            <th>Status</th>
            <th>Buyer Shop</th>
            <th>Buyer Phone</th>
          </tr>
      </thead>
      <tbody id="ordersBody">
        <tr>
          <td colspan="6">Loading...</td>
        </tr>
      </tbody>
    </table>

    <div class="center" style="margin: 15px 0;">
      <button id="refreshBtn" class="btn" type="button" style="width: 18%; min-width: 140px;">Refresh</button> <br>
      <a class="btn" href="index.php?url=household/dashboard">← Back</a>
    </div>
</main>

<script>
function loadOrders() {
  $.ajax({
    url: 'index.php?url=household/order_tracking_data',
    type: 'GET',
    success: function (res) {

      if (typeof res === 'string') {
        try { res = JSON.parse(res); } catch (e) {}
      }

      var tbody = '';
      var rows = res.rows || [];

      if (rows.length === 0) {
        tbody = '<tr><td colspan="6">No pickup requests yet.</td></tr>';
      } else {
        for (var i = 0; i < rows.length; i++) {
        var r = rows[i];

        var buyerShop = r.buyer_shop ? r.buyer_shop : '-';
        var buyerPhone = r.buyer_phone ? r.buyer_phone : '-';

        tbody += '<tr>' +
          '<td>' + r.scrap_name + ' (' + r.scrap_unit + ')' + '</td>' +
          '<td class="num">' + r.estimated_weight + '</td>' +
          '<td>' + r.desired_datetime + '</td>' +
          '<td>' + r.status + '</td>' +
          '<td>' + buyerShop + '</td>' +
          '<td>' + buyerPhone + '</td>' +
        '</tr>';
        }
      }

      $('#ordersBody').html(tbody);

      var now = new Date();
      $('#lastUpdated').text('Last updated: ' + now.toLocaleTimeString());
    },
    error: function () {
      $('#ordersBody').html('<tr><td colspan="6">Failed to load data</td></tr>');
    }
  });
}

$(function () {
  loadOrders();                 
  $('#refreshBtn').click(loadOrders);
  setInterval(loadOrders, 7000);
});
</script>

</body>
</html>

