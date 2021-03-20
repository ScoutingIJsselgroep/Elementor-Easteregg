<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class EmailAsEASTEREGG extends \ElementorPro\Modules\Forms\Classes\Action_Base {

	public function get_name() {
		return 'emailaseasteregg';
	}

	public function get_label() {
		return __( 'Email As EASTEREGG', 'elementor-pro' );
	}

	public function register_settings_section( $widget ) {
		$widget->start_controls_section(
			$this->get_control_id( 'section_emailaseasteregg' ),
			[
				'label' => $this->get_label(),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
				'condition' => [
					'submit_actions' => $this->get_name(),
				],
			]
		);

		$widget->add_control(
			$this->get_control_id( 'emailaseasteregg_to' ),
			[
				'label' => __( 'To', 'elementor-pro' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => get_option( 'admin_email' ),
				'placeholder' => get_option( 'admin_email' ),
				'label_block' => true,
				'title' => __( 'Separate emails with commas', 'elementor-pro' ),
				'render_type' => 'none',
			]
		);

		/* translators: %s: Site title. */
		$default_message = sprintf( __( 'New message from "%s"', 'elementor-pro' ), get_option( 'blogname' ) );

		$widget->add_control(
			$this->get_control_id( 'emailaseasteregg_subject' ),
			[
				'label' => __( 'Subject', 'elementor-pro' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => $default_message,
				'placeholder' => $default_message,
				'label_block' => true,
				'render_type' => 'none',
			]
		);

		$widget->add_control(
			$this->get_control_id( 'emailaseasteregg_content' ),
			[
				'label' => __( 'Message', 'elementor-pro' ),
				'type' => \Elementor\Controls_Manager::TEXTAREA,
				'default' => '[all-fields]',
				'placeholder' => '[all-fields]',
				'description' => __( 'By default, all form fields are sent via shortcode: <code>[all-fields]</code>. Want to customize sent fields? Copy the shortcode that appears inside the field and paste it above.', 'elementor-pro' ),
				'label_block' => true,
				'render_type' => 'none',
			]
		);

		$site_domain = \ElementorPro\Core\Utils::get_site_domain();

		$widget->add_control(
			$this->get_control_id( 'emailaseasteregg_from' ),
			[
				'label' => __( 'From Email', 'elementor-pro' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => 'email@' . $site_domain,
				'render_type' => 'none',
			]
		);

		$widget->add_control(
			$this->get_control_id( 'emailaseasteregg_from_name' ),
			[
				'label' => __( 'From Name', 'elementor-pro' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => get_bloginfo( 'name' ),
				'render_type' => 'none',
			]
		);

		$widget->add_control(
			$this->get_control_id( 'emailaseasteregg_reply_to' ),
			[
				'label' => __( 'Reply-To', 'elementor-pro' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'' => '',
				],
				'render_type' => 'none',
			]
		);

		$widget->add_control(
			$this->get_control_id( 'emailaseasteregg_to_cc' ),
			[
				'label' => __( 'Cc', 'elementor-pro' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => '',
				'title' => __( 'Separate emails with commas', 'elementor-pro' ),
				'render_type' => 'none',
			]
		);

		$widget->add_control(
			$this->get_control_id( 'emailaseasteregg_to_bcc' ),
			[
				'label' => __( 'Bcc', 'elementor-pro' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => '',
				'title' => __( 'Separate emails with commas', 'elementor-pro' ),
				'render_type' => 'none',
			]
		);

		$widget->add_control(
			$this->get_control_id( 'emailaseasteregg_form_metadata' ),
			[
				'label' => __( 'Meta Data', 'elementor-pro' ),
				'type' => \Elementor\Controls_Manager::SELECT2,
				'multiple' => true,
				'label_block' => true,
				'separator' => 'before',
				'default' => [
					'date',
					'time',
					'page_url',
					'user_agent',
					'remote_ip',
					'credit',
				],
				'options' => [
					'date' => __( 'Date', 'elementor-pro' ),
					'time' => __( 'Time', 'elementor-pro' ),
					'page_url' => __( 'Page URL', 'elementor-pro' ),
					'user_agent' => __( 'User Agent', 'elementor-pro' ),
					'remote_ip' => __( 'Remote IP', 'elementor-pro' ),
					'credit' => __( 'Credit', 'elementor-pro' ),
				],
				'render_type' => 'none',
			]
		);

		$widget->add_control(
			$this->get_control_id( 'emailaseasteregg_content_type' ),
			[
				'label' => __( 'Send As', 'elementor-pro' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'html',
				'render_type' => 'none',
				'options' => [
					'html' => __( 'HTML', 'elementor-pro' ),
					'plain' => __( 'Plain', 'elementor-pro' ),
					'easteregg' => __( 'EASTEREGG', 'elementor-pro' ),
				],
			]
		);

		$widget->end_controls_section();
	}

	public function on_export( $element ) {
		$controls_to_unset = [
			'emailaseasteregg_to',
			'emailaseasteregg_from',
			'emailaseasteregg_from_name',
			'emailaseasteregg_subject',
			'emailaseasteregg_reply_to',
			'emailaseasteregg_to_cc',
			'emailaseasteregg_to_bcc',
		];

		foreach ( $controls_to_unset as $base_id ) {
			$control_id = $this->get_control_id( $base_id );
			unset( $element['settings'][ $control_id ] );
		}

		return $element;
	}

	private function post_easteregg ( $name, $email ) {
		$post = [
			'name' => $name,
			'email' => $email,
		];
		
		$ch = curl_init('https://ei.ijssel.group/clients/add');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
		
		// execute!
		$response = curl_exec($ch);
		
		// close the connection, release resources used
		curl_close($ch);
		
		// do anything you want with your response
		$response = json_decode($response);

		return $response;
	}

	public function run( $record, $ajax_handler ) {
		$settings = $record->get( 'form_settings' );
		$sendeasteregg = false;
		$send_html = false;
		$attachments = array();
		$content_type_email = $settings[ $this->get_control_id( 'emailaseasteregg_content_type' ) ];
		
		if($content_type_email == 'easteregg'){
			
			$sendeasteregg = true;
			$send_html = false;
			
		}elseif($content_type_email == 'html'){
			
			$sendeasteregg = false;
			$send_html = true;
			
		}
		
		$line_break = ( $send_html || $sendeasteregg ) ? '<br>' : "\n";

		$fields = [
			'emailaseasteregg_to' => get_option( 'admin_email' ),
			/* translators: %s: Site title. */
			'emailaseasteregg_subject' => sprintf( __( 'New message from "%s"', 'elementor-pro' ), get_bloginfo( 'name' ) ),
			'emailaseasteregg_content' => '[all-fields]',
			'emailaseasteregg_from_name' => get_bloginfo( 'name' ),
			'emailaseasteregg_from' => get_bloginfo( 'admin_email' ),
			'emailaseasteregg_reply_to' => 'noreply@' . \ElementorPro\Core\Utils::get_site_domain(),
			'emailaseasteregg_to_cc' => '',
			'emailaseasteregg_to_bcc' => '',
		];

		foreach ( $fields as $key => $default ) {
			$setting = trim( $settings[ $this->get_control_id( $key ) ] );
			$setting = $record->replace_setting_shortcodes( $setting );
			if ( ! empty( $setting ) ) {
				$fields[ $key ] = $setting;
			}
		}

		$email_reply_to = '';

		if ( ! empty( $fields['emailaseasteregg_reply_to'] ) ) {
			$sent_data = $record->get( 'sent_data' );
			foreach ( $record->get( 'fields' ) as $field_index => $field ) {
				if ( $field_index === $fields['emailaseasteregg_reply_to'] && ! empty( $sent_data[ $field_index ] ) && is_email( $sent_data[ $field_index ] ) ) {
					$email_reply_to = $sent_data[ $field_index ];
					break;
				}
			}
		}

		// POST to https://ei.ijssel.group/clients/add and retrieve JSON
		$response = $this->post_easteregg("test", $fields["emailaseasteregg_to"]);
		$url = "https://ei.ijssel.group/start?code=".$response['code'];

		$url_tag = "<a href=\"".$url."\">Link naar de App</a>";

		// Replace [easteregg] by link
		$fields['emailaseasteregg_content'] = str_replace("[easteregg]", $url_tag.$url, $fields['emailaseasteregg_content']);
		
		
		$fields['emailaseasteregg_content'] = $this->replace_content_shortcodes( $fields['emailaseasteregg_content'], $record, $line_break );
		$fields['emailaseasteregg_content'] .= var_export($response, true);

		$email_meta = '';

		$form_metadata_settings = $settings[ $this->get_control_id( 'emailaseasteregg_form_metadata' ) ];

		foreach ( $record->get( 'meta' ) as $id => $field ) {
			if ( in_array( $id, $form_metadata_settings ) ) {
				$email_meta .= $this->field_formatted( $field ) . $line_break;
			}
		}

		if ( ! empty( $email_meta ) ) {
			$fields['emailaseasteregg_content'] .= $line_break . '---' . $line_break . $line_break . $email_meta;
		}

		$headers = sprintf( 'From: %s <%s>' . "\r\n", $fields['emailaseasteregg_from_name'], $fields['emailaseasteregg_from'] );
		$headers .= sprintf( 'Reply-To: %s' . "\r\n", $email_reply_to );

		if ( $send_html ) {
			$headers .= 'Content-Type: text/html; charset=UTF-8' . "\r\n";
		}
		
		if($sendeasteregg){
			
			//require_once(ELEMENTOR_FORM_EASTEREGG_PATH.'lib/feasteregg/html2easteregg.php');
			require_once(ELEMENTOR_FORM_EASTEREGG_PATH.'lib/measteregg/vendor/autoload.php');
			
			$separator = md5(time());
			$eol = PHP_EOL;
			$filename = 'form_submitted_'.$separator.'.easteregg';
			$file_path =  ELEMENTOR_FORM_EASTEREGG_UPLOAD_PATH .'/'. $filename;
			
			//$easteregg=new EASTEREGG_HTML();
			//$measteregg = new mEASTEREGG('c');
			$measteregg = new mEASTEREGG('','A4','','dejavusans',32,25,27,25,16,13);

			$measteregg->SetDirectionality('rtl');
			$measteregg->mirrorMargins = true;
			$measteregg->SetDisplayMode('fullpage','two');

			$measteregg->autoLangToFont = true;

			$measteregg->defaultPageNumStyle = 'arabic-indic';
			
			//$measteregg->SetFont('dejavusans','',14);
			
			$measteregg->WriteHTML($fields['emailaseasteregg_content']);
			$measteregg->Output($file_path,'F');
			//$easteregg->AddFont('DejaVuSans','','DejaVuSans.php');
			//$easteregg->AddPage();
			//$easteregg->SetFont('DejaVuSans','',14);
			//$easteregg->SetFont('Arial','',12);
			//$easteregg->AddPage();
			//$easteregg->WriteHTML($fields['emailaseasteregg_content']);
			//$easteregg->Output('F',$file_path,true);
			$headers .= 'Content-Type: text/html; charset=UTF-8' . "\r\n";
			$attachments = array($file_path);
		}

		$cc_header = '';
		if ( ! empty( $fields['emailaseasteregg_to_cc'] ) ) {
			$cc_header = 'Cc: ' . $fields['emailaseasteregg_to_cc'] . "\r\n";
		}

		/**
		 * Email headers.
		 *
		 * Filters the additional headers sent when the form send an email.
		 *
		 * @since 1.0.0
		 *
		 * @param string|array $headers Additional headers.
		 */
		$headers = apply_filters( 'elementor_pro/forms/wp_mail_headers', $headers );

		/**
		 * Email content.
		 *
		 * Filters the content of the email sent by the form.
		 *
		 * @since 1.0.0
		 *
		 * @param string $email_content Email content.
		 */
		$fields['emailaseasteregg_content'] = apply_filters( 'elementor_pro/forms/wp_mail_message', $fields['emailaseasteregg_content'] );
		
		if($sendeasteregg && count($attachments) > 0){
			
			$email_sent = wp_mail( $fields['emailaseasteregg_to'], $fields['emailaseasteregg_subject'], $fields['emailaseasteregg_content'], $headers . $cc_header , $attachments);
			
		}else{
			
			$email_sent = wp_mail( $fields['emailaseasteregg_to'], $fields['emailaseasteregg_subject'], $fields['emailaseasteregg_content'], $headers . $cc_header );
		}

		if ( ! empty( $fields['emailaseasteregg_to_bcc'] ) ) {
			$bcc_emails = explode( ',', $fields['emailaseasteregg_to_bcc'] );
			foreach ( $bcc_emails as $bcc_email ) {
				
				if($sendeasteregg && count($attachments) > 0){
			
					wp_mail( trim( $bcc_email ), $fields['emailaseasteregg_subject'], $fields['emailaseasteregg_content'], $headers ,$attachments);
					
				}else{
					wp_mail( trim( $bcc_email ), $fields['emailaseasteregg_subject'], $fields['emailaseasteregg_content'], $headers );
				}
			}
		}

		/**
		 * Elementor form mail sent.
		 *
		 * Fires when an email was sent successfully.
		 *
		 * @since 1.0.0
		 *
		 * @param array       $settings Form settings.
		 * @param Form_Record $record   An instance of the form record.
		 */
		do_action( 'elementor_pro/forms/mail_sent', $settings, $record );

		if ( ! $email_sent ) {
			$ajax_handler->add_error_message( \ElementorPro\Modules\Forms\Classes\Ajax_Handler::get_default_message( \ElementorPro\Modules\Forms\Classes\Ajax_Handler::SERVER_ERROR, $settings ) );
		}
	}

	private function field_formatted( $field ) {
		$formatted = '';
		if ( ! empty( $field['title'] ) ) {
			$formatted = sprintf( '%s: %s', $field['title'], $field['value'] );
		} elseif ( ! empty( $field['value'] ) ) {
			$formatted = sprintf( '%s', $field['value'] );
		}

		return $formatted;
	}

	// Allow overwrite the control_id with a prefix, @see Email2
	protected function get_control_id( $control_id ) {
		return $control_id;
	}

	/**
	 * @param string      $email_content
	 * @param Form_Record $record
	 *
	 * @return string
	 */
	private function replace_content_shortcodes( $email_content, $record, $line_break ) {
		$email_content = do_shortcode( $email_content );
		$all_fields_shortcode = '[all-fields]';

		if ( false !== strpos( $email_content, $all_fields_shortcode ) ) {
			$text = '';
			foreach ( $record->get( 'fields' ) as $field ) {
				$formatted = $this->field_formatted( $field );
				if ( ( 'textarea' === $field['type'] ) && ( '<br>' === $line_break ) ) {
					$formatted = str_replace( [ "\r\n", "\n", "\r" ], '<br />', $formatted );
				}
				$text .= $formatted . $line_break;
			}

			$email_content = str_replace( $all_fields_shortcode, $text, $email_content );

		}

		return $email_content;
	}
}
