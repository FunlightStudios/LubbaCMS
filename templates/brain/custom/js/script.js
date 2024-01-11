@import "https://fonts.googleapis.com/css?family=Open+Sans:300,400";
body, .player .head .front, .player .head .infos, .player .timeline .volume, .player .timeline .controllers, .player .timeline .controllers .back, .player .timeline .controllers .play, .player .timeline .controllers .forward {
  display: flex;
  justify-content: center;
  align-items: center;
}

html {
  height: 100%;
}

body {
  font-family: 'Open Sans', sans-serif;
  width: 100%;
  min-height: 100%;
  background: linear-gradient(141deg, #0C5B5F 0%, rgba(0, 212, 153, 0.77) 75%);
}

*, *:before, *:after {
  box-sizing: border-box;
}

.rotation {
  display: block;
  width: 300px;
  height: 300px;
  position: absolute;
  top: 50%;
  left: 50%;
  margin: -150px -150px;
  border-radius: 50%;
  background-color: #fff;
  opacity: 0.3;
  z-index: -1;
  animation: 2s girandomt infinite forwards linear;
}

.player {
  border-radius: 6px;
  background-color: white;
  width: 500px;
  min-height: 200px;
  box-shadow: 0px 5px 20px 2px rgba(0, 0, 0, 0.2);
  overflow: hidden;
}
.player .head {
  padding: 15px;
  color: white;
  text-shadow: 0px 0px 2px rgba(0, 0, 0, 0.3);
  height: auto;
  width: 100%;
  position: relative;
  overflow: hidden;
}
.player .head .front {
  position: relative;
  height: 100%;
  justify-content: space-around;
}
.player .head .back {
  height: 110%;
  width: 110%;
  top: -10px;
  left: -10px;
  position: absolute;
  background-position: center;
  background-size: cover;
  background-image: url("https://images.unsplash.com/photo-1471623817296-aa07ae5c9f47");
  -webkit-filter: blur(5px);
  filter: blur(5px);
}
.player .head .avatar {
  width: 90px;
  height: 90px;
  overflow: hidden;
  border-radius: 50%;
  box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.3);
}
.player .head .avatar img {
  width: 100%;
  height: 100%;
}
.player .head .infos {
  justify-content: space-around;
  flex-direction: column;
  height: inherit;
  align-items: baseline;
}
.player .head .tags span {
  border-radius: 3px;
  padding: 2px 11px;
  margin: 1px 2px;
  background-color: #15A58B;
  font-weight: bold;
}
.player .head .titulo_song {
  font-size: 20px;
  text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.75);
}
.player .head .duracao_song {
  font-size: 12px;
  color: white;
  margin-bottom: 8px;
  text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.75);
}
.player .timeline {
  height: auto;
  width: 100%;
  position: relative;
  display: flex;
  justify-content: center;
  flex-direction: column;
}
.player .timeline .volume {
  flex-direction: row;
}
.player .timeline .controllers.active .play {
  box-shadow: 0px 0px 10px 2px rgba(30, 177, 150, 0.1);
  animation: 3s pulseshadowplay infinite both;
}
.player .timeline .controllers .back, .player .timeline .controllers .play, .player .timeline .controllers .forward {
  font-size: 16px;
  margin: 10px;
  color: #4A4A4A;
  width: 40px;
  height: 40px;
  border-radius: 50%;
  border: 1px solid transparent;
}
.player .timeline .controllers .play {
  transition: all 0.3s ease-in-out;
}
.player .timeline .controllers .play:hover {
  box-shadow: 0px 0px 10px 2px rgba(30, 177, 150, 0.38);
  border: 2px solid rgba(143, 208, 196, 0.54) !important;
}
.player .timeline .controllers .back::after {
  content: '\f04a';
  font-family: 'FontAwesome';
  margin-right: 5px;
}
.player .timeline .controllers .play::after {
  content: '\f04b';
  font-family: 'FontAwesome';
  margin-left: 5px;
}
.player .timeline .controllers .forward::after {
  content: '\f04e';
  font-family: 'FontAwesome';
  margin-left: 5px;
}
.player .timeline .soundline {
  width: 100%;
  height: 6px;
  position: relative;
  background: #F3F3F3;
  border-radius: 2px;
  overflow: hidden;
}
.player .timeline .soundline::after {
  display: block;
  position: absolute;
  content: '';
  top: 0;
  left: 0;
  width: 0%;
  height: 100%;
  background: #6CE59C;
  box-shadow: 0px 0px 9px #94FFBF;
  transition: all 0.4s cubic-bezier(0.07, 0.82, 0.11, 1.02);
  animation: 20s soundline infinite both linear;
}

@keyframes pulseshadowplay {
  0% {
    box-shadow: 0px 0px 10px 2px rgba(30, 177, 150, 0.1);
  }
  50% {
    box-shadow: 0px 0px 50px 2px rgba(30, 177, 150, 0.38);
  }
}
@keyframes soundline {
  0% {
    width: 0%;
  }
  100% {
    width: 100%;
  }
}
@keyframes girandomt {
  0% {
    box-shadow: 0px 0px 0px 0px rgba(255, 255, 255, 0.8);
  }
  100% {
    box-shadow: 0px 0px 30px 40px rgba(255, 255, 255, 0);
  }
}