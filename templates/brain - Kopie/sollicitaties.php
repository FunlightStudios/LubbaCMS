<?php
	include_once 'includes/header.php';
?>
<title><?= $config['hotelName'] ?>: <?= $lang["Nstaffapply"] ?></title>
<div class="center">
<?php
		include_once 'includes/alerts.php';
	?>
	<div class="columleft">
		<div class="box">
			<div class="title">
				<?= $lang["Nstaffapply"] ?>
			</div>
			<div class="mainBox" style="float;left">
				<div class="boxHeader"></div>
				<form action="" method="POST">
					<?php staffApplication(); ?>
					<center><h3><font color="blue"></center></h3></font><img src="/templates/brain/style/images/icons/solli_succes.gif" align="right"> 
					<h1><?= $lang["Nstaffapply"] ?></h1>
					<hr><h2 style='font-family: verdana'><?= $lang["Naboutyourself"] ?></h2>
					<label><b style='font-family: verdana'><?= $lang["Nyourname"] ?></b><br>
					<input type="text" name="username" size="400" placeholder="<?= $lang["Nyourname"] ?>" value= "<?= User::userData('username') ?>" id="username" style="width: 100%;" disabled>
					<p><label><b style='font-family: verdana'><?= $lang["Nyourrealname"] ?></b><br>
					<input type="text" name="realname" size="400" placeholder="<?= $lang["Nyourrealname"] ?>" value= "" id="username" style="width: 100%;"></p>
					<p><label><b style='font-family: verdana'>Skype:</b><br>
					<input type="text" name="skype" size="400" placeholder="Skype" value= "" id="username" style="width: 100%;"></p>
					<p><label><b style='font-family: verdana'><?= $lang["Nyourage"] ?></b><br>
					<input type="number" name="age" size="400" placeholder="<?= $lang["Nyourage"] ?>" value= "" id="username" style="width: 100%;"></p>
					<hr><h2 style='font-family: verdana'><?= $lang["Nstaffapply"] ?></h2>
					</p>
					<label><b style='font-family: verdana'><?= $lang["Nfunction"] ?></b>
					<select style="width: 100%;" name="functie" class="form-control">
		            <option name="functie" value="1">Junior Moderator</option>
					</select></p>
					<p><label><b style='font-family: verdana'><?= $lang["Nonlineweak"] ?></b><br>
					<input type="number" name="onlinetime" size="400" placeholder="10" id="amount" style="width: 100%;"></p>
					<p><label><b style='font-family: verdana'><?= $lang["Nyourexperience"] ?></b><br>
					<textarea name="experience" size="400" rows="5" cols="50" style="width: 100%;"> </textarea></p>
					<p><label><b style='font-family: verdana'><?= $lang["Npeoplearguing"] ?></b><br>
					<textarea name="quarrel" size="400" rows="5" cols="50" style="width: 100%;"> </textarea></p>
					<p><label><b style='font-family: verdana'><?= $lang["Nyoutrust"] ?></b><br>
					<textarea name="serious" size="400" rows="5" cols="50" style="width: 100%;"> </textarea></p>
					<p><label><b style='font-family: verdana'><?= $lang["Nimprovehotel"] ?></b><br>
					<textarea name="improve" size="400" rows="5" cols="50" style="width: 100%;"> </textarea></p>
					<input type="submit" value="<?= $lang["Nsubmit"] ?>" name="addsollie" class="submit" style="float:right" >
					</form>
					</div>
					</div></div>
					<div class="columright">
						<div class="box">
						<div class="title blue">Wie zijn jou voor gegaan!</div>
						Hieronder een lijstje met jou voorgangers die door de sollicitaties aan zijn gekomen:<br>
						<ul><li> Nog geen medewerker aangenomen.</li>
						<li> Nog geen medewerker aangenomen.</li>
						<li> Nog geen medewerker aangenomen.</li>
						<li> Nog geen medewerker aangenomen.</li>
						</ul>
						
						<div class="title red">Onze mijlpalen.</div>
						De sollicitaties op Pixelmoon staan altijd open zoals je kunt zien. Maar de uitslagen
						van elke sollicitatie rondes. Hier zitten mijlpalen aan gebonden.<br>
						<ul>
						<li>Bij de <b>15 online</b> kiest ons management <b>1 Junior moderator</b> uit.</li>
						<li>Bij de <b>25 online</b> kiest ons management <b>1 Juniort moderator</b> uit.</li>
						<li>Bij de <b>30 online</b> kiest ons management <b>1 Junior moderator</b> uit.</li>
						<li>Bij de <b>35 online</b> kiest ons management <b>1 Evenementen medewerker</b> uit.</li>
						<li>Bij de <b>45 online</b> kiest ons management <b>1 Evenementen medewerker</b> uit.</li>
						</ul>
							<div class="title green">
								<?= $lang["Nstaffapply"] ?>
							</div>
							<?= $lang["NText"] ?>						
							<?php
								include_once 'includes/footer.php';
							?>
							
						</div>
					</div>
					
					<style>
input[type=text], select {
    width: 100%;
    padding: 5px 15px;
    margin: 8px 0;
    display: inline-block;
    border: 1px solid #ccc;
    border-radius: 4px;
    box-sizing: border-box;
	background-color: #f8f8f8;
	font-family:	verdana;
}

input[type=number], select {
    width: 100%;
    padding: 5px 15px;
    margin: 8px 0;
    display: inline-block;
    border: 1px solid #ccc;
    border-radius: 4px;
    box-sizing: border-box;
	background-color: #f8f8f8;
}


textarea {
    width: 100%;
    height: 100px;
    padding: 12px 20px;
    box-sizing: border-box;
    border: 1px solid #ccc;
    border-radius: 4px;
    background-color: #f8f8f8;
    font-size: 16px;
    resize: none;
}
input[type=submit] {
    width: 100%;
    background-color: #4CAF50;
    color: white;
    padding: 14px 20px;
    margin: 8px 0;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

input[type=submit]:hover {
    background-color: #45a049;
}

</style>
					</body>
					</html>																				