<?php
/**
 * Plugin Name: cookies_actecil
 * Description: Integration of Hopla Choice consent scripts.
 * Version: 1.2
 * Author: Actecil
 */
//0 - even earlier (e.g., before other plugins injecting code with priority 1)
add_action('wp_head', 'actecil_banner_connect', 1); 
function actecil_banner_connect() {
	echo '<link rel="preconnect" href="https://banner.actecil.eu" crossorigin>';
	echo '<link rel="dns-prefetch" href="https://banner.actecil.eu">';
	echo '<script src="https://banner.actecil.eu/prod/actecil_core.min.js" defer></script>';
}
?>