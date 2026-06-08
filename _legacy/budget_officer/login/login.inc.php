<?php

session_start();
include "datacon.php";

if (isset($_POST['Login']))
{
    if (!$conn) {
        echo "<p>Could not connect to the server '" . $dbServername . "'</p>\n";
    }

    
    
    $username=mysqli_real_escape_string($conn, $_POST['username']);
    $pass= mysqli_real_escape_string($conn, $_POST['pass']);
    
     
   
    //Error handlers
    //Check if inputs are empty

    if (empty($username)||empty($pass))
    {
        echo "<script> alert('Check Details'); window.location='../login/' </script> ";  
        exit();
    }
    else
        {
            $sql="SELECT * FROM student_registration WHERE index_number= '$username'";
            $result= mysqli_query($conn, $sql);
            $resultCheck= mysqli_num_rows($result);

            if($resultCheck < 1)
            {
                echo "<script> alert('Check Details'); window.location='../login/' </script> ";
                exit();
            }
            else
            {
                if($row= mysqli_fetch_assoc($result))
                {
                    $sql="SELECT user_password FROM student_registration WHERE index_number= '$username'";
                    $result= mysqli_query($conn, $sql);
                    $row= mysqli_fetch_array($result);
                            //De-hashing
                            $hashedpassCheck= password_verify($pass, $row['user_password']);
                            

                            if($hashedpassCheck == false)
                            {
                                echo "<script> alert('Invalid Credentials'); window.location='../login/' </script> ";
                                exit();
                            }

                            else if($hashedpassCheck  == true)
                            {
                                //login the user here
                                
                            $username=$_POST['username'];
                            $_SESSION['username']=$username;
                            header("Location: ../dashboard/?login=success");
            
                                exit();     
                            }  
                        
                }          
                
            }
        }
 
}
 
else
    {
        echo "<script> alert('Check Details'); window.location='SIA/login/' </script> ";
            exit();
    }   