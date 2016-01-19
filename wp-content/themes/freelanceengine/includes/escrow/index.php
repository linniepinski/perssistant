<?php
// silence is gold
require_once dirname(__FILE__) . '/settings.php';
if(ae_get_option('use_escrow')) {
	require_once dirname(__FILE__) . '/ppadaptive.php';
	require_once dirname(__FILE__) . '/paypal.php';
}
