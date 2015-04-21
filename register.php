<?PHP
require_once("./include/membersite_config.php");

if(isset($_POST['submitted']))
{
   if($fgmembersite->RegisterUser())
   {
        $fgmembersite->RedirectToURL("thank-you.html");
   }
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-US" lang="en-US">
<head>
    <meta http-equiv='Content-Type' content='text/html; charset=utf-8'/>
    <title>LCDI Register</title>
    <link rel="STYLESHEET" type="text/css" href="style/fg_membersite.css" />
    <script type='text/javascript' src='scripts/gen_validatorv31.js'></script>
    <link rel="STYLESHEET" type="text/css" href="style/pwdwidget.css" />
    <script src="scripts/pwdwidget.js" type="text/javascript"></script>      
</head>
<body>

<!-- Form Code Start -->
<div id='contact-form'>
    <h1>LCDI Registration</h1>
    <h2><span class="required">*</span> required fields</h2>
<form id='register' name='register' action='<?php echo $fgmembersite->GetSelfScript(); ?>' method='post' accept-charset='UTF-8' enctype="multipart/form-data">

<input type='hidden' name='submitted' id='submitted' value='1'/>

<!--<input type='text'  class='spmhidip' name='<?php echo $fgmembersite->GetSpamTrapInputName(); ?>' />-->

<div><span class='error'><?php echo $fgmembersite->GetErrorMessage(); ?></span></div>
<div class='container'>
    <label for='name' >Your Full Name <span class="required">*</span>: </label>
    <input placeholder='First Last' type='text' name='name' id='name' value='<?php echo $fgmembersite->SafeDisplay('name') ?>' maxlength="50" />
    <span id='register_name_errorloc' class='error'></span>
</div>
<div class='container'>
    <label for='email' >Email Address <span class="required">*</span>:</label>
    <input placeholder='first.last@mymail.champlain.edu' type='text' name='email' id='email' value='<?php echo $fgmembersite->SafeDisplay('email') ?>' maxlength="50" />
    <span id='register_email_errorloc' class='error'></span>
</div>
<div class='container'>
    <label for='primary_phone' >Primary Phone <span class="required">*</span>:</label>
    <input placeholder='(XXX)XXX-XXXX' type='text' name='primary_phone' id='primary_phone' value='<?php echo $fgmembersite->SafeDisplay('primary_phone') ?>' maxlength="13" />
    <span id='register_primary_phone_errorloc' class='error'></span>
</div>
<div class='container'>
    <label for='secondary_phone' >Secondary Phone:</label>
    <input placeholder='(XXX)XXX-XXXX' type='text' name='secondary_phone' id='secondary_phone' value='<?php echo $fgmembersite->SafeDisplay('cell_phone') ?>' maxlength="13" />
    <span id='register_secondary_phone_errorloc' class='error'></span>
</div>
<div class='container'>
    <label for='major' >Major <span class="required">*</span>:</label>
    <input style='text-transform: capitalize;' type='text' name='major' id='major' value='<?php echo $fgmembersite->SafeDisplay('major') ?>' maxlength="50" />
    <span id='register_major_errorloc' class='error'></span>
</div>
<div class='container'>
    <label for='username' >User Name <span class="required">*</span>:</label>
    <input type='text' name='username' id='username' value='<?php echo $fgmembersite->SafeDisplay('username') ?>' maxlength="50" />
    <span id='register_username_errorloc' class='error'></span>
</div>
<div class='container' style='height:80px;'>
    <label for='password' >Password <span class="required">*</span>:</label>
    <div class='pwdwidgetdiv' id='thepwddiv' ></div>
    <noscript>
    <input type='password' name='password' id='password' maxlength="50" />
    </noscript>
    <div id='register_password_errorloc' class='error' style='clear:both'></div>
</div>
<div class='container'>
    <input name="MAX_FILE_SIZE" value="10240000" type="hidden" />
    <input name="image" id="image" accept="image/jpeg" type="file">
</div>

<!-- BUTTONS -->
<div class='container'>
    <input class="blue" type='submit' name='Submit' value='Submit' />
    <input class="green" type="reset" name="Reset" value="Reset">
</div>
<div class='short_explanation'><a href='index.php'>Home</a></div>
</fieldset>
</form>
<!-- client-side Form Validations:
Uses the excellent form validation script from JavaScript-coder.com-->
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<script type='text/javascript'>
	$(document).ready(function(e) {
	    $('#name').blur(function() {
	    	window.capWords($('#name').val());
	    	window.genUsername($('#name').val());
	    });
    });

	function capWords(fullname) {
		document.register.name.value = '';
		var fname = fullname.split(' ').slice(0, -1).join(' ');
		var lname = fullname.split(' ').slice(-1).join(' ');
		fname = fname.substring(0, 1).toUpperCase() + fname.substring(1);
		lname = lname.substring(0, 1).toUpperCase() + lname.substring(1);
		document.register.name.value = fname + ' ' + lname;
	}

	function genUsername(fullname) {
		document.register.username.value = '';
		document.getElementById('username').readOnly = false;
		if(document.register.username.value=='' && document.register.name.value!='') {
			var fname = fullname.split(' ').slice(0, -1).join(' ');
			var lname = fullname.split(' ').slice(-1).join(' ');
		    var username = fname.substr(0,1) + lname.substr(0,49);
		    username = username.replace(/\s+/g, '');
		    username = username.replace(/\'+/g, '');
		    username = username.replace(/-+/g, '');
		    username = username.toLowerCase();
		    document.register.username.value = username;
		    document.getElementById('username').readOnly = true;
		}

		document.register.email.value = '';
		if(document.register.email.value == '' && document.register.name.value != '') {
			var fname = fullname.split(' ').slice(0, -1).join(' ');
			var lname = fullname.split(' ').slice(-1).join(' ');
			fname = fname.toLowerCase();
			lname = lname.toLowerCase();
			var email = fname + "." + lname + "@mymail.champlain.edu";
			document.register.email.value = email;
		}
	}

// <![CDATA[
    var pwdwidget = new PasswordWidget('thepwddiv','password');
    pwdwidget.MakePWDWidget();
    
    var frmvalidator  = new Validator("register");
    frmvalidator.EnableOnPageErrorDisplay();
    frmvalidator.EnableMsgsTogether();
    frmvalidator.addValidation("name","req","Please provide your name");

    frmvalidator.addValidation("email","req","Please provide your email address");

    frmvalidator.addValidation("email","email","Please provide a valid email address");

    frmvalidator.addValidation("username","req","Please provide a username");
    
    frmvalidator.addValidation("password","req","Please provide a password");

    frmvalidator.addValidation("phone", "req", "Please provide a primary phone number");

    frmvalidator.addValidation("cell_phone", "phone", "Please provide a secondary phone number");

    frmvalidator.addValidation("major", "major", "Please provide your major");

// ]]>
</script>

<!--
Form Code End (see html-form-guide.com for more info.)
-->

</body>
</html>
