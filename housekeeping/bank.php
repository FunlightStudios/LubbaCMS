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
    <title>King Records - Admin Bank</title>
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

    <script src="../style/js/jquery-3.6.0.min.js"></script>
        <script src="../style/bootstrap/js/bootstrap.bundle.min.js"></script>
  </head>
  <body>
    <div class="container-scroller">
      <!-- INCLUDE ADMIN MENU -->
        <?php include_once "../inc/adminnav.php"; ?>
        <!-- END INCLUDE ADMIN MENU -->
        <!-- PPAGE CONTENT -->
        <div class="main-panel">
          <div class="content-wrapper">
          <div class="row">
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
                  <h6 class="text-muted font-weight-normal">Persönlicher Kontostand</h6><h6 class="text-muted font-weight-normal"> Nicht ausgezahlt</h6>
                </div>
              </div>
            </div>
              <div class="col-xl-3 col-sm-6 grid-margin stretch-card">
                <div class="card">
                  <div class="card-body">
                    <div class="row">
                      <div class="col-9">
                        <div class="d-flex align-items-center align-self-start">
                          <h3 class="mb-0">CHF 3548.35</h3>
                          <p class="text-success ml-2 mb-0 font-weight-medium">+11%</p>
                        </div>
                      </div>
                      <div class="col-3">
                        <div class="icon icon-box-success">
                          <span class="mdi mdi-arrow-top-right icon-item"></span>
                        </div>
                      </div>
                    </div>
                    <h6 class="text-muted font-weight-normal">Persönlicher Umsatz</h6><h6 class="text-muted font-weight-normal"> YTD</h6>
                  </div>
                </div>
              </div>
              <!-- LABEL-UMSATZ -->
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
              <!-- END LABEL-UMSATZ -->
              <!-- LABEL KONTOSTAND -->
              <?php
              $sql = "SELECT amount, SUM(IF(amount < 0, amount, 0)) AS fiat_neg_total, SUM(IF(amount > 0, amount, 0)) AS fiat_pos_total FROM label_transactions";
              $result = mysqli_query($conn, $sql);
              while($row = mysqli_fetch_assoc($result)) {
                $fiat_total = $row["fiat_pos_total"] - abs($row["fiat_neg_total"]);
              }
              ?>
              <div class="col-xl-3 col-sm-6 grid-margin stretch-card">
                <div class="card">
                  <div class="card-body">
                    <div class="row">
                      <div class="col-9">
                        <div class="d-flex align-items-center align-self-start">
                          <h3 class="mb-0">CHF <?php echo $fiat_total ?></h3>
                          <p class="text-success ml-2 mb-0 font-weight-medium">+3.5%</p>
                        </div>
                      </div>
                      <div class="col-3">
                        <div class="icon icon-box-success ">
                          <span class="mdi mdi-chart-bar icon-item"></span>
                        </div>
                      </div>
                    </div>
                    <h6 class="text-muted font-weight-normal">Kontostand</h6><h6 class="text-muted font-weight-normal"> Verfügbare Fiat-Mittel</h6>
                  </div>
                </div>
              </div>
            </div>
            <!-- END LABEL KONTOSTAND -->
            <!-- JAHRESGEWINN CHART -->
            <script>
            // JavaScript-Code für das Bar-Chart
            var data = {
              labels: <?php echo $json_labels; ?>,
              datasets: [{
              label: 'CHF',
              data: <?php echo $json_data; ?>,
              backgroundColor: [
              'rgba(255, 99, 132, 0.2)',
              'rgba(54, 162, 235, 0.2)',
              'rgba(255, 206, 86, 0.2)',
              'rgba(75, 192, 192, 0.2)',
              'rgba(153, 102, 255, 0.2)',
              'rgba(255, 159, 64, 0.2)'
            ],
              borderColor: [
              'rgba(255,99,132,1)',
              'rgba(54, 162, 235, 1)',
              'rgba(255, 206, 86, 1)',
              'rgba(75, 192, 192, 1)',
              'rgba(153, 102, 255, 1)',
              'rgba(255, 159, 64, 1)'
            ],
              borderWidth: 1,
              fill: false
              }]
            };

            // Bar-Chart initialisieren
            var ctx = document.getElementById('barChart').getContext('2d');
            var myChart = new Chart(ctx, {
              type: 'bar',
              data: data,
                options: {
                  scales: {
                    yAxes: [{
                      ticks: {
                        beginAtZero: true
                      }
                    }]
                  }
                }
              });
            </script>
          <!-- ROW LINE 2 -->
          <div class="row">
            <div class="col-lg-6 grid-margin stretch-card">
                <div class="card">
                  <div class="card-body">
                    <h4 class="card-title">Jahresgewinne</h4>
                    <canvas id="barChart" style="height:230px"></canvas>
                  </div>
                </div>
              </div>
              <!-- END JAHRESGEWINN CHART -->
              <!-- LABEL TRANSACTIONS -->
              <div class="col-lg-6 grid-margin stretch-card">
                <div class="card">
                  <div class="card-body">
                    <h4 class="card-title">Transaktionen</h4>
                    <p class="card-description">Ein und Auszahlungen <code>(Intern)</code></p>
                      <div style="float:right; margin-top:-60px;" class="add-transactions">
                      <button data-bs-toggle="modal" data-bs-target="#add-intern-transaction" type="button" class="btn btn-success btn-sm"><i style="margin-top: 2px;" class="mdi mdi-plus"></i> Transaktion</button>
                      </div>
                      <div class="table-responsive">
                        <table class="table">
                          <thead>
                              <tr>
                                <th>T-ID</th>
                                <th>Betrag</th>
                                <th>Typ</th>
                                <th>U-ID</th>
                                <th>Erstellt am</th>
                                <th>Details</th>
                              </tr>
                          </thead>
                          <tbody>
                            <?php
                            // SQL-Abfrage ausführen
                            $result = mysqli_query($conn, "SELECT label_transactions.id, label_transactions.amount, label_transactions.type, user.username, label_transactions.created_at
                                FROM label_transactions
                                INNER JOIN user ON label_transactions.payout_user = user.id
                                ORDER BY label_transactions.created_at DESC
                                LIMIT 7;");
                                // Daten aus der Datenbank abrufen und in die Tabelle einfügen
                                while($row = mysqli_fetch_assoc($result)) {
                                  echo "<tr>";
                                  echo "<td>" . $row["id"] . "</td>";
                                  echo "<td>" . $row["amount"] . "</td>";
                                  echo "<td>" . $row["type"] . "</td>";
                                  echo "<td>" . $row["username"] . "</td>";
                                  echo "<td>" . $row["created_at"] . "</td>";
                                  echo "<td><a href='#'><i class='mdi mdi-loupe'></i></a></td>";
                                  echo "</tr>";
                                }
                                ?>
                              </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="modal fade" style="margin: 40px auto;" id="add-intern-transaction" tabindex="-1" aria-labelledby="listen-modal-label" aria-hidden="true">
                  <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title" id="add-intern-transaction">Transaktion hinzufügen</h5>
                      </div>
                      <div class="modal-body">
                        <form action="../inc/insert-transaction.php" method="POST">
                          <div class="mb-3">
                            <label for="amount" class="form-label">Betrag</label>
                            <input type="number" step="0.01" class="form-control" id="amount" name="amount" required>
                          </div>
                          <div class="mb-3">
                            <label for="type" class="form-label">Typ</label>
                            <select class="form-control" id="type" name="type" required>
                              <option value="Einzahlung">Einzahlung</option>
                              <option value="Auszahlung">Auszahlung</option>
                            </select>
                          </div>
                          <div class="mb-3">
                            <label for="payout_user" class="form-label">Überweisung für</label>
                            <select class="form-control" id="payout_user" name="payout_user">
                              <option value="">Kein User</option>
                              <?php
                              $query = "SELECT id, username FROM user";
                              $result = mysqli_query($conn, $query);
                              while ($row = mysqli_fetch_assoc($result)) {
                                echo "<option value='" . $row['id'] . "'>" . $row['username'] . "</option>";
                              }
                              ?>
                            </select>
                          </div>
                          <button type="submit" class="btn btn-primary">Transaktion hinzufügen</button>
                        </form>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Schließen</button>
                      </div>
                    </div>
                  </div>
                </div>
                <!-- END LABEL TRANSACTIONS -->
              </div>
              <!-- END ROW LINE 2 -->
              <div class="row">
            <div class="col-lg-6 grid-margin stretch-card">
                <div class="card">
                  <div class="card-body">
                    <h4 class="card-title">Users Bank Status</h4>
                    <p class="card-description"> Add class <code>.table-bordered</code>
                    </p>
                    <div style="float:right; margin-top:-60px;" class="add-transactions">
                    <button data-bs-toggle="modal" data-bs-target="#update-user-money" type="button" class="btn btn-success btn-sm"><i style="margin-top: 2px;" class="mdi mdi-plus"></i> Guthaben</button>
                    <button data-bs-toggle="modal" data-bs-target="#add-intern-bankacc" type="button" class="btn btn-success btn-sm"><i style="margin-top: 2px;" class="mdi mdi-plus"></i> Bankaccount</button>
                    </div>
                    <div class="table-responsive">
                      <table class="table table-bordered">
                        <thead>
                          <tr>
                            <th> U-ID </th>
                            <th> Benutzername </th>
                            <th> Vorname </th>
                            <th> Guthaben in CHF </th>
                          </tr>
                        </thead>
                        <tbody>
                        <?php
                            // SQL-Abfrage ausführen
                            $result = mysqli_query($conn, "SELECT u.id, u.username, u.first_name, b.net_worth FROM user u JOIN bank b ON u.id = b.user_id ORDER BY u.id;");
                            // Daten aus der Datenbank abrufen und in die Tabelle einfügen
                            while($row = mysqli_fetch_assoc($result)) {
                                echo "<tr>";
                                echo "<td>" . $row["id"] . "</td>";
                                echo "<td>" . $row["username"] . "</td>";
                                echo "<td>" . $row["first_name"] . "</td>";
                                echo "<td>" . $row["net_worth"] . "</td>";
                                echo "</tr>";
                            }
                        ?>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
              <div class="modal fade" style="margin: 40px auto;" id="add-intern-bankacc" tabindex="-1" aria-labelledby="listen-modal-label" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title" id="add-intern-bankacc">Bankaccount hinzufügen</h5>
                    </div>
                    <div class="modal-body">
                      <form action="../inc/insert-bankacc.php" method="POST">
                        <div class="mb-3">
                          <label for="amount" class="form-label">Kontostand?</label>
                          <input type="number" step="0.01" class="form-control" id="net_worth" name="net_worth" required>
                        </div>
                        <div class="mb-3">
                          <label for="payout_user" class="form-label">Benutzer</label>
                          <select class="form-control" id="bank_user" name="bank_user">
                            <option value="">Auswählen</option>
                            <?php
                            $query = "SELECT id, username FROM user";
                            $result = mysqli_query($conn, $query);
                            while ($row = mysqli_fetch_assoc($result)) {
                              echo "<option value='" . $row['id'] . "'>" . $row['username'] . "</option>";
                            }
                            ?>
                          </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                      <button type="submit" class="btn btn-primary">Bestätigen</button></form>
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Schließen</button>
                    </div>
                  </div>
                </div>
              </div>
              <div class="modal fade" style="margin: 40px auto;" id="update-user-money" tabindex="-1" aria-labelledby="listen-modal-label" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title" id="update-user-money">Guthaben hinzufügen</h5>
                    </div>
                    <div class="modal-body">
                      <form action="../inc/update-usermoney.php" method="POST">
                        <div class="mb-3">
                          <label for="amount" class="form-label">Betrag in CHF</label>
                          <input type="number" step="0.01" class="form-control" id="amount" name="amount" required>
                        </div>
                        <div class="mb-3">
                          <label for="type" class="form-label">Typ</label>
                          <select class="form-control" id="type" name="type" required>
                            <option value="Einzahlung">Einzahlung</option>
                            <option value="Auszahlung">Auszahlung</option>
                          </select>
                        </div>
                        <div class="mb-3">
                          <label for="description" class="form-label">Bemerkung</label>
                          <input type="description" class="form-control" id="description" name="description">
                        </div>
                        <div class="mb-3">
                          <label for="b_user" class="form-label">Benutzer</label>
                          <select class="form-control" id="b_user" name="b_user" required>
                            <option value="">Auswählen</option>
                            <?php
                            $query = "SELECT id, username FROM user";
                            $result = mysqli_query($conn, $query);
                            while ($row = mysqli_fetch_assoc($result)) {
                              echo "<option value='" . $row['id'] . "'>" . $row['username'] . "</option>";
                            }
                            ?>
                          </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                      <button type="submit" class="btn btn-primary">Bestätigen</button></form>
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Schließen</button>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            </div>

          <!-- END PAGE-CONTENT -->
          <!-- FOOTER -->
          <footer class="footer">
            <div class="d-sm-flex justify-content-center justify-content-sm-between">
              <span class="text-muted d-block text-center text-sm-left d-sm-inline-block">Copyright © 2023 by Funlight Studios</span>
              <span class="text-muted float-none float-sm-right d-block mt-1 mt-sm-0 text-center">KingRecordsCMS Alpha w18y23a</span>
            </div>
          </footer>
          <!-- END FOOTER -->
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
