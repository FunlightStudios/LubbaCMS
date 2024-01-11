<?php include_once'includes/header.php' ?>
<title> <?= $config['hotelName'] ?> ~ BouwTeal</title>       

	<div class="container_24">
       <div id="center" class="marginTop">
        <div id="left">
          <div class="box">
                <div class="title pink">
                    BouwTeam Beheer
                </div>
                 <?php
							{
								$getMembers = $dbh->prepare("SELECT id,username,motto,look,online FROM users WHERE hulptroepen = 31");
								$getMembers->execute();
								if ($getMembers->RowCount() > 0)
									{
                                                while ($member = $getMembers->fetch())
											{	
												$userId = filter($member['id']);
												$username = filter($member['username']);
												$motto = filter($member['motto']);
												$look = filter($member['look']);
												$last_online = filter($member['last_online']);
												$online = filter($member['online']);
												if($online == 1){ $OnlineStatus = "<img src='/templates/Nabbo/style/images/cms/online.gif'>"; } else { $OnlineStatus = "<img src='/templates/Nabbo/style/images/cms/offline.png'>";}
												
                    ?>
					
					<div style="background: linear-gradient(rgba(0, 0, 0, 0.1), rgba(0, 0, 0, 0.4)), url(https://www.nabbohotel.nl/templates/Nabbo/style/images/staff/bouw_team.png)" class="staffBox"><a href='/home/<?php echo $username; ?>'><img class="head" src="http://avatar-retro.com/habbo-imaging/avatarimage?figure=<?php echo $look; ?>&head_direction=3&action=sit,wav&gesture=sml"></a><div class="RightSide"><div class="nameStaff"><?php echo $username; ?></div><br><div class="functStaff"><?php echo $motto; ?>									
                    <p><td><?php if ($online == 0)echo $OnlineStatus; ?><?php if ($online == 1)echo $OnlineStatus; ?></span></td></p>
					 </div></div></div>
					
					<?php
											}
									}
									else
							{
								echo "<p style='margin-left:10px;'>".$lang['Snostaff'];"</p>";
							}
							}
                    ?>
					</div>

          <div class="box">
           <div class="title purple">
                     Bouwteam
                </div>           
          <?php
							{
								$getMembers = $dbh->prepare("SELECT id,username,motto,look,online FROM users WHERE hulptroepen = 30");
								$getMembers->execute();
								if ($getMembers->RowCount() > 0)
									{
                                                while ($member = $getMembers->fetch())
											{	
												$userId = filter($member['id']);
												$username = filter($member['username']);
												$motto = filter($member['motto']);
												$look = filter($member['look']);
												$last_online = filter($member['last_online']);
												$online = filter($member['online']);
												if($online == 1){ $OnlineStatus = "<img src='/templates/Nabbo/style/images/cms/online.gif'>"; } else { $OnlineStatus = "<img src='/templates/Nabbo/style/images/cms/offline.png'>";}
												
                    ?>
					
					<div style="background: linear-gradient(rgba(0, 0, 0, 0.1), rgba(0, 0, 0, 0.4)), url(https://www.nabbohotel.nl/templates/Nabbo/style/images/staff/dj_team.png)" class="staffBox"><a href='/home/<?php echo $username; ?>'><img class="head" src="http://avatar-retro.com/habbo-imaging/avatarimage?figure=<?php echo $look; ?>&head_direction=3&action=sit,wav&gesture=sml"></a><div class="RightSide"><div class="nameStaff"><?php echo $username; ?></div><br><div class="functStaff"><?php echo $motto; ?>									
                    <p><td><?php if ($online == 0)echo $OnlineStatus; ?><?php if ($online == 1)echo $OnlineStatus; ?></span></td></p>                     
					 </div></div></div>
					
					<?php
											}
									}
									else
							{
								echo "<p style='margin-left:10px;'>".$lang['Snostaff'];"</p>";
							}
							}
                    ?>
          </div>
          </div>
	<div id="right">
		<div class="box">
                <div class="title red">
                     DeejayTeam
                </div>
            <p>Deejays Laat die beat maar starten, deejays zorgen voor de muziek in het hotel. Als je wilt solliciteren voor deejay, moet je wachten totdat ze open staan in het nieuws! NabboStream maakt samen met alle deejays elke dag een feest op de radio voor jou!</div>

                    </div>
               	    </div>
            	</div>
       	    </div>


</body>
</html>
</body></html>