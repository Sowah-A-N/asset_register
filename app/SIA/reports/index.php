<?php
require_once '../init.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Welcome- HOD ICT</title>
  <!-- plugins:css -->
  <link rel="stylesheet" href="assets/vendors/mdi/css/materialdesignicons.min.css">
  <link rel="stylesheet" href="assets/vendors/flag-icon-css/css/flag-icon.min.css">
  <link rel="stylesheet" href="assets/vendors/css/vendor.bundle.base.css">
  <!-- endinject -->
  <!-- Plugin css for this page -->
  <link rel="stylesheet" href="assets/vendors/font-awesome/css/font-awesome.min.css" />
  <link rel="stylesheet" href="assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.css">
  <!-- End plugin css for this page -->
  <!-- inject:css -->
  <!-- endinject -->
  <!-- Layout styles -->
  <link rel="stylesheet" href="assets/css/style.css">
  <!-- End layout styles -->
  <link rel="shortcut icon" href="assets/images/favicon.png" />
</head>

<body>
  <div class="container-scroller">
    <!-- partial:partials/_navbar.html -->
    <nav class="navbar default-layout-navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
      <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center">
      </div>
      <div class="navbar-menu-wrapper d-flex align-items-stretch">
        <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-toggle="minimize">
          <span class="mdi mdi-menu"></span>
        </button>
        <ul class="navbar-nav navbar-nav-right">
          <li class="nav-item nav-profile dropdown">
            <a class="nav-link dropdown-toggle" id="profileDropdown" data-toggle="dropdown" aria-expanded="false">
              <div class="nav-profile-img">
                <img src="assets/images/favicon.png" alt="image">
              </div>
              <div class="nav-profile-text">
                <p class="mb-1 text-black"><?php echo ($username) ?></p>
              </div>
            </a>
            <div class="dropdown-menu navbar-dropdown dropdown-menu-right p-0 border-0 font-size-sm" aria-labelledby="profileDropdown" data-x-placement="bottom-end">
              <div class="p-3 text-center bg-light">
                <img class="img-avatar img-avatar48 img-avatar-thumb" src="assets/images/favicon.png" alt="">
              </div>
              <div class="p-2">
                <h5 class="dropdown-header text-uppercase pl-2 text-dark">User Options</h5>
                <a class="dropdown-item py-1 d-flex align-items-center justify-content-between" href="/admin_dashboard/change_password/">
                  <span>Change Password</span>
                  <i class="mdi mdi-settings"></i></a>
                </a>
                <div role="separator" class="dropdown-divider"></div>
                <h5 class="dropdown-header text-uppercase  pl-2 text-dark mt-2">Actions</h5>
                <a class="dropdown-item py-1 d-flex align-items-center justify-content-between" href="../logout/">
                  <span>Log Out</span>
                  <i class="mdi mdi-logout ml-1"></i>
                </a>
              </div>
            </div>
        </ul>
        <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-toggle="offcanvas">
          <span class="mdi mdi-menu"></span>
        </button>
      </div>
    </nav>
    <?php include "sidebar.html" ?>
    <!-- partial -->
    <div class="main-panel">
      <div class="content-wrapper">
        <div class="row" id="proBanner">
        </div>
        <div class="d-xl-flex justify-content-between align-items-start">
          <h2 class="text-dark font-weight-bold mb-2"> Reports Overview (GH₵)</h2>
          <div class="d-sm-flex justify-content-xl-between align-items-center mb-2">
            <div class="dropdown ml-0 ml-md-4 mt-2 mt-lg-0">
              <!-- <a href='../generator/' target="_blank"><button class="btn bg-primary" type="button" id="dropdownMenuButton1" aria-haspopup="true" aria-expanded="false"> Generate Timetable</button></a> -->
            </div>
          </div>
        </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <?php
    // ── Chart data — sourced from the same tables the reports use ─────────────

    // Selected year (defaults to current year; updated via AJAX below)
    $chartYear = isset($_POST['chart_year']) ? (int)$_POST['chart_year'] : (int)date('Y');

    // Available years for the selector
    $yearRangeResult = mysqli_query($conn, "SELECT MIN(year) AS min_y, MAX(year) AS max_y FROM asset_class_opbal_year");
    $yearRange       = $yearRangeResult ? mysqli_fetch_assoc($yearRangeResult) : null;
    $minChartYear    = ($yearRange && $yearRange['min_y']) ? (int)$yearRange['min_y'] : (int)date('Y');
    $maxChartYear    = ($yearRange && $yearRange['max_y']) ? (int)$yearRange['max_y'] : (int)date('Y');

    // ── Bar chart: per-class NBV Opening / Additions / NBV End for selected year
    $barStmt = mysqli_prepare($conn,
        "SELECT
            a.asset_class,
            a.opening_balance,
            a.total_accum_depr_start,
            a.net_book_value,
            COALESCE(b.total_additions_cedi, 0) AS total_additions_cedi,
            COALESCE(b.total_disposals_cedi, 0) AS total_disposals_cedi
         FROM asset_class_opbal_year a
         LEFT JOIN asset_additions_year b
            ON a.asset_class COLLATE utf8mb4_general_ci = b.asset_class COLLATE utf8mb4_general_ci
            AND a.year = b.year
         WHERE a.year = ?
         ORDER BY a.asset_class");

    $barLabels      = [];
    $barNbvOpening  = [];
    $barAdditions   = [];
    $barNbvEnd      = [];

    if ($barStmt) {
        mysqli_stmt_bind_param($barStmt, 'i', $chartYear);
        mysqli_stmt_execute($barStmt);
        $barResult = mysqli_stmt_get_result($barStmt);
        while ($row = mysqli_fetch_assoc($barResult)) {
            $openingCost    = (float)$row['opening_balance'];
            $accumDeprStart = (float)$row['total_accum_depr_start'];
            $nbvOpening     = $openingCost - $accumDeprStart;
            $additions      = (float)$row['total_additions_cedi'];
            $disposals      = (float)$row['total_disposals_cedi'];
            $totalCost      = $openingCost + $additions - $disposals;
            // Recalculate NBV end consistently with the reports
            $nbvEnd         = max((float)$row['net_book_value'], 0);

            $barLabels[]     = $row['asset_class'];
            $barNbvOpening[] = round($nbvOpening, 2);
            $barAdditions[]  = round($additions, 2);
            $barNbvEnd[]     = round($nbvEnd, 2);
        }
        mysqli_stmt_close($barStmt);
    }

    // ── Line chart: total additions & disposals by year (trend)
    $trendResult = mysqli_query($conn,
        "SELECT year,
                SUM(total_additions_cedi)  AS yearly_additions,
                SUM(total_disposals_cedi)  AS yearly_disposals
         FROM asset_additions_year
         GROUP BY year
         ORDER BY year ASC");

    $trendYears     = [];
    $trendAdditions = [];
    $trendDisposals = [];

    if ($trendResult) {
        while ($row = mysqli_fetch_assoc($trendResult)) {
            $trendYears[]     = $row['year'];
            $trendAdditions[] = round((float)$row['yearly_additions'], 2);
            $trendDisposals[] = round((float)$row['yearly_disposals'], 2);
        }
    }
    ?>

    <!-- Year selector for bar chart -->
    <div class="d-flex align-items-center gap-2 mb-3">
        <label class="mb-0 font-weight-bold text-dark">Asset Values by Class — Year:</label>
        <select id="chartYearSelect" class="form-control form-control-sm" style="width:auto">
            <?php for ($y = $maxChartYear; $y >= $minChartYear; $y--): ?>
                <option value="<?php echo $y; ?>" <?php echo ($y === $chartYear) ? 'selected' : ''; ?>>
                    <?php echo $y; ?>
                </option>
            <?php endfor; ?>
        </select>
    </div>

    <!-- Chart 1: Grouped bar — NBV Opening / Additions / NBV End per class -->
    <div class="mb-4" style="position:relative; height:300px">
        <canvas id="nbvByClassChart"></canvas>
    </div>

    <hr class="my-4">

    <!-- Chart 2: Line — additions & disposals trend by year -->
    <div class="mb-1">
        <span class="font-weight-bold text-dark">Capital Additions &amp; Disposals — All Years (GH₵)</span>
    </div>
    <div style="position:relative; height:260px">
        <canvas id="trendChart"></canvas>
    </div>

    <script>
    // ── Chart 1: NBV by Class (bar) ───────────────────────────────────────────
    const barLabels     = <?php echo json_encode($barLabels); ?>;
    const barNbvOpening = <?php echo json_encode($barNbvOpening); ?>;
    const barAdditions  = <?php echo json_encode($barAdditions); ?>;
    const barNbvEnd     = <?php echo json_encode($barNbvEnd); ?>;

    const nbvChart = new Chart(document.getElementById('nbvByClassChart'), {
        type: 'bar',
        data: {
            labels: barLabels,
            datasets: [
                {
                    label: 'NBV — Opening (GH₵)',
                    data: barNbvOpening,
                    backgroundColor: 'rgba(54, 162, 235, 0.75)',
                    borderColor:     'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Additions — Year (GH₵)',
                    data: barAdditions,
                    backgroundColor: 'rgba(75, 192, 100, 0.75)',
                    borderColor:     'rgba(75, 192, 100, 1)',
                    borderWidth: 1
                },
                {
                    label: 'NBV — Closing (GH₵)',
                    data: barNbvEnd,
                    backgroundColor: 'rgba(255, 159, 64, 0.75)',
                    borderColor:     'rgba(255, 159, 64, 1)',
                    borderWidth: 1
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'top' },
                tooltip: {
                    callbacks: {
                        label: ctx => ' GH₵ ' + ctx.parsed.y.toLocaleString('en-GH', {minimumFractionDigits: 2})
                    }
                }
            },
            scales: {
                x: { beginAtZero: true },
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: v => 'GH₵ ' + Number(v).toLocaleString()
                    }
                }
            }
        }
    });

    // ── Chart 2: Trend line ───────────────────────────────────────────────────
    const trendYears     = <?php echo json_encode($trendYears); ?>;
    const trendAdditions = <?php echo json_encode($trendAdditions); ?>;
    const trendDisposals = <?php echo json_encode($trendDisposals); ?>;

    new Chart(document.getElementById('trendChart'), {
        type: 'line',
        data: {
            labels: trendYears,
            datasets: [
                {
                    label: 'Total Additions (GH₵)',
                    data: trendAdditions,
                    borderColor:     'rgba(54, 162, 235, 1)',
                    backgroundColor: 'rgba(54, 162, 235, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.3,
                    pointRadius: 5
                },
                {
                    label: 'Total Disposals (GH₵)',
                    data: trendDisposals,
                    borderColor:     'rgba(255, 99, 132, 1)',
                    backgroundColor: 'rgba(255, 99, 132, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.3,
                    pointRadius: 5
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'top' },
                tooltip: {
                    callbacks: {
                        label: ctx => ' GH₵ ' + ctx.parsed.y.toLocaleString('en-GH', {minimumFractionDigits: 2})
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { callback: v => 'GH₵ ' + Number(v).toLocaleString() }
                }
            }
        }
    });

    // ── Year selector — reload bar chart via POST ─────────────────────────────
    document.getElementById('chartYearSelect').addEventListener('change', function () {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '';
        const input = document.createElement('input');
        input.type  = 'hidden';
        input.name  = 'chart_year';
        input.value = this.value;
        form.appendChild(input);
        document.body.appendChild(form);
        form.submit();
    });
    </script>


        <div class="row">
          <div class="col-md-12">
            <div class="d-sm-flex justify-content-between align-items-center transaparent-tab-border {">
            </div>
            <div class="tab-content tab-transparent-content">
              <div class="tab-pane fade show active" id="business-1" role="tabpanel" aria-labelledby="business-tab">
                <div class="row">
                  
                  <?php

                  $sql = "SELECT asset_classes.ast_id, asset_classes.asset_class, COUNT(assets.asset_id) AS asset_count
                  FROM asset_classes
                  LEFT JOIN assets ON asset_classes.asset_class = assets.asset_class
                  GROUP BY asset_classes.asset_class;";
                  $result = mysqli_query($conn, $sql);

                  if(mysqli_num_rows( $result ) > 0){
                    while($row = mysqli_fetch_assoc($result)){
                    //var_dump($row);
                     echo "<div class='col-xl-4 col-lg-6 col-sm-6 grid-margin stretch-card'>"; 
                     echo "<div class='card' id='". $row['ast_id'] ."'>"; 
                     echo "<div class='card-body text-center'>";
                     echo "<i class='mdi mdi-account icon-md text-dark'></i>";
                     echo "<a href=index2.php/?asset_class=" . urlencode($row['asset_class']).">";
                     //print_r($row['asset_class']);
                     echo "<h3 class='mb-2 text-dark font-weight-normal'>" . $row['asset_class'] . "</h3><br />";
                     echo "<h2 class='mb-4 text-dark font-weight-bold'>" . $row['asset_count'] . "</h2>";       
                     echo "</a>";             
                     echo "<div class='dashboard-progress dashboard-progress-1 d-flex align-items-center justify-content-center item-parent'></div>";
                     echo "</div>";
                     echo "</div>";
                     echo "</div>";
                    }
                  }

                  ?>

                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- content-wrapper ends -->
      <!-- partial:partials/_footer.html -->
      <footer class="footer">
        <div class="footer-inner-wraper">
          <div class="d-sm-flex justify-content-center justify-content-sm-between"> </div>
        </div>
      </footer>
      <!-- partial -->
    </div>
    <!-- main-panel ends -->
  </div>
  <!-- page-body-wrapper ends -->
  </div>
  <!-- container-scroller -->
  <!-- plugins:js -->
  <script src="assets/vendors/js/vendor.bundle.base.js"></script>
  <!-- endinject -->
  <!-- Plugin js for this page -->
  <script src="assets/vendors/chart.js/Chart.min.js"></script>
  <script src="assets/vendors/jquery-circle-progress/js/circle-progress.min.js"></script>
  <!-- End plugin js for this page -->
  <!-- inject:js -->
  <script src="assets/js/off-canvas.js"></script>
  <script src="assets/js/hoverable-collapse.js"></script>
  <script src="assets/js/misc.js"></script>
  <!-- endinject -->
  <!-- Custom js for this page -->
  <script src="assets/js/dashboard.js"></script>
  <!-- End custom js for this page -->
</body>

</html>