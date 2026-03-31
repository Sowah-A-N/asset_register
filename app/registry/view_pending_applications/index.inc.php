<?php

require_once __DIR__ . "/../../security.php";
secure_session_start();
if (!isset($_SESSION['username']))
{
    header("Location:../login/");
    die();
}
$username=$_SESSION['username'];
include "datacon.php";

if(isset($_POST['approve']))
{
  $hidden_index = mysqli_real_escape_string($conn, $_POST['id']);
  $sql="SELECT hostel_name, room_type FROM student_applications WHERE index_number='$hidden_index'";
  $result=mysqli_query($conn, $sql);
  $row=mysqli_fetch_array($result);
  $hostel_name=$row['hostel_name'];
  $room_type = $row['room_type'];

  $sql = "SELECT sex, email FROM student_registration WHERE index_number='$hidden_index'";
  $result = mysqli_query($conn, $sql);
  $row = mysqli_fetch_array($result);
  $sex = $row['sex'];
  $email = $row['email'];

  //Checking if applicant has been blacklisted
  $sql= "SELECT index_number FROM  blacklist_students WHERE index_number='$hidden_index' ";
  $result = mysqli_query($conn, $sql);
  $resultCheck=mysqli_num_rows($result);

  if($resultCheck>0)
  {
    echo "<script>alert('Student has been blacklisted. Kindly Decline this request instead.'); window.location='index.php';</script>";
  }
  else
  {

    //Checking if the room costs have been set
    $sql= "SELECT hostel_name, hostel_description, hostel_cost_usd FROM  hostel_cost WHERE hostel_name='$hostel_name' AND hostel_description='$room_type' ";
    $result = mysqli_query($conn, $sql);
    $resultCheck=mysqli_num_rows($result);

    if($resultCheck==0)
    {
      echo "<script>alert('Room Costs have not been set. Kindly alert the Finance Department'); window.location='index.php';</script>";
    }
    else
    {
      $row=mysqli_fetch_array($result);
      $hostel_cost=$row['hostel_cost_usd'];
      //Checking if the working dollar rate has been set
      $sql= "SELECT dollar_rate FROM  dollar_rate WHERE rate_status='ACTIVE' ";
      $result = mysqli_query($conn, $sql);
      $resultCheck=mysqli_num_rows($result);

      if($resultCheck==0)
      {
        echo "<script>alert('Working dollar rate has not been set. Kindly alert the Finance Department'); window.location='index.php';</script>";
      }
      else
      {
        $row=mysqli_fetch_array($result);
        $active_rate=$row['dollar_rate'];
        //Checking if rooms are available
        $sql = "SELECT hostel_name, room_description, room_sex, beds_available FROM  hostel_data
        WHERE hostel_name='$hostel_name' AND room_description='$room_type' AND room_sex='$sex' AND beds_available >0 ";
        $result = mysqli_query($conn, $sql);
        $resultCheck = mysqli_num_rows($result);

        if ($resultCheck == 0) {
          echo "<script>alert('There are no available rooms based on the application. Kindly Decline this request instead.'); window.location='index.php';</script>";
        }
        else
        {
          $sql="UPDATE student_applications SET room_status='APPROVED', approvedBy='$username' WHERE index_number='$hidden_index' ";
          $result = mysqli_query($conn, $sql);

          //Calculating Cedi equivalent to be paid by the applicant
          $amount_to_pay=$active_rate*$hostel_cost;

          $sql = "INSERT INTO rmu_pay_details(index_number, amount_to_pay) VALUES ('$hidden_index', '$amount_to_pay') ";
          $result = mysqli_query($conn, $sql);

          

          echo "<script>alert('Application Approved Successfully. Email will be sent to applicant to notify them.'); window.location='index.php';</script>";

        }

      }

    }

  }


  //echo "<script>alert('$hostel_name.$room_type');</script>";
}
?>

