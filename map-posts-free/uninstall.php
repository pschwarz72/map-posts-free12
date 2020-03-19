<?php
 
//Security verifying script only runs in WP installation
if ( ! defined( 'WP_UNINSTALL_PLUGIN' )) {
	die;
}
 

//Verifies user is admin
$iptmp_uninstall_chk_role = iptmp_uninstall_chk_role();

if ( is_user_logged_in() && $iptmp_uninstall_chk_role ) {
	
	//Checks if Map Posts Pro also installed
	$iptmp_map_free_folder = __FILE__;
	$iptmp_map_pro_folder = str_replace('map-posts-free','map-posts-pro',$iptmp_map_free_folder);
	//Replaces backslashes with forwardslashes which is compatible with both Windows and Linux
	$iptmp_map_pro_folder_slashed = str_replace('\\','/',$iptmp_map_pro_folder);
	$iptmp_map_pro_exists = file_exists($iptmp_map_pro_folder);

	//Only delete database items if Pro version is not installed
	if ($iptmp_map_pro_exists == false) {
	
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