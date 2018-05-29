<?php
require_once 'Teamspeak3/TeamSpeak3.php';
include 'config.php';

try{
$user = $_GET['user'];

// Find user
$clientDBInfo = $ts3->clientGetByName($user)->getInfo();

//var_dump($clientDBInfo);
//vars
$onlineTime = $clientDBInfo['connection_connected_time']/1000;
$idleTime = $clientDBInfo['client_idle_time']/1000;
$lastConnected = $clientDBInfo['client_lastconnected'];
$nickname = $clientDBInfo['client_nickname'];
$totalConnections = $clientDBInfo['client_totalconnections'];

//last connected time.
echo 'Last Connected: ' . gmdate("Y-m-d\ H:i:s\ ", $lastConnected) . PHP_EOL;

// Last nickname:
echo '<br>Nickname: ' . $nickname;

//Total Connections to server
echo '<br>Total connections: ' . $totalConnections;

//Online Time
echo '<br>Online Time: ' . $onlineTime;

//Idle Time
echo '<br>Idle Time: ' . $idleTime;

//statuses
echo '<br>Mic Status: ';
if($clientDBInfo['client_input_muted'] == 0){
   echo 'enabled';
 }else{
   echo 'disabled';
 }
 echo '<br>Speaker Status: ';
 if($clientDBInfo['client_output_muted'] == 0){
    echo 'enabled';
  }else{
    echo 'disabled';
  }

if($clientDBInfo = true){
  echo '<br>User Online';
}

$findUser = $odb -> prepare("SELECT COUNT(*) FROM `teamspeak` WHERE `UniqueID` = :username");
$findUser -> execute(array(':username' => $user));
$findUser = $findUser -> fetchColumn(0);

$findTime = $odb -> prepare("SELECT `Time` FROM `teamspeak` WHERE `UniqueID` = :username");
$findTime -> execute(array(':username' => $user));
$findTime = $findTime -> fetchColumn(0);

$findIdle = $odb -> prepare("SELECT `Idle` FROM `teamspeak` WHERE `UniqueID` = :username");
$findIdle -> execute(array(':username' => $user));
$findIdle = $findIdle -> fetchColumn(0);

//echo $findTime;
$time = $findTime + $timePerCredit;

if($findUser == 0 && $onlineTime >= $timePerCredit && $clientDBInfo['client_output_muted'] == $soundRequired && $clientDBInfo['client_input_muted'] == $micRequired){
  $addUser = $odb->prepare("INSERT INTO `teamspeak` (`UniqueID`,`Credits`,`Time`,`Idle`) VALUES (:ID, :credits, :onlinetime, :idle);");
  $addUser->execute(array( ":ID" => $user, ":credits" => $creditsPer, ":onlinetime" => $onlineTime, ":idle" => $idleTime));
  //$logAddr = $odb->prepare("UPDATE `teamspeak` SET `Credits` = Credits + 1 WHERE `UniqueID` = :ID;");
  //$logAddr->execute(array( ":ID" => $user));
}else if(!($findUser == 0) && $onlineTime >= $timePerCredit && $onlineTime >= $time && $findIdle <= $noCredits && $clientDBInfo['client_output_muted'] == $soundRequired && $clientDBInfo['client_input_muted'] == $micRequired){
  $updateUser = $odb->prepare("UPDATE `teamspeak` SET `Credits` = Credits + :credits, `Time` = :newTime, `Idle` = :idle WHERE `UniqueID` = :ID;");
  $updateUser->execute(array( ":ID" => $user, ":credits" => $creditsPer, ":newTime" => $onlineTime, ":idle" => $idleTime));
}else{
  $updateUser = $odb->prepare("UPDATE `teamspeak` SET `Idle` = :idle WHERE `UniqueID` = :ID;");
  $updateUser->execute(array( ":ID" => $user, ":idle" => $idleTime));
}

}catch(TeamSpeak3_Adapter_ServerQuery_Exception $e)
{
    // catch error if client was not found on the server
    echo "User offline";
    //reset timer
    $resetTime = $odb->prepare("UPDATE `teamspeak` SET `Time` = 0, `Idle` = 0 WHERE `UniqueID` = :ID;");
    $resetTime->execute(array( ":ID" => $user));
}
