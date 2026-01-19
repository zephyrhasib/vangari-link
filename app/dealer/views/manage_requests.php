<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Manage Requests</title>

  <link rel="stylesheet" href="household/css/style.css" />
  <link rel="stylesheet" href="household/css/forms.css" />
  <link rel="stylesheet" href="household/css/tables.css" />

  <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
</head>
<body>

<header>
  <h1>Manage Requests</h1>
  <p id="areaText"></p>
</header>

<main class="container">

  <div id="msg" style="margin: 10px 0;"></div>

  <h2>Pending Requests (Your Area)</h2>
  <table class="simple-table">
    <thead>
      <tr>
        <th>Scrap</th>
        <th class="num">Weight</th>
        <th>Address</th>
        <th>Preferred Time</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody id="pendingBody">
      <tr><td colspan="5">Loading...</td></tr>
    </tbody>
  </table>

  <h2 style="margin-top:25px;">My Accepted Orders</h2>
  <table class="simple-table">
    <thead>
      <tr>
        <th>Scrap</th>
        <th class="num">Weight</th>
        <th>Address</th>
        <th>Preferred Time</th>
        <th>Seller Info</th>
        <th>Status</th>
        <th>Update</th>
      </tr>
    </thead>
    <tbody id="myBody">
      <tr><td colspan="7">Loading...</td></tr>
    </tbody>
  </table>

  <div style="margin-top:12px;">
    <a class="btn" href="index.php?url=dealer/dashboard">← Back</a>
  </div>

</main>

<script>
function showMsg(html) {
  $('#msg').html(html);
  setTimeout(function(){ $('#msg').html(''); }, 3000);
}

function loadManageData() {
  $.ajax({
    url: 'index.php?url=dealer/manage_requests_data',
    type: 'GET',
    success: function(res) {
      if (typeof res === 'string') {
        try { res = JSON.parse(res); } catch (e) {}
      }
      if (!res || !res.success) {
        $('#pendingBody').html('<tr><td colspan="5">Failed to load</td></tr>');
        $('#myBody').html('<tr><td colspan="7">Failed to load</td></tr>');
        return;
      }

      $('#areaText').text('Showing requests for area: ' + (res.area || ''));

      var p = res.pending || [];
      var pHtml = '';

      if (p.length === 0) {
        pHtml = '<tr><td colspan="5">No pending requests in your area.</td></tr>';
      } else {
        for (var i = 0; i < p.length; i++) {
          var r = p[i];
          pHtml += '<tr>' +
            '<td>' + r.scrap_name + ' (' + r.scrap_unit + ')' + '</td>' +
            '<td class="num">' + r.estimated_weight + '</td>' +
            '<td>' + r.address + '</td>' +
            '<td>' + r.desired_datetime + '</td>' +
            '<td><button class="btn acceptBtn" data-id="' + r.id + '">Accept</button></td>' +
          '</tr>';
        }
      }
      $('#pendingBody').html(pHtml);

      var m = res.myOrders || [];
      var mHtml = '';

      if (m.length === 0) {
        mHtml = '<tr><td colspan="7">No accepted orders yet.</td></tr>';
      } else {
        for (var j = 0; j < m.length; j++) {
          var o = m[j];

          var sellerInfo = o.seller_name + '<br>' + o.seller_phone + '<br>' + o.seller_email;

          var btn = '';
          if (o.status === 'accepted') {
            btn = '<button class="btn dispatchBtn" data-id="' + o.id + '">Dispatched</button>';
          } else if (o.status === 'dispatched') {
            btn = '<button class="btn collectBtn" data-id="' + o.id + '">Collected</button>';
          } else {
            btn = '-';
          }

          mHtml += '<tr>' +
            '<td>' + o.scrap_name + ' (' + o.scrap_unit + ')' + '</td>' +
            '<td class="num">' + o.estimated_weight + '</td>' +
            '<td>' + o.address + '</td>' +
            '<td>' + o.desired_datetime + '</td>' +
            '<td>' + sellerInfo + '</td>' +
            '<td>' + o.status + '</td>' +
            '<td>' + btn + '</td>' +
          '</tr>';
        }
      }
      $('#myBody').html(mHtml);
    },
    error: function() {
      $('#pendingBody').html('<tr><td colspan="5">Server error</td></tr>');
      $('#myBody').html('<tr><td colspan="7">Server error</td></tr>');
    }
  });
}

$(document).on('click', '.acceptBtn', function() {
  var id = $(this).data('id');

  $.ajax({
    url: 'index.php?url=dealer/accept_request',
    type: 'POST',
    data: { request_id: id },
    success: function(res) {
      if (typeof res === 'string') {
        try { res = JSON.parse(res); } catch (e) {}
      }
      if (res && res.success) {
        showMsg('<p style="color:green;">' + res.message + '</p>');
        loadManageData();
      } else {
        showMsg('<p style="color:#b00020;">' + (res.errors ? res.errors[0] : 'Failed') + '</p>');
        loadManageData();
      }
    },
    error: function() {
      showMsg('<p style="color:#b00020;">Server error</p>');
    }
  });
});

$(document).on('click', '.dispatchBtn', function() {
  var id = $(this).data('id');

  $.ajax({
    url: 'index.php?url=dealer/mark_dispatched',
    type: 'POST',
    data: { request_id: id },
    success: function(res) {
      if (typeof res === 'string') {
        try { res = JSON.parse(res); } catch (e) {}
      }
      if (res && res.success) {
        showMsg('<p style="color:green;">' + res.message + '</p>');
        loadManageData();
      } else {
        showMsg('<p style="color:#b00020;">Cannot update</p>');
      }
    }
  });
});

$(document).on('click', '.collectBtn', function() {
  var id = $(this).data('id');

  $.ajax({
    url: 'index.php?url=dealer/mark_collected',
    type: 'POST',
    data: { request_id: id },
    success: function(res) {
      if (typeof res === 'string') {
        try { res = JSON.parse(res); } catch (e) {}
      }
      if (res && res.success) {
        showMsg('<p style="color:green;">' + res.message + '</p>');
        loadManageData();
      } else {
        showMsg('<p style="color:#b00020;">Cannot update</p>');
      }
    }
  });
});

$(function() {
  loadManageData();
  setInterval(loadManageData, 7000);
});
</script>

</body>
</html>
