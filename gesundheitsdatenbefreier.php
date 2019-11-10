<?php
/*
== gesundheitsdatenbefreier ==
Plugin Name: gesundheitsdatenbefreier
Description: Ein WordPress-Plugin, mit dem Versicherte ihre Daten dank DSGVO befreien können
Version: 1.3
Author: Phil Lehmann, AK Vorratsdatenspeicherung
Author URI: http://www.vorratsdatenspeicherung.de/
Contributors: philrykoff
Tested up to: 5.2.4
Requires at least: 5.2.4
Requires PHP: 7.1
Stable tag: trunk
*/

defined('ABSPATH') or die('');

// Main entry point (shortcode)

function gesundheitsdatenbefreier_router() {
	$page = 'infos';
	$pages = array('infos', 'form', 'result', 'good-bye');
	if(isset($_GET['gp'])) {
		if(in_array($_GET['gp'], $pages)) {
			$page = $_GET['gp'];			
		} else {
			wp_die('Seite nicht gefunden.');
		}
	}
	
	require_once __DIR__ . '/pages/' . $page . '.php';
}

add_shortcode('gesundheitsdatenbefreier', 'gesundheitsdatenbefreier_router');

// Functions to include

function gesundheitsdatenbefreier_get_mail_text($params) {
	if(!isset($params['gp_name']) || !isset($params['gp_strasse']) || !isset($params['gp_plz']) || !isset($params['gp_ort']) || !isset($params['gp_kasse']) || !isset($params['gp_nummer'])) {
		wp_die('Einer der Parameter "gp_name", "gp_strasse", "gp_plz", "gp_ort", "gp_kasse", "gp_nummer" fehlt.');
	}
	if($params['gp_kasse'] == 'other' && !isset($params['gp_kk_name'])) {
		wp_die('Der Parameter "gp_kk_name" fehlt.');
	}
	$from = array('{{name}}', '{{strasse}}', '{{plz}}', '{{ort}}', '{{kasse}}', '{{versichertennummer}}');
	$to = array(esc_html($params['gp_name']), esc_html($params['gp_strasse']), esc_html($params['gp_plz']), esc_html($params['gp_ort']), esc_html($params['gp_kasse'] !== 'other' ? $params['gp_kasse'] : $params['gp_kk_name']), esc_html($params['gp_nummer']));
	return str_replace($from, $to, get_option('gesundheitsdatenbefreier_mail_text'));
}

// Include CSS and script files

function gesundheitsdatenbefreier_router_enqueue_scripts() {
	
	wp_enqueue_script('jquery');
	wp_enqueue_script('select2-script', plugin_dir_url(__FILE__) . 'assets/select2.min.js', array('jquery'));
	wp_enqueue_style('select2-style', plugin_dir_url(__FILE__) . 'assets/select2.min.css');
	wp_enqueue_script('gesundheitsdatenbefreier-script', plugin_dir_url(__FILE__) . 'assets/scripts.js', array('jquery'));	
	wp_enqueue_style('gesundheitsdatenbefreier-style', plugin_dir_url(__FILE__) . 'assets/style.css');
	
	if(isset($_GET['gp_kasse'])) {
		wp_add_inline_script('gesundheitsdatenbefreier-script', 'jQuery(document).ready(function($){ $(\'.select2.krankenkasse\').val(\'' . esc_js($_GET['gp_kasse']) . '\').trigger(\'change\'); if(\'' . esc_js($_GET['gp_kasse']) . '\' == \'other\') { $(\'.other.fields\').slideDown("slow"); } });');
	}
}

add_action('wp_enqueue_scripts', 'gesundheitsdatenbefreier_router_enqueue_scripts');

// Provide PDF as API, so that WordPress theme is not sent and corrupting the PDF

function gesundheitsdatenbefreier_api() {
	require_once __DIR__ . '/includes/pdf.php';
	register_rest_route('gesundheitsdatenbefreier', 'pdf', array( 
		'methods' => 'POST',
		'callback' => 'gesundheitsdatenbefreier_pdf',
	));
}

add_action('rest_api_init','gesundheitsdatenbefreier_api');

// Start the session so we can remember users that already made a data information request

function gesundheitsdatenbefreier_init() {
	if(!session_id()) {
		session_start();
	}
}
add_action('init', 'gesundheitsdatenbefreier_init');

// Admin section

function gesundheitsdatenbefreier_settings() {
    register_setting('gesundheitsdatenbefreier_options_section', 'gesundheitsdatenbefreier_threshold', array(
		'type' => 'integer',
		'description' => 'Die Anzahl der Abfragen, ab der der Zähler auf der Info-Seite dargestellt werden soll.',
		'default' => 0,
	));
	
    register_setting('gesundheitsdatenbefreier_options_section', 'gesundheitsdatenbefreier_info_text', array(
		'type' => 'string',
		'description' => 'Der Text, der auf der Info-Seite dargestellt werden soll.',
		'default' => 'Lorem Ipsum.',
	));
    register_setting('gesundheitsdatenbefreier_options_section', 'gesundheitsdatenbefreier_mail_text', array(
		'type' => 'string',
		'description' => 'Der Text, der auf der Ergebnis-Seite, in der PDF und der Mail verwendet werden soll.',
		'default' => 'Lorem Ipsum.',
	));
    register_setting('gesundheitsdatenbefreier_options_section', 'gesundheitsdatenbefreier_good_bye_text', array(
		'type' => 'string',
		'description' => 'Der Text, der auf der letzten Seite dargestellt werden soll.',
		'default' => 'Lorem Ipsum.',
	));
	
	add_settings_section('gesundheitsdatenbefreier_options_section', 'Gesundheitsdatenbefreier', 'gesundheitsdatenbefreier_options_section', 'gesundheitsdatenbefreier_options');
	add_settings_field('gesundheitsdatenbefreier_counter', 'Stand des Abfragen-Zählers', 'gesundheitsdatenbefreier_counter_render', 'gesundheitsdatenbefreier_options', 'gesundheitsdatenbefreier_options_section');
	add_settings_field('gesundheitsdatenbefreier_threshold', 'Schwellwert für die Anzeige des Abfragen-Zählers', 'gesundheitsdatenbefreier_threshold_render', 'gesundheitsdatenbefreier_options', 'gesundheitsdatenbefreier_options_section');
	add_settings_field('gesundheitsdatenbefreier_info_text', 'Text der Info-Seite', 'gesundheitsdatenbefreier_info_text_render', 'gesundheitsdatenbefreier_options', 'gesundheitsdatenbefreier_options_section');
	add_settings_field('gesundheitsdatenbefreier_mail_text', 'Text der versendeten Nachricht', 'gesundheitsdatenbefreier_mail_text_render', 'gesundheitsdatenbefreier_options', 'gesundheitsdatenbefreier_options_section');
	add_settings_field('gesundheitsdatenbefreier_good_bye_text', 'Text der letzten Seite', 'gesundheitsdatenbefreier_good_bye_text_render', 'gesundheitsdatenbefreier_options', 'gesundheitsdatenbefreier_options_section');
} 
add_action('admin_init', 'gesundheitsdatenbefreier_settings');

function gesundheitsdatenbefreier_add_admin_menu() {
	add_options_page('Gesundheitsdatenbefreier', 'Gesundheitsdatenbefreier', 'manage_options', 'gesundheitsdatenbefreier_options', 'gesundheitsdatenbefreier_options_page');
}
add_action('admin_menu', 'gesundheitsdatenbefreier_add_admin_menu');

function gesundheitsdatenbefreier_options_page() {
?><div>
	<h1>Einstellungen › Gesundheitsdatenbefreier</h1>
	<form action="options.php" method="post">
		<?php settings_fields('gesundheitsdatenbefreier_options_section'); ?>
		<?php do_settings_sections('gesundheitsdatenbefreier_options'); ?>
	 
		<input name="Submit" type="submit" value="<?php esc_attr_e('Save Changes'); ?>" />
	</form>
</div><?php
}

function gesundheitsdatenbefreier_options_section() {}
 
function gesundheitsdatenbefreier_counter_render() {
	echo '<code>' . get_option('gesundheitsdatenbefreier_counter', 0) . '</code>';
}

function gesundheitsdatenbefreier_threshold_render() {
	echo '<input name="gesundheitsdatenbefreier_threshold" id="gesundheitsdatenbefreier_threshold" type="text" value="' . get_option('gesundheitsdatenbefreier_threshold', 0) . '"  />';
}

function gesundheitsdatenbefreier_info_text_render() {
    wp_editor(get_option('gesundheitsdatenbefreier_info_text'), 'gesundheitsdatenbefreier_info_text', array( 
        'textarea_name' => 'gesundheitsdatenbefreier_info_text',
        'media_buttons' => false,
    ));
}

function gesundheitsdatenbefreier_mail_text_render() {
    wp_editor(get_option('gesundheitsdatenbefreier_mail_text'), 'gesundheitsdatenbefreier_mail_text', array( 
        'textarea_name' => 'gesundheitsdatenbefreier_mail_text',
        'media_buttons' => false,
    ));
?>

<p>
	Verfügbare Platzhalter: 
	<ul>
		<li><code>{{name}}</code> für den kompletten Namen des Versicherten</li>
		<li><code>{{strasse}}</code> für die Straße des Versicherten</li>
		<li><code>{{plz}}</code> für die Postleitzahl des Versicherten</li>
		<li><code>{{ort}}</code> für den Ort des Versicherten</li>
		<li><code>{{kasse}}</code> für die Krankenkasse des Versicherten</li>
		<li><code>{{versichertennummer}}</code> für die Versichertennummer</li>
	</ul>
</p>

<?php
}

function gesundheitsdatenbefreier_good_bye_text_render() {
    wp_editor(get_option('gesundheitsdatenbefreier_good_bye_text'), 'gesundheitsdatenbefreier_good_bye_text', array( 
        'textarea_name' => 'gesundheitsdatenbefreier_good_bye_text',
        'media_buttons' => false,
    ));
}

