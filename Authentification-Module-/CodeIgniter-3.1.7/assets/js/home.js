$(document).ready(function(){

  //Initialisation of  the webpage
  $('select').material_select();
  $('ul.tabs').tabs();
  $(".dropdown-button").dropdown();
  $(".button-collapse").sideNav(); //Allow to click on the logo of the navbar
  $('.slider').slider(); //Allow to the slider to move
  $('.modal').modal({ //Allow to open the connexion field
      dismissible: true, // Modal can be dismissed by clicking outside of the modal
      opacity: .7, // Opacity of modal background
      inDuration: 300, // Transition in duration
      outDuration: 200, // Transition out duration
      startingTop: '4%', // Starting top style attribute
      endingTop: '10%', // Ending top style attribute
  });


});


$(document).scroll(function(){

  var a = $(".navbar-fixed").offset().top; // heigh position of navbar

      if($(this).scrollTop() > a) //if navbar position isn't on top
      {   
        $('nav').css({"opacity":"1"});
      } else {
        $('nav').css({"opacity":"0.7"});
      }
});

