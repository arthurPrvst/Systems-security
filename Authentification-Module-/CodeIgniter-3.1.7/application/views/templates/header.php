<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

//To handle prefilling of form accordign to current policy
$this->load->model('Admin_model');
$passwordPolicy = $this->Admin_model->get_password_policy();
$passwordManagement = $this->Admin_model->get_password_management();


?>
<html>
<head>
  <meta charset="utf-8"/>
  <!--Let browser know website is optimized for mobile-->
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title> AuthenticationModule</title>
  <link rel="icon" type="image/png" href=<?php echo img_url("favicon.png","");?> />
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.97.5/css/materialize.min.css">
  <link rel="stylesheet" type="text/css" href=<?php echo css_url("home");?> > 
</head>
<body style="background: linear-gradient(to right, rgb(189, 195, 199), rgb(44, 62, 80));">
  <!-- NAVBAR -->
  <div class="navbar-fixed hoverable">
    <nav class="indigo darken-2">
      <div class="nav-wrapper transparent">
        <a href=<?php echo base_url();?> class="logo"><img src=<?php echo img_url("favicon.png","");?>></a>
        <a href="#" data-activates="mobile-demo" class="button-collapse"><i class="material-icons">menu</i></a>
        <ul class="right hide-on-med-and-down">
          <li><a href="#modal1" class="modal-trigger"><i class="small material-icons left">account_circle</i> Connexion</a></li>
        </ul>
        <ul class="side-nav" id="mobile-demo">
          <li><a href="#modal1" class="modal-trigger"><i class="small material-icons left">account_circle</i> Connexion</a></li>
        </ul>
      </div>
    </nav>
  </div>
  <!-- END NAVBAR -->

  <!-- Modal Connexion Structure -->
  <div id="modal1" class="modal modal-fixed-footer center-align halign-wrapper">
    <div class="modal-content ">
      <center>
        <ul id="tabs-swipe-demo" class="tabs">
          <li class="tab col s3"><a class="active" href="#swipe-signin">Sign In</a></li>
          <li class="tab col s3"><a href="#swipe-signup">Sign Up</a></li>
        </ul>

        <div id="swipe-signin" class="col s12">
          <h5 class="indigo-text" style="margin-top: 10px;">Please, login into your account</h5>
          <div class="section"></div>
          <div class="container">
            <div class="z-depth-1 grey lighten-4 row" style="display: inline-block; padding: 32px 48px 0px 48px; border: 0px solid #EEE;">
              <form class="col s12" method="post" action="<?php echo site_url().'/Login/user_login_process';?>">
                <div class='row'>
                  <div class='col s12'>
                    <?php
                    echo "<div class='error_msg'>";
                    if (isset($error_message)) {
                      echo $error_message;
                    }
                    echo validation_errors();
                    echo "</div>";
                    ?>
                  </div>
                </div>

                <div class='row'>
                  <div class='input-field col s12'>
                    <input class='validate' type='text' name='log' id='log' maxlength="30" />
                    <label for='log'>Login</label>
                  </div>
                </div>

                <div class='row'>
                  <div class='input-field col s12'>
                    <input class='validate' type='password' name='pass' id='pass' maxlength="30"/>
                    <label for='pass'>Password</label>
                  </div>
                  <?php
                  if($passwordManagement['forgotten']==1){

                    ;?>
                    <label style='float: right;'>
                      <a class='pink-text' href= <?php echo site_url()."/forgotten";?>> <b>Forgot Password?</b></a>
                    </label>
                    <?php } ;?>

                  </div>

                  <br />
                  <div class="g-recaptcha" data-sitekey="6LfXEVEUAAAAAD9CwgAzbK4OqQfZh2nCNxKE7umj"></div>

                  <center>
                    <div class='row'>
                      <button type='submit' name='btn_login' class='col s12 btn btn-large waves-effect indigo'>Login</button>
                    </div>
                  </center>
                </form>
              </div>
            </div>
          </div>

          <div id="swipe-signup" class="col s12">
            <div class="section"></div>

            <div class="container">
              <div class="z-depth-1 grey lighten-4 row" style="display: inline-block; padding: 32px 48px 0px 48px; border: 1px solid #EEE;">

                <form class="col s12" name="signupForm" method="post" action="index.php/Signup">
                  <div class="row">
                    <div class="input-field col s12">
                      <select name="role">
                        <option value="1" name="role">Residential</option>
                        <option value="2" name="role">Business</option>
                      </select>
                      <label>Role</label>
                    </div>
                  </div>
                  <div class="row">
                    <div class="input-field col s12">
                      <input id="first_name" name="first_name" type="text" class="validate" maxlength="60">
                      <label for="first_name">First Name</label>
                    </div>
                  </div>
                  <div class="row">
                    <div class="input-field col s12">
                      <input id="last_name" name="last_name" type="text" class="validate" maxlength="60">
                      <label for="last_name">Last Name</label>
                    </div>
                  </div>
                  <div class="row">
                    <div class="input-field col s12">
                      <input id="login" name="login" type="text" class="validate" maxlength="30">
                      <label for="login">Login</label>
                    </div>
                  </div>

                  <div class="row">
                    <div class="input-field col s12">

                      <!-- Generate placehodler string and pattern for pwd-->
                      <?php 
                      $placeHolder = "Min length of ".$passwordPolicy['minLength']; 
                      $pattern = ".{".$passwordPolicy['minLength'].",}";
                      if($passwordPolicy['number']==1){
                        $placeHolder = $placeHolder.", with number ";
                        $pattern = "(?=.*\d)".$pattern;
                      }
                      if($passwordPolicy['lowerAndUpper']==1){
                        $placeHolder = $placeHolder.", with lower and uppercase ";
                        $pattern = "(?=.*[a-z])(?=.*[A-Z])".$pattern;
                      }
                      if($passwordPolicy['specialCharacter']==1){
                        $placeHolder = $placeHolder." and special character ";
                        $pattern = "(?=.*[\W])".$pattern;
                      }
                      ?>

                      <input id="password" name="password" type="password" class="validate" maxlength="30" placeholder="Please match the requested format" pattern="<?php echo $pattern?>" >
                      <i><?php echo $placeHolder?></i>
                      <label for="password">Password</label>
                    </div>
                  </div>
                  <div class="row">
                    <div class="input-field col s12">
                      <input id="email" name="email" type="email" class="validate" maxlength="100">
                      <label for="email">Email</label>
                    </div>
                  </div>

                  <div class="row">
                    <div class="input-field col s12">
                      <input id="code" name="code" type="number" class="validate" maxlength="11">
                      <label for="code">Permanent code</label>
                    </div>
                  </div>

                  <div class="row">
                    <lab>What is your dream job ?</lab>
                    <div class="input-field col s12">
                      <input id="secret" name="secret" type="text" class="validate" maxlength="20">
                      <label for="secret">Secret answer</label>
                    </div>
                  </div>

                  <div class="g-recaptcha" data-sitekey="6LfXEVEUAAAAAD9CwgAzbK4OqQfZh2nCNxKE7umj"></div>

                  <center>
                    <div class='row'>
                      <button type='submit' name='btn_login' class='col s12 btn btn-large waves-effect indigo'>Create account</button>
                    </div>
                  </center>
                </form>
              </div>
            </div>
          </div>

        </center>
      </div>
    </div>
    <!-- END Modal Connexion Structure -->



    <br/>