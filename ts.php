<?php
require_once 'Teamspeak3/TeamSpeak3.php';
include 'config.php';

try{
$user = $_GET['user'];

// Find user
$clientDBInfo = $ts3->clientGetByName($user)->getInfo();

//var_dump($clientDBInfo);
//vars
$idleTime = $clientDBInfo['client_idle_time']/1000;
$lastConnected = $clientDBInfo['client_lastconnected'];
$nickname = $clientDBInfo['client_nickname'];
$totalConnections = $clientDBInfo['client_totalconnections'];
$channel = $clientDBInfo->cid;

//last connected time.
echo 'Last Connected: ' . gmdate("Y-m-d\ H:i:s\ ", $lastConnected) . PHP_EOL;

// Last nickname:
echo '<br>Nickname: ' . $nickname;

//Total Connections to server
echo '<br>Total connections: ' . $totalConnections;

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

if($findUser == 0 && $idleTime >= $timePerCredit && $clientDBInfo['client_output_muted'] == $soundRequired && $clientDBInfo['client_input_muted'] == $micRequired){
  $addUser = $odb->prepare("INSERT INTO `teamspeak` (`UniqueID`,`Credits`,`Time`) VALUES (:ID, :credits, :idletime);");
  $addUser->execute(array( ":ID" => $user, ":credits" => $creditsPer, ":idletime" => $idleTime));
  //$logAddr = $odb->prepare("UPDATE `teamspeak` SET `Credits` = Credits + 1 WHERE `UniqueID` = :ID;");
  //$logAddr->execute(array( ":ID" => $user));
}else if(!($findUser == 0) && $idleTime >= $timePerCredit && $clientDBInfo['client_output_muted'] == $soundRequired && $clientDBInfo['client_input_muted'] == $micRequired){
  $updateUser = $odb->prepare("UPDATE `teamspeak` SET `Credits` = Credits + :credits WHERE `UniqueID` = :ID;");
  $updateUser->execute(array( ":ID" => $user, ":credits" => $creditsPer));
}

}catch(TeamSpeak3_Adapter_ServerQuery_Exception $e)
{
    // catch error if client was not found on the server
    echo "User offline";
    //reset timer
    $resetTime = $odb->prepare("UPDATE `teamspeak` SET `Time` = 0 WHERE `UniqueID` = :ID;");
    $resetTime->execute(array( ":ID" => $user));
}
