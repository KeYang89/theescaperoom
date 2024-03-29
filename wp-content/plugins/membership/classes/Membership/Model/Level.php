<?php

// +----------------------------------------------------------------------+
// | Copyright Incsub (http://incsub.com/)                                |
// +----------------------------------------------------------------------+
// | This program is free software; you can redistribute it and/or modify |
// | it under the terms of the GNU General Public License, version 2, as  |
// | published by the Free Software Foundation.                           |
// |                                                                      |
// | This program is distributed in the hope that it will be useful,      |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of       |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the        |
// | GNU General Public License for more details.                         |
// |                                                                      |
// | You should have received a copy of the GNU General Public License    |
// | along with this program; if not, write to the Free Software          |
// | Foundation, Inc., 51 Franklin St, Fifth Floor, Boston,               |
// | MA 02110-1301 USA                                                    |
// +----------------------------------------------------------------------+

/**
 * The level model class.
 *
 * @since 3.5
 *
 * @category Membership
 * @package Model
 * @subpackage Level
 */
class Membership_Model_Level {

	var $id = false;
	var $db;
	var $tables = array( 'membership_levels', 'membership_rules', 'subscriptions_levels', 'membership_relationships', 'levelmeta' );
	var $membership_levels;
	var $membership_rules;
	var $subscriptions_levels;
	var $membership_relationships;
	var $levelmeta;
	var $active = null;
	// if the data needs reloaded, or hasn't been loaded yet
	var $dirty = true;
	var $level;
	var $ruledetails = array();
	// Active rules
	var $positiverules = array();
	var $negativerules = array();
	var $lastlevelid;
	// Allows cascading of page rules across children
	var $allow_page_cascade = false;

	public function __construct( $id = false, $fullload = false, $loadtype = array( 'public', 'core' ) ) {
		global $wpdb, $M_options;

		$this->id = $id;
		$this->db = $wpdb;

		foreach ( $this->tables as $table ) {
			$this->$table = membership_db_prefix( $this->db, $table );
		}

		if ( is_null($this->active) ) {	//only run if hasn't been run before
			//check if this level is active - if it isn't we don't want to load it's rules
			$this->active = (bool) $wpdb->get_var($wpdb->prepare("
				SELECT level_active
				FROM {$this->membership_levels}
				WHERE level_active = 1
					AND id = %d", $this->id
			));
		}
		$allow_page_cascade = (isset($M_options['allow_page_rule_cascade'])) ? $M_options['allow_page_rule_cascade'] : 'yes';
		$this->allow_page_cascade = 'yes' == $allow_page_cascade ? true : false;

		if ( $fullload && $this->active ) {
			$this->load_rules( $loadtype );
		}

		add_action( 'remove_this_level_members', 'remove_level_members', 10, 1);
	}

	// Fields

	function level_title() {

		if(empty($this->level)) {
			$level = $this->get();

			if($level) {
				return $level->level_title;
			} else {
				return false;
			}
		} else {
			return $this->level->level_title;
		}

	}

	function get_shortcode() {

		if(empty($this->level)) {
			$level = $this->get();

			if($level) {
				return sanitize_title_with_dashes('level-' . $level->level_title);
			} else {
				return false;
			}
		} else {
			return sanitize_title_with_dashes('level-' . $level->level->level_title);
		}

	}

	// Gets

	function get() {

		if($this->dirty) {
			$sql = $this->db->prepare( "SELECT * FROM {$this->membership_levels} WHERE id = %d", $this->id);

			$this->level = $this->db->get_row($sql);

			$this->dirty = false;
		}

		return $this->level;

	}

	function get_rules($type) {

		$sql = $this->db->prepare( "SELECT * FROM {$this->membership_rules} WHERE level_id = %d AND rule_ive = %s ORDER BY rule_order ASC", $this->id, $type );

		$this->ruledetails[$type] = $this->db->get_results( $sql );

		return $this->ruledetails[$type];

	}

	function delete($forced = false) {

		$sql = $this->db->prepare( "DELETE FROM {$this->membership_levels} WHERE id = %d", $this->id);

		$sql2 = $this->db->prepare( "DELETE FROM {$this->membership_rules} WHERE level_id = %d", $this->id);

		$sql3 = $this->db->prepare( "DELETE FROM {$this->subscriptions_levels} WHERE level_id = %d", $this->id);

		if($this->db->query($sql)) {

			$this->db->query($sql2);
			$this->db->query($sql3);

			$this->dirty = true;

			return true;

		} else {
			return false;
		}

		do_action( 'remove_this_level_members', $this->id );

	}

	static function get_dates( $level_id, $user_id = false ) {
		global $wpdb;
		if ( empty( $user_id ) ) {
			$user_id = Membership_Plugin::current_member()->ID;
		}

		$user_id = (int) $user_id;
		$level_id = (int) $level_id;

		// $sql = $wpdb->prepare( "SELECT startdate, updateddate, expirydate FROM '%s' WHERE user_id=%d AND level_id=%d", MEMBERSHIP_TABLE_RELATIONS, $user_id, $level_id );
		$sql = $wpdb->prepare( "SELECT * FROM " . MEMBERSHIP_TABLE_RELATIONS . " WHERE user_id=%d AND level_id=%d", $user_id, $level_id );

		$results = $wpdb->get_results( $sql );

		return $results;

	}

	function update() {

		$this->dirty = true;

		if($this->id < 0 ) {
			return $this->add();
		} else {
			$return = $this->db->update($this->membership_levels, array('level_title' => $_POST['level_title'], 'level_slug' => sanitize_title($_POST['level_title'])), array('id' => $this->id));

			// Remove the existing rules for this membership level
			$this->db->query( $this->db->prepare( "DELETE FROM {$this->membership_rules} WHERE level_id = %d", $this->id ) );

			$filter_rules = array( 'menu' );

			// Process the new rules
			if(!empty($_POST['in-positive-rules'])) {
				$rules = explode(',', $_POST['in-positive-rules']);
				$count = 1;
				foreach( (array) $rules as $rule ) {
					if(!empty($rule)) {

						// Check if the rule has any information for it.
						if(isset($_POST[$rule])) {

							$rule_array = $_POST[$rule];

							if( in_array( $rule, $filter_rules) ) {
								$rule_array = Membership_Model_Rule::filter_rule_array( $rule_array );
							}

							$ruleval = maybe_serialize( $rule_array );
							// write it to the database
							$this->db->insert($this->membership_rules, array("level_id" => $this->id, "rule_ive" => 'positive', "rule_area" => $rule, "rule_value" => $ruleval, "rule_order" => $count++));
							// Hit an action - two methods of hooking
							do_action('membership_update_positive_rule', $rule, $_POST, $this->id);
							do_action('membership_update_positive_rule_' . $rule, $_POST, $this->id);
						}
					}

				}
			}

			if(!empty($_POST['in-negative-rules'])) {
				$rules = explode(',', $_POST['in-negative-rules']);
				$count = 1;
				foreach( (array) $rules as $rule ) {
					if(!empty($rule)) {

						// Check if the rule has any information for it.
						if(isset($_POST[$rule])) {

							$rule_array = $_POST[$rule];

							if( in_array( $rule, $filter_rules) ) {
								$rule_array = Membership_Model_Rule::filter_rule_array( $rule_array );
							}

							$ruleval = maybe_serialize($_POST[$rule]);
							// write it to the database
							$this->db->insert($this->membership_rules, array("level_id" => $this->id, "rule_ive" => 'negative', "rule_area" => $rule, "rule_value" => $ruleval, "rule_order" => $count++));
							// Hit an action - two methods of hooking
							do_action('membership_update_negative_rule', $rule, $_POST, $this->id);
							do_action('membership_update_negative_rule_' . $rule, $_POST, $this->id);
						}
					}
				}
			}

			do_action('membership_level_update', $this->id);

		}

		return true; // for now

	}

	function add() {

		$this->dirty = true;

		if($this->id > 0 ) {
			return $this->update();
		} else {
			$return = $this->db->insert($this->membership_levels, array('level_title' => $_POST['level_title'], 'level_slug' => sanitize_title($_POST['level_title'])));

			$this->id = $this->db->insert_id;

			// Process the new rules
			if(!empty($_POST['in-positive-rules'])) {
				$rules = explode(',', $_POST['in-positive-rules']);
				$count = 1;
				foreach( (array) $rules as $rule ) {
					if(!empty($rule)) {
						// Check if the rule has any information for it.
						if(isset($_POST[$rule])) {
							$ruleval = maybe_serialize($_POST[$rule]);
							// write it to the database
							$this->db->insert($this->membership_rules, array("level_id" => $this->id, "rule_ive" => 'positive', "rule_area" => $rule, "rule_value" => $ruleval, "rule_order" => $count++));
							// Hit an action - two methods of hooking
							do_action('membership_add_positive_rule', $rule, $_POST, $this->id);
							do_action('membership_add_positive_rule_' . $rule, $_POST, $this->id);
						}
					}

				}
			}

			if(!empty($_POST['in-negative-rules'])) {
				$rules = explode(',', $_POST['in-negative-rules']);
				$count = 1;
				foreach( (array) $rules as $rule ) {
					if(!empty($rule)) {
						// Check if the rule has any information for it.
						if(isset($_POST[$rule])) {
							$ruleval = maybe_serialize($_POST[$rule]);
							// write it to the database
							$this->db->insert($this->membership_rules, array("level_id" => $this->id, "rule_ive" => 'negative', "rule_area" => $rule, "rule_value" => $ruleval, "rule_order" => $count++));
							// Hit an action - two methods of hooking
							do_action('membership_add_negative_rule', $rule, $_POST, $this->id);
							do_action('membership_add_negative_rule_' . $rule, $_POST, $this->id);
						}
					}
				}
			}

		}

		do_action('membership_level_add', $this->id);

		return true; // for now

	}

	function toggleactivation($forced = false) {

		$this->dirty = true;

		$sql = $this->db->prepare( "UPDATE {$this->membership_levels} SET level_active = NOT level_active WHERE id = %d", $this->id);

		return $this->db->query($sql);

	}
	// UI functions


	function load_rules( $loadtype = array( 'public', 'core' ) ) {
		global $M_Rules;

		membership_debug_log( __( 'Loading level - ', 'membership' ) . $this->level_title() );

		$positive = $this->get_rules( 'positive' );
		if ( !empty( $positive ) ) {
			$key = 0;
			foreach ( (array) $positive as $key => $rule ) {

				if ( isset( $M_Rules[$rule->rule_area] ) && class_exists( $M_Rules[$rule->rule_area] ) ) {
					$this->positiverules[$key] = new $M_Rules[$rule->rule_area]( $this->id );
					if( $rule->rule_area == 'pages' ) {
						$this->positiverules[$key]->allow_page_cascade = $this->allow_page_cascade;
					}
					if ( in_array( $this->positiverules[$key]->rulearea, $loadtype ) ) {
						$this->positiverules[$key]->on_positive( maybe_unserialize( $rule->rule_value ) );
						$this->positiverules[$key]->set_level_data( $this->level->id );
						$key++;
					} else {
						unset( $this->positiverules[$key] );
					}
				}
			}
		}

		$negative = $this->get_rules( 'negative' );
		if ( !empty( $negative ) ) {
			$key = 0;
			foreach ( (array) $negative as $key => $rule ) {
				if( $rule->rule_area == 'pages' ) {
					$rule->allow_page_cascade = $this->allow_page_cascade;
				}
				if ( isset( $M_Rules[$rule->rule_area] ) && class_exists( $M_Rules[$rule->rule_area] ) ) {
					$this->negativerules[$key] = new $M_Rules[$rule->rule_area]( $this->id );
					if( $rule->rule_area == 'pages' ) {
						$this->negativerules[$key]->allow_page_cascade = $this->allow_page_cascade;
					}

					if ( in_array( $this->negativerules[$key]->rulearea, $loadtype ) ) {
						$this->negativerules[$key]->on_negative( maybe_unserialize( $rule->rule_value ) );
						$this->negativerules[$key]->set_level_data( $this->level->id );
						$key++;
					} else {
						unset( $this->negativerules[$key] );
					}
				}
			}
		}
	}

	function has_positive_rule($rulename) {

		if(!empty($this->positiverules)) {
			foreach($this->positiverules as $key => $rule) {
				if($rule->name == $rulename) {
					return true;
				}
			}
		}

		return false;

	}

	function has_negative_rule($rulename) {

		if(!empty($this->negativerules)) {
			foreach($this->negativerules as $key => $rule) {
				if($rule->name == $rulename) {
					return true;
				}
			}
		}

		return false;

	}

	function has_rule($rulename) {

		if($this->has_negative_rule($rulename) || $this->has_positive_rule($rulename)) {
			return true;
		} else {
			return false;
		}

	}

	// pass thrus

	function positive_pass_thru($rulename, $function, $arg) {

		if(!empty($this->positiverules)) {
			foreach($this->positiverules as $key => $rule) {
				if($rule->name == $rulename) {
					return $rule->$function('positive', $arg);
				}
			}
		}

		return false;

	}

	function negative_pass_thru($rulename, $function, $arg) {

		if(!empty($this->negativerules)) {
			foreach($this->negativerules as $key => $rule) {
				if($rule->name == $rulename) {
					return $rule->$function('negative', $arg);
				}
			}
		}

		return false;

	}

	// Counting
	function count( ) {

		$sql = $this->db->prepare( "SELECT count(*) as levelcount FROM {$this->membership_relationships} WHERE level_id = %d", $this->id );

		return $this->db->get_var( $sql );

	}

	// Meta information
	function get_meta($key, $default = false) {

		$sql = $this->db->prepare( "SELECT meta_value FROM {$this->levelmeta} WHERE meta_key = %s AND level_id = %d", $key, $this->id);

		$row = $this->db->get_var( $sql );

		if(empty($row)) {
			return $default;
		} else {
			return $row;
		}

	}

	function add_meta($key, $value) {

		return $this->insertorupdate( $this->levelmeta, array( 'level_id' => $this->id, 'meta_key' => $key, 'meta_value' => $value) );

	}

	function update_meta($key, $value) {

		return $this->insertorupdate( $this->levelmeta, array( 'level_id' => $this->id, 'meta_key' => $key, 'meta_value' => $value) );

	}

	function delete_meta($key) {

		$sql = $this->db->prepare( "DELETE FROM {$this->levelmeta} WHERE meta_key = %s AND level_id = %d", $key, $this->id);

		return $this->db->query( $sql );

	}

	function insertorupdate( $table, $query ) {

			$fields = array_keys($query);
			$formatted_fields = array();
			foreach ( $fields as $field ) {
				$form = '%s';
				$formatted_fields[] = $form;
			}
			$sql = "INSERT INTO `$table` (`" . implode( '`,`', $fields ) . "`) VALUES ('" . implode( "','", $formatted_fields ) . "')";
			$sql .= " ON DUPLICATE KEY UPDATE ";

			$dup = array();
			foreach($fields as $field) {
				$dup[] = "`" . $field . "` = VALUES(`" . $field . "`)";
			}

			$sql .= implode(',', $dup);

			return $this->db->query( $this->db->prepare( $sql, $query ) );

	}

	function can_view_current_page() {
		global $wp_query, $M_options;
		$valid = true;

		$global = Membership_Plugin::is_global_tables()
			? MEMBERSHIP_GLOBAL_MAINSITE != get_current_blog_id()
			: false;

		$this->positive_results = array();

		// validate positive rules
		foreach ( $this->positiverules as $key => $rule ) {
			if ( $global && !$rule->is_network_wide() ) {
				continue;
			}

			$this->positive_results[$key]['name'] = $rule->name;
			$this->positive_results[$key]['cascade'] = $this->allow_page_cascade;
			$this->positive_results[$key]['result'] = $rule->validate_positive( $this->positive_results );
			$valid = $this->positive_results[$key]['result'];

			if ( !$this->positive_results[$key]['result'] ) {
				if( !in_array( $rule->name, array('posts', 'categories') ) ) {
					break;
				}
			}
		}

		$this->negative_results = array();

		if ( $valid ) {
			// validate negative rules
			foreach ( $this->negativerules as $key => $rule ) {
				if ( $global && !$rule->is_network_wide() ) {
					continue;
				}

				$this->negative_results[$key]['name'] = $rule->name;
				$this->negative_results[$key]['cascade'] = $this->allow_page_cascade;
				$this->negative_results[$key]['result'] = $rule->validate_negative( $this->negative_results );
				$valid = $this->negative_results[$key]['result'];

				if ( ! $this->negative_results[$key]['result'] ) {
					if( !in_array( $rule->name, array('posts', 'categories') ) ) {
						break;
					}
				}
			}
		}

		// Are we protecting the front page?
		$protect_front_page = (isset($M_options['protect_front_page']) && 'yes' == $M_options['protect_front_page'] ) ? true : false;
		$protect_front_page = apply_filters( 'membership_protect_front_page', $protect_front_page );

		if( ( is_home() || is_front_page() ) && ! $protect_front_page ) {
			return true;
		}

		return $valid;
	}

	// Remove members at this levels when this level is deleted
	function remove_level_members( $id )
	{
		$this->db->delete( MEMBERSHIP_TABLE_RELATIONS, array( 'level_id' => $id ), array( '%d' ) );
	}



	public static function get_associated_role( $id ) {
		global $wpdb;

		$role = '';

		$table_name = $wpdb->prefix . "m_levelmeta";

		$retrieve_data = $wpdb->get_results( "SELECT meta_value FROM $table_name WHERE level_id = {$id} AND meta_key='associated_wp_role' LIMIT 1" );

		foreach ( $retrieve_data as $retrieved_data ) {
			$role = $retrieved_data->meta_value;
		}

		// If there is no associated role, make it the default site role.
		if ( 'none' == $role || '' == $role ){
			$role = get_option( 'default_role' );
		}

		return $role;
	}

}

/**
 * Deprecated version of the level class. Do not use this class anymore.
 *
 * @deprecated since version 3.5
 *
 * @category Membership
 * @package Model
 * @subpackage Level
 */
class M_Level extends Membership_Model_Level {}