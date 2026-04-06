<?php
// Front controller — every request enters here.
// Loads bootstrap then hands off to the router.

require_once dirname(__DIR__) . '/src/bootstrap.php';
require_once SRC . '/core/router.php';

dispatch($conn);
