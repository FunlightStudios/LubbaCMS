<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8"/>
        <title>Lubba: Wartungsarbeiten</title>
        <style>
            @import url('//fonts.googleapis.com/css2?family=Ubuntu&display=swap');
            @-webkit-keyframes fadeIn { from {opacity: 0; } to { opacity: 1; } }
            @-moz-keyframes fadeIn { from {opacity: 0; } to { opacity: 1; } }
            @keyframes fadeIn { from {opacity: 0; } to { opacity: 1; } }
            body { margin: 0; padding: 0; font-size: 16px; font-family: Ubuntu; color: #fff; background: rgb(0,80,100); overflow: hidden; }
            #overlay { position:absolute; width:100%; height:100%; background: url('https://i.imgur.com/37fCSlp.png') no-repeat 102% 220px; animation: fadeIn 1s ease; -moz-animation: fadeIn 1s ease; -webkit-animation: fadeIn 1s ease; }
            #content { position: absolute; margin: auto; top: 0; right: 0; bottom: 0; left: 0; width: 700px; height: 500px; animation: fadeIn 3s ease-in; }
            #title { clear:both; font-size:52px; margin:15px 0 0 120px; }
            #message { clear:both; font-size:30px; text-align:center; }
			#copyright { clear:both; font-size:12px; text-align:center; }
            img { width: 200px; clear: both; margin: 0 0 0 260px; }
        </style>
    </head>
    <body>
        <div id="overlay"></div>
        <div id="content">
            <img src="/templates/brain/style/images/logo/logo.png" />
            <p id="title">Wartungsarbeiten</p>
            <p id="message">Wir arbeiten derzeit an unserer Hoteltechnik. <br />Bitte versuchs später nochmal!</p><br>
			<p id="copyright">Copyright © 2011 - 2023 by Lubba Hotel</p>
        </div>
    </body>
</html>