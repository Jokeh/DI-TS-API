<?php

// Connect to server
$ts3 = TeamSpeak3::factory("serverquery://serveradmin:zcC8iqon@127.0.0.1:10011/?nickname=Bot&server_port=9987");

//database config
define('DBHOST','localhost');
define('DBUSER','root');
define('DBPASS','');
define('DBNAME','di');

//config
$timePerCredit = 60;
//0 = yes 1 = no
$micRequired = 0;
//0 = yes 1 = no
$soundRequired = 0;
//credits per $timePerCredit
$creditsPer = 1;
//Don't give credits after idle timer
$noCredits = 1800;









//Database connection shit xD kappa blaze meme 420
$odb = new PDO("mysql:host=".DBHOST.";port=3306;dbname=".DBNAME, DBUSER, DBPASS);
$odb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

 ?>
