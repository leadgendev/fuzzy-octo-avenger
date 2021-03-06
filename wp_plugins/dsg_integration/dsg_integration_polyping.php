<?php
/**
 * Plugin Name: DSG Integration - PolyPing
 * Version: 1.4.0
 * Author: Eric A Mohlenhoff <eamohl@leadsanddata.net>
 */

if ( class_exists( "GFForms" )) {
	GFForms::include_addon_framework();

	class DSG_Integration_PolyPing extends GFAddOn
	{
		protected $_version = "1.4.0";
		protected $_min_gravityforms_version = "1.8.1";
		protected $_slug = "dsg_integration_polyping";
		protected $_path = "dsg_integration/dsg_integration_polyping.php";
		protected $_full_path = __FILE__;
		protected $_title = "PolyPing";
		protected $_short_title = "PolyPing";

		public function __construct()
		{
			parent::__construct();
		}

		public function pre_init()
		{
			parent::pre_init();
		}

		public function init()
		{
			parent::init();
		}

		public function init_admin()
		{
			parent::init_admin();
			
			add_action( "gform_field_advanced_settings", array( $this, "choose_polyping_post_field" ), 10, 2 );
			add_action( "gform_editor_js", array( $this, "choose_polyping_post_field_js" ));
			add_filter( "gform_tooltips", array( $this, "choose_polyping_post_field_tooltip" ));
			add_action( "gform_entry_info", array( $this, "custom_entry_info" ), 10, 2 );
		}
		
		public function init_frontend()
		{
			parent::init_frontend();
			
			add_filter( "gform_confirmation", array( $this, "custom_confirmation" ), 20, 4 );
		}

		public function init_ajax()
		{
			parent::init_ajax();
		}
		
		public function post_to_polyping( $entry, $form )
		{
			$settings = $this->get_form_settings( $form );
			
			if ( $settings['polyping_integration_enabled'] != '1' ) {
				return;
			}
			
			$post_url = $settings['polyping_vertical_posting_url'];
			
			$post_body = array();
			$post_body['license'] = $settings['polyping_platform_license'];
			$post_body['password'] = $entry[$settings['polyping_publisher_password_field']];
			$post_body['sub_id'] = $entry[$settings['polyping_subid_source_field']];
			
			for ( $i = 0; $i < count( $form['fields'] ); $i++ ) {
				$field = $form['fields'][$i];
				
				if (( isset( $field['polyPingPostField'] )) && ( $field['polyPingPostField'] != "" )) {
					$post_body[$field['polyPingPostField']] = $entry[$field['id']];
				}
			}
			
			gform_update_meta( $entry['id'], 'polyping_post_url', $post_url );
			gform_update_meta( $entry['id'], 'polyping_post_body', $post_body );
			
			$request = new WP_Http();
			$response = $request->post( $post_url, array( 'body' => $post_body, 'timeout' => 600 ));
			
			gform_update_meta( $entry['id'], 'polyping_response_raw', $response );
			
			$xml = simplexml_load_string( $response['body'] );
			gform_update_meta( $entry['id'], 'polyping_response_success', (string) $xml->success );
			gform_update_meta( $entry['id'], 'polyping_response_message', (string) $xml->message );
			if ( isset( $xml->session_id )) {
				gform_update_meta( $entry['id'], 'polyping_response_session_id', (string) $xml->session_id );
			}
			if ( isset( $xml->price )) {
				gform_update_meta( $entry['id'], 'polyping_response_price', (string) $xml->price );
			}
			if ( isset( $xml->redirect )) {
				gform_update_meta( $entry['id'], 'polyping_response_redirect', (string) $xml->redirect );
			}
			if ( isset( $xml->bucket )) {
				gform_update_meta( $entry['id'], 'polyping_response_bucket', (string) $xml->bucket );
			}
		}
		
		public function custom_confirmation( $confirmation, $form, $entry, $is_ajax )
		{
			$settings = $this->get_form_settings( $form );
			
			$this->post_to_polyping( $entry, $form );
			$polyping_success_response = gform_get_meta( $entry['id'], 'polyping_response_success' );
			
			if ( $polyping_success_response == 'true' ) {
				$redirect = gform_get_meta( $entry['id'], 'polyping_response_redirect' );
				$confirmation = array( 'redirect' => $redirect );
				$polyping_response_bucket = gform_get_meta( $entry['id'], 'polyping_response_bucket' );
				
				if ( $settings['polyping_convert_on'] == 'bucket_filled' ) {
					if ( $polyping_response_bucket == '1' ) {
						file_get_contents( $settings['polyping_postback_url'] . $entry[$settings['polyping_transaction_id_field']] );
					}
				} else if ( $settings['polyping_convert_on'] == 'every_accept' ) {
					file_get_contents( $settings['polyping_postback_url'] . $entry[$settings['polyping_transaction_id_field']] );
				}
			}

			return $confirmation;
		}
		
		public function choose_polyping_post_field( $position, $form_id )
		{
			$form = $this->get_current_form();
			$settings = $this->get_form_settings( $form );
			
			if ( $settings['polyping_integration_enabled'] != '1' ) {
				return;
			}
			
			if ( $position == 50 ) {
				echo '<li class="polyping_post_field_setting field_setting">';
				echo '	<label for="polyping_post_field">PolyPing Post Field';
				gform_tooltip( "form_polyping_post_field" );
				echo '	</label>';
				echo '	<select id="polyping_post_field" onchange="SetFieldProperty( ' . "'polyPingPostField'" . ', jQuery( this ).val());">';
				echo '		<option value="">None Selected</option>';
				$polyping_post_fields = $this->get_all_polyping_post_fields();
				for ( $i = 0; $i < count( $polyping_post_fields ); $i++ ) {
					echo '		<option value="' . $polyping_post_fields[$i] . '">' . $polyping_post_fields[$i] . '</option>';
				}
				echo '	</select>';
				echo '</li>';
			}
		}
		
		public function choose_polyping_post_field_js ()
		{
		?>
		<script type="text/javascript">
		//<![CDATA[
			fieldSettings['text'] += ', .polyping_post_field_setting';
			fieldSettings['date'] += ', .polyping_post_field_setting';
			fieldSettings['email'] += ', .polyping_post_field_setting';
			fieldSettings['hidden'] += ', .polyping_post_field_setting';
			fieldSettings['number'] += ', .polyping_post_field_setting';
			fieldSettings['phone'] += ', .polyping_post_field_setting';
			fieldSettings['radio'] += ', .polyping_post_field_setting';
			fieldSettings['select'] += ', .polyping_post_field_setting';
			fieldSettings['textarea'] += ', .polyping_post_field_setting';
			fieldSettings['time'] += ', .polyping_post_field_setting';
			fieldSettings['website'] += ', .polyping_post_field_setting';
			
			jQuery( document ).bind( 'gform_load_field_settings',
				function ( event, field, form ) {
					jQuery( '#polyping_post_field' ).val( field['polyPingPostField'] );
				});
		//]]>
		</script>
		<?php
		}
		
		public function choose_polyping_post_field_tooltip ( $tooltips )
		{
			$tooltips['form_polyping_post_field'] = "<h6>PolyPing</h6>Select mapping onto PolyPing vertical post field";
			return $tooltips;
		}
		
		protected function get_all_polyping_post_fields()
		{
			$form = $this->get_current_form();
			$settings = $this->get_form_settings( $form );
			
			$polyping_post_fields = explode( ',', $settings['polyping_vertical_fields'] );
			
			$polyping_post_fields[] = 'test';
			
			return $polyping_post_fields;
		}

		public function form_settings_fields( $form )
		{
			$field_choices = $this->get_all_field_choices( $form );
			
			$config = array(
				array(
					"title" => "General Settings"
					,"fields" => array(
						array(
							"label" => "PolyPing Integration"
							,"type" => "checkbox"
							,"name" => "polyping_integration_enabled"
							,"tooltip" => "Checking this box will enable integration with the PolyPing lead distribution platform."
							,"choices" => array(
								array(
									"label" => "Enabled"
									,"name" => "polyping_integration_enabled"
								)
							)
						)
					)
				)
				,array(
					"title" => "Credentials"
					,"fields" => array(
						array(
							"label" => "Publisher Password Field"
							,"type" => "select"
							,"name" => "polyping_publisher_password_field"
							,"tooltip" => "If choosing to use a dynamically obtained publisher password, select the form field providing it here."
							,"choices" => $field_choices
						)
						,array(
							"label" => "Sub ID Source Field"
							,"type" => "select"
							,"name" => "polyping_subid_source_field"
							,"tooltip" => "Select the form field to use for affiliate subinformation identification."
							,"choices" => $field_choices
						)
						,array(
							"label" => "Transaction ID Field"
							,"type" => "select"
							,"name" => "polyping_transaction_id_field"
							,"tooltip" => "Select the form field to use for the transaction ID."
							,"choices" => $field_choices
						)
					)
				)
				,array(
					"title" => "Vertical Information"
					,"fields" => array(
						array(
							"label" => "Post Doc URL"
							,"type" => "text"
							,"name" => "polyping_post_doc_url"
							,"tooltip" => "Enter the URL of the PolyPing posting instructions for the target vertical."
							,"class" => "large"
						)
						,array(
							"label" => "Vertical Name"
							,"type" => "text"
							,"name" => "polyping_vertical_name"
							,"tooltip" => "Dynamically populated after saving post doc URL setting."
							,"class" => "medium"
						)
						,array(
							"label" => "Posting URL"
							,"type" => "text"
							,"name" => "polyping_vertical_posting_url"
							,"tooltip" => "Dynamically populated after saving post doc URL setting."
							,"class" => "large"
						)
						,array(
							"label" => "Platform License"
							,"type" => "text"
							,"name" => "polyping_platform_license"
							,"tooltip" => "Dynamically populated after saving post doc URL setting."
							,"class" => "small"
						)
						,array(
							"label" => "Vertical Fields"
							,"type" => "textarea"
							,"name" => "polyping_vertical_fields"
							,"tooltip" => "Comma-separated list of vertical fields. Dynamically populated after saving post doc URL setting."
							,"class" => "large"
						)
					)
				)
				,array(
					"title" => "Conversion Tracking"
					,"fields" => array(
						array(
							"label" => "Convert On"
							,"type" => "radio"
							,"name" => "polyping_convert_on"
							,"tooltip" => "Choose the criteria on which to fire a conversion here."
							,"choices" => array(
								array(
									"label" => "Every Accept"
									,"value" => "every_accept"
								)
								,array(
									"label" => "Bucket Filled"
									,"value" => "bucket_filled"
								)
								,array(
									"label" => "Do Not Convert"
									,"value" => "do_not_convert"
								)
							)
						)
						,array(
							"label" => "Server Postback URL"
							,"type" => "text"
							,"name" => "polyping_postback_url"
							,"tooltip" => "Set the server postback URL to be used when firing a conversion here. The transaction ID will be automatically appended when used. Example: http://link.go2oursite.net/GP7X?transaction_id="
							,"class" => "large"
						)
					)
				)
			);

			return $config;
		}
		
		protected function get_all_field_choices( $form )
		{
			$field_choices = array(
				array(
					"label" => "None Selected"
					,"value" => ""
				)
			);
			
			for ( $i = 0; $i < count( $form["fields"] ); $i++ ) {
				$field_choices[] = array(
					"label" => $form["fields"][$i]["label"]
					,"value" => $form["fields"][$i]["id"]
				);
			}
			
			return $field_choices;
		}
		
		protected function get_posted_settings()
		{
			$settings = parent::get_posted_settings();
			
			if ( empty( $settings )) {
				return $settings;
			}
			
			$url = $settings["polyping_post_doc_url"];
			if ( $url == "" ) {
				$settings['polyping_vertical_name'] = "";
				$settings['polyping_vertical_posting_url'] = "";
				$settings['polyping_platform_license'] = "";
				$settings['polyping_vertical_fields'] = "";
				
				return $settings;
			}
			
			$post_doc = file_get_contents( $url );
			
			$matches = array();
			$pattern = '/Name:<\/td>[\s]*<td><strong>([^<]*)<\/strong><\/td>/';
			preg_match( $pattern, $post_doc, $matches );
			$settings["polyping_vertical_name"] = $matches[1];
			
			$matches = array();
			$pattern = '/Live URL:<\/td>[\s]*<td>([^<]*)<\/td>/';
			preg_match( $pattern, $post_doc, $matches );
			$settings["polyping_vertical_posting_url"] = $matches[1];
			
			$matches = array();
			$pattern = '/license<\/td>[\s]*<td>([^<]*)<\/td>/';
			preg_match( $pattern, $post_doc, $matches );
			$settings["polyping_platform_license"] = $matches[1];
			
			$matches = array();
			$pattern = '/password=test<br>&sub_id=123<br>&([^\/]*)<\/td>/';
			preg_match( $pattern, $post_doc, $matches );
			
			$vertical_fields = $matches[1];
			$vertical_fields = preg_replace( '/=<br>/', '', $vertical_fields );
			$vertical_fields = preg_replace( '/&/', ',', $vertical_fields );
			$settings["polyping_vertical_fields"] = $vertical_fields;
			
			//print_r( $settings );
			
			return $settings;
		}
		
		public function custom_entry_info ( $form_id, $entry )
		{
			$id = $entry['id'];
			$post_url = gform_get_meta( $id, 'polyping_post_url' );
			$post_body = gform_get_meta( $id, 'polyping_post_body' );
			$response_raw = gform_get_meta( $id, 'polyping_response_raw' );
			$response_success = gform_get_meta( $id, 'polyping_response_success' );
			$response_message = gform_get_meta( $id, 'polyping_response_message' );
			$response_session_id = gform_get_meta( $id, 'polyping_response_session_id' );
			$response_price = gform_get_meta( $id, 'polyping_response_price' );
			$response_redirect = gform_get_meta( $id, 'polyping_response_redirect' );
			$response_bucket = gform_get_meta( $id, 'polyping_response_bucket' );
			
			echo "<strong>PolyPing Meta Info</strong><br /><br />\n";
			echo "Post URL: $post_url <br /><br />\n";
			echo "Post Body: ";
			print_r( $post_body );
			echo "<br /><br />\n";
			echo "Response Raw: ";
			print_r( $response_raw );
			echo "<br /><br />\n";
			echo "Response Success: $response_success <br /><br />\n";
			echo "Response Message: $response_message <br /><br />\n";
			echo "Response Session ID: $response_session_id <br /><br />\n";
			echo "Response Price: $response_price <br /><br />\n";
			echo "Response Redirect: $response_redirect <br /><br />\n";
			echo "Response Bucket: $response_bucket <br /><br />\n";
		}
	}

	new DSG_Integration_PolyPing();
}

?>
