<?php

if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>

<?php
    $this->load->model('User');
    $this->load->model('Role');
    echo "<script> console.log('PHP: ','COUCOU');</script>";
    if(isset($_SESSION['sessionData']) && $_SESSION['sessionData']['logged_in']==TRUE) {
        echo "<script> console.log('PHP: ','C BON');</script>";

        $u = User::getByUsername($_SESSION['sessionData']['username']);
        if (!($u->hasPrivilege("displayBusinessContent"))) {
            echo "<script> console.log('PHP: ','T\'AS PAS LES ACCES CONNARD');</script>";
            show_404();
        }
    }
    else {
        show_404();
    }
?>

<div class="container">

        <div class="row">
        <h5>Business Clients List</h5>
        <div class="col s8 offset-s2">
            <?php foreach ($affaires as $affaires_item): ?>

            <div class="card">
                <div class="card-image">
                    <?php echo "<img src=".img_url($affaires_item['id'].".jpg","business").">"; ?>
                    <span class="card-title">  <?php echo $affaires_item['name']; ?> </span>
                </div>
                <div class="card-content">
                    <div class="row">
                        <div class="col">
                            <p>
                                <?php echo $affaires_item['name']; ?>
                            </p>
                            <p>
                                <?php echo $affaires_item['bio']; ?>
                            </p>
                        </div>
                       </div>

                </div>
            </div>

            <?php endforeach; ?>
        </div>
    </div>
</div>

