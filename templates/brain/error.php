<head>
   <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Ubuntu:regular,bold&subset=Latin">
   <link rel="stylesheet" href="/templates/brain/style/css/main2.css?v=<?= $config['hash'] ?>" type="text/css">
   <link rel="stylesheet" href="/templates/brain/style/css/home.css?v=<?= $config['hash'] ?>" type="text/css">
</head>
<style>
   input[type="submitred"], input:-webkit-autofill, .submitred {
   -webkit-appearance: none;
   border-radius: 3px;
   height: 35px;
   width: 100%;
   background: #c70c0c;
   border-bottom: 2px solid #3c0606;
   color: rgba(255,255,255,1);
   cursor: pointer;
   text-align: center;
   display: block;
   text-decoration: none;
   border: 0px;
   }
</style>
<title><?= $config['hotelName'] ?>: Client Error!</title>
<div class="center">
   <div style="width: 100%;"class="columleft">
      <div class="box">
         <div class="title red">
            <center>Client Error!</center>
         </div>
         <center>
            <font color="red">
               <h1>BITTE ENTSCHULDIGE UNS!</h1>
            </font>
            <font color="white">
            <b>Etwas ist schiefgelaufen, vielleicht hat Frank sich verirrt?</b>
            <br>
            <br>
            <i>Bitte versuche es erneut dich im Client einzuloggen! Sollte dies nach mehrmaligem veruchen nicht klappen schau doch auf unserem <a style="color:white" href="<?= $config["discordUrl"] ?>">Discord</a> bei den Störungsmeldungen vorbei!</i>
            <br>
            <br>
            </font>
            <img src="/templates/brain/style/images/account/Frank_26.webp">
            <a href="/client">	<input type="submit" class="submit" value=" Flash Client " href="/client" style="margin-top: 10px; float:right;"> </a>
            <a href="/nitro">	<input type="submit" class="submit" value=" Nitro Client " href="/nitro" style="margin-top: 60px; float:right; margin-right: -90px"> </a>
            <a href="/logout">	<input type="submitred" class="submitred" value="Ausloggen" href="/logout" style="margin-top: 10px;"> </a>
         </center>
         <!--<head>
            <meta http-equiv="refresh" content="10;url=/game">
         </head>-->
      </div>
   </div>
</div>
</div>
</body>
</html>