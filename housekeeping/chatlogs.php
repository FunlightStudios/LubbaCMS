
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
													Room Chatlogs<br>
													<form name="mygallery" action="" method="POST">
													</header>
													<div class="panel-body">
														<table class="table table-striped table-hover">
															<b>	<strong><tr><td><b>ID</b></td><td><b>User ID</b></td><td><b>Room ID</b></td><td><b>Message</b></td><td><b>Date</b></td></tr></strong></b
															<tbody>
															<?php
																$getArticles = $dbh->prepare("SELECT * FROM chatlogs ORDER BY id DESC  LIMIT 1000");
																$getArticles->execute();
																while($news = $getArticles->fetch())
																{
																	echo'';
																	echo'<tr>
																	<td>'.$news["id"].'</td>
																	<td style="width: 13%;">'.$news["user_id"].'</td>
																	<td style="width: 7%;">'.$news["room_id"].'</td>
																	<td style="width: 25%;">'.$news["message"].'</td>
																	<td>'. gmdate('d-m-Y, H:i ', $news['timestamp']).'</td>
																	';
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
