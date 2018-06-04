<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>


<div id=content> <!-- To allow footer to be below the fullscreen slider -->  
    <footer class="page-footer indigo darken-2">
        <div class="container">
            <div class="row">
                <div class="col l6 s12">
                    <h5 class="white-text">GTI 619 - Laboratoire 5</h5>
                    <p class="grey-text text-lighten-4"> Credits go to PROVOST Arthur, LAMOUREUX Samuel, PUISSEGUR Alexis, VERMELLE Léandre.</p>
                </div>
                <div class="col l4 offset-l2 s12">
                    <h5 class="white-text">Links</h5>
                    <ul>
                        <?php echo "<li><a class=\"grey-text text-lighten-3\" href=".base_url().">Home</a></li>"; ?>
                    </ul>
                </div>
            </div>
        </div>
        <div class="footer-copyright">
            <div class="container">
                © 2018 Copyright Text
            </div>
        </div>
    </footer>
</div>

</body>
</html>


<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.1/jquery.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.97.5/js/materialize.min.js"></script>

<script type="text/javascript" src="https://code.jquery.com/jquery-3.2.1.min.js"></script>

<!-- Compiled and minified CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.100.2/css/materialize.min.css">

<!-- Compiled and minified JavaScript -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.100.2/js/materialize.min.js"></script>

<!-- ReCaptcha Google -->
<script src="https://www.google.com/recaptcha/api.js" async defer></script>

<!-- Our script -->
<script type="text/javascript" src=<?php echo js_url("home");?> ></script>
