<?php
/**
 * Plugin Name: DSG Integration - PolyPing
 * Version: 1.2.0
 * Author: Eric A Mohlenhoff <eamohl@leadsanddata.net>
 */

if ( class_exists( "GFForms" )) {
	GFForms::include_addon_framework();

	class DSG_Integration_PolyPing extends GFAddOn
	{
		protected $_version = "1.2.0";
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
			
			return $polyping_post_fields;
		}

		public function init_frontend()
		{
			parent::init_frontend();
		}

		public function init_ajax()
		{
			parent::init_ajax();
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
							,"name" => "polyping_dynamic_publisher_password"
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
					"title" => "Response Handling"
					,"fields" => array(
						array(
							"label" => "Confirmation Settings"
							,"type" => "checkbox"
							,"name" => "polyping_override_confirmation"
							,"tooltip" => "Check this setting if the existing confirmation settings for the form are to be overridden. The below fields for specifying redirect URLs specific to each possible PolyPing response will be used instead."
							,"choices" => array(
								array(
									"label" => "Override"
									,"name" => "polyping_override_confirmation"
								)
							)
						)
						,array(
							"label" => "Accept URL"
							,"type" => "text"
							,"name" => "polyping_accept_url"
							,"tooltip" => "The URL to redirect to after form submission on an accept response from PolyPing."
							,"class" => "large"
						)
						,array(
							"label" => "Reject URL"
							,"type" => "text"
							,"name" => "polyping_reject_url"
							,"tooltip" => "The URL to redirect to after form submission on a reject response from PolyPing."
							,"class" => "large"
						)
						,array(
							"label" => "Error URL"
							,"type" => "text"
							,"name" => "polyping_error_url"
							,"tooltip" => "The URL to redirect to after form submission on an error response from PolyPing."
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
							)
						)
						,array(
							"label" => "Server Postback URL"
							,"type" => "text"
							,"name" => "polyping_postback_url"
							,"tooltip" => "Set the server postback URL to be used when firing a conversion here."
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
					,"value" => "$i"
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
			
			//print_r( $settings );
			
			$url = $settings["polyping_post_doc_url"];
			if ( $url == "" ) {
				$settings['polyping_vertical_name'] = "";
				$settings['polyping_vertical_posting_url'] = "";
				$settings['polyping_platform_license'] = "";
				$settings['polyping_vertical_fields'] = "";
				//print_r( $settings );
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
	}

	new DSG_Integration_PolyPing();
}

?>
