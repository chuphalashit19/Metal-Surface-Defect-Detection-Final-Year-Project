<html>
    <head>
        <link rel="stylesheet" href="main.css">
    </head>
    <body style="background-image: url(2.jpg);" >
        <div class="header">
            <ul>
                <li> <a href="home.php"><strong> LOGOUT </strong></a></li>

            </ul>
        </div>
        
            <h2 style="font-size: 40px; color: black; text-align: center; margin-top: 100px;">Let's find the defects</h2>
            <div class="container1" style="width: 100%;">
                <form method="POST">
                <button type="button" onclick="window.location.href='upload.html'" style="background-color:cadetblue;color: black;">Upload Again</button><br><br> 
                
            </form>
        </div>
        <?php
        if(isset($_POST['check']))
        {   
            $conn=mysqli_connect("localhost","root","","metal");
            if(isset($_POST['cancel']))
            {
        	    header( "Refresh:1; url=CancelBooking.php"); 
            }
            if(isset($_POST['logout']))
            {
	            session_unset();
	            session_destroy();
	            header( "Refresh:1; url=home.php"); 
            }
        }
        ?>
    </body>
</html>