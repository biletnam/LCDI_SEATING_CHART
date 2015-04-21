<?PHP
require_once("./include/membersite_config.php");
/*
if(empty($_POST['username'])) {
	echo "ERROR: username or password dropped!";
} else {
	if(!$fgmembersite->LogOutNew($_POST['username'], $_POST['password'], $_POST['seat'])) {
		echo "ERROR: DB login failed";
	}
}
*/
$fgmembersite->LogOut();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-US" lang="en-US">
<head>
      <meta http-equiv='Content-Type' content='text/html; charset=utf-8'/>
      <title>LCDI Logout</title>
      <link rel="STYLESHEET" type="text/css" href="style/fg_membersite.css" />
      <script type='text/javascript' src='scripts/gen_validatorv31.js'></script>
</head>
<body>

<div id="contact-form">
<h2>You have logged out</h2>
<p>
<a href='index.php'>Home</a>
</p>
</div>

</body>
</html>
