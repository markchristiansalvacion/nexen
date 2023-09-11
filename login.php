 <?php
  include 'includes/load.php';

  if (is_login_auth()) {
    redirect("index");
  }
  ?>
 <!DOCTYPE html>
 <html lang="en" class="h-100">

 <head>

   <meta charset="utf-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="keywords" content="" />
   <meta name="author" content="" />
   <meta name="robots" content="" />
   <meta name="viewport" content="width=device-width, initial-scale=1">


   <!-- PAGE TITLE HERE -->
   <title>Arrowgo Logistics WMS</title>
   <!-- FAVICONS ICON -->
   <link rel="shortcut icon" href="img/Logo ArrowgoL.png">
   <link href="./vendor/jquery-nice-select/css/nice-select.css" rel="stylesheet">
   <link rel="stylesheet" href="@sweetalert2/theme-minimal/minimal.css">
   <link href="https://fonts.googleapis.com/css2?family=Material+Icons" rel="stylesheet">
   <link href="css/style.css" rel="stylesheet">
   <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
 </head>

 <body>
   <?php
    if (isset($_SESSION['msg'])) {
    ?>
     <script>
       swal({

         title: "<?php echo $_SESSION['msg_heading']; ?>",
         text: "<?php echo $_SESSION['msg']; ?>",
         icon: "<?php echo $_SESSION['msg_type']; ?>",
         button: "Close",

       });
     </script>

   <?php

      unset($_SESSION['msg']);
      unset($_SESSION['msg_type']);
      unset($_SESSION['msg_heading']);
    }
    ?>
   <div class="container h-100">

     <div class="page-holder d-flex align-items-center">
       <div class="container">
         <div class="row align-items-center py-5">
           <div class="col-xl-6 col-md-6 sign text-center">
             <div>
               <div class="text-center my-5">
                 <!-- <a href="index.html"><img src="images/logo-full.png" alt=""></a> -->
               </div>
               <img src="img/agl_logo.png" class="img-fix bitcoin-img sd-shape7"></img>
             </div>
           </div>
           <div class="col-xl-6 col-md-6">
             <div class="sign-in-your py-4 px-2">
               <h2 class="mb-4">Welcome!</h2>
               <span>Welcome back! Login with your data that you entered<br> during registration</span></br></br></br>

               <p class="text-muted">This system is intended for the generation,monitoring and recoding data of Arrowgo Logistics WMS</p>

               <form id="loginForm" action="login_proc" class="mt-4" method="post">
                 <div class="mb-3">
                   <label class="mb-1"><strong>Email</strong></label>
                   <input type="email" class="form-control" name="loginUsername" placeholder="email@user.com">
                 </div>
                 <div class="mb-3">
                   <label class="mb-1"><strong>Password</strong></label>
                   <input type="password" name="loginPassword" class="form-control" placeholder="************">
                 </div>
                 <button type="submit" class="btn btn-primary btn-block">Sign Me In</button>
               </form>
             </div>
           </div> 
           <p class="mt-5 mb-0 text-gray-400 text-center">Arrowgo Logistics © 2020-2025</p>
           <!-- Please do not remove the backlink to us unless you support further theme's development at https://bootstrapious.com/donate. It is part of the license conditions. Thank you for understanding :)                 -->
         </div>
       </div>
     </div>


     <!--**********************************
        Scripts
    ***********************************-->
     <!-- Required vendors -->
     <script src="./vendor/global/global.min.js"></script>
     <script src="./js/dlabnav-init.js"></script>


 </body>

 </html>