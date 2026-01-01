<?php
/**
 * Notification templates
 */

if( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class Myrtle_Chat_Template
 */
class Myrtle_Chat_Template {

	/**
	 * @var self
	 */
	private static $instance = null;

	/**
     * Chat Settings
     */
    private $settings;

    /**
     * user_id
     */
    private $user_id;

    /**
     * group_ids
     */
    private $group_ids;

    /**
	 * @var return
	 */
	private $return = '';

	/**
	 * @since 1.0
	 * @return $this
	 */
	public static function instance() {

		if ( is_null( self::$instance ) && ! ( self::$instance instanceof Myrtle_Chat_Template ) ) {
			self::$instance = new self;
			self::instance()->hooks();
			self::instance()->user_id = get_current_user_id();
		}

		return self::$instance;
	}

	/**
	 * Call hooks
	 *
	 * @return void
	 */
	public function hooks() {

		add_action( 'wp_ajax_update_chat', [ $this, 'mld_update_chat' ] );
		add_action( 'wp_ajax_get_group_users', [ $this, 'mld_get_group_users' ] );
		add_action( 'wp_ajax_update_live_chat', [ $this, 'mld_update_live_chat' ] );
		add_action( 'wp_ajax_delete_message', [ $this, 'mld_delete_message' ] );
		add_action( 'wp_ajax_load_more_messages', [ $this, 'mld_load_more_messages' ] );
		add_shortcode( 'myrtle_chat', [ $this, 'mld_chatbox' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'mld_enqueue_scripts' ] );
	}

	/**
	 * get more messages
	 */
	public function mld_load_more_messages() {

		$group_id = isset( $_POST['group_id'] ) ? $_POST['group_id'] : 0;
		$user_id = isset( $_POST['user_id'] ) ? $_POST['user_id'] : 0;
		$chat_type = isset( $_POST['chat_type'] ) ? $_POST['chat_type'] : 0;
		$chat_user = isset( $_POST['chat_user'] ) ? $_POST['chat_user'] : 0;
		$page = isset( $_POST['page'] ) ? $_POST['page'] : 0;
		
		if( 'general' != $chat_type ) {
			$messages = self::instance()->get_group_messages( $chat_type, $group_id, 20, $page );
		}

		if( 'general' == $chat_type ) {
			$messages = self::instance()->get_user_messages( $chat_type, $group_id, $user_id, $chat_user, 20, $page );
		}

		if( ! empty( $messages ) || is_array( $messages ) ) {

			ob_start();
			foreach( $messages as $index => $message ) {

				$msg_date = isset( $message->dates ) ? $message->dates : '';
				$message_id = isset( $message->ID ) ? $message->ID : 0;
				$prev_index = $index - 1;
				$prev_doer = isset( $messages[$prev_index]->doer ) ? $messages[$prev_index]->doer : '';

				$doer = isset( $message->doer ) ? intval( $message->doer ) : 0;

				$message = isset( $message->message ) ? $message->message : '';
				$parent_class = ( $loggin_id == $doer ) ? 'mld-sender-wrapper mld-sender-msg' : 'mld-reciever-wrapper mld-reciever-msg';
				$child_class = ( $loggin_id == $doer ) ? 'mld-sender' : 'mld-reciever';
				?>
				<div class="mld-chat-msg-wrap">
					<?php
					if( $doer != $prev_doer && $loggin_id != $doer ) {

						?>
						<div class="mld-message-user"><?php echo self::$instance->get_user_name( $doer ); ?></div>
						<?php
					}
					?>
					<div class="mld-chat-date">
						<button>
							<?php 
							echo date( "Y-m-d", $msg_date );
							?>
						</button>
					</div>
					<div class="<?php echo $parent_class; ?>">
						<div class="mld-chat-msg <?php echo $child_class;?>">
							<div class="mld-user-chat">
								<?php 
								echo stripslashes( $message );
								?>
							</div>
						</div>
					</div>
					<div class="mld-clear-both"></div>
				</div>
				<?php
				if( current_user_can( 'manage_options' ) ) {
					?>
					<div class="mld-delete-message" data-id="<?php echo $message_id; ?>"><?php echo __( 'Delete', 'myrtle-learning-dashboard' ); ?></div>
					<?php
				}
			}

			$content = ob_get_contents();
			ob_get_clean();

			$response['content'] = $content;
			$response['count'] = count( $messages );
			$response['status'] = 'true';
			echo json_encode( $response );
			wp_die();
		}
	}

	/**
	 * create a shortcode to chat box
	 */
	public function mld_chatbox( $attr ) {

		$settings = self::instance()->settings;
		$user_id = self::instance()->user_id;
		$type = isset( $attr['type'] ) && $attr['type'] == 'page' ? $attr['type'] : 'section';

		return Myrtle_Chat_Template::get_chat_template( $user_id, $type, $settings );
	}

	/**
	 * enqueue chat files
	 */
	public function mld_enqueue_scripts() {
		
		//if( has_shortcode( get_the_content( get_the_ID() ), 'myrtle_chat' ) ) {
 
			$rand = rand( 1000000, 1000000000 );
			wp_enqueue_style( 'chat-css', MLD_ASSETS_URL . 'css/chat.css', [], $rand, null );
			wp_register_script( 'mld-chat-js', MLD_ASSETS_URL. 'js/chat.js', [ 'jquery' ], $rand, true );

			wp_localize_script( 'mld-chat-js', 'MLD', [
				'ajaxURL'       => admin_url( 'admin-ajax.php' ),
				'security'      => wp_create_nonce( 'mld_ajax_nonce' ),
				'chat_setings'  => self::instance()->settings,
				'user_id'       => self::instance()->user_id
			] );

			wp_enqueue_script( 'mld-chat-js' );
		//}
	}

	/**
	 * delete messages
	 */
	public function mld_delete_message() {
		
		global $wpdb;
		$id = isset( $_POST['id'] ) ? intval( $_POST['id'] ) : 0;
		$table_name = $wpdb->prefix.'mld_chats';
		$wpdb->query(
			$wpdb->prepare("DELETE FROM $table_name WHERE ID = %d", $id )
		);
		wp_die();
	}

	public static function get_chat_template( $user_id, $type, $settings ) {

		self::instance()->settings = $settings;
		self::instance()->user_id = $user_id;
		call_user_func( [ self::$instance, 'get_chat_' . $type ] );
		return self::instance()->return;
	}

	/**
	 * create a function to convert time to sec/min ago
	 */
	private function time_elapsed_string($datetime, $full = false) {
		
		$now = new DateTime;
		$ago = new DateTime($datetime);
		$diff = $now->diff($ago);

    	// Calculate weeks separately (without adding dynamic property)
		$weeks = floor($diff->d / 7);
		$days  = $diff->d - ($weeks * 7);

		$string = array(
			'y' => 'year',
			'm' => 'month',
			'w' => 'week',
			'd' => 'day',
			'h' => 'hour',
			'i' => 'min',
			's' => 'sec',
		);

		foreach ($string as $k => &$v) {
			$value = 0;

			if ($k === 'w') {
				$value = $weeks;
			} elseif ($k === 'd') {
				$value = $days;
			} else {
				$value = $diff->$k;
			}

			if ($value) {
				$v = $value . ' ' . $v . ($value > 1 ? 's' : '');
			} else {
				unset($string[$k]);
			}
		}

		if (!$full) {
			$string = array_slice($string, 0, 1);
		}

		return $string ? implode(', ', $string) . ' ago' : '';
	}

	// private function time_elapsed_string( $datetime, $full = false ) {

	//     $now = new DateTime;
	//     $ago = new DateTime( $datetime );
	//     $diff = $now->diff( $ago );

	//     $diff->w = floor( $diff->d / 7 );
	//     $diff->d -= $diff->w * 7;

	//     $string = array(
	//         'y' => 'year',
	//         'm' => 'month',
	//         'w' => 'week',
	//         'd' => 'day',
	//         'h' => 'hour',
	//         'i' => 'min',
	//         's' => 'sec',
	//     );

	//     foreach ( $string as $k => &$v ) {

	//         if ( $diff->$k ) {
	//             $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
	//         } else {
	//             unset($string[$k]);
	//         }
	//     }

	//     if (!$full) $string = array_slice($string, 0, 1);
	//     return $string ? implode(', ', $string) . ' ago' : '';
	// }

	/**
	 * get last group message
	 */
	public static function mld_get_last_group_message( $chat_type, $group_id ) {

        global $wpdb;
		$table_name = $wpdb->prefix.'mld_chats';

		$last_group_message = $wpdb->get_results( $wpdb->prepare( "
			SELECT message, dates, ID FROM $table_name WHERE
			chat_type = '$chat_type' AND group_id = $group_id ORDER BY dates DESC LIMIT 1" ) );

		$last_message = isset( $last_group_message[0]->message ) ? $last_group_message[0]->message : '';
		$last_message = str_replace( '“', '', $last_message );
		$last_message = str_replace( '”', '', $last_message );
		
		$last_message_time = isset( $last_group_message[0]->dates ) ? intval( $last_group_message[0]->dates ) : '';

		if( $last_message_time > 0 ) {
			$last_message_time = date( 'Y-m-d H:i:s', $last_message_time );
		} else {
			$last_message_time = '';
		}
	
		$id = isset( $last_group_message[0]->ID ) ? $last_group_message[0]->ID : 0;
		$data = [];
		$data['last_message'] = $last_message;
		$data['last_time'] = $last_message_time;
		$data['unique_id'] = $id;
		return $data;
	}

	/**
	 * get last message of user
	 */
	private function mld_get_last_message( $doer, $user_id, $group_id, $chat_type ) {

        global $wpdb;

		$table_name = $wpdb->prefix.'mld_chats';
		$last_general_messages = $wpdb->get_results( $wpdb->prepare( "

			SELECT * FROM $table_name WHERE
			( doer = $user_id AND user_id = $doer )
			OR
			( user_id = $user_id AND doer = $doer )
			AND group_id = $group_id AND chat_type = '$chat_type' ORDER BY ID DESC LIMIT 1
			" ) );

		$data = [];

		$last_messages = isset( $last_general_messages[0]->message ) ? $last_general_messages[0]->message : '';
		$last_message_time = isset( $last_general_messages[0]->dates ) ? intval( $last_general_messages[0]->dates ) : '';
		if( $last_message_time > 0 ) {
			$last_message_time = date( 'Y-m-d H:i:s', intval( $last_message_time ) );	
		} else {
			$last_message_time = '';
		}
		
		$last_message_id = isset( $last_general_messages[0]->ID ) ? $last_general_messages[0]->ID : 0;
		$data['last_message'] = $last_messages;
		$data['last_time'] = $last_message_time;
		$data['unique_id'] = $last_message_id;

		return $data;
	}

	/**
	 * update live chat
	 */
	public function mld_update_live_chat() {

		$response = [];

		if( ! wp_verify_nonce( $_POST['mld_nounce'], 'mld_ajax_nonce' ) ) {

			$response['message'] = __( 'data not found', 'myrtle-learning-dashboard' );
			$response['status'] = 'false';

			echo json_encode( $response );
			wp_die();
		}

		$group_id = isset( $_POST['group_id'] ) ? $_POST['group_id'] : 0;
		$chat_type = isset( $_POST['chat_type'] ) ? $_POST['chat_type'] : '';
		$chat_user = isset( $_POST['chat_user'] ) ? $_POST['chat_user'] : '';
		$loggin_id = isset( $_POST['loggin_user'] ) ? $_POST['loggin_user'] : self::instance()->user_id;
		$message_data = isset( $_POST['message_data'] ) ? $_POST['message_data'] : [];
		$paged = isset( $_POST['page'] ) ? $_POST['page'] : 1; 
		$messages = [];

		if( 'general' != $chat_type ) {
			$messages = self::instance()->get_group_messages( $chat_type, $group_id, 20, $paged );
		}

		if( 'general' == $chat_type ) {
			$messages = self::instance()->get_user_messages( $chat_type, $group_id, $loggin_id, $chat_user, 20, $paged );
		}

		if( ! empty( $messages ) || is_array( $messages ) ) {

			$last_index_id = end( $messages );

			ob_start();
?>
<button class="mld-load-more-btn"
  data-group_id="<?php echo esc_attr( $group_id ); ?>"
  data-user_id="<?php echo esc_attr( $loggin_id ); ?>"
  data-chat_type="<?php echo esc_attr( $chat_type ); ?>"
  data-chat_user="<?php echo esc_attr( $chat_user ); ?>"
  data-paged="<?php echo esc_attr( $paged ); ?>">
  <?php echo __( 'Load more', 'myrtle-learning-dashboard' ); ?>
</button>

<?php
foreach( $messages as $index => $msg_obj ) {

  $msg_date    = isset( $msg_obj->dates ) ? $msg_obj->dates : '';
  $message_id  = isset( $msg_obj->ID ) ? $msg_obj->ID : 0;
  $doer        = isset( $msg_obj->doer ) ? intval( $msg_obj->doer ) : 0;
  $message_txt = isset( $msg_obj->message ) ? $msg_obj->message : '';

  // direction based on logged-in user
  $is_me = ( $loggin_id == $doer );

  // avatar (keep simple; adjust if you have your own avatar function)
  $avatar_url = get_avatar_url( $doer, array( 'size' => 80 ) );
  ?>
  <?php if ( ! $is_me ) : ?>
    <div class="mld-msg is-left">
      <img class="mld-msg-av" src="<?php echo esc_url( $avatar_url ); ?>" alt="">
      <div class="mld-bubble"><?php echo stripslashes( $message_txt ); ?></div>
    </div>
  <?php else : ?>
    <div class="mld-msg is-right">
      <div class="mld-bubble is-me"><?php echo stripslashes( $message_txt ); ?></div>
      <img class="mld-msg-av" src="<?php echo esc_url( $avatar_url ); ?>" alt="">
    </div>
  <?php endif; ?>

  <?php
  if( current_user_can( 'manage_options' ) ) {
    ?>
    <div class="mld-delete-message" data-id="<?php echo esc_attr( $message_id ); ?>">
      <?php echo __( 'Delete', 'myrtle-learning-dashboard' ); ?>
    </div>
    <?php
  }
}
$l_i_id = isset( $last_index_id->ID ) ? $last_index_id->ID : 0;
?>
<input type="hidden" class="mld-u_id" value="<?php echo esc_attr( $l_i_id ); ?>">
<?php

$content = ob_get_contents();
ob_get_clean();


			$response['content'] = $content;
			$response['status'] = 'true';
		}

		if( ! empty( $message_data ) && is_array( $message_data ) ) {

			foreach( $message_data as $data ) {

				$chat_user_id = isset( $data['chat_user_id'] ) ? $data['chat_user_id'] : 0;
				$chat_type = isset( $data['chat_type'] ) ? $data['chat_type'] : 0;
				$message_id = isset( $data['last_message_id'] ) ? $data['last_message_id'] : 0;
				if( 'general' == $chat_type ) {
					
					// $last_message = self::$instance->mld_get_last_message( $user_id, $chat_user_id, $group_id, $chat_type );
					$last_message = self::$instance->mld_get_last_message( $loggin_id, $chat_user_id, $group_id, $chat_type );
				} else {

					$last_message = self::mld_get_last_group_message( $chat_type, $group_id );
				}

				$last_message_id = isset( $last_message['unique_id'] ) ? $last_message['unique_id'] : 0;

				$message = isset( $last_message['last_message'] ) ? $last_message['last_message'] : '';

				if( $message_id < $last_message_id ) {

					$response['id']				= $last_message_id;
					$response['message']   		= $message;
					$response['userId']    		= $chat_user_id;
					$response['chatType']  		= $chat_type;
					$response['status'] 		= 'true';
				}
			}
		}
		echo json_encode( $response );
		wp_die();
	}

	/**
	 * Get Group users :Ajax
	 */
	public function mld_get_group_users() {
		
		$response = [];

		if( ! wp_verify_nonce( $_POST['mld_nounce'], 'mld_ajax_nonce' ) ) {
			
			$response['message'] = __( 'data not found', 'myrtle-learning-dashboard' );
			$response['status'] = 'false';
			echo json_encode( $response );
			wp_die();
		}

		$group_id = isset( $_POST['group_id'] ) ? intval( $_POST['group_id'] ) : 0;
		$chat_type = isset( $_POST['chat_type'] ) ? sanitize_text_field( $_POST['chat_type'] ) : 0;
		$logged_in_user = get_current_user_id();
		if( empty( $group_id ) ) {
			
			$response['message'] = __( 'data not found', 'myrtle-learning-dashboard' );
			$response['status'] = 'false';

			echo json_encode( $response );
			wp_die();
		}

		$user_ids = mld_get_group_users( $group_id );

		ob_start();

		if( ! empty( $user_ids ) && is_array( $user_ids ) ) {

			foreach( $user_ids as $index => $user_id ) {

				$user_id = intval( $user_id );
				if( ! $user_id ) {
					continue;
				}

				$user = get_userdata( $user_id );
				if( ! $user ) {
					continue;
				}

				$user_name = ! empty( $user->display_name ) ? $user->display_name : $user->user_login;
				$avatar    = get_avatar_url( $user_id, [ 'size' => 80 ] );

				if( empty( $avatar ) ) {
					$avatar = 'https://i.pravatar.cc/80';
				}

				$active_class = ( $index === 0 ) ? ' is-active' : '';
				?>
				<div class="mld-user-row<?php echo esc_attr( $active_class ); ?>" data-chat-user="<?php echo esc_attr( $user_id ); ?>" data-group-id="<?php echo esc_attr( $group_id ); ?>" data-logged_user="<?php echo esc_attr( $logged_in_user ); ?>" data-chat_type="<?php echo esc_attr( $chat_type ); ?>">
					<img class="mld-user-avatar" src="<?php echo esc_url( $avatar ); ?>" alt="">
					<div class="mld-user-meta">
						<div class="mld-user-name"><?php echo esc_html( $user_name ); ?></div>
						<div class="mld-user-last">—</div>
					</div>
					<div class="mld-user-time">1h</div>
				</div>
				<?php
			}

		}

		$content = ob_get_contents();
		ob_get_clean();

		$response['content'] = $content;
		$response['status']  = 'true';

		echo json_encode( $response );
		wp_die();
	}


	/**
	 * create a function to get group messages
	 */
	private function get_group_messages( $type, $group_id, $limit, $page ) {

		global $wpdb;
		$table_name = $wpdb->prefix.'mld_chats';
		
		$offset = ( $page - 1 ) * $limit;
		
		$messages = $wpdb->get_results( 
			"SELECT * FROM $table_name 
			WHERE chat_type = '$type' 
			AND group_id = $group_id 
			ORDER BY dates DESC 
			LIMIT $limit OFFSET $offset"
		);
		return array_reverse( $messages );
	}

	/**
	 * create a function to get user messages
	 */
	private function get_user_messages( $type, $group_id, $user_id, $chat_user, $limit, $page ) {

        global $wpdb;

		$table_name = $wpdb->prefix.'mld_chats';
		$offset = ( $page - 1 ) * $limit;

		$messages = $wpdb->get_results( "
			SELECT * FROM $table_name WHERE
			group_id = ( $group_id )
			AND
			( ( doer = $user_id AND user_id = $chat_user )
			OR
			( user_id = $user_id AND doer = $chat_user ) )
			ORDER BY dates DESC
			LIMIT $limit OFFSET $offset
		" );
		return array_reverse($messages);
	}

	/**
	 * update user chat at 'wp_mld_chats' table
	 */
	public function mld_update_chat() {

		$response = [];
		global $wpdb;

		$message = isset( $_POST['message'] ) ? sanitize_text_field( $_POST['message'] ) : '';
		$group_id = isset( $_POST['group_id'] ) ? intval( $_POST['group_id'] ) : 0;
		$chat_user_id = isset( $_POST['chat_user'] ) ? intval( $_POST['chat_user'] ) : 0;
		$chat_type = ( isset( $_POST['chat_type'] ) && 'general' == $_POST['chat_type'] ) ? 'general' : 'group';
		$user_id = isset( $_POST['logged_in_user'] ) ? intval( $_POST['logged_in_user'] ) : 0;
		$group_leader = mld_get_group_leaders( $group_id );

		if( ! empty( $group_leader ) || is_array( $group_leader ) ) {
			$group_leader = array_column( $group_leader, 'ID' );
			$group_leader = serialize( $group_leader );
		} else {
			$group_leader = 0;
		}

		if( 'general' != $chat_type ) {
			$chat_user_id = 0;
		}

		$chatuser_name = mld_get_username( $chat_user_id );
		// $subject = sprintf(
		// 	__('New message from %s', 'myrtle-learning-dashboard'),
		// 	$sender_name
		// );
		$subject = __( 'Update from Myrtle Chatbox', 'myrtle-learning-dashboard' );
		$g_email = '';

		if( 'general' == $chat_type || 'group' == $chat_type ) {
			
			if( 'general' == $chat_type ) {
				$email = mld_get_user_email( $chat_user_id );
				$user_capability = get_user_meta( $chat_user_id, $wpdb->prefix.'capabilities', true );
				$array_keys = array_keys( $user_capability );
				if( in_array( 'student', $array_keys ) ) {
					$p_email = get_user_meta( $chat_user_id, 'mld_user_parent_email', true ); 
				} else {
					$p_email = get_user_meta( $user_id, 'mld_user_parent_email', true );
				}
			} else {

				$users_email = [];
				$parent_email = [];
				$group_users = mld_get_group_users( $group_id );
				if( ! empty( $group_users ) && is_array( $group_users ) ) {
					foreach( $group_users as $student_id ) {
						$users_email[] = mld_get_user_email( $student_id );
						$parent_email[] = get_user_meta( $student_id, 'mld_user_parent_email', true );
					}
				}

				$group_leader_email = [];
				$group_leaders = mld_get_group_leaders( $group_id );
				if( $group_leaders ) {
					foreach( $group_leaders as $group_leader_id ) {
						$group_leader_email[] = mld_get_user_email( $group_leader_id );
					}
				}

				$g_email = implode( ',', $group_leader_email );
				$email = implode( ',', $users_email );
				$p_email = implode( ',', $parent_email );
			}

			ob_start();
			require_once MLD_TEMPLATES_DIR . 'chat-email.php';
			$content = ob_get_contents();
			ob_get_clean();

			$headers = array( 'Content-Type: text/html; charset=UTF-8' );
			
			wp_mail( $email, $subject, $content, $headers);
			wp_mail( $p_email, $subject, $content, $headers);
			if( 'general' != $chat_type ) {
				wp_mail( $g_email, $subject, $content, $headers);
			}
		}

		self::instance()->mld_insert_user_message( $group_id, $group_leader, $chat_user_id, $chat_type, $user_id, $message );
		wp_die();
	}

	/**
	 * create a fuinction to insert user data
	 */
	private function mld_insert_user_message( $group_id, $group_leader_id, $user_id, $chat_type, $doer, $message ) {

		global $wpdb;

		$tabel = "{$wpdb->base_prefix}mld_chats";
		$wpdb->insert( $tabel, array(

			'group_id'          => $group_id,
			'group_leader_id'   => $group_leader_id,
			'user_id'           => $user_id,
			'chat_type'         => $chat_type,
			'doer'              => $doer,
			'message'           => stripslashes( $message ),
			'dates'         	=> time()
		) );
	}

	/**
	 * Get user data by user id
	 */
	public function get_user_name( $user_id ) {

		$user_f_name = get_user_by( 'id', $user_id );
		$user = get_user_meta( $user_id, 'first_name', true );

		if( $user ) {
			return ucwords( $user );
		} else {
			return ucwords( $user_f_name->display_name );
		}
	}

	/**
	 * Create chat shortcode
	 */
	public static function get_chat_page() {

		if( ! is_user_logged_in() ) {
			return false;
		}

		$chat_settings = self::instance()->settings;

		$blocked_users = isset( $chat_settings['block_users'] ) ? $chat_settings['block_users'] : [];

		$user_id = self::instance()->user_id;
		if( ! empty( $blocked_users ) && is_array( $blocked_users ) ) {

			if( in_array( $user_id, $blocked_users ) ) {
				return __( "You are not allowed to access this page", 'myrtle-learning-dashboard' );
			}
		}

        if ( learndash_is_group_leader_user( $user_id ) == false && !current_user_can( 'manage_options' ) ) {
            self::instance()->group_ids = mld_get_user_groups( $user_id );
        } else {

        	if( current_user_can( 'manage_options' ) ) {
        		self::instance()->group_ids = mld_get_groups_for_admin( $user_id );
        	} else {
        		self::instance()->group_ids = mld_get_groups_for_leader( $user_id );
        	}
        }

        $profile_url = MLD_ASSETS_URL . 'images/profile-img.png';

		ob_start();

		?>
		<div class="mld-group-options">
			<select name="mld_selected_group" class="mld-group-field">
				<option value=""><?php echo __( 'Select any group', 'myrtle-learning-dashboard' ); ?></option>
				<?php
                $group_ids = self::instance()->group_ids;
				if( ! empty( $group_ids ) && is_array( $group_ids ) ) {
					foreach( $group_ids as $group_id ) {
						?>
						<option value="<?php echo $group_id; ?>"><?php echo get_the_title( $group_id ); ?></option>
						<?php
					}
				}
				?>
			</select>
		</div>

		<div class="mld-chatbox-container">
			<div class="mld-chat-users">
				<div class="mld-chat-search">
					<div class="search-wrapper">
						<span class="dashicons dashicons-search"></span>
						<input type="text" placeholder="<?php echo __( 'Search Message or Name', 'myrtle-learning-dashboard' ); ?>" name="search" id="mld-search-input">
					</div>
				</div>
				<div class="mld-users">
					<table id="mld-user-table">
						<?php
						do {
							?>
                            <tr>
                                <td>
                                    <div class="mld-user-wrapper">
                                        <div class="mls-user-avatar">
                                            <img src="<?php echo $profile_url; ?>">
                                        </div>
                                        <div class="mld-user-message-wrapper">
                                            <div class="mld-usrename mld-dummy-user-name"><?php echo __( 'Username', 'myrtle-learning-dashboard' ); ?></div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
							<?php
						} while ( false );
						?>
					</table>
				</div>
			</div>
			<div class="mld-chatbox">
				<div class="mld-chat-starter">
					<?php echo __( 'Select Group to Start Chat', 'myrtle-learning-dashboard' ); ?>
				</div>
				<div class="mld-chat-box">
					<div class="mld-chat-header">
						<div class="mld-user-avatar">
							<img src="<?php echo $profile_url; ?>">
						</div>
						<div class="mld-user-name-wrapper">
							<div class="mld-usrename"></div>
						</div>
						<div class="mld-user-close">
							<span class="dashicons dashicons-no"></span>
						</div>
						<div class="mld-clear-both"></div>
                    </div>
					<div class="mld-chat-msgs" id="mld-chat-scrolldown">
					</div>
					<div class="mld-footer">
						<div class="mld-footer-inner-wrap">
							<div class="mld-message-box">
								<textarea rows="2" cols="30" placeholder="<?php echo __( 'Type your message here...', 'myrtle-learning-dashboard' ); ?>"></textarea>
							</div>
						</div>
					</div>
                    <input type="hidden" name="groupID">
                    <input type="hidden" name="loggedInUser">
                    <input type="hidden" name="chatUser">
                    <input type="hidden" name="chatType">
				</div>
			</div>
			<div class="mld-clear-both"></div>
		</div>
		<?php
		$content = ob_get_contents();
		ob_get_clean();
		self::instance()->return = $content;
	}

	/**
     * Return section for chat
     *
	 * @return string|void
	 */
	public static function get_chat_section() {

		global $wpdb;

		$table_name = $wpdb->prefix.'mld_chats';
		$user_id = self::instance()->user_id;

		if( current_user_can( 'manage_options' ) ) {
			$user_adminitrator_id = mld_get_groups_for_admin();
		} else {
			$user_adminitrator_id = mld_get_groups_for_leader( $user_id );
		}

		$user_group_id = mld_get_user_groups( $user_id );
		$groups = array_merge( $user_adminitrator_id, $user_group_id );
		
		$section_query = $wpdb->get_results( "
			SELECT message, dates, doer, chat_type, group_id, user_id
			FROM $table_name
			WHERE doer = $user_id OR user_id = $user_id
			OR group_id IN( '" . implode("', '", $groups) . "' )
			ORDER BY dates
			DESC
			LIMIT 4" );

		if( empty( $section_query ) || ! is_array( $section_query ) || is_null( $section_query ) ) {

			ob_start();
			?>
			<div class="mld-no-message-found-wrapper">
				<img class="mld-no-message-found" src="<?php echo MLD_ASSETS_URL.'images/mld-no-message-found.png' ?>">
			</div>
			<?php
			$content = ob_get_contents();
			ob_get_clean();
			self::instance()->return = $content;
			return;
		}

		ob_start();
		?>
		<div class="mld-users">
			<table id="mld-user-table">
			<?php
			foreach( $section_query as $query ) {

				$date = date( 'Y-m-d H:i:s', $query->dates );
				?>
					<tr class="mld-single-user-wrap mld-user-local-storage" data-doer="<?php echo $query->doer; ?>" data-group_id="<?php echo $query->group_id; ?>" data-type="<?php echo $query->chat_type; ?>" data-user_id="<?php echo $query->user_id; ?>" data-site="<?php echo site_url();?>/dashboard/chat/">
						<td>
							<div class="mld-user-wrapper">
								<div class="mls-user-avatar">
									<?php
										echo get_avatar( $query->doer ); 
									?>
								</div>
								<div class="mld-user-message-wrapper">
									<div class="mld-usrename"><?php echo self::$instance->get_user_name( $query->doer ); ?></div>
									<div class="mld-group-last-message">
										<?php echo substr( $query->message, 0, 22 ); ?>
									</div>
								</div>
								<div class="mld-user-last-login"><?php echo self::$instance->time_elapsed_string( $date ); ?></div>
							</div>
						</td>
					<tr>
				<?php
			}
			?>
			</table>
			<div class="mld-view-more chat-view-more">
				<a href="<?php echo site_url();?>/dashboard/chat/">
					<?php echo __( 'View more', 'myrtle-learning-dashboard' ) ?>
				</a>
            </div>
		</div>
		<?php
		$content = ob_get_contents();
		ob_get_clean();
		self::instance()->return = $content;
	}
}

Myrtle_Chat_Template::instance();
