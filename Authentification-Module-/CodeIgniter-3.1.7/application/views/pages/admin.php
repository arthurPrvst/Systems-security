<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>

<?php
//To handle prefilling of form accordign to current policy
$this->load->model('Admin_model');
$passwordPolicy = $this->Admin_model->get_password_policy();
$passwordManagement = $this->Admin_model->get_password_management();
$bruteforcePolicy = $this->Admin_model->get_bruteforce_policy();
//To handle RBAC
$this->load->model('User');
$this->load->model('Role');
echo "<script> console.log('PHP: ','COUCOU');</script>";
if(isset($_SESSION['sessionData']) && $_SESSION['sessionData']['logged_in']==TRUE) {
    echo "<script> console.log('PHP: ','C BON');</script>";
    $u = User::getByUsername($_SESSION['sessionData']['username']);
    if (!($u->hasPrivilege("displayAdminContent"))) {
        echo "<script> console.log('PHP: ','T\'AS PAS LES ACCES CONNARD');</script>";
        show_404();
    }
}
else {
    show_404();
}
?>

<main>

    <div class="row">
        <div class="col s4">
            <div align="center" class="card z-depth-3" style="background-color: rgba(255,255,255,0.3);">
                <div class="row">
                    <div class="center card-title">
                        <b>Password policy</b>
                    </div>
                </div>
                <div class="row">
                  <form class="col s12" method="post" action="Admin/update_password_policy">

                    <div class="col s8 offset-s2 waves-effect">
                        <span class="indigo-text text-lighten-1"><h5>Minimum length</h5></span>
                        <p class="range-field">
                            <input type="range" id="minLength" name="minLength" min="7" max="15" value="<?php echo $passwordPolicy['minLength'] ?>"/>
                        </p>
                    </div>

                    <br><br>

                    <div class="col s8 offset-s2 waves-effect" style="padding-top: 5%;">
                        <span class="indigo-text text-lighten-1"><h5>Must contains special characters</h5></span>
                        <div class="switch">
                            <label>
                                Off
                                <?php 
                                if($passwordPolicy['specialCharacter'] == 1){
                                    ?>
                                    <input type="checkbox" id="containSpecialCharacter" name="containSpecialCharacter" checked>
                                    <?php
                                }else{
                                    ?>
                                    <input type="checkbox" id="containSpecialCharacter" name="containSpecialCharacter" >
                                    <?php
                                }
                                ?>
                                <span class="lever"></span>
                                On
                            </label>
                        </div>
                    </div>
                    <br><br>

                    <div class="col s8 offset-s2 waves-effect" style="padding-top: 5%;">
                        <span class="indigo-text text-lighten-1"><h5>Must contains numbers</h5></span>
                        <div class="switch">
                            <label>
                                Off
                                <?php 
                                if($passwordPolicy['number'] == 1){
                                    ?>
                                    <input type="checkbox" name="containNumber" id="containNumber" checked>
                                    <?php
                                }else{
                                    ?>
                                    <input type="checkbox" name="containNumber" id="containNumber">
                                    <?php
                                }
                                ?>
                                <span class="lever"></span>
                                On
                            </label>
                        </div>
                    </div>
                    <br> <br>

                    <div class="col s8 offset-s2 waves-effect" style="padding-top: 5%;">
                        <span class="indigo-text text-lighten-1"><h5>Must contains lowercase and uppercase</h5></span>
                        <div class="switch">
                            <label>
                                Off
                                <?php 
                                if($passwordPolicy['lowerAndUpper'] == 1){
                                    ?>
                                    <input type="checkbox" name="containLowerAndUpper" id="containLowerAndUpper" checked>
                                    <?php
                                }else{
                                    ?>
                                    <input type="checkbox" name="containLowerAndUpper" id="containLowerAndUpper">
                                    <?php
                                }
                                ?>
                                <span class="lever"></span>
                                On
                            </label>
                        </div>
                    </div>

                    <div class='row' style="padding-top: 80%;">
                      <button type='submit' name='btn_password_policy' class='col s8 offset-s2 btn btn-large waves-effect indigo'>Save</button>
                  </div>

              </form>
          </div>
      </div>
  </div>

  <div class="col s4">
    <div align="center" class="card z-depth-3" style="background-color: rgba(255,255,255,0.3);">
        <div class="row">
            <div class="center card-title">
                <b>Password management</b>
            </div>
        </div>
        <div class="row">
          <form class="col s12" method="post" action="Admin/update_password_management">
              <div class="col s8 offset-s2 waves-effect" style="padding-top: 21%;">
                <span class="indigo-text text-lighten-1"><h5>Allow to change it if forgotten</h5></span>
                <div class="switch">
                    <label>
                        Off
                        <?php 
                        if($passwordManagement['forgotten'] == 1){
                            ?>
                            <input type="checkbox" id="changeForgotten" name="changeForgotten" checked>
                            <?php
                        }else{
                            ?>
                            <input type="checkbox" id="changeForgotten" name="changeForgotten" >
                            <?php
                        }
                        ?>
                        <span class="lever"></span>
                        On
                    </label>
                </div>
            </div>
            <br><br>

            <div class='row' style="padding-top: 55%;">
              <button type='submit' name='btn_password_management' class='col s8 offset-s2 btn btn-large waves-effect indigo'>Save</button>
          </div>

      </form>
  </div>
</div>
</div>

<div class="col s4">
    <div align="center" class="card z-depth-3" style="background-color: rgba(255,255,255,0.3);">
        <div class="row">
            <div class="center card-title">
                <b>Protection against brute force</b>
            </div>
        </div>
        <div class="row">
          <form class="col s12" method="post" action="Admin/update_bruteforce_protection">

            <div class="col s8 offset-s2 waves-effect" style="padding-top: 5%;">
                <span class="indigo-text text-lighten-1"><h5>Locking account if 3 bad attempts</h5></span>
                <div class="switch">
                    <label>
                        Off
                        <?php 
                        if($bruteforcePolicy['lockingAccount'] == 1){
                            ?>
                            <input type="checkbox" id="lockingAccount" name="lockingAccount" checked>
                            <?php
                        }else{
                            ?>
                            <input type="checkbox" id="lockingAccount" name="lockingAccount">
                            <?php
                        }
                        ?>
                        <span class="lever"></span>
                        On
                    </label>
                </div>
            </div>
            <br><br>

            <div class="col s8 offset-s2 waves-effect" style="padding-top: 5%;">
                <span class="indigo-text text-lighten-1"><h5>Delay between connection attempts (min)</h5></span>
                <p class="range-field">
                    <input type="range" id="delay" name="delay" min="0" max="60" value="<?php echo $bruteforcePolicy['delay'] ?>"/>
                </p>
            </div>
            <br><br>

            <div class='row' style="padding-top: 47%;">
              <button type='submit' name='btn_bruteforce_protection' class='col s8 offset-s2 btn btn-large waves-effect indigo'>Save</button>
          </div>

      </form>
  </div>
</div>
</div>



</div>

</main>
