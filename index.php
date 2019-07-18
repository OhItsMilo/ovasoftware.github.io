<html>
<body>
<?php
 session_start();
// print_r($_SESSION);
 if(isset($_GET["logout"])){
  session_destroy();
 }
$redirect_uri ='http://localhost/calendar/relax.php';
    require_once '/Google/vendor/autoload.php';
    $client = new Google_Client();
    // Get your credentials from the console
$client->setClientId('355731952390-qilsv3gg9vvtbgccd1aq1b09mj1nkoou.apps.googleusercontent.com');
$client->setClientSecret('TK1X-ESiILjZ1rF83M5FF8LM');
    $client->setRedirectUri($redirect_uri);
$client->addScope('profile');

$client->addScope(Google_Service_Calendar::CALENDAR);


print_r($client->getAccessToken());

    $authUrl = $client->createAuthUrl();
    if (isset($_GET['code'])) {

  $client->authenticate($_GET['code']);
  $_SESSION['access_token'] = $client->getAccessToken();
  header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));

    }
    if (!$client->getAccessToken() && !isset($_SESSION['access_token'])) {
        $authUrl = $client->createAuthUrl();
        print "<a class='login' href='$authUrl'>Conectar</a>";
    }        
   if (isset($_SESSION['access_token'])) {

       print "<a class='logout' href='".$_SERVER['PHP_SELF']."?logout=1'>Salir</a><br>";
      $client->setAccessToken($_SESSION['access_token']);

      $service = new Google_Service_Calendar($client);
      //Code
      $mes=date('m');
      $ano=date('Y');
      $dia=date('d');
       
    
      $start_date=$ano."-".$mes."-".$dia."T00:00:00-00:00";
      $end_date=$ano."-".$mes."-".$dia."T23:59:59-00:00";
  $results = $service->events->listEvents('primary', array('timeMin'=>$start_date, 'timeMax'=>$end_date,'timeZone'=>'America/Chicago'));
if (count($results->getItems()) == 0) {
  print "<h3>No hay Eventos</h3>";
} else {
  print "<h3>Proximos Eventos</h3>";
  $n=$_GET['code'];
   print "<h3>Codigo:</h3>".$n;
  echo "<table border=1>";
  foreach ($results->getItems() as $event) {
    echo "<tr>";
    $start = $event->start->dateTime;
    
    $month=date("m/d/Y h:i",strtotime($start));
    $endHour=date("H",strtotime($start));
    if($endHour>="12"){
      $end="PM";
    }
    else{
      $end="AM";
    }
//$futureDate = $date1->format('Y-m-d');
    if (empty($start)) {
      $start = $event->start->date;
    }
    $status=$event->getDescription();
   
    echo "<td>".$event->getSummary()."</td>";
    echo "<td>".$month.$end."</td>";
     if($status==""){
      echo "<td>Pendiente</td>";
    }
    else{
      echo "<td>Completo</td>";
    }
    echo "</tr>";
  }
    echo "<table>";


}


    }
?>
</body>
</html>