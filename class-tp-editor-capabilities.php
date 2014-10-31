<?php
/**
 * Plugin Name: Editor capabilities
 * Description: Sets up the editor role a bit different.
 */

class TP_Editor_Capabilities {
	function __construct() {
		add_action( 'init', array( $this, 'set_capabilities' ) );
		add_filter( 'map_meta_cap', array( $this, 'limit_lower_roles' ), 10, 4);
	}
	
	/**
	 * Set some of the editors capabilities
	 */
	function set_capabilities() {
		$editor = get_role( 'editor' );
		
		$editor->add_cap( 'list_users' );
		$editor->add_cap( 'remove_users' );
		$editor->add_cap( 'add_users' );
		$editor->add_cap( 'promote_users' );
		$editor->add_cap( 'create_users' );
		$editor->add_cap( 'delete_users' );
		$editor->add_cap( 'edit_users' );
		
		$editor->add_cap( 'edit_theme_options' );
		$editor->remove_cap( 'switch_themes' );
	}
	
  	/**
  	 * Refrain non-admins from editing / promoting / deleting administrators
  	 */
	function limit_lower_roles( $caps, $cap, $user_id, $args ) {
		if( 'promote_user' == $cap ) {

			if( ! isset( $args[0] ) )
				$caps[] = 'do_not_allow';

			if( $args[0] == $user_id ) 
				$caps[] = 'do_not_allow';
			
			$other = new WP_User( absint( $args[0] ) );
			if( $other->has_cap('administrator') && ! current_user_can( 'administrator' ) )
				$caps[] = 'do_not_allow';

		} else if( $cap == 'delete_users' || $cap == 'edit_user' ) {
			if( ! isset( $_GET['user'] ) )
				return $caps;

			$other_id = absint( $_GET['user'] );
			
			if( ! $other_id )
				return $caps;

			if( $other_id == $user_id )
				return $caps;
			
			$other = new WP_User( $other_id );
			if( $other->has_cap( 'administrator' ) && ! current_user_can( 'administrator' ) )
				$caps[] = 'do_not_allow';

		}
		
		return $caps;
	}
} new TP_Editor_Capabilities;
