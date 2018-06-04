<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>

<div class="container center ">
    <div class="grey lighten-4 row valign-wrapper" style="display: inline-block; padding: 96px 72px 0px 72px; margin : 64px 48px 0px 48px; border: 0px solid #EEE;">
        <?php
            if(isset($_GET['change'])){
                echo "<div class='error_msg'>";
                echo  $_GET['change'];
                echo "</div>";
            }
        ?>


        <h5 class="indigo-text" style="margin-top: 10px;">Change password</h5>
        <form class="col s12" method="post" action="Password/user_change_password">
            <div class='row'>
                <div class='input-field col s12'>
                    <input class='validate' type='PASSWORD' name='old' id='old' maxlength="30" />
                    <label for='old'>Old password</label>
                </div>
            </div>

            <div class='row'>
                <div class='input-field col s12'>
                    <input class='validate' type='password' name='pass1' id='pass1' maxlength="30" />
                    <label for='pass1'>New password</label>
                </div>
            </div>

            <div class='row'>
                <div class='input-field col s12'>
                    <input class='validate' type='password' name='pass2' id='pass2' maxlength="30" />
                    <label for='pass2'>Validate new password</label>
                </div>
            </div>

            <br />
            <div class="g-recaptcha" data-sitekey="6LfXEVEUAAAAAD9CwgAzbK4OqQfZh2nCNxKE7umj"></div>
            <center>
                <div class='row'>
                    <button type='submit' name='btn_login' class='col s12 btn btn-large waves-effect indigo'>Validate</button>
                </div>
            </center>
        </form>
    </div>
</div>