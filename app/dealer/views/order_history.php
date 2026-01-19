<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Order History</title>

  <link rel="stylesheet" href="household/css/style.css" />
  <link rel="stylesheet" href="household/css/forms.css" />
  <link rel="stylesheet" href="household/css/tables.css" />

  <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
</head>
<body>

<header>
  <h1>Order History</h1>
  <p>Completed (Collected) orders</p>
</header>

<main class="container">
  <div class="box" style="width:95%; margin:auto;">

    <div id="msg" style="margin:10px 0;"></div>

    <table class="simple-table">
      <thead>
        <tr>
          <th>Scrap</th>
          <th class="num">Weight</th>
          <th>Seller</th>
          <th>Address</th>
          <th>Preferred Time</th>
          <th>Collected At</th>
        </tr>
      </thead>
      <tbody id="historyBody">
        <tr><td colspan="6">Loading...</td></tr>
      </tbody>
    </table>

    <div class="center" style="margin-top:15px;">
      <button id="loadMoreBtn" class="btn" type="button" style="width: 18%; min-width: 160px;">
        Load More
      </button>
    </div>

    <div style="margin-top:12px;">
      <a class="btn" href="index.php?url=dealer/dashboard">← Back</a>
    </div>

  </div>
</main>

<script>
var offset = 0;
var limit = 2;

function renderRows(rows, append) {
  var html = '';
  for (var i = 0; i < rows.length; i++) {
    var r = rows[i];
    var sellerInfo = r.seller_name + '<br>' + r.seller_phone;

    html += '<tr>' +
      '<td>' + r.scrap_name + ' (' + r.scrap_unit + ')' + '</td>' +
      '<td class="num">' + r.estimated_weight + '</td>' +
      '<td>' + sellerInfo + '</td>' +
      '<td>' + r.address + '</td>' +
      '<td>' + r.desired_datetime + '</td>' +
      '<td>' + (r.collected_at || '') + '</td>' +
    '</tr>';
  }

  if (append) {
    $('#historyBody').append(html);
  } else {
    $('#historyBody').html(html);
  }
}

function loadHistory(isLoadMore) {
  $('#msg').html('');

  $.ajax({
    url: 'index.php?url=dealer/order_history_data',
    type: 'GET',
    data: { limit: limit, offset: offset },
    success: function(res) {

      if (typeof res === 'string') {
        try { res = JSON.parse(res); } catch (e) {}
      }

      if (!res || !res.success) {
        $('#historyBody').html('<tr><td colspan="6">Failed to load</td></tr>');
        return;
      }

      var rows = res.rows || [];

      if (!isLoadMore) {
        if (rows.length === 0) {
          $('#historyBody').html('<tr><td colspan="6">No history yet.</td></tr>');
          $('#loadMoreBtn').hide();
          return;
        }
        renderRows(rows, false);
      } else {
        if (rows.length > 0) {
          renderRows(rows, true);
        }
      }

      offset = res.newOffset;

      if (res.hasMore) {
        $('#loadMoreBtn').show();
      } else {
        $('#loadMoreBtn').hide();
      }
    },
    error: function() {
      $('#historyBody').html('<tr><td colspan="6">Server error</td></tr>');
    }
  });
}

$(function() {
  loadHistory(false);

  $('#loadMoreBtn').on('click', function() {
    loadHistory(true);
  });
});
</script>

</body>
</html>
