
<!DOCTYPE html>
<?php
	/*staffCheckHk();*/
	define('BRAIN_CMS', 2);
	include_once $_SERVER['DOCUMENT_ROOT'].'/global.php';
	include_once $_SERVER['DOCUMENT_ROOT'].'/adminpan/includes/script.php';
	staffCheckHk();
	admin::CheckRank(6);
?>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Lubba HK</title>
    <!-- plugins:css -->
    <link rel="stylesheet" href="assets/vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="assets/vendors/css/vendor.bundle.base.css">
    <!-- endinject -->
    <!-- Plugin css for this page -->
    <link rel="stylesheet" href="assets/vendors/jvectormap/jquery-jvectormap.css">
    <link rel="stylesheet" href="assets/vendors/flag-icon-css/css/flag-icon.min.css">
    <link rel="stylesheet" href="assets/vendors/owl-carousel-2/owl.carousel.min.css">
    <link rel="stylesheet" href="assets/vendors/owl-carousel-2/owl.theme.default.min.css">
    <!-- End plugin css for this page -->
    <!-- inject:css -->
    <!-- endinject -->
    <!-- Layout styles -->
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- End layout styles -->
    <link rel="shortcut icon" href="assets/images/favicon.png" />
		<style>
    .cke {
        direction: ltr !important; /* Von links nach rechts ausrichten */
    }
</style>
  </head>
  <body>
    <div class="container-scroller">
      <!-- partial:partials/_sidebar.html -->
      <?php include_once "includes/adminnav.php"; ?>
        <!-- partial -->
        <div class="main-panel">
          <div class="content-wrapper">
						<div class="row">
							<div class="col-12 grid-margin stretch-card">
                <div class="card">
                  <div class="card-body">
										<?php
											if (User::userData('rank') > '5')
											{
											?>
											<div class="col-md-12">
												<section class="panel">
													<header class="panel-heading">
														User suchen
														<form action="" method="POST">
														</header>
														<div class="panel-body">
															<?php admin::searchUser(); ?>
															<div class="form-group">
																<label class="col-sm-2 col-sm-2 control-label">Username</label>
																<div class="col-sm-10">
																	<input type="text"  value="" name="user" class="form-control">
																</div>
															</div>
															<br><br>
															<button style="width: 140px;
															float: right;
															margin-right: 14px; margin-top: -98px;" name="zoek" type="submit" class="btn btn-success">Find User</button>
														</div>
													</section>
												</div>
											</form>
											<?php
											}
											else{
											?>
											<input type="hidden"  value="<?php echo admin::EditUser("zoek"); ?>" name="zoek" class="form-control" disabled>
											<?php
											}
										?>
                  </div>
                </div>
              </div>
							<div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                  <div class="card-body">
                    <h4 class="card-title">Striped Table</h4>
                    <p class="card-description"> Add class <code>.table-striped</code>
                    </p>
										<div class="container-fluid">
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th class="col-md-2">User</th>
                    <th class="col-md-2">Rank</th>
                    <th class="col-md-2">Motto</th>
                    <th class="col-md-2">Credits</th>
                    <th class="col-md-2">Last IP</th>
                    <th class="col-md-2">Reg IP</th>
                    <th class="col-md-2">Edit</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $getArticles = $dbh->prepare("SELECT * FROM users ORDER BY id DESC");
                    $getArticles->execute();
                    while ($users = $getArticles->fetch()) {
                        echo '<tr>
                                <td class="col-md-2">' . $users["username"] . '</td>
                                <td class="col-md-2">' . $users["rank"] . '</td>
                                <td class="col-md-2">' . $users["motto"] . '</td>
                                <td class="col-md-2">' . $users["credits"] . '</td>
                                <td class="col-md-2">' . $users["ip_last"] . '</td>
                                <td class="col-md-2">' . $users["ip_reg"] . '</td>
                                <td class="col-md-2"><a href=' . $config["hotelUrl"] . '/housekeeping/edituser/' . $users["username"] . '><i style="padding-top: 4px; color: green;" class="fa fa-edit"></i></a></td>
                              </tr>';
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

						</div>
          </div>
					<script>
    CKEDITOR.replace('editor2', {
        contentsLangDirection: 'ltr',
        toolbar: 'full'
    });

    // Weitere Anpassungen für den CKEditor-Container
    var editorContainer = document.getElementById('cke_editor2');
    if (editorContainer) {
        editorContainer.style.width = '120%';
    }
</script>
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
    <script src="assets/vendors/progressbar.js/progressbar.min.js"></script>
    <script src="assets/vendors/jvectormap/jquery-jvectormap.min.js"></script>
    <script src="assets/vendors/jvectormap/jquery-jvectormap-world-mill-en.js"></script>
    <script src="assets/vendors/owl-carousel-2/owl.carousel.min.js"></script>
    <!-- End plugin js for this page -->
    <!-- inject:js -->
    <script src="assets/js/off-canvas.js"></script>
    <script src="assets/js/hoverable-collapse.js"></script>
    <script src="assets/js/misc.js"></script>
    <script src="assets/js/settings.js"></script>
    <script src="assets/js/todolist.js"></script>
    <!-- endinject -->
    <!-- Custom js for this page -->
    <script src="assets/js/dashboard.js"></script>
    <!-- End custom js for this page -->
  </body>
</html>
