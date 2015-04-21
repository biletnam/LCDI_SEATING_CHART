<?PHP

require_once("class.phpmailer.php");
require_once("formvalidator.php");

// ADDED
$num_Users = 0;
$max_Users = 32;
// END

class FGMembersite
{
    var $admin_email;
    var $from_address;
    
    var $username;
    var $pwd;
    var $database;
    var $tablename;
    var $connection;
    var $rand_key;
    
    var $error_message;
    var $seat_Num;
    
    //-----Initialization -------
    function FGMembersite()
    {
        $this->sitename = 'whatever';
        $this->rand_key = '0iQx5oBk66oVZep';
    }
    
    function InitDB($host,$uname,$pwd,$database,$tablename)
    {
        $this->db_host  = $host;
        $this->username = $uname;
        $this->pwd  = $pwd;
        $this->database  = $database;
        $this->tablename = $tablename;
        
    }

    function SetAdminEmail($email)
    {
        $this->admin_email = $email;
    }
    
    function SetWebsiteName($sitename)
    {
        $this->sitename = $sitename;
    }
    
    function SetRandomKey($key)
    {
        $this->rand_key = $key;
    }
    
    //-------Main Operations ----------------------
    function RegisterUser()
    {
        if(!isset($_POST['submitted']))
        {
           return false;
        }
        
        $formvars = array();
        
        if(!$this->ValidateRegistrationSubmission())
        {
            return false;
        }
        
        $this->CollectRegistrationSubmission($formvars);
        
        if(!$this->SaveToDatabase($formvars))
        {
            return false;
        }
        
        if(!$this->SendUserConfirmationEmail($formvars))
        {
            return false;
        }

        $this->SendAdminIntimationEmail($formvars);
        
        return true;
    }

    function ConfirmUser()
    {
        if(empty($_GET['code'])||strlen($_GET['code'])<=10)
        {
            $this->HandleError("Please provide the confirm code");
            return false;
        }
        $user_rec = array();
        if(!$this->UpdateDBRecForConfirmation($user_rec))
        {
            return false;
        }
        
        $this->SendUserWelcomeEmail($user_rec);
        
        $this->SendAdminIntimationOnRegComplete($user_rec);
        
        return true;
    }    
    
    function LoginOld()
    {
	if(empty($_POST['username'])) {
		$this->HandleError("Username is empty!");
		return false;
	}

	if(empty($_POST['oldpwd'])) {
		$this->HandleError("Password is empty!");
		return false;
	}

	$username = trim($_POST['username']);
	$password = trim($_POST['oldpwd']);

	if ($username != "jwilliams" && $username != "aacebo" && $username != "acaron") {
        	$this->HandleError("YOU ARE NOT AN ADMIN!!!");
                return false;
        }


	if(!isset($_SESSION)) { session_start(); }
	if(!$this->CheckLoginInDB($username,$password)) {
		return false;
	}

	$_SESSION[$this->GetLoginSessionVar()] = $username;

	return true;
    }

    function OffLimits()
    {
	if(file_exists("OffLimits.txt")) {
		unlink("OffLimits.txt");
	} else {
		$myfile = fopen("OffLimits.txt", "w") or die("Unable to open OffLimits.txt!");
		fclose($myfile);
	}
    }

    function Login()
    {
        global $num_Users, $max_Users;

        $this->seat_Num = $_POST['seat'];

        if($num_Users <= $max_Users) {
            if(empty($_POST['username']))
            {
                $this->HandleError("UserName is empty!");
                return false;
            }
            
            if(empty($_POST['password']))
            {
                $this->HandleError("Password is empty!");
                return false;
            }
            
            $username = trim($_POST['username']);
            $password = trim($_POST['password']);
            
            if(!isset($_SESSION)){ session_start(); }
            if(!$this->CheckLoginInDB($username,$password))
            {
                return false;
            }
            
            $_SESSION[$this->GetLoginSessionVar()] = $username;
            $_SESSION['seat'] = $this->seat_Num;

	    if ($this->seat_Num == "30" && $username != "jwilliams" && $username != "aacebo") {
		$this->HandleError("YOU ARE NOT JOE WILLIAMS!!!");
		return false;
	    } else if ($this->seat_Num == "31" && $username != "acaron" && $username != "aacebo") {
		$this->HandleError("YOU ARE NOT ALEX CARON!!!");
		return false;
	    } else if ($this->seat_Num == "32" && $username != "" && $username != "aacebo") {
		$this->HandleError("SEAT 32 NOT YET CONFIGURED!!!");
		return false;
	    } else if (empty($this->seat_Num) == true) {
		$this->HandleError("Please enter a seat number");
		return false;
            }


            // Add non-duplicate name entries for currently logged on users
            // -------- ADDED ----------------------- //
            //$seat_Num_arr[$this->UserFullName()] = $this->seat_Num;
            //$file = fopen("tmp.txt", "a");
            //fwrite($file, $seat_Num_arr[$this->UserFullName()]);
            //fclose($file);
            //$GLOBALS['seat_Num'][$this->UserFullName()] = $this->seat_Num;

	    if (!$this->SetLoginTime($username)) {
	    	return false;
	    }

            $filename = "LoggedInCurrently.txt";
	    if (file_exists($filename) == false) {
                $openFile = fopen($filename, "w") or die("create login file failed");
		fclose($openFile);
            }
            $search = $this->UserFullName();
            $tmp = file_get_contents($filename);
            $pos = strpos($tmp, $search);
            $seatPos = strpos($tmp, "|".$this->seat_Num.",\r\n");

            if($pos === false) {
                if($seatPos === false) {
                    $myFile = fopen($filename, "a") or die("Unable to open file"); // File that keeps track of logged in users
                    fwrite($myFile, $search."|".$this->seat_Num.",\r\n");
                    fclose($myFile);
                }
                else {
                    $this->HandleError("Seat ".$this->seat_Num." is occupied");
                    return false;
                }
            } else {
                $this->HandleError("User already logged in");
                return false;
            }
            // ------- END ------------------------- //
            $num_Users++;
        } else {
            $this->HandleError("No seats left");
            return false;
        }

        return true;
    }
    
    function CheckLogin()
    {
         if(!isset($_SESSION)){ session_start(); }

         $sessionvar = $this->GetLoginSessionVar();
         
         if(empty($_SESSION[$sessionvar]))
         {
            return false;
         }
         return true;
    }
    
    function GetAllUserTime() {
	if (!$this->DBLogin()) {
		$this->HandleError("Database login failed!");
		return false;
	}

	$sql = mysql_query("SELECT name,last_login_time,last_logout_time,username FROM $this->tablename WHERE 1 ORDER BY username");
	echo "<table style='border-spacing:10px'>";
	echo "<tr>";
	echo "<th style='text-align:left'>", "NAME", "</th>";
	echo "<th>", "LAST LOGIN TIME", "</th>";
	echo "<th>", "LAST LOGOUT TIME", "</th>";
	echo "</tr>";
	while ($row = mysql_fetch_assoc($sql)) {
		echo "<tr>";
		echo "<td style='text-align:left'>", $row['name'], "</td>";
		echo "<td>", $row['last_login_time'], "</td>";
		echo "<td>", $row['last_logout_time'], "</td>";
		echo "</tr>";
	}
	echo "</table>";
    }

    function GetWeekTimeTable() {
	if (!$this->DBLogin()) {
		$this->HandleError("Database login failed!");
		return false;
	}

	$sql = mysql_query("SELECT name,username FROM $this->tablename WHERE 1 ORDER BY username");

	echo "<table style='border-spacing:10px'>";
	echo "<tr>", "<th style='text-align:left'>", "NAME", "</th>";
	echo "<th>Monday</th>", "<th>Tuesday</th>", "<th>Wednesday</th>", "<th>Thursday</th>", "<th>Friday</th>", "<th>Saturday</th>", "<th>Sunday</th>", "<th>Total Hours</th>";
	echo "</tr>";
	while ($row = mysql_fetch_assoc($sql)) {
		$uname = $row['username'];
                $name = $row['name'];

		$qry = mysql_query("SELECT username,monday,tuesday,wednesday,thursday,friday,saturday,sunday FROM week_hours WHERE username='$uname' ORDER BY username");
		$row3 = mysql_fetch_assoc($qry);
		$total = $row3['monday'] + $row3['tuesday'] + $row3['wednesday'] + $row3['thursday'] + $row3['friday'] + $row3['saturday'] + $row3['sunday'];

		$qry = mysql_query("SELECT username,monday,tuesday,wednesday,thursday,friday,saturday,sunday FROM timetracker WHERE username='$uname' ORDER BY username");

		if ($row2 = mysql_fetch_assoc($qry)) {
			echo "<tr>", "<td style='text-align:left'>", $name, "</td>";
			echo "<td>", $row2['monday'], "</td>";
			echo "<td>", $row2['tuesday'], "</td>";
			echo "<td>", $row2['wednesday'], "</td>";
			echo "<td>", $row2['thursday'], "</td>";
			echo "<td>", $row2['friday'], "</td>";
			echo "<td>", $row2['saturday'], "</td>";
			echo "<td>", $row2['sunday'], "</td>";
			echo "<td>", $total, "</td>";
			echo "</tr>";
		}
	}
	echo "</table>";
    }

    function SetLoginTime($uname)
    {
        if (!$this->DBLogin()) {
            $this->HandleError("Database login failed!");
            return false;
        }

	$nowFormat = date("Y-m-d H:i:s");
	$sql = mysql_query("UPDATE $this->tablename SET last_login_time='$nowFormat' WHERE username='$uname'");
	if ($sql == false) {
		$this->HandleError("$uname | $nowFormat");
		return false;
	}

	return true;
    }

    function SetTimeDay($uname)
    {
	if (!$this->DBLogin()) {
		$this->HandleError("Database login failed!");
		return false;
	}

	$date = date("w");
	if ($date == 0)
		$date = "sunday";
	elseif ($date == 1)
		$date = "monday";
	elseif ($date == 2)
		$date = "tuesday";
	elseif ($date == 3)
		$date = "wednesday";
	elseif ($date == 4)
		$date = "thursday";
	elseif ($date == 5)
		$date = "friday";
	elseif ($date == 6)
		$date = "saturday";
	else {
		$this->HandleError("date/time error");
		return false;
	}

	$sql = mysql_query("SELECT id,last_login_time,last_logout_time FROM $this->tablename WHERE username='$uname'");

	if ($row = mysql_fetch_assoc($sql)) {
		$time1 = $row['last_login_time'];
		$time2 = $row['last_logout_time'];
		$time3 = strtotime($time1);
		$time4 = strtotime($time2);
		$total = $time4 - $time3;
		$total = (double)($total / 60) / 60;
		$total = round($total, 1);

		$sql = mysql_query("UPDATE week_hours SET $date='$total' WHERE username='$uname'");
                if ($sql == false)
                        return false;

		$total = "(" . (string)$time1 . "), (" . (string)$time2 . "), (" . (string)$total . ")";

		$sql = mysql_query("UPDATE timetracker SET $date='$total' WHERE username='$uname'");
		if ($sql == false)
			return false;
	} else {
		$this->HandleError("Database login failed!");
		return false;
	}

	return true;
    }

    function SetLogoutTime($uname)
    {
	if (!$this->DBLogin()) {
		$this->HandleError("Database login failed!");
		return false;
	}

	$nowFormat = date("Y-m-d H:i:s");
	$sql = mysql_query("UPDATE $this->tablename SET last_logout_time='$nowFormat' WHERE username='$uname'");
	if ($sql == false) {
		$this->HandleError("$uname | $nowFormat");
		return false;
	}

	return true;
    }
    
    function UserFullName()
    {
        return isset($_SESSION['name_of_user'])?$_SESSION['name_of_user']:'';
    }
    
    function UserEmail()
    {
        return isset($_SESSION['email_of_user'])?$_SESSION['email_of_user']:'';
    }

    function UploadPhoto()
    {
        $target = "images/UserPhotos/";
        $src = $target . $_FILES['image']['name'];
        $dest = $target . "Thumbnails/" . "s" . $_FILES['image']['name'];
        $target = $target . basename($_FILES['image']['name']);

        if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
            if($this->CreateThumb($src, $target, 215)) {
            	$this->CreateThumb($src, $dest, 38);
	    } else {
		$this->HandleError("error creating thumb");
		return false;
	    }
            return true;
        } else {
            return false;
        }
    }

    function UpdatePhoto()
    {
	if(empty($_POST['username'])) {
		$this->HandleError("Username empty!");
		return false;
	}

	if(empty($_POST['password'])) {
		$this->HandleError("Password empty!");
		return false;
	}

	if(!$this->DBLogin()) {
		$this->HandleError("Database login failed!");
		return false;
	}

	if(empty($_FILES['image']['name'])) {
		$this->HandleError("File not transfered properly");
		return false;
	}

	$username = trim($_POST['username']);
	$password = trim($_POST['password']);
	$image = $_FILES['image']['name'];

	if(!$this->CheckLoginInDB($username,$password)) {
		return false;
	}

	$res = mysql_query("SELECT * FROM $this->tablename WHERE username='$username'");
	if($row = mysql_fetch_assoc($res)) {
		if($row['image'] !== "default.jpg" && file_exists($row['image'])) {
			$path = "images/UserPhotos/".$row['image'];
			$thumb_path = "images/UserPhotos/Thumbnails/s".$row['image'];

			unlink($path);
			unlink($thumb_path);
		}

		if($this->UploadPhoto()) {
			$tmp = mysql_query("UPDATE $this->tablename SET image='$image' WHERE username='$username'");
		} else {
			$file = fopen("test.txt", "w");
			fwrite($file, $_FILES['image']['error']);
			fclose($file);
			$this->HandleError("Photo upload failed");
			return false;
		}
	} else {
		$this->HandleError("sql fetch error");
		return false;
	}
	return true;
    }

    function UserPhoto($user)
    {
        if (!$this->DBLogin()) {
            $this->HandleError("Database login failed!");
            return false;
        }

        $res = mysql_query("SELECT * FROM `$this->tablename` WHERE `name` = '$user'");
        echo "<table>";
        while ($row = mysql_fetch_array($res)) {
            echo "<tr>";
            echo "<td>"; ?> <img src="images/UserPhotos/<?php echo $row["image"]; ?>" height="100" width="100" style="border-radius: 150px; box-shadow: 0 0 8px rgba(0, 0, 0, .8);"> <?php echo "</td>";
            echo "<td><center>"; echo "&nbsp&nbsp&nbsp&nbsp&nbsp<font size='5.0em'>", $row["name"], "</font><br>", "<i>&nbsp&nbsp&nbsp&nbsp&nbsp", $row["major"]; echo "</i></center></td>";
            echo "</tr>";
        }
        echo "</table>";
    }

    function UserPhotoThumb($user, $num)
    {
        if (!$this->DBLogin()) {
            $this->HandleError("Database login failed!");
            return false;
        }

        $res = mysql_query("SELECT * FROM `$this->tablename` WHERE `name` = '$user'");

        /*
        while ($row = mysql_fetch_array($res)) {
            echo "<div id='$num'>";
            ?> <a href="images/UserPhotos/<?php echo $row["image"]; ?>" title="<?php echo $row["name"]; ?>" class="preview"><img src="images/UserPhotos/Thumbnails/s<?php echo $row["image"]; ?>" height="25" width="25"> <?php
            //echo "<br>"; echo $row["name"];
            echo "</div>";
        }
        */
        if ($row = mysql_fetch_array($res)) {
            echo "<div id='$num'>";
            ?> <a href="images/UserPhotos/<?php echo $row["image"]; ?>" title="<?php echo $row["name"]; echo "<br>"; echo $row["major"]; echo "<br>"; echo $row["last_login_time"];?>" class="preview"><img src="images/UserPhotos/Thumbnails/s<?php echo $row["image"]; ?>" class="scale-image"> <?php
            //echo "<br>"; echo $row["name"];
            echo "</a>", "</div>";
        }
    }

    function CreateThumb($src, $dest, $desired_width)
    {
        $source_image = imagecreatefromjpeg($src);
        $width = imagesx($source_image);
        $height = imagesy($source_image);

        $desired_height = floor($height * ($desired_width / $width));
        $virtual_image = imagecreatetruecolor($desired_width, $desired_height);

        imagecopyresampled($virtual_image, $source_image, 0, 0, 0, 0, $desired_width, $desired_height, $width, $height);
        if(!imagejpeg($virtual_image, $dest, 83)) {
		return false;
	}

	return true;
    }

    function SeatingChartReset()
    {
	$filename = "LoggedInCurrently.txt";

	if (file_exists($filename) == true) {
                unlink($filename);
        }

	if (file_exists($filename) == false) {
                $newFile = fopen($filename, "w") or die("reset failed");
		fclose($newFile);
        }
    }

    function LogOutNew($uname_data, $password, $seat_num_data)
    {
        global $num_Users;

        if(empty($uname_data) || empty($password) || empty($seat_num_data)) {
            $this->HandleError("Please fill in all fields");
            return false;
        }

        //if (!$this->DBLogin()) {
            //$this->HandleError("Database login failed!");
            //return false;
        //}

        //if (!$res = mysql_query("SELECT * FROM `$this->tablename` WHERE `name` = '$uname_data'")) {
            //$this->HandleError("Database query failed!");
            //return false;
        //}
        

        session_start();

        if(!$this->CheckLoginInDB($uname_data,$password))
        {
            return false;
        }

        //$_SESSION[$this->GetLoginSessionVar()] = $uname_data;
        $name = $this->UserFullName();

        $sessionvar = $this->GetLoginSessionVar();

        $_SESSION[$sessionvar]=NULL;

        unset($_SESSION[$sessionvar]);
	
	if (!$this->SetLogoutTime($uname_data)) {
		return false;
	}

	if (!$this->SetTimeDay($uname_data)) {
		return false;
	}

        $filename = "LoggedInCurrently.txt";
	if (file_exists($filename) == false) {
		fopen($filename, "w");
	}
        $tmp = file_get_contents($filename);
        $pos = strpos($tmp, $name."|".$seat_num_data.",\r\n");

        if($pos !== false) {
            $tmp = str_replace($name."|".$seat_num_data.",\r\n", '', $tmp);
            file_put_contents($filename, $tmp);
        } else {
            $this->HandleError("User not logged in!");
            return false;
        }

        $num_Users--;
        return true;
    }

    function LogOutAdmin($uname,$seatnum)
    {
	global $num_Users;
	if(empty($uname) || empty($seatnum)) {
		$this->HandleError("data not sent to server correctly");
	}

	//session_start();

	$user_rec = array();
	if(!$this->GetFullNameFromUserName($uname,$user_rec)) {
		$this->HandleError("sql user fullname query failed");
		return false;
	}
	$fullname = $user_rec['name'];

	if (!$this->SetLogoutTime($uname)) {
                return false;
        }

	$filename = "LoggedInCurrently.txt";
	$tmp = file_get_contents($filename);
	$pos = strpos($tmp, $fullname."|".$seatnum.",\r\n");

	if($pos !== false) {
		$tmp = str_replace($fullname."|".$seatnum.",\r\n", '', $tmp);
		file_put_contents($filename, $tmp);
	} else {
		$this->HandleError("User not logged in!");
		return false;
	}
	$num_Users--;
	return true;
    }

    function LogOut()
    {
        global $num_Users;
        session_start();

        //unset($seat_Num_arr[$this->UserFullName()]);

        $this->seat_Num = $_SESSION['seat'];
        
        $sessionvar = $this->GetLoginSessionVar();
        
        $_SESSION[$sessionvar]=NULL;

        unset($_SESSION[$sessionvar]);

        // Remove name entry from file
        // ----------- ADDED -------------- //
        $filename = "LoggedInCurrently.txt";
        $tmp = file_get_contents($filename);
        $tmp = str_replace($this->UserFullName()."|".$this->seat_Num.",\r\n", '', $tmp);
        file_put_contents($filename, $tmp);
        // ---------- END ---------------- //

        $num_Users--;
    }
    
    function EmailResetPasswordLink()
    {
        if(empty($_POST['email']))
        {
            $this->HandleError("Email is empty!");
            return false;
        }
        $user_rec = array();
        if(false === $this->GetUserFromEmail($_POST['email'], $user_rec))
        {
            return false;
        }
        if(false === $this->SendResetPasswordLink($user_rec))
        {
            return false;
        }
        return true;
    }
    
    function ResetPassword()
    {
        if(empty($_GET['email']))
        {
            $this->HandleError("Email is empty!");
            return false;
        }
        if(empty($_GET['code']))
        {
            $this->HandleError("reset code is empty!");
            return false;
        }
        $email = trim($_GET['email']);
        $code = trim($_GET['code']);
        
        if($this->GetResetPasswordCode($email) != $code)
        {
            $this->HandleError("Bad reset code!");
            return false;
        }
        
        $user_rec = array();
        if(!$this->GetUserFromEmail($email,$user_rec))
        {
            return false;
        }
        
        $new_password = $this->ResetUserPasswordInDB($user_rec);
        if(false === $new_password || empty($new_password))
        {
            $this->HandleError("Error updating new password");
            return false;
        }
        
        if(false == $this->SendNewPassword($user_rec,$new_password))
        {
            $this->HandleError("Error sending new password");
            return false;
        }
        return true;
    }
    
    function ChangePassword()
    {
        if(!$this->CheckLogin())
        {
            $this->HandleError("Not logged in!");
            return false;
        }
        
        if(empty($_POST['oldpwd']))
        {
            $this->HandleError("Old password is empty!");
            return false;
        }
        if(empty($_POST['newpwd']))
        {
            $this->HandleError("New password is empty!");
            return false;
        }
        
        $user_rec = array();
        if(!$this->GetUserFromEmail($this->UserEmail(),$user_rec))
        {
            return false;
        }
        
        $pwd = trim($_POST['oldpwd']);
        
        if($user_rec['password'] != md5($pwd))
        {
            $this->HandleError("The old password does not match!");
            return false;
        }
        $newpwd = trim($_POST['newpwd']);
        
        if(!$this->ChangePasswordInDB($user_rec, $newpwd))
        {
            return false;
        }
        return true;
    }
    
    //-------Public Helper functions -------------
    function GetSelfScript()
    {
        return htmlentities($_SERVER['PHP_SELF']);
    }    
    
    function SafeDisplay($value_name)
    {
        if(empty($_POST[$value_name]))
        {
            return'';
        }
        return htmlentities($_POST[$value_name]);
    }
    
    function RedirectToURL($url)
    {
        header("Location: $url");
        exit;
    }
    
    function GetSpamTrapInputName()
    {
        return 'sp'.md5('KHGdnbvsgst'.$this->rand_key);
    }
    
    function GetErrorMessage()
    {
        if(empty($this->error_message))
        {
            return '';
        }
        $errormsg = nl2br(htmlentities($this->error_message));
        return $errormsg;
    }    
    //-------Private Helper functions-----------
    
    function HandleError($err)
    {
        $this->error_message .= $err."\r\n";
    }
    
    function HandleDBError($err)
    {
        $this->HandleError($err."\r\n mysqlerror:".mysql_error());
    }
    
    function GetFromAddress()
    {
        if(!empty($this->from_address))
        {
            return $this->from_address;
        }

        $host = $_SERVER['SERVER_NAME'];

        $from ="admin@$host";
        return $from;
    } 
    
    function GetLoginSessionVar()
    {
        $retvar = md5($this->rand_key);
        $retvar = 'usr_'.substr($retvar,0,10);
        return $retvar;
    }
    
    function CheckLoginInDB($username,$password)
    {
        if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        }          
        $username = $this->SanitizeForSQL($username);
        $pwdmd5 = md5($password);
        $qry = "Select name, email from $this->tablename where username='$username' and password='$pwdmd5' and confirmcode='y'";
        
        $result = mysql_query($qry,$this->connection);
        
        if(!$result || mysql_num_rows($result) <= 0)
        {
            $this->HandleError("Error logging in. The username or password does not match");
            return false;
        }
        
        $row = mysql_fetch_assoc($result);
        
        
        $_SESSION['name_of_user']  = $row['name'];
        $_SESSION['email_of_user'] = $row['email'];
        
        return true;
    }
    
    function UpdateDBRecForConfirmation(&$user_rec)
    {
        if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        }   
        $confirmcode = $this->SanitizeForSQL($_GET['code']);
        
        $result = mysql_query("Select name, email from $this->tablename where confirmcode='$confirmcode'",$this->connection);   
        if(!$result || mysql_num_rows($result) <= 0)
        {
            $this->HandleError("Wrong confirm code.");
            return false;
        }
        $row = mysql_fetch_assoc($result);
        $user_rec['name'] = $row['name'];
        $user_rec['email']= $row['email'];
        
        $qry = "Update $this->tablename Set confirmcode='y' Where  confirmcode='$confirmcode'";
        
        if(!mysql_query( $qry ,$this->connection))
        {
            $this->HandleDBError("Error inserting data to the table\nquery:$qry");
            return false;
        }      
        return true;
    }
    
    function ResetUserPasswordInDB($user_rec)
    {
        $new_password = substr(md5(uniqid()),0,10);
        
        if(false == $this->ChangePasswordInDB($user_rec,$new_password))
        {
            return false;
        }
        return $new_password;
    }
    
    function ChangePasswordInDB($user_rec, $newpwd)
    {
        $newpwd = $this->SanitizeForSQL($newpwd);
        
        $qry = "Update $this->tablename Set password='".md5($newpwd)."' Where name='".$user_rec['name']."'";
        
        if(!mysql_query( $qry ,$this->connection))
        {
            $this->HandleDBError("Error updating the password \nquery:$qry");
            return false;
        }     
        return true;
    }
    
    function GetUserFromEmail($email,&$user_rec)
    {
        if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        }   
        $email = $this->SanitizeForSQL($email);
        
        $result = mysql_query("Select * from $this->tablename where email='$email'",$this->connection);  

        if(!$result || mysql_num_rows($result) <= 0)
        {
            $this->HandleError("There is no user with email: $email");
            return false;
        }
        $user_rec = mysql_fetch_assoc($result);

        
        return true;
    }

    function GetFullNameFromUserName($uname,&$user_rec)
    {
	if(!$this->DBLogin()) {
		$this->HandleError("Database login failed!");
		return false;
	}
	$uname = $this->SanitizeForSQL($uname);
	$result = mysql_query("Select * from $this->tablename where username='$uname'",$this->connection);
	if(!result || mysql_num_rows($result) <= 0) {
		$this->HandleError("There is no user with username: $uname");
		return false;
	}
	$user_rec = mysql_fetch_assoc($result);
	return true;
    }
    
    function SendUserWelcomeEmail(&$user_rec)
    {
        $mailer = new PHPMailer();
        
        $mailer->CharSet = 'utf-8';
        
        $mailer->AddAddress($user_rec['email'],$user_rec['name']);
        
        $mailer->Subject = "Welcome to ".$this->sitename;

        $mailer->From = $this->GetFromAddress();        
        
        $mailer->Body ="Hello ".$user_rec['name']."\r\n\r\n".
        "Welcome! Your registration  with ".$this->sitename." is completed.\r\n".
        "\r\n".
        "Regards,\r\n".
        "Webmaster\r\n".
        $this->sitename;

        if(!$mailer->Send())
        {
            $this->HandleError("Failed sending user welcome email.");
            return false;
        }
        return true;
    }
    
    function SendAdminIntimationOnRegComplete(&$user_rec)
    {
        if(empty($this->admin_email))
        {
            return false;
        }
        $mailer = new PHPMailer();
        
        $mailer->CharSet = 'utf-8';
        
        $mailer->AddAddress($this->admin_email);
        
        $mailer->Subject = "Registration Completed: ".$user_rec['name'];

        $mailer->From = $this->GetFromAddress();         
        
        $mailer->Body ="A new user registered at ".$this->sitename."\r\n".
        "Name: ".$user_rec['name']."\r\n".
        "Email address: ".$user_rec['email']."\r\n";
        
        if(!$mailer->Send())
        {
            return false;
        }
        return true;
    }
    
    function GetResetPasswordCode($email)
    {
       return substr(md5($email.$this->sitename.$this->rand_key),0,10);
    }
    
    function SendResetPasswordLink($user_rec)
    {
        $email = $user_rec['email'];
        
        $mailer = new PHPMailer();
        
        $mailer->CharSet = 'utf-8';
        
        $mailer->AddAddress($email,$user_rec['name']);
        
        $mailer->Subject = "Your reset password request at ".$this->sitename;

        $mailer->From = $this->GetFromAddress();
        
        $link = $this->GetAbsoluteURLFolder().
                '/resetpwd.php?email='.
                urlencode($email).'&code='.
                urlencode($this->GetResetPasswordCode($email));

        $mailer->Body ="Hello ".$user_rec['name']."\r\n\r\n".
        "There was a request to reset your password at ".$this->sitename."\r\n".
        "Please click the link below to complete the request: \r\n".$link."\r\n".
        "Regards,\r\n".
        "Webmaster\r\n".
        $this->sitename;
        
        if(!$mailer->Send())
        {
            return false;
        }
        return true;
    }
    
    function SendNewPassword($user_rec, $new_password)
    {
        $email = $user_rec['email'];
        
        $mailer = new PHPMailer();
        
        $mailer->CharSet = 'utf-8';
        
        $mailer->AddAddress($email,$user_rec['name']);
        
        $mailer->Subject = "Your new password for ".$this->sitename;

        $mailer->From = $this->GetFromAddress();
        
        $mailer->Body ="Hello ".$user_rec['name']."\r\n\r\n".
        "Your password is reset successfully. ".
        "Here is your updated login:\r\n".
        "username:".$user_rec['username']."\r\n".
        "password:$new_password\r\n".
        "\r\n".
        "Login here: ".$this->GetAbsoluteURLFolder()."/login.php\r\n".
        "\r\n".
        "Regards,\r\n".
        "Webmaster\r\n".
        $this->sitename;
        
        if(!$mailer->Send())
        {
            return false;
        }
        return true;
    }    
    
    function ValidateRegistrationSubmission()
    {
        //This is a hidden input field. Humans won't fill this field.
        if(!empty($_POST[$this->GetSpamTrapInputName()]) )
        {
            //The proper error is not given intentionally
            $this->HandleError("Automated submission prevention: case 2 failed");
            return false;
        }
        
        $validator = new FormValidator();
        $validator->addValidation("name","req","Please fill in Name");
        $validator->addValidation("email","email","The input for Email should be a valid email value");
        $validator->addValidation("email","req","Please fill in Email");
        $validator->addValidation("username","req","Please fill in UserName");
        $validator->addValidation("password","req","Please fill in Password");

        
        if(!$validator->ValidateForm())
        {
            $error='';
            $error_hash = $validator->GetErrors();
            foreach($error_hash as $inpname => $inp_err)
            {
                $error .= $inpname.':'.$inp_err."\n";
            }
            $this->HandleError($error);
            return false;
        }        
        return true;
    }
    
    function CollectRegistrationSubmission(&$formvars)
    {
        $formvars['name'] = $this->Sanitize($_POST['name']);
        $formvars['email'] = $this->Sanitize($_POST['email']);
        $formvars['primary_phone'] = $this->Sanitize($_POST['primary_phone']);
        $formvars['secondary_phone'] = $this->Sanitize($_POST['secondary_phone']);
        $formvars['major'] = $this->Sanitize($_POST['major']);
        $formvars['username'] = $this->Sanitize($_POST['username']);
        $formvars['password'] = $this->Sanitize($_POST['password']);
        
        if(empty($_FILES['image']['name'])) {
            $formvars['image'] = "default.jpg";
        } else {
            $formvars['image'] = $this->Sanitize($_FILES['image']['name']);
        }
    }
    
    function SendUserConfirmationEmail(&$formvars)
    {
        $mailer = new PHPMailer();
        
        $mailer->CharSet = 'utf-8';
        
        $mailer->AddAddress($admin_email/*$formvars['email']*/,$formvars['name']);
        
        $mailer->Subject = "Your registration with ".$this->sitename;

        $mailer->From = $this->GetFromAddress();
        
        $confirmcode = $formvars['confirmcode'];
        
        $confirm_url = $this->GetAbsoluteURLFolder().'/confirmreg.php?code='.$confirmcode;
        
        $mailer->Body ="Hello ".$formvars['name']."\r\n\r\n".
        "Thanks for your registration with ".$this->sitename."\r\n".
        "Please click the link below to confirm your registration.\r\n".
        "$confirm_url\r\n".
        "\r\n".
        "Regards,\r\n".
        "Webmaster\r\n".
        $this->sitename;

        if(!$mailer->Send())
        {
            $this->HandleError("Failed sending registration confirmation email.");
            return false;
        }
        return true;
    }
    function GetAbsoluteURLFolder()
    {
        $scriptFolder = (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on')) ? 'https://' : 'http://';
        $scriptFolder .= $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']);
        return $scriptFolder;
    }
    
    function SendAdminIntimationEmail(&$formvars)
    {
        if(empty($this->admin_email))
        {
            return false;
        }
        $mailer = new PHPMailer();
        
        $mailer->CharSet = 'utf-8';
        
        $mailer->AddAddress($this->admin_email);
        
        $mailer->Subject = "New registration: ".$formvars['name'];

        $mailer->From = $this->GetFromAddress();         
        
        $mailer->Body ="A new user registered at ".$this->sitename."\r\n".
        "Name: ".$formvars['name']."\r\n".
        "Email address: ".$formvars['email']."\r\n".
        "UserName: ".$formvars['username'];
        
        if(!$mailer->Send())
        {
            return false;
        }
        return true;
    }
    
    function SaveToDatabase(&$formvars)
    {
        if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        }
        if(!$this->Ensuretable())
        {
            return false;
        }
        if(!$this->IsFieldUnique($formvars,'email'))
        {
            $this->HandleError("This email is already registered");
            return false;
        }
        if(!$this->IsFieldUnique($formvars,'username'))
        {
            $this->HandleError("This User Name is already used. Please try another username");
            return false;
        }        
        if(!$this->InsertIntoDB($formvars))
        {
            $this->HandleError("Inserting to Database failed!");
            return false;
        }
        if($formvars['image'] !== "default.jpg") {
            if(!$this->UploadPhoto())
            {
                $this->HandleError("Photo upload failed");
                return false;
            }
        }

        return true;
    }
    
    function IsFieldUnique($formvars,$fieldname)
    {
        $field_val = $this->SanitizeForSQL($formvars[$fieldname]);
        $qry = "select username from $this->tablename where $fieldname='".$field_val."'";
        $result = mysql_query($qry,$this->connection);   
        if($result && mysql_num_rows($result) > 0)
        {
            return false;
        }
        return true;
    }
    
    function DBLogin()
    {

        $this->connection = mysql_connect($this->db_host,$this->username,$this->pwd);

        if(!$this->connection)
        {   
            $this->HandleDBError("Database Login failed! Please make sure that the DB login credentials provided are correct");
            return false;
        }
        if(!mysql_select_db($this->database, $this->connection))
        {
            $this->HandleDBError('Failed to select database: '.$this->database.' Please make sure that the database name provided is correct');
            return false;
        }
        if(!mysql_query("SET NAMES 'UTF8'",$this->connection))
        {
            $this->HandleDBError('Error setting utf8 encoding');
            return false;
        }
        return true;
    }    
    
    function Ensuretable()
    {
        $result = mysql_query("SHOW COLUMNS FROM $this->tablename");   
        if(!$result || mysql_num_rows($result) <= 0)
        {
            return $this->CreateTable();
        }
        return true;
    }
    
    function CreateTable()
    {
        $qry = "Create Table $this->tablename (".
                "id_user INT NOT NULL AUTO_INCREMENT ,".
                "name VARCHAR( 128 ) NOT NULL ,".
                "email VARCHAR( 64 ) NOT NULL ,".
                "phone_number VARCHAR( 16 ) NOT NULL ,".
                "username VARCHAR( 16 ) NOT NULL ,".
                "password VARCHAR( 32 ) NOT NULL ,".
                "confirmcode VARCHAR(32) ,".
                "PRIMARY KEY ( id_user )".
                ")";
                
        if(!mysql_query($qry,$this->connection))
        {
            $this->HandleDBError("Error creating the table \nquery was\n $qry");
            return false;
        }
        return true;
    }
    
    function InsertIntoDB(&$formvars)
    {
    
        $confirmcode = $this->MakeConfirmationMd5($formvars['email']);
        
        $formvars['confirmcode'] = $confirmcode;
        
        $insert_query = 'insert into '.$this->tablename.'(
                name,
                email,
                primary_phone,
                secondary_phone,
                major,
                username,
                password,
                image,
                confirmcode
                )
                values
                (
                "' . $this->SanitizeForSQL($formvars['name']) . '",
                "' . $this->SanitizeForSQL($formvars['email']) . '",
                "' . $this->SanitizeForSQL($formvars['primary_phone']) . '",
                "' . $this->SanitizeForSQL($formvars['secondary_phone']) . '",
                "' . $this->SanitizeForSQL($formvars['major']) . '",
                "' . $this->SanitizeForSQL($formvars['username']) . '",
                "' . md5($formvars['password']) . '",
                "' . $this->SanitizeForSQL($formvars['image']) . '",
                "' . $confirmcode . '"
                )';

	$sql = 'INSERT INTO timetracker (
		monday,
		tuesday,
		wednesday,
		thursday,
		friday,
		username,
		saturday,
		sunday
		)
		VALUES
		(
		0,
		0,
		0,
		0,
		0,
		"' . $this->SanitizeForSQL($formvars['username']) . '",
		0,
		0
		)';

	 $sql2 = 'INSERT INTO week_hours (
                 monday,
                 tuesday,
                 wednesday,
                 thursday,
                 friday,
                 username,
                 saturday,
                 sunday
                 )
                 VALUES
                 (
                 0,
                 0,
                 0,
                 0,
                 0,
                 "' . $this->SanitizeForSQL($formvars['username']) . '",
                 0,
                 0
                 )';

        if(!mysql_query( $insert_query ,$this->connection) || !mysql_query($sql, $this->connection) || !mysql_query($sql2, $this->connection))
        {
            $this->HandleDBError("Error inserting data to the table\nquery:$insert_query");
            return false;
        }        
        return true;
    }
    function MakeConfirmationMd5($email)
    {
        $randno1 = rand();
        $randno2 = rand();
        return md5($email.$this->rand_key.$randno1.''.$randno2);
    }
    function SanitizeForSQL($str)
    {
        if( function_exists( "mysql_real_escape_string" ) )
        {
              $ret_str = mysql_real_escape_string( $str );
        }
        else
        {
              $ret_str = addslashes( $str );
        }
        return $ret_str;
    }
    
 /*
    Sanitize() function removes any potential threat from the
    data submitted. Prevents email injections or any other hacker attempts.
    if $remove_nl is true, newline chracters are removed from the input.
    */
    function Sanitize($str,$remove_nl=true)
    {
        $str = $this->StripSlashes($str);

        if($remove_nl)
        {
            $injections = array('/(\n+)/i',
                '/(\r+)/i',
                '/(\t+)/i',
                '/(%0A+)/i',
                '/(%0D+)/i',
                '/(%08+)/i',
                '/(%09+)/i'
                );
            $str = preg_replace($injections,'',$str);
        }

        return $str;
    }    
    function StripSlashes($str)
    {
        if(get_magic_quotes_gpc())
        {
            $str = stripslashes($str);
        }
        return $str;
    }    
}
?>
