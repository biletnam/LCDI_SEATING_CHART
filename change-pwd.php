<?PHP
require_once("./include/membersite_config.php");

//if(!$fgmembersite->CheckLogin())
//{
//    $fgmembersite->RedirectToURL("login.php");
//    exit;
//}

if(isset($_POST['submitted']))
{
   $fgmembersite->LoginOld();
   if($fgmembersite->ChangePassword())
   {
        $fgmembersite->RedirectToURL("changed-pwd.html");
   }
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-US" lang="en-US">
<head>
      <meta http-equiv='Content-Type' content='text/html; charset=utf-8'/>
      <title>Change password</title>
      <link rel="STYLESHEET" type="text/css" href="style/fg_membersite.css" />
      <script type='text/javascript' src='scripts/gen_validatorv31.js'></script>
      <link rel="STYLESHEET" type="text/css" href="style/pwdwidget.css" />
      <link rel="lcdi icon" href="images/lcdi_logo.ico">
      <script src="scripts/pwdwidget.js" type="text/javascript"></script>       
</head>
<body>

<!-- Form Code Start -->
<div id='contact-form'>
<form id='changepwd' action='<?php echo $fgmembersite->GetSelfScript(); ?>' method='post' accept-charset='UTF-8'>
<img src="images/lcdi_banner.png" alt="LCDI" height="auto" width="auto" style="display: block; margin: auto;" />
<input type='hidden' name='submitted' id='submitted' value='1'/>

<h2><span class="required">*</span> required fields</h2>

<div><span class='error'><?php echo $fgmembersite->GetErrorMessage(); ?></span></div>
<div class='container'>
    <label for='username' >Username <span class="required">*</span>:</label>
    <input type='text' name='username' id='username' maxlength="50" value='<?php echo $fgmembersite->SafeDisplay('username'); ?>' />
    <span id='username_errorloc' class='error'></span>
</div>

<div class='container'>
    <label for='oldpwd' >Old Password <span class="required">*</span>:</label>
    <div class='pwdwidgetdiv' id='oldpwddiv' ></div><br>
    <noscript>
    <input type='password' name='oldpwd' id='oldpwd' maxlength="50" />
    </noscript>    
    <span id='changepwd_oldpwd_errorloc' class='error'></span>
</div>

<div class='container'>
    <label for='newpwd' >New Password <span class="required">*</span>:</label>
    <div class='pwdwidgetdiv' id='newpwddiv' ></div>
    <noscript>
    <input type='password' name='newpwd' id='newpwd' maxlength="50" />
    </noscript>
    <span id='changepwd_newpwd_errorloc' class='error'></span>
</div>

<br/><br/><br/>
<div class='container'>
    <input class='blue' type='submit' name='Submit' value='Submit' />
</div>
</form>
<!-- client-side Form Validations:
Uses the excellent form validation script from JavaScript-coder.com-->

<script type='text/javascript'>
// <![CDATA[
    var pwdwidget = new PasswordWidget('oldpwddiv','oldpwd');
    pwdwidget.enableGenerate = false;
    pwdwidget.enableShowStrength=false;
    pwdwidget.enableShowStrengthStr =false;
    pwdwidget.MakePWDWidget();
    
    var pwdwidget = new PasswordWidget('newpwddiv','newpwd');
    pwdwidget.MakePWDWidget();
    
    
    var frmvalidator  = new Validator("changepwd");
    frmvalidator.EnableOnPageErrorDisplay();
    frmvalidator.EnableMsgsTogether();

    frmvalidator.addValidation("oldpwd","req","Please provide your old password");
    
    frmvalidator.addValidation("newpwd","req","Please provide your new password");

// ]]>
</script>

<p>
<a href='index.php'>Home</a>
</p>

</div>
<!--
Form Code End (see html-form-guide.com for more info.)
-->

</body>
</html>
