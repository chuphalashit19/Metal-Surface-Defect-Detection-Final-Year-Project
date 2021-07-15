<?php
   include "db.php";
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"/>
    <title>Registration</title>
    <link rel="stylesheet" href="style.css"/>
    <body style="background-image: url(Images/4.jpg);" >
</head>
<body>
     <header>
        <h1 style="color: black; font-size: 50px ;background-color: lightgreen; text-align: center;">SAT SURFACE DEFECT DETECTOR</h1>
    </header>

    <form class="form" action="" method="post">
        <h1 class="login-title">Registration</h1>
        <input type="text" class="login-input" name="username" placeholder="Username" required="">
        <input type="text" class="login-input" name="email" placeholder="Email Adress" required="">
        <input type="text" class="login-input" name="mobile" placeholder="Mobile Number" required="">
        <input type="password" class="login-input" name="password" placeholder="Password" required="">
        <input type="submit" name="submit" value="Register" class="login-button">
        <p class="link">Already have an account? <a href="login.php">Login here</a></p>
    </form>

<?php

       if(isset($_POST['submit']))
       {
          $count=0;
          $sql="SELECT username from users";
          $res=mysqli_query($con,$sql);

          while($row=mysqli_fetch_assoc($res))
          {
            if($row['username']==$_POST['username'])
            {
                $count=$count+1;
            }
          }
        if($count==0)
         { mysqli_query($con,"INSERT INTO `users` VALUES('', '$_POST[username]', '$_POST[email]', '$_POST[mobile]', '$_POST[password]');");

       ?>
       <script type="text/javascript">
        alert("Registration successful");
       </script>
    <?php

    }
    else
    {
        ?>
       <script type="text/javascript">
        alert("User name already exist");
       </script>
    <?php

    }

    }

    ?>

</body>
</html>
