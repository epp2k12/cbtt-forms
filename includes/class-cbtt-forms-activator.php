<?php

/**
 * Fired during plugin activation
 *
 * @link       https://webappdevz.com
 * @since      1.0.0
 *
 * @package    Cbtt_Forms
 * @subpackage Cbtt_Forms/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Cbtt_Forms
 * @subpackage Cbtt_Forms/includes
 * @author     Erwin Presbitero <epp2k12@gmail.com>
 */
class Cbtt_Forms_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {

		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		global $wpdb;

		$table_name = $wpdb->prefix . 'cbtt_forms';
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE $table_name (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			name varchar(100) NOT NULL,
			email varchar(100) NOT NULL,
			phone varchar(50) DEFAULT '' NOT NULL,
			tour_date date DEFAULT NULL,
			message text,
			created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
			PRIMARY KEY  (id)
		) $charset_collate;";

		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);

		// First table: tours
		$tours_table = $wpdb->prefix . 'tours';
		
		$sql = "CREATE TABLE $tours_table (
			id INT NOT NULL AUTO_INCREMENT,
			post_id BIGINT(20) UNSIGNED NOT NULL,
			post_type VARCHAR(20) NOT NULL,
			category VARCHAR(100) NOT NULL,
			name VARCHAR(255) NOT NULL,
			price DECIMAL(10,2) NOT NULL,
			discount DECIMAL(5,2) DEFAULT 0.00,
			PRIMARY KEY (id),
			UNIQUE KEY post_id (post_id),
			FOREIGN KEY (post_id) REFERENCES {$wpdb->prefix}posts(ID) ON DELETE CASCADE
		) $charset_collate;";
		
		dbDelta($sql);
		
		// Second table: add_ons (related to tours)
		$add_ons_table = $wpdb->prefix . 'add_ons';
		
		$sql = "CREATE TABLE $add_ons_table (
			id INT NOT NULL AUTO_INCREMENT,
			tours_id INT NOT NULL,
			name VARCHAR(255) NOT NULL,
			status TINYINT(1) DEFAULT 1,
			PRIMARY KEY (id),
			FOREIGN KEY (tours_id) REFERENCES $tours_table(id) ON DELETE CASCADE
		) $charset_collate;";
		
		dbDelta($sql);
		
		// Add version option for potential future updates
		add_option('cbtt_forms_db_version', '1.0');



	}


}


