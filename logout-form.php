<?PHP
require_once("./include/membersite_config.php");

if(isset($_POST['submitted']))
{
	if($fgmembersite->LogOutNew($_POST['username'], $_POST['password'], $_POST['seat']))
	{
		$fgmembersite->RedirectToURL("logout.php");
	}
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-US" lang="en-US">
<head>
      <meta http-equiv='Content-Type' content='text/html; charset=utf-8'/>
      <title>LCDI Logout</title>
      <link rel="STYLESHEET" type="text/css" href="style/fg_membersite.css" />
      <link rel="lcdi icon" href="images/lcdi_logo.ico">
      <script type='text/javascript' src='scripts/gen_validatorv31.js'></script>
</head>
<body>

<div id='contact-form' class="clearfix">
<img src="images/lcdi_banner.png" alt="LCDI" height="auto" width="auto" style="display: block; margin: auto;" />
<h2><span class="required">*</span> required fields</h2>

<form id='login' action='<?php echo $fgmembersite->GetSelfScript(); ?>' method='post' accept-charset='UTF-8'>

<input type='hidden' name='submitted' id='submitted' value='1'/>

<div><span class='error'><?php echo $fgmembersite->GetErrorMessage(); ?></span></div>
<div class='container'>
    <label for='username' >User Name <span class="required">*</span>:</label>
    <input type='text' name='username' id='username' value='<?php echo $fgmembersite->SafeDisplay('username') ?>' maxlength="50" /><br/>
    <span id='login_username_errorloc' class='error'></span>
</div>
<div class='container'>
    <label for='password' >Password <span class="required">*</span>:</label>
    <input type='password' name='password' id='password' maxlength="50" /><br/>
    <span id='login_password_errorloc' class='error'></span>
</div>
<div class='container'>
  <label for='seat'>Seat Number <span class="required">*</span>:</label>
  <input type="number" name="seat" min="1" max="32" value="<?php echo $seat ?>" /><br/>
</div>

<div class='container'>
    <input class="blue" type='submit' name='Submit' value='Submit' />
    <input class="green" type="reset" value="Reset" >
</div>
<div class='short_explanation'><a href='reset-pwd-req.php'>Forgot Password?</a></div>
<div class='short_explanation'><a href='index.php'>Home</a></div>
</form>

<script type='text/javascript'>
// <![CDATA[

    var frmvalidator  = new Validator("login");
    frmvalidator.EnableOnPageErrorDisplay();
    frmvalidator.EnableMsgsTogether();

    frmvalidator.addValidation("username","req","Please provide your username");
    
    frmvalidator.addValidation("password","req","Please provide the password");

// ]]>
</script>
</div>

</body>
</html>
