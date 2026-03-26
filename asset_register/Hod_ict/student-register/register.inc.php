<?php

 session_start();

 include "datacon.php";
 if(mysqli_connect_errno())
 {
     echo "Failed to connect to MYSQli:".mysqli_connect_error();
 }

 if(isset($_POST['register']))
 {
    $fname=strtoupper(mysqli_real_escape_string($conn,$_POST['fname']));
    $lname=strtoupper(mysqli_real_escape_string($conn,$_POST['lname']));
    $index_no=strtoupper(mysqli_real_escape_string($conn,$_POST['index_no']));
    $email=strtoupper(mysqli_real_escape_string($conn,$_POST['email']));
    $nationality=strtoupper(mysqli_real_escape_string($conn,$_POST['nationality']));
    $gender=strtoupper(mysqli_real_escape_string($conn,$_POST['gender']));
    $country_code=strtoupper(mysqli_real_escape_string($conn,$_POST['country_code']));
    $phone_number=strtoupper(mysqli_real_escape_string($conn,$_POST['phone_number']));
    

     //Error Handlers
     //Checking for empty fields

    $sql="SELECT index_number FROM rmu_active_students WHERE index_number='$index_no' ";
    $result=mysqli_query($conn,$sql);
    $rowNum=mysqli_num_rows($result);
    if($rowNum < 1)
    {
        echo "<script> alert('Student not Recognized. Kindly contact the Registry'); window.location='index.html' </script>";
        exit();
    }
    else if(empty($fname)||empty($lname)||empty($index_no)||empty($email)||empty($nationality)||empty($gender)||empty($country_code)||empty($phone_number))
     {
         echo "<script> alert('Check Details Submitted'); window.location='index.html' </script> ";  
         exit();
     }
     else
     {
         //Check if input chararcters are valid
         if(!preg_match("/^[a-zA-Z0-9]*$/", $index_no))
         {
             echo "<script> alert('Check Details Submitted'); window.location='index.html' </script> ";  
             exit();
         }
         else
         {
             //Check if email is valid
             if(!filter_var($email,FILTER_VALIDATE_EMAIL))
             {
                 echo "<script> alert('Check Details Submitted'); window.location='index.html' </script> ";  
                 exit();
             }
             else
             {
                 if(strlen($index_no)>10 )
                 {
                    echo "<script> alert('Check Details Submitted'); window.location='index.html' </script> ";  
                    exit();
                 }
                 else
                 {
                    $sql="SELECT email FROM student_registration WHERE index_number='$index_no' ";
                    $result= mysqli_query($conn, $sql);
                    $result_check=mysqli_num_rows($result);
                    if($result_check>0)
                    {
                        echo "<script> alert('Check Details Submitted'); window.location='index.html' </script> ";  
                        exit();
                    }
                    else
                    {
                        $sql="SELECT phone_number FROM student_registration WHERE index_number='$index_no' ";
                        $result= mysqli_query($conn, $sql);
                        $result_check=mysqli_num_rows($result);
                        if($result_check>0)
                        {
                            echo "<script> alert('Check Details Submitted'); window.location='index.html' </script> ";  
                            exit();
                        }
                        else
                        {
                            $sql="SELECT * FROM student_registration WHERE index_number='$index_no' ";
                            $result= mysqli_query($conn, $sql);
                            $result_check=mysqli_num_rows($result);
        
                            if($result_check>0)
                            {
                                echo "<script> alert('User has Already Registered. Login.'); window.location='../login/' </script> ";  
                                exit();
                            }
                            else
                            {    
                                //Choosing user programme from existing Registry records
                                $sql="SELECT programme_studied FROM rmu_active_students WHERE index_number='$index_no' ";
                                $result=mysqli_query($conn, $sql);
                                $row=mysqli_fetch_array($result);
                                $programme_studied=$row['programme_studied'];

                                //Generating user password
                                $confirm_code=substr(str_shuffle("ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789"), -8);
                                //hashing password
                                $hashed_password= password_hash($confirm_code, PASSWORD_DEFAULT);  

                                //Final query to insert into the database
            $query="INSERT INTO student_registration (first_name, last_name, index_number, program_of_study, nationality, email, phone_number, sex, user_password) 
            VALUES ('$fname', '$lname', '$index_no', '$programme_studied', '$nationality', '$email', '$phone_number', '$gender', '$hashed_password')";
        
                                $result=mysqli_query($conn, $query);
                                if(!$result)
                                {
                                    echo "Not Inserted";
                                    echo "Errormessage:".mysqli_error($conn);
                                    exit();
                                }
        
                                echo "<script> alert('Registration Successful. Kindly check your email for approval: $confirm_code'); window.location='../login/' </script> ";  
                                    
                                
                            }

                        }

                    }
                 }

             }
         }

     }
 }
 else
 {
                    
    echo "<script> alert('Check Details Submitted'); window.location='index.html' </script> ";  
 }
 
 