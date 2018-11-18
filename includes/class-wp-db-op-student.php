<?php
class Wp_DB_Operation_Student{
	
	public $page_title	= 'Add New';
	public $button_text	= 'Add';
	public $row_action	= 'add_row';
	
	
	public $message; 
	
	/**
	* Construct
	*/
	public function __construct()
	{	
		//core
		add_action( 'admin_menu', array( $this, 'wp_db_op_student_menu_page' ) );
		add_action( 'admin_action_wp_db_op_student_admin_action',  array( $this,'wp_db_op_student_admin_action') );
		add_action( 'wp_ajax_wp_db_op_delete_row',  array( $this,'wp_db_op_delete_row') );
		//add_action( 'wp_ajax_nopriv_wp_db_op_delete_row',  array( $this,'wp_db_op_delete_row') );
		
		add_action( 'wp_db_op_admin_notices', array( $this,'wp_db_op_student_admin_notices'));
	}
	
	public function wp_db_op_student_menu_page(){
		add_submenu_page( 'wp_db_op', 'Students', 'Students', 'edit_themes',  'wp_db_op_page_student',  array( $this,'wp_db_op_student_page_handler'));	
		add_submenu_page( 'wp_db_op_page_student', 'Add New ', 'Add New', 'edit_themes',  'wp_db_op_page_add_student',  array( $this,'wp_db_op_student_form'));
	}
	
	
	public function wp_db_op_student_page_handler(){
		global $wpdb;
		$table_name = $wpdb->prefix . 'student';
		/******** pagination start **************/
		$customPagHTML     = "";
		$query             = "SELECT * FROM {$table_name}";
		$total_query     = "SELECT COUNT(1) FROM (${query}) AS combined_table";
		$total             = $wpdb->get_var( $total_query );
		$items_per_page = 10;
		$page             = isset( $_GET['cpage'] ) ? abs( (int) $_GET['cpage'] ) : 1;
		$offset         = ( $page * $items_per_page ) - $items_per_page;
		$totalPage         = ceil($total / $items_per_page);

		if($totalPage > 1){
			$customPagHTML     =  '<div class="tablenav-pages">
			<span class="displaying-num">'.$total.' items Page '.$page.' of '.$totalPage.'</span>'.paginate_links( array(
			'base' => add_query_arg( 'cpage', '%#%' ),
			'format' => '',
			'prev_text' => __('&laquo;'),
			'next_text' => __('&raquo;'),
			'total' => $totalPage,
			'current' => $page,
			//'before_page_number' => '<li>',
			//'after_page_number'  => '</li>'
			//'type'               => 'list'
			)).'</div>';
		}

		/***** pagination end ***********/
		
		
		
	
	$results = $wpdb->get_results( "SELECT * FROM {$table_name} LIMIT {$offset}, {$items_per_page}", ARRAY_A );	
	?>
	<div class="wrap">
		<h1 class="wp-heading-inline">Students <a href="<?php echo admin_url('admin.php?page=wp_db_op_page_add_student'); ?>" class="page-title-action">Add New</a></h1>
		
		<?php do_action('wp_db_op_admin_notices');  ?>
		
		<div class="tablenav top">
			<?php echo $customPagHTML; ?>
		</div>
		
		
		<form method="get">
			<?php wp_nonce_field('handle_student_form', 'nonce_student_form'); ?>
			<table class="widefat fixed" cellspacing="0">
				<thead>
					<tr>
						<th id="cb" class="manage-column column-cb check-column" scope="col">
							<input type="checkbox">
						</th>
						<!-- this column contains checkboxes -->
						<th id="columnname" class="manage-column column-columnname" scope="col">Name</th>
						<th id="columnname" class="manage-column column-columnname" scope="col">Email</th>
						<!-- "num" added because the column contains numbers -->
					</tr>
				</thead>

				<tfoot>
					<tr>
						<th class="manage-column column-cb check-column" scope="col">
							<input type="checkbox">
						</th>
						<th class="manage-column column-columnname" scope="col">Name</th>
						<th class="manage-column column-columnname" scope="col">Email</th>
					</tr>
				</tfoot>

				<tbody>
					
					<?php if(count($results) > 0){ ?>
						<?php foreach($results as $result){ ?>
						<tr valign="top">
							<!-- this row contains actions -->
							<th class="check-column" scope="row">
								<input type="checkbox">
							</th>
							
							<td class="column-columnname"><?php echo $result['std_name']; ?>
								<div class="row-actions">
									<span class="edit"><a href="<?php echo admin_url('admin.php?page=wp_db_op_page_add_student&id='.$result['id'].'&action=edit_row'); ?>" aria-label="Edit “Blog”">Edit</a> | </span>
									<span class="trash"><a href="#" onclick="wp_db_op_trach('<?php echo $result['id']; ?>'); return false;" class="submitdelete" aria-label="Move “Blog” to the Trash">Trash</a></span>
								</div>
							</td>
							<td class="column-columnname"><?php echo $result['std_email']; ?></td>
						</tr>
						<?php } ?>
					<?php } else { ?>
						<tr valign="top">
							<td class="column-columnname" colspan="2">Not Record Found.</td>
						</tr>
					<?php } ?>
					
				</tbody>
			</table>
			</form>
			<div class="tablenav bottom">
			<?php echo $customPagHTML; ?>
		</div>
			
		</div>
		<?php 
	}
	
	public function wp_db_op_student_form(){
		
		
		$std_name	= '';
		$std_email	= '';
		
		if(isset($_GET['action']) && $_GET['action'] == 'edit_row'){
			
			$this->page_title	= 'Edit';
			$this->button_text = 'Update';
			$this->row_action = 'edit_row';
			
			if(isset($_GET['id']) && $_GET['id'] != ''){
				$id = $_GET['id'];
				global $wpdb;
				$table_name = $wpdb->prefix . 'student';
				$data = $this->wp_db_op_get_row($table_name,$id);
				
				$std_name	= $data[0]['std_name'];
				$std_email	= $data[0]['std_email'];
			}
			
		} else if(isset($_GET['action']) && $_GET['action'] == 'delete_row'){
			//exit;
		}
		
		?>
		<div class="wrap">
			<h2><?php echo $this->page_title; ?></h2>
		<?php 
		
		
		
		do_action('wp_db_op_admin_notices');
		
		
		?>	
			
		<form name="" method="post" action="<?php echo admin_url( 'admin.php' ); ?>" >
			<p>
				<label>Name<label>
				<input type="text" name="std_name" value="<?php echo $std_name; ?>">
			</p>
			<p>
				<label>Email<label>
				<input type="text" name="std_email" value="<?php echo $std_email; ?>">
			</p>
			<p>
				<?php 
				wp_nonce_field('handle_student_form', 'nonce_student_form'); 
				submit_button($this->button_text); ?>
				<input type="hidden" name="row_action" value="<?php echo $this->row_action; ?>" />
				<input type="hidden" name="id" value="<?php if(isset($_GET['id'])){ echo $_GET['id']; } ?>" />
				<input type="hidden" name="action" value="wp_db_op_student_admin_action" />
			</p>
		</form>
		</div>
		<?php
	}
	
	public function wp_db_op_student_admin_action(){
		
		
		
		global $wpdb;
		$table_name = $wpdb->prefix . 'student';
		
		if (!wp_verify_nonce($_POST['nonce_student_form'], 'handle_student_form'))
        {	
			wp_safe_redirect( esc_url_raw(add_query_arg(array(
				'status' => 'error',
				'message' => 'You are not authorized to perform this action.') ,admin_url( 'admin.php?page=wp_db_op_page_add_student' )) ));
			
		} else {
			
			if($_POST['row_action'] == 'add_row'){
				
				$wpdb->insert($table_name, array(
				'std_name' => $_POST['std_name'],
				'std_email' => $_POST['std_email']
				));
							
				wp_safe_redirect( esc_url_raw(add_query_arg(array(
				'status' => 'success',
				'message' => 'Added succesfully.') , admin_url( 'admin.php?page=wp_db_op_page_student' )) ));
				
			} else if($_POST['row_action'] == 'edit_row'){
				
				
				$data = array(
					'std_name' => $_POST['std_name'],
					'std_email' => $_POST['std_email']
				);
				
				$wpdb->update($table_name, $data, array('id'=> $_POST['id']));
				
				wp_safe_redirect( esc_url_raw(add_query_arg(array(
				'status' => 'success',
				'message' => 'Updated succesfully.') , admin_url( 'admin.php?page=wp_db_op_page_student' )) ));
				
				
			} else {
				
			}
		}	
		
	}
	
	public function wp_db_op_student_admin_notices(){
		
		if(isset($_REQUEST['page']) && $_REQUEST['page'] == 'wp_db_op_page_add_student' || isset($_REQUEST['page']) && $_REQUEST['page'] == 'wp_db_op_page_student'){
		
			$message_class= '';
			if(isset($_REQUEST['status']) && $_REQUEST['status'] == 'success'){
				$message_class = 'updated';
			} else if(isset($_REQUEST['status']) && $_REQUEST['status'] == 'error') {
				$message_class = 'error';
			}
			
			if(isset($_REQUEST['message']) && $_REQUEST['message'] != ''){	
				
				$message = sprintf($_REQUEST['message']);
				echo "<div class=\"{$message_class} notice\"><p>{$message}</p></div>";
			}
			
			
		}
		
	}
	
	public function wp_db_op_get_row($table,$id){
		global $wpdb;
		$results = $wpdb->get_results( "SELECT * FROM {$table} WHERE id={$id}", ARRAY_A );
		return $results;
	}
	
	public function wp_db_op_delete_row(){
		
		
		global $wpdb;
		$return_array = array();
		$id = $_POST['id'];
		$table_name = $wpdb->prefix . 'student';
		$result = $wpdb->delete( $table_name, array( 'id' => $id ) );
		if($result){
			$return_array = array( 'status' => 'success', 'message' => '1 item deleted.' );	
		} else {
			$return_array = array( 'status' => 'error', 'message' => 'Item not found' );	
		}
		
		wp_send_json($return_array);
		die();
	}
	
}
?>
