<?php
 
//Security verifying script only runs in WP installation
if ( ! defined( 'WP_UNINSTALL_PLUGIN' )) {
	die;
}
 

//Verifies user is admin
$iptmp_uninstall_chk_role = iptmp_uninstall_chk_role();

if ( is_user_logged_in() && $iptmp_uninstall_chk_role ) {

	//Deletes plugin options
	delete_option('iptmp_defaults');

	//Deletes all post metadata added by plugin
	global $wpdb;
	$iptmp_uninstall_post_meta = $wpdb-> prefix.'postmeta';
	$iptmp_uninstall_meta_value = 'iptmp_post_config';

	$iptmp_uninstall_meta_delete = $wpdb -> prepare (
		"DELETE FROM $iptmp_uninstall_post_meta"." WHERE meta_key = %s",
		$iptmp_uninstall_meta_value
	);

	$wpdb->query($iptmp_uninstall_meta_delete);


}

function iptmp_uninstall_chk_role() {
	/**
	 * Returns true or false depending on whether user is admin
	 * 
	 */
		
	//Gets all user roles
	$iptmp_uninstall_user = wp_get_current_user();
	$iptmp_uninstall_roles = $iptmp_uninstall_user -> roles;
		
	//Identifies if user has role that allows action
	$iptmp_uninstall_has_role = false;

	while (list($iptmp_uninstall_key,$iptmp_uninstall_value) = each($iptmp_uninstall_roles)) {
		if ($iptmp_uninstall_value == 'super admin' || $iptmp_uninstall_value == 'administrator' ) {
			$iptmp_uninstall_has_role = true;
		}
	}
		
	//Returns whether user has requisite role or not
	return $iptmp_uninstall_has_role;	
}