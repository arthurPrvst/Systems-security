<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>


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
            <a href=<?php echo base_url();?> class="logo"><img src=<?php echo img_url("favicon","").".png";?>></a>
            <a href="#" data-activates="mobile-demo" class="button-collapse"><i class="material-icons">menu</i></a>
            <ul class="right hide-on-med-and-down">
                <li><a href=<?php echo site_url()."/Affaires";?> >Business</a></li>
                <li><a href="#!" ><i class="small material-icons left green-text text-green darken-3">verified_user</i><?php echo $_SESSION['sessionData']['username'];?> is online</a></li>
                <li><a href=<?php echo site_url()."/Login/Logout";?> ><i class="small material-icons left red-text text-red accent-4">exit_to_app</i>Logout</a></li>
            </ul>
            <ul class="side-nav" id="mobile-demo">
                <li><a href=<?php echo site_url()."/Affaires";?> >Business</a></li>
                <li><a href="#!"><i class="small material-icons left green-text text-green darken-3">verified_user</i>Online</a></li>
                <li><a href=<?php echo site_url()."/Login/Logout";?> ><i class="small material-icons left red-text text-red accent-4">exit_to_app</i>Logout</a></li>
            </ul>
        </div>
    </nav>
  </div>
  <!-- END NAVBAR -->
