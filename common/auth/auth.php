<?php

    
if(isset($_POST['forget_password']) || $action == "forget_password") {
        
    $title = "Forget Password";
    $type_page = "forget_password";
    
}
else if(isset($_POST['register']) || $action  == "register") {     
    
    $title="Register";
    $type_page = "register";
    
}
else if(isset($_POST['login']) || $action  == "login") {
        
    $title="Login";
    $type_page = "login";

}
else if($action  == "logout") {
        
    $title="Logout";
    $type_page = "logout";

}
else {
    
    $title="Login";

}

require_once('include/header.php');
    
$redirect = $_POST['redirect'];
 
if((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) {
    
    $protocol = "https://"; 
    
} 
else {
    $protocol = "http://"; 

}

$CurPageURL = $protocol.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];  

if(!isset($_POST['redirect'])) {
    
    $referer = $_SERVER['HTTP_REFERER'];

    if(isset($referer) && $curPageName != $referer && strpos($referer, $setting["url"]) !== false && !$verifier->space($referer)) {
        
        $redirect = $_SERVER['HTTP_REFERER'];
        
    }
    else {
        
        $redirect = $setting["url"]."/";
        
    }
    
}



if($setting["user"]->login_status == 1 && $type_page == "logout") {

    setcookie("username", "", time()-60*60*24*100, "/");

    setcookie("userid", "", time()-60*60*24*100, "/");

    setcookie("code", "", time()-60*60*24*100, "/");

    setcookie("username", "", time()-60*60*24*100, "/" ,".".$setting["doamin"]);

    setcookie("userid", "", time()-60*60*24*100, "/" ,".".$setting["domain"]);

    setcookie("code", "", time()-60*60*24*100, "/" ,".".$setting["domain"]);
    
    $errors[] = "You have successfully Logout, You will now be redirected to the home page.";

    $error_count=count($errors);
    
    header("Refresh: 5; URL=".$redirect."");


    
}
else if($setting["user"]->login_status == 1) {
    header("Location: ".$redirect."");
}

if (isset($_POST['submit']) && $type_page == "forget_password") {

    $errors = array();
    $email = $_POST["email"];
    $username = $_POST["username"];

    $query = mysqli_query($setting["Lid"],"SELECT SQL_CALC_FOUND_ROWS `id`,`email`,`username` FROM `users` WHERE `email`='".$email."' AND `username`='".$username."'");

    $row = mysqli_fetch_object($query);

    $count=array_pop(mysqli_fetch_row(mysqli_query($setting["Lid"],"SELECT FOUND_ROWS()")));

    if ($count == 0) {

        $errors[]="Invalid details were supplied.";

    }

    $error_count=count($errors);

    if($error_count == 0) {

        $errors[] = "Your new password has been sent to your email address.";

        $subject =   $setting["url"]." Password Reset";

        $salt = $generator->randomurl($setting["alp"]).microtime();

        $new_password = $generator->randomurl($setting["alp"]);

        $password = md5($salt.$new_password);

        mysqli_query($setting["Lid"],"UPDATE `users` SET `password`='".$password."', `salt`='".$salt."' WHERE `id`='".$row->id."' ");

        $message .= '<img src="'.$setting["url"].'/files/image/cragglist_logo.png" alt="Cragglist logo" style="padding:10px;">';
        $message .= '<div style="padding:10px;">Your password has been reset, These are you new login information.</div>';
        $message .= '<table rules="all" style="border-color: #666;margin:10px;" cellpadding="10">';
        $message .= "<tr style='background: #eee;'><td><strong>Username:</strong> </td><td>".$row->username."</td></tr>";
        $message .= "<tr style='background: #eee;'><td><strong>Password:</strong> </td><td>".$new_password."</td></tr>";
        $message .= "</table>";

        email_system($row->email, $subject, $message);

        $error_count=count($errors);

    }

}

if (isset($_POST["submit"]) && $type_page == "login") {

    session_start();

    $errors = array();

    if ((!$_POST['username']) || (!$_POST['password'])) {

        $errors[]="Incorrect login details have been entered.";

    }
    else {

        $username = htmlspecialchars($_POST['username']);
        
        $password_old = $_POST['password'];

        $query = mysqli_query($setting["Lid"],"SELECT SQL_CALC_FOUND_ROWS * FROM `users` WHERE `username`='".$username."'");

        $user_exist = array_pop(mysqli_fetch_row(mysqli_query($setting["Lid"],"SELECT FOUND_ROWS()")));

        if ($user_exist > 0) {

            $row = mysqli_fetch_object($query);

            $password = md5($row->salt.$password_old);

            if($password == $row->password) {

                if($row->banned == 1) {

                    $errors[]= 'Your account has been blocked for violating one of our term of use and for misusing our site.';

                }
                else if ($row->active == 0) {

                    $errors[] = 'Your account inactive, please validate your account via the validation email.';
                    $errors[] = 'Final step: just before you can start using you account, you must verify your email.';
                    $errors[] = 'Please check your inbox for a validation email.';

                    $hash = $generator->randomurl($setting["alp"]).microtime();
                    
                    $hash = md5($hash);


                    mysqli_query($setting["Lid"],"UPDATE `users` SET `hash` = '".$hash."' WHERE `id` = '".$row->id."'");

                    $subject = $setting["url"]." Account Valification";

                    $message .= '<img src="'.$setting["url"].'/files/image/cragglist_logo.png" alt="Cragglist logo" style="padding:10px;">';
                    $message .= '<div style="padding:10px;">Thanks for signing up!</br>';
                    $message .= 'Your account has been created, you can login with the following credentials after you have activated your account by pressing the url below.</br>';
                    $message .= '</br>Please click this link to activate your account:</br>';
                    $message .= '<a href="'.$setting["url"].'/verify/'.$row->id.'/'.$hash.'">'.$setting["url"].'/verify/'.$row->id.'/'.$hash.'</a></div>';

                    email_system($row->email, $subject, $message);

                }
                else {

                    $salt = $generator->randomurl($setting["alp"]).microtime();

                    $password = md5($salt.$password_old);

                    mysqli_query($setting["Lid"],"UPDATE `users` SET `password` = '".$password."', `salt` = '".$salt."' WHERE `id` = '".$row->id."'");

                    if (isset($_POST['remember'])) {

                        setcookie("username", $row->username, time()+60*60*24*100, "/");

                        setcookie("userid", $row->id, time()+60*60*24*100, "/");

                        setcookie("code", $password, time()+60*60*24*100, "/");

                        setcookie("username", $row->username, time()+60*60*24*100, "/", ".".$setting["domain"]);

                        setcookie("userid", $row->id, time()+60*60*24*100, "/", ".".$setting["domain"]);

                        setcookie("code", $password, time()+60*60*24*100, "/", ".".$setting["domain"]);

                    }
                    else {

                        setcookie("username", $row->username, 0, "/");

                        setcookie("userid", $row->id, 0, "/");

                        setcookie("code", $password, 0, "/");

                        setcookie("username", $row->username, 0, "/", ".".$setting["domain"]);

                        setcookie("userid", $row->id, 0, "/" ,".".$setting["domain"]);

                        setcookie("code", $password, 0, "/", ".".$setting["domain"]);

                    }
                    
                    $errors[]="You've been logged in successfully.";

                    header("Location: ".$redirect."");

                }

            }
            else {

                $errors[]="Incorrect login details have been entered.";

            }

        }
        else {

            $errors[]="Incorrect login details have been entered.";

        }

    }
    
    $error_count = count($errors);

}


if(isset($_POST['submit']) && $type_page == "register") {

	$errors = array();

	$username = htmlspecialchars($_POST['username']);
    
    $password = $_POST['password'];
    
    $confirm_password = $_POST['confirm_password'];
    
    $email = str_replace(" ","",$_POST['email']);

	if($confirm_password !== $password) {

		$errors[]="passwords don't match";

	}

	mysqli_query($setting["Lid"],"SELECT SQL_CALC_FOUND_ROWS `email` FROM `users` WHERE `email`='".$email."'");

	$email_count=array_pop(mysqli_fetch_array(mysqli_query($setting["Lid"],"SELECT FOUND_ROWS()")));

	if($email_count > 0) {

		$errors[]="email already exists.";

	}
	else if(!$verifier->validate_email($email)) {

		$errors[]="please insert a valid email.";

	}

	mysqli_query($setting["Lid"],"SELECT SQL_CALC_FOUND_ROWS `username` FROM `users` WHERE `username`='".$username."'");

	$username_count=array_pop(mysqli_fetch_array(mysqli_query($setting["Lid"],"SELECT FOUND_ROWS()")));

	if($username_count > 0) {

		$errors[]="username already exists.";

	}
	else {

		$username_valid = preg_match('/^[A-Za-z \-][A-Za-z0-9 \-]*(?:_[A-Za-z0-9 ]+)*$/', $username);

		if(($username_valid == false)) {

			$errors[]="you must enter a valid alphanumeric username";

		}

	}

	if((!$username) || (!$email) || (!$password) || (!$confirm_password)) {

		$errors[]="please make sure that you have correctly filled in all of the fields.";

	}

	$error_count = count($errors);

	if($error_count == 0) {

		$subject = $setting["site_url"]." Account Valification";

		$errors[] = 'Your account has been successfully created.';
		$errors[] = 'Final step: just before you can start using you account, you must verify your email.';
		$errors[] = 'Please check your inbox for a validation email.';

		$salt = $generator->randomurl($setting["alp"]).microtime();

		$salt_password = md5($salt.$password);

		$hash = $generator->randomurl($setting["alp"]).microtime();
        
        $hash = md5($hash);

		mysqli_query($setting["Lid"],"INSERT INTO `users` (`username`, `password`, `email`, `salt`, `hash`) VALUES('".$username."', '".$salt_password."', '".$email."', '".$salt."', '".$hash."')");

		$id = mysqli_insert_id($setting["Lid"]);

		$message .= '<img src="'.$setting["url"].'/files/image/cragglist_logo.png" alt="Cragglist logo" style="padding:10px;">';
		$message .= '<div style="padding:10px;">Thanks for signing up!</br>';
		$message .= 'Your account has been created, you can login with the following credentials after you have activated your account by pressing the url below.</br>';
		$message .= '</br>Please click this link to activate your account:</br>';
		$message .= '<a href="'.$setting["url"].'/verify/'.$id.'/'.$hash.'">'.$setting["url"].'/verify/'.$id.'/'.$hash.'</a></div>';

		email_system($email, $subject, $message);

		$error_count = count($errors);

	}

}

?>

    <!-- Login Register area Start-->
    <div class="login-content">
        <!-- Login -->

        <?php
            
        if($type_page != "register" && $type_page != "forget_password") {
            $login_toggle = "toggled";
        }
        ?>
        <div class="nk-block <?=$login_toggle?>" id="l-login">
        
            <form method="post" enctype="multipart/form-data" >

                <div class="nk-form">

                    <?php

                    if((empty($_POST['agree'])) && (isset($_POST['submit'])) && $error_count > 0 && $type_page == "login" || $type_page == "logout") {

                        foreach ($errors as $error) {

                            echo '<p class="text-left">'.$error.'</p>';

                        }

                    }

                    ?>

                    <div class="input-group">
                        <span class="input-group-addon nk-ic-st-pro"><i class="notika-icon notika-support"></i></span>
                        <div class="nk-int-st">
                            <input type="text"  name="username" value="<?=htmlentities($username)?>" class="form-control" placeholder="Username">
                        </div>
                    </div>
                    <div class="input-group mg-t-15">
                        <span class="input-group-addon nk-ic-st-pro"><i class="notika-icon notika-edit"></i></span>
                        <div class="nk-int-st">
                            <input type="password" name="password" class="form-control" placeholder="Password">
                        </div>
                    </div>
                    <div class="fm-checkbox">
                        <label><input type="checkbox"  name="remember" class="i-checks"> <i></i> Keep me signed in</label>
                    </div>
                    <a href="#l-register" data-ma-action="nk-login-switch" data-ma-block="#l-register" class="btn btn-login btn-success btn-float"><i class="notika-icon notika-right-arrow right-arrow-ant"></i></a>

                    <input name="login" type="hidden" value="login">

                    <input name="redirect" type="hidden" value="<?=$redirect?>">

                    <div class="input-group mg-t-15">
                        <div class="nk-int-st">
                            <input name="submit" type="submit" class="form-control" value="Login"></input>
                        </div>
                    </div>

                </div>

            </form>

            <div class="nk-navigation nk-lg-ic">
                <a href="#" data-ma-action="nk-login-switch" data-ma-block="#l-register"><i class="notika-icon notika-plus-symbol"></i> <span>Register</span></a>
                <a href="#" data-ma-action="nk-login-switch" data-ma-block="#l-forget-password"><i>?</i> <span>Forgot Password</span></a>
            </div>

        </div>

        <!-- Register -->
        <?php
            
        if($type_page == "register") {
            $register_toggle = "toggled";
        }
        ?>

        <div class="nk-block <?=$register_toggle?>" id="l-register">

            <form method="post" enctype="multipart/form-data">

                <div class="nk-form">

                    <p class="text-left">By submiting this form you agree to our terms of use.</p>

                    <?php

                    if((empty($_POST['agree'])) && (isset($_POST['submit'])) && $error_count > 0 && $type_page == "register") {

                        foreach ($errors as $error) {

                            echo '<p class="text-left">'.$error.'</p>';

                        }

                    }

                    ?>

                    <div class="input-group">
                        <span class="input-group-addon nk-ic-st-pro"><i class="notika-icon notika-support"></i></span>
                        <div class="nk-int-st">
                            <input type="text" name="username" value="<?=htmlentities($username)?>" class="form-control" placeholder="Username">
                        </div>
                    </div>

                    <div class="input-group mg-t-15">
                        <span class="input-group-addon nk-ic-st-pro"><i class="notika-icon notika-mail"></i></span>
                        <div class="nk-int-st">
                            <input type="text" name="email" value="<?=htmlentities($email)?>" class="form-control" placeholder="Email Address">
                        </div>
                    </div>

                    <div class="input-group mg-t-15">
                        <span class="input-group-addon nk-ic-st-pro"><i class="notika-icon notika-edit"></i></span>
                        <div class="nk-int-st">
                            <input type="password"  name="password" value="<?=htmlentities($password)?>" class="form-control" placeholder="Password">
                        </div>
                    </div>

                    <div class="input-group mg-t-15">
                        <span class="input-group-addon nk-ic-st-pro"><i class="notika-icon notika-edit"></i></span>
                        <div class="nk-int-st">
                            <input type="password"  name="confirm_password" value="" class="form-control" placeholder="Confirm Password">
                        </div>
                    </div>

                    <input name="register" type="hidden" value="register">

                    <div class="input-group mg-t-15">
                        <div class="nk-int-st">
                            <input name="submit" type="submit" class="form-control" value="Register"></input>
                        </div>
                    </div>

                    <a href="#l-login" data-ma-action="nk-login-switch" data-ma-block="#l-login" class="btn btn-login btn-success btn-float"><i class="notika-icon notika-right-arrow"></i></a>

                </div>

             </form>

            <div class="nk-navigation rg-ic-stl">
                <a href="#" data-ma-action="nk-login-switch" data-ma-block="#l-login"><i class="notika-icon notika-right-arrow"></i> <span>Sign in</span></a>
                <a href="#" data-ma-action="nk-login-switch" data-ma-block="#l-forget-password"><i>?</i> <span>Forgot Password</span></a>
            </div>

        </div>

        <?php
            
        if($type_page == "forget_password") {
            $forget_password_toggle = "toggled";
        }
        ?>

        <!-- Forgot Password -->
        <div class="nk-block <?=$forget_password_toggle?>" id="l-forget-password">

            <form method="post" enctype="multipart/form-data">

                <div class="nk-form">
                   
                    <?php

                    if((empty($_POST['agree'])) && (isset($_POST['submit'])) && $error_count > 0 && $type_page == "forget_password") {

                        foreach ($errors as $error) {

                            echo '<p class="text-left">'.$error.'</p>';

                        }

                    }

                    ?>

                    <div class="input-group">
                        <span class="input-group-addon nk-ic-st-pro"><i class="notika-icon notika-support"></i></span>
                        <div class="nk-int-st">
                            <input name="username" type="text" value="<?=htmlentities($username)?>" class="form-control" placeholder="Username">
                        </div>
                    </div>


                    <div class="input-group">
                        <span class="input-group-addon nk-ic-st-pro"><i class="notika-icon notika-mail"></i></span>
                        <div class="nk-int-st">
                            <input name="email" type="text" value="<?=htmlentities($email)?>" class="form-control" placeholder="Email Address">
                        </div>
                    </div>

                    <input name="forget_password" type="hidden" value="forget_password">

                    <div class="input-group mg-t-15">
                        <div class="nk-int-st">
                            <input name="submit" type="submit" class="form-control" value="Rest Password"></input>
                        </div>
                    </div>

                    <a href="#l-login" data-ma-action="nk-login-switch" data-ma-block="#l-login" class="btn btn-login btn-success btn-float"><i class="notika-icon notika-right-arrow"></i></a>

                </div>

            </form>

            <div class="nk-navigation nk-lg-ic rg-ic-stl">
                <a href="#" data-ma-action="nk-login-switch" data-ma-block="#l-login"><i class="notika-icon notika-right-arrow"></i> <span>Sign in</span></a>
                <a href="#" data-ma-action="nk-login-switch" data-ma-block="#l-register"><i class="notika-icon notika-plus-symbol"></i> <span>Register</span></a>
            </div>

        </div>

    </div>


<?php require_once("include/footer.php"); ?>


<!--<div class="form-content-footer  pure-g">

        <div class="pure-u-1-2"><a href="<?=$setting["url"]?>/forgot">forgot password!</a></div>

        <div class="form-content-controls pure-u-1-2"><input name="submit" type="submit" class="secondary-button pure-button" value="Register"></input></div>

</div>--!>
