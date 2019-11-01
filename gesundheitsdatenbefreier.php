<?php
/*
== gesundheitsdatenbefreier ==
Plugin Name: gesundheitsdatenbefreier
Description: Ein WordPress-Plugin, mit dem Versicherte ihre Daten dank DSGVO befreien können
Version: 1.0
Author: Phil Lehmann, AK Vorratsdatenspeicherung
Author URI: http://www.vorratsdatenspeicherung.de/
Contributors: philrykoff
Tested up to: 5.2.4
Requires at least: 5.2.4
Requires PHP: 7.2.19
Stable tag: trunk
*/

defined('ABSPATH') or die('');

function get_mail_text($params) {
	$from = array('{{name}}', '{{strasse}}', '{{plz}}', '{{ort}}', '{{kasse}}', '{{versichertennummer}}');
	$to = array(htmlentities($params['gp_name']), htmlentities($params['gp_strasse']), htmlentities($params['gp_plz']), htmlentities($params['gp_ort']), htmlentities($params['gp_kasse'] !== 'other' ? $params['gp_kasse'] : $params['gp_kk_name']), htmlentities($params['gp_nummer']));
	return str_replace($from, $to, get_option('gesundheitsdatenbefreier_mail_text'));
}

function gesundheitsdatenbefreier_formular() {
	$page = 'infos';	
	if(isset($_GET['gp'])) {
		$page = $_GET['gp'];
	}

	$pages = array('infos', 'form', 'result', 'good-bye');
	if (in_array($page, $pages)) {
		require_once __DIR__ . '/pages/' . $page . '.php';
	} else {
		return 'Seite nicht gefunden.';
	}
}

add_shortcode('gesundheitsdatenbefreier', 'gesundheitsdatenbefreier_formular');

function gesundheitsdatenbefreier_api() {
	require_once __DIR__ . '/includes/pdf.php';
	register_rest_route('gesundheitsdatenbefreier', 'pdf', array( 
		'methods' => 'POST',
		'callback' => 'gesundheitsdatenbefreier_pdf',
	));
}

add_action('rest_api_init','gesundheitsdatenbefreier_api');

function gesundheitsdatenbefreier_init() {
	if(!session_id()) {
		session_start();
	}
}
add_action('init', 'gesundheitsdatenbefreier_init');

function gesundheitsdatenbefreier_scripts() {
	wp_enqueue_script('jquery');
}
add_action('wp_enqueue_scripts', 'gesundheitsdatenbefreier_scripts');

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

