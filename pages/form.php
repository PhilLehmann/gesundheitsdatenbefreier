<?php

defined('ABSPATH') or die('');

require_once __DIR__ . '/../includes/validator.php';

function gesundheitsdatenbefreier_getError($name) {
	if(gesundheitsdatenbefreier_Validator::hasError($name)) {
		return '<i class="error">' . gesundheitsdatenbefreier_Validator::getError($name) . '</i>';
	}
}

function gesundheitsdatenbefreier_getValue($name) {
	$html = '<input type="text" name="' . $name . '"';
	if(isset($_POST[$name])) {
		$html .= ' value="' . esc_attr($_POST[$name]) . '"';
	}
	$error = gesundheitsdatenbefreier_getError($name);
	if($error) {
		$html .= ' class="error"';
	}
	$html .= '/>';
	$html .= $error;
	return $html;
}

if(isset($_POST['gp_kasse'])) {
	wp_add_inline_script('gesundheitsdatenbefreier-script', 'jQuery(document).ready(function($){ $(\'.select2.krankenkasse\').val(\'' . esc_js($_POST['gp_kasse']) . '\').trigger("change"); });');
}

?>
<div class="gesundheitsdatenbefreier form">
	<form action="?gp=result" method="post">
		<?php
			if(isset($showResult) && !gesundheitsdatenbefreier_Validator::isValidPost()) {
				echo '<div class="error">Da ist etwas schiefgelaufen.</div>';
			}
		?>
		
		Name: <?=gesundheitsdatenbefreier_getValue("gp_name")?><br/>
		Straße: <?=gesundheitsdatenbefreier_getValue("gp_strasse")?><br/>
		Postleitzahl: <?=gesundheitsdatenbefreier_getValue("gp_plz")?><br/>
		Ort: <?=gesundheitsdatenbefreier_getValue("gp_ort")?><br/>
		
		<p><hr/></p>
		
		Krankenkasse: 
		<select class="select2 krankenkasse" name="gp_kasse">
			<option></option>
			<?php
				require_once __DIR__ . '/../includes/krankenkassen.php';
				$gesundheitsdatenbefreier_krankenkassen = gesundheitsdatenbefreier_Krankenkassenliste::getInstance();
				$gesundheitsdatenbefreier_krankenkassen->printOptions();
			?>
			<option value="other">Andere...</option>
		</select>
		<?=gesundheitsdatenbefreier_getError("gp_kasse")?><br/>
		<div class="other fields">
			Name: <?=gesundheitsdatenbefreier_getValue("gp_kk_name")?><br/>
			Straße: <?=gesundheitsdatenbefreier_getValue("gp_kk_strasse")?><br/>
			Postleitzahl: <?=gesundheitsdatenbefreier_getValue("gp_kk_plz")?><br/>
			Ort: <?=gesundheitsdatenbefreier_getValue("gp_kk_ort")?><br/>
			E-Mail: <?=gesundheitsdatenbefreier_getValue("gp_kk_mail")?><br/>
		</div>
		Versichertennummer: <?=gesundheitsdatenbefreier_getValue("gp_nummer")?><br/>
		<input class="submit" type="submit" value="Anfrage erstellen" />
	</form>
	<p>
		<input class="form infos button" type="button" value="&lt; Zurück" />
	</p>
</div>