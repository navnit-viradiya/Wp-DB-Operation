<?php
/*
Plugin Name: Wp DB Operation
Plugin URI:  
Description: wordpress plugin to insert, delete and update data of custom table 
Version:     1.0
Author:      
Author URI:
*/

//if(!class_exists(Wp_DB_Operation)){
	Class Wp_DB_Operation{
		
		/**
		* Construct
		*/
		public function __construct()
		{
			$this->wp_db_op_define_constants();
			add_action( 'admin_menu', array( $this, 'wp_db_op_add_menu' ) ); //Add Admin Menu
			$this->wp_db_op_includes();
			add_action( 'admin_enqueue_scripts', array( $this, 'wp_db_op_admin_scripts'));
			register_activation_hook(__FILE__, array($this, 'wp_db_op_activation')); // Activation
			//register_activation_hook(__FILE__, array($this, 'wp_db_op_install_data'));
			register_deactivation_hook(__FILE__, array($this, 'wp_db_op_deactivation'));
			//register_uninstall_hook(__FILE__, array($this, 'wp_db_op_uninstall'));
			
			add_action('admin_head', array($this,'my_custom_fonts'));
			
			
			//add_action( 'admin_action_suid_admin_action',  array( $this,'suid_admin_action_handler') );
			//register_activation_hook(WP_SUID_BASENAME, array(&$this, 'wp_db_op_activation'));
		}
		
		/**
		* Define costants
		*/
		public function wp_db_op_define_constants() {
			
			if ( !defined( 'WP_DB_OPERATION_BASENAME' ) )
				define( 'WP_DB_OPERATION_BASENAME', plugin_basename( __FILE__ ) );
				
			if ( !defined( 'WP_DB_OPERATION_URL' ) )
				define( 'WP_DB_OPERATION_URL', plugin_dir_url( __FILE__ ) );

			if ( !defined( 'WP_DB_OPERATION_DIR_PATH' ) )
				define( 'WP_DB_OPERATION_DIR_PATH', plugin_dir_path( __FILE__ ) );
				
			if ( !defined( 'WP_DB_OPERATION_ADMIN_DIR' ) )        
				define('WP_DB_OPERATION_ADMIN_DIR', plugin_dir_url(__FILE__)."admin/");
				
			if ( !defined( 'WP_DB_OPERATION_PUBLIC_DIR' ) )        
				define('WP_DB_OPERATION_PUBLIC_DIR', plugin_dir_url(__FILE__)."public/");
         
		}
		
		public function wp_db_op_admin_scripts() {
			wp_enqueue_script('wp-db-op-js', WP_DB_OPERATION_ADMIN_DIR.'assets/scripts/wp-db-op.js');
			// Localize the script with new data
			$wp_db_js_obj_array = array(
				'listing_page_url' => admin_url('admin.php?page=wp_db_op_page_student')
			);
			wp_localize_script( 'wp-db-op-js', 'wp_db_js_object', $wp_db_js_obj_array );
		}
		
		public function my_custom_fonts(){
			echo '<style>
    .navigation li a, .navigation li a:hover, .navigation li.active a, .navigation li.disabled {color: #fff; text-decoration:none;}
.navigation li {display: inline;}
.navigation li a, .navigation li a:hover, .navigation li.active a, .navigation li.disabled {background-color: #c4497c; border-radius: 3px; cursor: pointer; padding: 7px; padding: 0.55rem;}
.navigation li a:hover, .navigation li.active a {background-color: #b21456;}
div.navigation {float: right;}
  </style>';
		}
		
		/**
		* Activation
		*/
		static function wp_db_op_activation(){
		
			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			global $wpdb;
		
			$table_name = $wpdb->prefix . 'student';
			$charset_collate = $wpdb->get_charset_collate();
		
			if($wpdb->get_var("show tables like '$table_name'") != $table_name) 
			{
				$sql = "CREATE TABLE $table_name (
					id int(11) AUTO_INCREMENT,
					std_name varchar(50) NOT NULL,
					std_email varchar(50) NOT NULL,
					date TIMESTAMP,
					PRIMARY KEY  (id)
				) $charset_collate;";
				//echo $sql;
				//exit;
				dbDelta( $sql );
			}
		}
		
		/**
		* Deactivation
		*/
		static function wp_db_op_deactivation(){
			
			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			
			global $wpdb;
			$table_name = $wpdb->prefix . 'student';
			
			$sql = "DROP TABLE IF EXISTS `$table_name`";
			$wpdb->query($sql);
		}
		
		/**
		* Actions perform at loading of admin menu
		*/
		public function wp_db_op_add_menu() {
			//add_menu_page('Qd Demo Title', 'Qd Demo', 'qd_demo', 'qd-demo-main-page', 'qd_main_page_callback');
			add_menu_page( 'DB Operation', 'DB Operation', 'activate_plugins', 'wp_db_op',  array( $this,'wp_db_op_form_handler'), 'dashicons-list-view' );		
			//add_menu_page( 'wp_db_op', 'List', 'List', '',  'wp_db_op_data_list',  array( $this,'wp_db_op_data_list_handler') );
		}
		
		/**
		* Include files
		*/
		public function wp_db_op_includes(){
			
			include('includes/class-wp-db-op-student.php');
			$Wp_DB_Operation_Student_Obj = new Wp_DB_Operation_Student();
		}
		
		public function wp_db_op_form_handler(){
			?>
			<div class="wrap">
				<h2>DB Operation</h2>
			</div>
			<?php
		}
	}
//}
$Wp_DB_Operation = new Wp_DB_Operation();
?>
