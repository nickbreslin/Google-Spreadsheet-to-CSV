<?php
//----------------------------------------------------------------------------
// Bootstrap
//----------------------------------------------------------------------------

define('ROOT_PATH', realpath(dirname(__FILE__) . '/../').'/');
require ROOT_PATH.'lib/boot/bootstrap.php';
session_start();


$token = "ya29.AHES6ZSi8xWxhiidj5s4BlP0_OotCaNbP5NBwh_dkok9rHiDY-OmQA";

$docs = new Googledocs();
$results = array();
$docInfo = array();
if(!getParam('action'))
{
	$results = $docs->getDocs($token);
}
else
{
	$docInfo = $docs->getDoc(getParam('docId'), $token);
}


require_once "src/apiClient.php";

$client = new apiClient();
$client->discover('plus');
$client->setScopes(array('http://docs.google.com/feeds/'));

if (isset($_GET['logout'])) {
  unset($_SESSION['token']);
}

if (isset($_GET['code'])) {
  $client->authenticate();
  $_SESSION['token'] = $client->getAccessToken();
  $redirect = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
  header('Location: ' . filter_var($redirect, FILTER_SANITIZE_URL));
}

if (isset($_SESSION['token'])) {
  $client->setAccessToken($_SESSION['token']);
} else {
  //$client->authenticate();
}

?>

<html>
	<head>
		<title>Assembler</title>
		<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
		<link rel="icon" href="/favicon.ico" type="image/x-icon">
		<link href='http://fonts.googleapis.com/css?family=Droid+Sans:400,700|Droid+Serif:400,700' rel='stylesheet' type='text/css'>
		<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
		<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jqueryui/1/jquery-ui.min.js"></script>
		
		<!--css-->
		<link rel="stylesheet" href="/css/bootstrap.css" type="text/css" charset="utf-8"/>
		<link rel="stylesheet" href="/css/bootstrap.responsive" type="text/css" charset="utf-8"/>
		<!--js-->
		<script type="text/javascript" src="/js/notifier.js"></script>
		<script type="text/javascript" src="/js/bootstrap.js"></script>
		<script type="text/javascript" src="/js/bootstrap-modal.js"></script>
		<script type="text/javascript" src="/js/bootstrap-tooltip.js"></script>
		<!-- overrides -->
		<link rel="stylesheet" href="/css/style.css" type="text/css" charset="utf-8"/>
		<script type="text/javascript" src="/js/script.js"></script>
		
		<!-- map -->
		
	</head>
	<body>		
		<div class="container-fluid">
		<h1>Assembler<span style='font-size:50%'> for Assembla</span></h1>
		  <div class="row-fluid">
			<div class="span2">
		    <div class="sidebar well">
		      <!--Sidebar content-->
			</div>
		      <!--Body content-->
				<?php
				foreach($results as $result)
				{
					echo "<p><a href='?action=getDoc&docId=".urlencode($result['src'])."'>".$result['title']."</a></p>";
				}
				?>
		    </div>
		</div>
			<?php echo Debug::display(); ?>
		</div>
		<a href="http://github.com/nickbreslin/Assembler"><img style="position: absolute; top: 0; right: 0; border: 0;" src="https://a248.e.akamai.net/assets.github.com/img/7afbc8b248c68eb468279e8c17986ad46549fb71/687474703a2f2f73332e616d617a6f6e6177732e636f6d2f6769746875622f726962626f6e732f666f726b6d655f72696768745f6461726b626c75655f3132313632312e706e67" alt="Fork me on GitHub"></a>
        <div id="myModal" class="modal hide fade">
          <div class="modal-header">
            <h2>Loading...</h2>
          </div>
          <div class="modal-body">
				<div class="progress progress-success
				     progress-striped active">
				  <div class="bar"
				       style="width: 100%;"></div>
				</div>
				<h3>Retrieving Projects, Milestones, Tickets and Users</h3>
        </div>
<div class="modal-footer">Please be patient, do not interrupt the loading...</div>
	</body>
</html>