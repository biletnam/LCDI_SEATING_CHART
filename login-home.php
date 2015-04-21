<?PHP
require_once("./include/membersite_config.php");

if(!$fgmembersite->CheckLogin())
{
    $fgmembersite->RedirectToURL("login-admin.php");
    exit;
}

if (isset($_GET['offlimits'])) {
	$fgmembersite->OffLimits();
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-US" lang="en-US">
<head>
      <meta http-equiv='Content-Type' content='text/html; charset=utf-8'/>
      <title>LCDI Home</title>
      <link rel="STYLESHEET" type="text/css" href="style/fg_membersite.css">
      <link rel="lcdi icon" href="images/lcdi_logo.ico">
</head>
<body>
<div id='contact-form' style='max-width:550px'>
<img src="images/lcdi_banner.png" alt="LCDI" height="auto" width="auto" style="display: block; margin: auto;" />
<h3>
<?= $fgmembersite->UserPhoto($fgmembersite->UserFullName()); ?>
</h3>

<h3>Administrative Tools</h3>

<select id="drop">
	<option value="">Select a Tool</option>
	<option value="change-pwd.php">Change Password</option>
	<option value="test.php">Seating Chart</option>
	<option value="logout-admin.php">Force Logout</option>
	<option value="update-photo.php">Update Photo</option>
	<option value="<?= $fgmembersite->GetAllUserTime(); ?>">Last Login Times</option>
	<option value="<?= $fgmembersite->GetWeekTimeTable(); ?>">Week Time Table</option>
	<option value="http://seatingchart/phpmyadmin/">phpmyadmin</option>
</select>

<div id="time">
</div>

<p id="list"><a href='logout.php'>Logout</a></p>
<p id="list"><a href='login-home.php?offlimits=true'>Enable/Disable Sign</a></p>

</div>

<script>
	document.getElementById("drop").onchange = function() {
		if(this.selectedIndex !== 0 && this.selectedIndex !== 5 && this.selectedIndex !== 6) {
			window.location.href = this.value;
		} else if(this.selectedIndex == 5) {
			document.getElementById("time").innerHTML = this.value;
		} else if(this.selectedIndex == 6) {
			document.getElementById("contact-form").style.maxWidth = "2000px";
			document.getElementById("contact-form").style.width = "1200px";
			document.getElementById("time").innerHTML = this.value;
		}
	};
</script>
</body>
</html>
