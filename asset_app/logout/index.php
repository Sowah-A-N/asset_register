<?php

session_start();

session_unset();
session_destroy();

// Return to the SHARED sign-in (root /login/), not the retired asset_app/login/.
header("Location: ../../login/");
exit();