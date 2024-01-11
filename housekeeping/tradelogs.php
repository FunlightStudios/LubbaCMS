
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
													<b>Trade Logs</b><br><code>Unknown items are either exchanged credits/clothing</code><br><br>
													<form name="mygallery" action="" method="POST">
													</header>
													<div class="panel-body">
														<table class="table table-striped table-hover">
															<b>	<strong><tr><td><b>Trade (ID)</b></td><td><b>User 1 Trades</b></td><td><b>User 1 Gives</b></td><td><b>User 2 Trades</b></td><td><b>User 2 Gives</b></td><td><b>Date/Time</b></td></tr></strong></b
															<tbody>
															<?php
																$getLogs = $dbh->prepare("SELECT logs_client_trade.*, users.username FROM logs_client_trade INNER JOIN users ON users.id = logs_client_trade.1id ORDER BY id DESC LIMIT 100");
																$getLogs->execute();
																while($log = $getLogs->fetch())
																{
																	echo'<tr>
																	<td>'.$log["id"].'</td>
																	<td style="width: 13%;">'.$log["username"].'</td>
																	<td style="width: 25%;">';
																	if(!empty($log['1items'])){
																		$aitems = array();
																		foreach(array_diff( explode(";", $log['1items']), array('')) as $item){
																			$itemLookup = $dbh->prepare("SELECT items.base_item, catalog_items.catalog_name FROM items INNER JOIN catalog_items ON catalog_items.item_id = items.base_item WHERE items.id = ?");
																			$itemLookup->execute(array($item));
																			$item = $itemLookup->fetch(PDO::FETCH_ASSOC);
																			$id = $item['base_item'];
																			if(!$item){
																				$id = 'uk';
																				$aitems[$id]['name'] = 'Unknown';
																			}else{
																				$aitems[$id]['name'] = $item['catalog_name'];
																			}
																			if(strlen($id) == 0)
																				break;
																			if($aitems[$id]){
																				$aitems[$id]['count']++;
																			}else{
																				$aitems[$id]['count'] = 1;
																			}
																		}
																		foreach($aitems as $base){
																			echo $base['count'] . "x ". $base['name'].'<br />';
																		}
																	}
																	echo '</td>
																	<td style="width: 7%;">';
																	$userLookup = $dbh->prepare("SELECT username FROM users WHERE id = ?");
																	$userLookup->execute(array($log['2id']));
																	$user = $userLookup->fetch(PDO::FETCH_ASSOC);
																	echo $user['username'].'</td>
																	<td style="width: 25%;">';
																	if(!empty($log['2items'])){
																		$bitems = array();
																		foreach(array_diff( explode(";", $log['2items']), array('')) as $item){
																			$itemLookup = $dbh->prepare("SELECT items.base_item, catalog_items.catalog_name FROM items INNER JOIN catalog_items ON catalog_items.item_id = items.base_item WHERE items.id = ?");
																			$itemLookup->execute(array($item));
																			$item = $itemLookup->fetch(PDO::FETCH_ASSOC);
																			$id = $item['base_item'];
																			if(!$item){
																				$id = 'uk';
																				$bitems[$id]['name'] = 'Unknown';
																			}else{
																				$bitems[$id]['name'] = $item['catalog_name'];
																			}
																			if(strlen($id) == 0)
																				break;
																			if($bitems[$id]){
																				$bitems[$id]['count']++;
																			}else{
																				$bitems[$id]['count'] = 1;
																			}
																		}
																		foreach($bitems as $base){
																			echo $base['count'] . "x ". $base['name'].'<br />';
																		}
																	}
																	echo '</td>
																	<td>'. gmdate('d-m-Y, H:i ', $log['timestamp']).'</td>
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
