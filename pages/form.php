<?php

defined('ABSPATH') or die('');

require_once __DIR__ . '/../includes/validator.php';

function getError($name) {
	if(Validator::hasError($name)) {
		return '<i class="error">' . Validator::getError($name) . '</i>';
	}
}

function getValue($name) {
	$html = '<input type="text" name="' . $name . '"';
	if(isset($_GET[$name])) {
		$html .= ' value="' . htmlentities($_GET[$name]) . '"';
	}
	$error = getError($name);
	if($error) {
		$html .= ' class="error"';
	}
	$html .= '/>';
	$html .= $error;
	return $html;
}

?>

<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/js/select2.min.js"></script>

<form action="" method="get">
<?php
	if(isset($showResult) && !Validator::isValid($_GET)) {
		echo '<div class="error">Da ist etwas schiefgelaufen.</div>';
	}
?>
	<input type="hidden" name="gp" value="result">
	Name: <?=getValue("gp_name")?><br/>
	Straße: <?=getValue("gp_strasse")?><br/>
	Postleitzahl: <?=getValue("gp_plz")?><br/>
	Ort: <?=getValue("gp_ort")?><br/>
	<p><hr/></p>
	Krankenkasse: 
	<select class="select2 krankenkasse" name="gp_kasse">
		<option></option>
<?php
	require_once __DIR__ . '/../includes/krankenkassen.php';
	$gesundheitsdatenbefreier_krankenkassen->printOptions();
?>
		<option value="other">Andere...</option>
	</select>
	<?=getError("gp_kasse")?><br/>
	<div class="other fields">
		Name: <?=getValue("gp_kk_name")?><br/>
		Straße: <?=getValue("gp_kk_strasse")?><br/>
		Postleitzahl: <?=getValue("gp_kk_plz")?><br/>
		Ort: <?=getValue("gp_kk_ort")?><br/>
		E-Mail: <?=getValue("gp_kk_mail")?><br/>
	</div>
	Versichertennummer: <?=getValue("gp_nummer")?><br/>
	<input class="submit" type="submit" value="Absenden" />
</form>
<p>
	<input class="back button" type="button" value="&lt; Zurück" />
</p>

<script type="text/javascript">
jQuery(document).ready(function() {
    $el = jQuery('.select2.krankenkasse');
	$el.select2({
		placeholder: ""
	}).on('select2:select', function (e) {
		if(e.params.data.id == 'other') {
			jQuery('.other.fields').slideDown("slow");
		} else {
			jQuery('.other.fields').slideUp("slow");			
		}		
	});
<?php
	if(isset($_GET['gp_kasse'])) {
		echo '$el.val("' . $_GET['gp_kasse'] . '").trigger("change");';
	}
?>
	if($el.val() == 'other') {
		jQuery('.other.fields').slideDown("slow");
	}
	jQuery('.back.button').click(function() {
		window.location = "?gp=infos";
	});
});
</script>

<style type="text/css">
.select2 {
	style: block;
	width: 100%;
}
form {
	position: relative;
}
input.error {
	background-color: #fff6f6;
    border-color: #e0b4b4;
    color: #9f3a38;
    -webkit-box-shadow: none;
    box-shadow: none;
}
i.error {
    color: #9f3a38;
}
div.error {
position: relative;
    min-height: 1em;
    margin: 1em 0;
    padding: 1em 1.5em;
    line-height: 1.4285em;
    border-radius: .28571429rem;
	background-color: #ffe8e6;
    color: #db2828;
    box-shadow: 0 0 0 1px #db2828 inset, 0 0 0 0 transparent;
}
.other.fields {
	display: none;
}
.back.button {
	margin-top: 20px;
}
.submit {
	position: absolute;
	right: 0;
	margin-top: 20px;
}
</style>