<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

//To handle prefilling of form accordign to current policy
$this->load->model('Admin_model');
$passwordPolicy = $this->Admin_model->get_password_policy();
?>

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
        <form class="col s12" method="post" action="Forgotten/user_change_password">
            <div class='row'>
                <div class='input-field col s12'>
                    <input class='validate' type='text' name='username' id='username' maxlength="30" />
                    <label for='username'>Username</label>
                </div>
            </div>

            <div class='row'>
                <label>What is your dream job ?</label>
                <div class='input-field col s12'>
                    <input class='validate' type='text' name='secret' id='secret' maxlength="20" />
                    <label for='secret'>Secret answer</label>
                </div>
            </div>

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

            <div class='row'>
                <div class='input-field col s12'>
                    <input class='validate' type='password' name='pass1' id='pass1' maxlength="30" placeholder="Please match the requested format" pattern="<?php echo $pattern?>"/>
                    <i><?php echo $placeHolder?></i>
                    <label for='pass1'>New password</label>
                </div>
            </div>

            <div class='row'>
                <div class='input-field col s12'>
                    <input class='validate' type='password' name='pass2' id='pass2' maxlength="30" placeholder="Please match the requested format" pattern="<?php echo $pattern?>"/>
                    <i><?php echo $placeHolder?></i>
                    <label for='pass1'>Validate new password</label>
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