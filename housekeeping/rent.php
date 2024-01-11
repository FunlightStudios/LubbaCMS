<?php
 include_once  "../system/config.php";
 include_once  "../inc/checkadmin.php";
 include_once  "../system/core.php";
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>King Records - Studiomiete</title>
    <!-- plugins:css -->
    <link rel="stylesheet" href="assets/vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="assets/vendors/css/vendor.bundle.base.css">
    <!-- endinject -->
    <!-- Plugin css for this page -->
    <!-- End Plugin css for this page -->
    <!-- inject:css -->
    <!-- endinject -->
    <!-- Layout styles -->
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- End layout styles -->
    <link rel="shortcut icon" href="assets/images/favicon.png" />
  </head>
  <body>
    <div class="container-scroller">
      <!-- partial:partials/_sidebar.html -->
        <?php include_once "../inc/adminnav.php"; ?>
        <!-- partial -->
        <div class="main-panel">
          <div class="content-wrapper">
          <div class="row">
          <div class="col-xl-3 col-sm-6 grid-margin stretch-card">
                <div class="card">
                  <div class="card-body">
                    <div class="row">
                      <div class="col-9">
                        <div class="d-flex align-items-center align-self-start">
                          <h3 class="mb-0">CHF 411</h3>
                          <p class="text-success ml-2 mb-0 font-weight-medium">+11%</p>
                        </div>
                      </div>
                      <div class="col-3">
                        <div class="icon icon-box-success">
                          <span class="mdi mdi-arrow-top-right icon-item"></span>
                        </div>
                      </div>
                    </div>
                    <h6 class="text-muted font-weight-normal">X-Project Miete</h6><h6 class="text-muted font-weight-normal"><code>*Inkl. Schulden</code></h6>
                  </div>
                </div>
              </div>
              <div class="col-xl-3 col-sm-6 grid-margin stretch-card">
                <div class="card">
                  <div class="card-body">
                    <div class="row">
                      <div class="col-9">
                        <div class="d-flex align-items-center align-self-start">
                          <?php
                          // Query the user_rent table to get the rent for the logged-in user and current month/year
                          $sql = "SELECT * FROM user_rent WHERE user_id = $admin_id";
                          $result = mysqli_query($conn, $sql);

                          if (!$result) {
                            die('Die Abfrage konnte nicht ausgeführt werden: ' . mysqli_error($conn));
                          }

                          if (mysqli_num_rows($result) > 0) {
                            $row = mysqli_fetch_assoc($result);
                            $rent = $row['rent'];
                            // Display the rent for the logged-in user
                            echo "<h3 class='mb-0'>CHF $rent</h3>";
                          } else {
                            echo "<h3 class='mb-0'>NO DATA</h3>";
                          }
                          ?>
                          <p class="text-success ml-2 mb-0 font-weight-medium">mtl.</p>
                        </div>
                      </div>
                      <div class="col-3">
                        <div class="icon icon-box-success">
                          <span class="mdi mdi-account icon-item"></span>
                        </div>
                      </div>
                    </div>
                    <?php
                    // Fetch rent payment for the current user and month
                    $sql = "SELECT * FROM rent WHERE user_id = $admin_id AND MONTH(month_year) = $currentMonth AND YEAR(month_year) = $currentYear";
                    $result = mysqli_query($conn, $sql);

                    if (!$result) {
                      die('Die Abfrage konnte nicht ausgeführt werden: ' . mysqli_error($conn));
                    }

                    if (mysqli_num_rows($result) > 0) {
                      // Rent payment for current month exists
                      echo '<h6 class="text-muted font-weight-normal">Persönliche Miete</h6><h6 class="text-muted font-weight-normal">Bezahlt</h6>';
                    } else {
                      // Rent payment for current month does not exist
                      echo '<h6 class="text-muted font-weight-normal">Persönliche Miete</h6><h6 class="text-muted font-weight-normal">Ausstehend</h6>';

                      // Check if user has enough money to pay rent
                      if ($net_worth >= $rent) {
                        // Display the "Bezahlen" button
                        echo '<button class="btn btn-success" type="button" style="float:right; margin-top:-20px;" onclick="bezahlen()"> Bezahlen </button>';
                      }
                    }
                    ?>
                  </div>
                  <script>
                  function bezahlen() {
                      $.ajax({
                          type: "POST",
                          url: "../inc/payrent.php",
                          data: {
                              admin_id: <?php echo $admin_id; ?>,
                              rent: <?php echo $rent; ?>
                          },
                          success: function(response) {
                              if (response == "success") {
                                  // Aktualisieren Sie die Seite oder eine andere Funktion hier
                                  location.reload();
                                  alert("Erfolgreich bezahlt!");
                              } else {
                                  alert("Nicht genügend Guthaben auf Ihrem Konto!");
                              }
                          }
                      });
                  }
                  </script>
                </div>
              </div>
              <?php
              $sql = "SELECT year, SUM(IF(amount > 0, amount, 0)) AS rev_total FROM label_transactions GROUP BY year";
              $result = mysqli_query($conn, $sql);
              while($row = mysqli_fetch_assoc($result)) {
                $revenue_total = $row["rev_total"];
              }
              ?>
              <div class="col-xl-3 col-sm-6 grid-margin stretch-card">
                <div class="card">
                  <div class="card-body">
                    <div class="row">
                      <div class="col-9">
                        <div class="d-flex align-items-center align-self-start">
                          <h3 class="mb-0">CHF <?php echo $revenue_total ?></h3>
                          <p class="text-danger ml-2 mb-0 font-weight-medium">-2.4%</p>
                        </div>
                      </div>
                      <div class="col-3">
                        <div class="icon icon-box-danger">
                          <span class="mdi mdi-arrow-bottom-left icon-item"></span>
                        </div>
                      </div>
                    </div>
                    <h6 class="text-muted font-weight-normal">Umsatz</h6><h6 class="text-muted font-weight-normal"><code>Dieses Jahr</code></h6>
                  </div>
                </div>
              </div>
              <div class="col-xl-3 col-sm-6 grid-margin stretch-card">
                <div class="card">
                  <div class="card-body">
                    <div class="row">
                      <div class="col-9">
                        <div class="d-flex align-items-center align-self-start">
                          <h3 class="mb-0">CHF <?php echo $net_worth ?></h3>
                          <p class="text-success ml-2 mb-0 font-weight-medium">+3.5%</p>
                        </div>
                      </div>
                      <div class="col-3">
                        <div class="icon icon-box-success ">
                          <span class="mdi mdi-arrow-top-right icon-item"></span>
                        </div>
                      </div>
                    </div>
                    <h6 class="text-muted font-weight-normal">Kontostand</h6><h6 class="text-muted font-weight-normal"> Nicht ausgezahlt</h6>
                  </div>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-lg-6 grid-margin stretch-card">
                <div class="card">
                  <div class="card-body">
                    <h4 class="card-title">Studiomiete</h4>
                    <p class="card-description"> Monatsübersicht <code>(<?php echo $currentYear?>)</code>
                    </p>
                    <div class="table-responsive">
                      <?php
                      // Fetch all users
                      $sql = "SELECT id, username FROM user";
                      $result = mysqli_query($conn, $sql);

                      if (!$result) {
                          die('Die Abfrage konnte nicht ausgeführt werden: ' . mysqli_error($conn));
                      }
                      ?>

                      <table class="table">
                          <thead>
                              <tr>
                                  <th> Monat </th>
                                  <?php
                                  while ($row = mysqli_fetch_assoc($result)) {
                                      echo "<th>" . $row['username'] . "</th>";
                                  }

                                  // Add the "Bezahlt?" table header
                                  echo "<th> Bezahlt? </th>";
                                  ?>
                              </tr>
                          </thead>
                          <tbody>
                              <?php
                              // Loop through months and display rent payments for each user
                              for ($month = 1; $month <= 12; $month++) {
                                  echo "<tr>";
                                  echo "<td>" . date('F', mktime(0, 0, 0, $month, 1, $currentYear)) . "</td>";

                                  mysqli_data_seek($result, 0);

                                  while ($row = mysqli_fetch_assoc($result)) {
                                      $userId = $row['id'];
                                      echo "<td>";

                                      // Fetch rent payment for the current user and month
                                      $sql = "SELECT * FROM rent WHERE user_id = $userId AND MONTH(month_year) = $month AND YEAR(month_year) = $currentYear";
                                      $rentResult = mysqli_query($conn, $sql);

                                      if (!$rentResult) {
                                          die('Die Abfrage konnte nicht ausgeführt werden: ' . mysqli_error($conn));
                                      }

                                      if (mysqli_num_rows($rentResult) > 0) {
                                          echo "<label class='badge badge-success'>Bezahlt</label>";
                                      } else {
                                          echo "<label class='badge badge-danger'>Ausstehend</label>";
                                      }

                                      echo "</td>";
                                  }

                                  // Add the "Bezahlt?" table cell for the last column
                                  echo "<td></td>";
                                  echo "</tr>";
                              }
                              ?>
                          </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            </div>
          <!-- content-wrapper ends -->
          <!-- partial:partials/_footer.html -->
          <footer class="footer">
            <div class="d-sm-flex justify-content-center justify-content-sm-between">
              <span class="text-muted d-block text-center text-sm-left d-sm-inline-block">Copyright © 2023 by Funlight Studios</span>
              <span class="text-muted float-none float-sm-right d-block mt-1 mt-sm-0 text-center">KingRecordsCMS Alpha w18y23a</span>
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
    <!-- End plugin js for this page -->
    <!-- inject:js -->
    <script src="assets/js/off-canvas.js"></script>
    <script src="assets/js/hoverable-collapse.js"></script>
    <script src="assets/js/misc.js"></script>
    <script src="assets/js/settings.js"></script>
    <script src="assets/js/todolist.js"></script>
    <!-- endinject -->
    <!-- Custom js for this page -->
    <script src="assets/js/chart.js"></script>
    <!-- End custom js for this page -->
  </body>
</html>
