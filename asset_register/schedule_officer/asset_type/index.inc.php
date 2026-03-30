<?php

require_once __DIR__ . "/../../security.php";
secure_session_start();
if (isset($_POST['edit']))
{
  echo "<script> window.location='edit_asset.php?id'  </script> ";  
  exit();
}
