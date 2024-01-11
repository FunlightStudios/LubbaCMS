
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
            <!-- Erste Karte -->
            <div class="col-md-6 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <?php admin::PostNews(); ?>
                        <header class="card-title">
                            Create a News Article<br><br>
                            <form name="mygallery" action="" method="POST">
                        </header>
                        <div style="margin-right: -120px;" class="panel-body">

									<?php admin::PostNews(); ?>
									<div class="form-group">
										<label class="col-sm-2 col-sm-2 control-label">Title</label>
										<div class="col-sm-10">
											<input type="text" value="<?php echo $_SESSION['title']; ?>" name="title"class="form-control">
										</div>
									</div><br><br>
									<div class="form-group">
										<label class="col-sm-2 col-sm-2 control-label">Short Story</label>
										<div class="col-sm-10">
											<input type="text" value="<?php echo $_SESSION['slogan']; ?>" name="slogan"class="form-control">
										</div>
									</div><br><br>
									<div class="form-group">
										<label class="col-sm-2 col-sm-2 control-label">Image</label>
										<div class="col-sm-10">
											<?php
												echo '<select onChange="showimage()" class="form-control" name="topstory" style="    width: 100%;font-size: 14px;"';
												if ($handle = opendir(''.$_SERVER['DOCUMENT_ROOT'].'/housekeeping/assets/newsimages'))
												{
													while (false !== ($file = readdir($handle)))
													{
														echo'';
														if ($file == '.' || $file == '..')
														{
															continue;
														}
														echo '<option name="topstory" data-image="'.$config['hotelUrl'].'/housekeeping/assets/newsimages/' . $file . '" value="'.$config['hotelUrl'].'/housekeeping/assets/newsimages/' . $file . '"';
														if (isset($_POST['topstory']) && $_POST['topstory'] == $file)
														{
															echo ' selected';
														}
														echo '>' . $file . '</option>';
													}
												}
												echo '</select>';
											?>
											<br>
											<style>
											.imagebox {

							border-radius: 6px;
							float: left;
							margin-right: 0.72pc;
							margin-bottom: 10px;
							webkit-box-shadow: 0 3px rgba(0,0,0,.17), inset 0px 0px 0px 1px rgba(0,0,0,0.31), inset 0 0 0 2px rgba(255,255,255,0.44)!important;
							-moz-box-shadow: 0 3px rgba(0,0,0,.17), inset 0px 0px 0px 1px rgba(0,0,0,0.31), inset 0 0 0 2px rgba(255,255,255,0.44)!important;
							box-shadow: 0 3px rgba(0,0,0,.17), inset 0px 0px 0px 1px rgba(0,0,0,0.31), inset 0 0 0 2px rgba(255,255,255,0.44)!important;
					}

					.imagebox img {
							width: 100%; /* Damit das Bild die Breite des Containers füllt */
							border-radius: 6px;

					}
											</style>
											<div class="imagebox">
												<img style="border-radius: 6px;"src="<?= $config['hotelUrl'];?>/housekeeping/assets/newsimages/choose.gif" name="topstory" border=0>
											</div>
											<br><br><br><br><br><br><br><br><br><br><br>
										</div>
									</div>
									<br><br>
									<script src="https://cdn.ckeditor.com/4.16.2/full/ckeditor.js"></script>

									<div class="form-group">
    <label class="col-sm-2 col-sm-2 control-label">Long Story</label>
    <div class="col-sm-10">
        <textarea id="editor2" name="news" rows="15" cols="80"><?php echo $_SESSION['news']; ?></textarea>
    </div>
</div><br><br>
                        <button style="width: 130px; float: left; margin-right: 14px;" name="postnews" type="submit" class="btn btn-success">Post the Article</button>
                    </div>
                </div>
            </div>

            <!-- Zweite Karte -->
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="col-md-12">
                            <section class="panel">
                                <header class="panel-heading">
                                    All Existing News Articles<br><br>
                                    <div class="panel-body">
                                        <?php admin::DeleteNews(); ?>
																				<div class="table-responsive">
                                        <table class="table table-striped table-hover">
																					<thead>
															                <tr>
															                    <th class="col-md-2">ID</th>
															                    <th class="col-md-2">Titel</th>
															                    <th class="col-md-2">Short</th>
															                    <th class="col-md-2">Author</th>
															                    <th class="col-md-2">Datum</th>
															                </tr>
															            </thead>
                                            <tbody>
                                                <?php
                                                $getArticles = $dbh->prepare("SELECT * FROM cms_news ORDER BY id DESC");
                                                $getArticles->execute();

                                                while ($news = $getArticles->fetch()) {
                                                    $shortStory = substr($news["shortstory"], 0, 22);
                                                    echo '<tr>
                                                            <td>' . $news["id"] . '</td>
                                                            <td>' . $news["title"] . '</td>
                                                            <td>' . $shortStory . '...</td>
                                                            <td>' . $news["author"] . '</td>
                                                            <td>' . date('d-m-Y', $news['date']) . '</td>
                                                            <td><center><a href="' . $config['hotelUrl'] . '/adminpan/news/edit/' . $news["id"] . '"><i style="padding-top: 5px; color:green;" class="fa fa-edit"></i></a></td>
                                                            <td><a href=' . $config['hotelUrl'] . '/adminpan/news/delete/' . $news["id"] . '><i style="padding-top: 4px; color:red;" class="fa fa-trash"></i></center></a></td>
                                                        </tr>';
                                                }
                                                ?>
                                            </tbody>
                                        </table>
																			</div>
                                    </div>
                                </header>
                            </section>
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
              <span class="text-muted d-block text-center text-sm-left d-sm-inline-block">Copyright © 2024 by Funlight Studios</span>
              <span class="text-muted float-none float-sm-right d-block mt-1 mt-sm-0 text-center">LubbaCMS Alpha w18y23a</span>
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
