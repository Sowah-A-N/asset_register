<?php
session_start();
if (!isset($_SESSION['username']))
{
    header("Location:../login/");
    die();
}
$username=$_SESSION['username'];
include "datacon.php";


if(isset($_POST['submit']))
{
    $sql="SELECT sex FROM student_registration WHERE index_number= '$username' ";
    $result=mysqli_query($conn, $sql);
    $row=mysqli_fetch_array($result);
    $sex=strtoupper($row['sex']);


    $hostel_name='BACK ACCOMMODATION';
    $room_type=mysqli_real_escape_string($conn, $_POST['room_type']);

    if(empty($hostel_name))
    {
      echo "<script> alert('Check Details'); window.location='apply' </script> ";
    }
    else
    {
      $sql="SELECT * FROM student_applications WHERE index_number='$username'";
      $result= mysqli_query($conn, $sql);
      $result_check=mysqli_num_rows($result);
      if($result_check>0)
      {
          echo "<script> alert('Application Submitted Already. Kindly Await Approval'); window.location='../dashboard/' </script> ";
          exit();
      }
      else
      {
          $sql= "INSERT INTO student_applications (index_number, hostel_name, room_type, sex)
          VALUES ('$username','$hostel_name','$room_type','$sex') "; 
          $result=mysqli_query($conn,$sql);

          echo "<script> alert('Application Submitted Succesfully'); window.location='../dashboard/' </script> ";
          if(!$result)
          {
              echo "Not Inserted";
              error_log(mysqli_error($conn));
              exit();
          }

      }

    }
}
