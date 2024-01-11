
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
										<div class="col-md-12">
											<section class="panel">
												<header class="panel-heading">
													<b><?php echo admin::LookSollie("username"); ?>'s Application</b>.
												</header>
												<div class="panel-body">
													<?php admin::DeleteSollie(); ?>
													<div class="col-md-6">
														Username:<br><b><?php echo admin::LookSollie("username"); ?></b> <br><br>
														Real Name:<br><b><?php echo admin::LookSollie("realname"); ?></b> <br><br>
														Age:<br><b><?php echo admin::LookSollie("age"); ?></b> <br><br>
														Skype:<br><b><?php echo admin::LookSollie("skype"); ?></b> <br><br>
														Rank:<br><b><?php echo admin::LookSollie("functie"); ?></b> <br><br>
														Number of hours online per week:<br><b><?php echo admin::LookSollie("onlinetime"); ?></b> <br><br>
														Experience at other hotels:<br><b><?php echo admin::LookSollie("experience"); ?></b> <br><br>
													</div>
													<div class="col-md-6">
														If 2 people have an argument:<br><b><?php echo admin::LookSollie("quarrel"); ?></b> <br><br>
														Trust and Serious:<br><b><?php echo admin::LookSollie("serious"); ?></b> <br><br>
														What Can you bring to the Hotel:<br><b><?php echo admin::LookSollie("improve"); ?></b> <br><br>
														Date of apply:<br><b><?php echo date('d-m-Y H:i', admin::LookSollie("date")) ?></b> <br><br>
														IP of the User:<br><b><?php echo admin::LookSollie("ip"); ?></b> <br><br>
														<?php
															if (User::userData('rank') > '8')
															{
															?>
															<a href = "<?php echo''.$config['hotelUrl'].'/adminpan/sollielook/delete/'.admin::LookSollie("id").'' ?>"><button style="width: 200px;
																float: left;
																margin-right: 14px;" value="<?php echo admin::LookSollie("id"); ?>" name="DeleteSollieNow" type="submit" class="btn btn-danger">Remove Application</button>
															</a></div>
															<?php
															}
															else{
															?>
															<?php
															}
													?>
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
