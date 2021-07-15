<?php
   include "db.php";
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"/>
    <title>SAT Login</title>
    <link rel="stylesheet" href="style.css"/>
    <body style="background-image: url(Images/2.jpg);" >
</head>
<body>
     <header>
        <h1 style="color: black; font-size: 50px ;background-color: lightgreen; text-align: center;">SAT SURFACE DEFECT DETECTOR</h1>
    </header>

    <form class="form" method="post" name="login">
        <h1 class="login-title">Login</h1>
        <input type="text" class="login-input" name="username" placeholder="Username" autofocus="true"/>
        <input type="password" class="login-input" name="password" placeholder="Password"/>
        <input type="submit" value="Login" name="submit" class="login-button"/>
        <p class="link">Don't have an account? <a href="registration.php">Register Now</a></p>
  </form>

  <?php
    
     if(isset($_POST['submit']))
     {
        $count=0;
        $res=mysqli_query($con,"SELECT * FROM `users` WHERE username='$_POST[username]' && password='$_POST[password]';");

      $row= mysqli_fetch_assoc($res);

        $count=mysqli_num_rows($res);
        if($count==0)
        {
            ?>
            <script type="text/javascript">
                alert("User name and password doesnot match.");
            </script>
            <?php
        }
        else
        {
        /*--------------------------if username and password matches--------------------*/
            $_SESSION['login_user'] = $_POST['username'];

            ?>
             <script type="text/javascript">
                window.location="ClearTestDirectory.php"
             </script>
            <?php
        }
     }

    ?>


</body>
</html>
