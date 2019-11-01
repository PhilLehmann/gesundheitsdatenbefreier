<?php

defined('ABSPATH') or die('');

$showResult = true;

require_once __DIR__ . '/../includes/validator.php';
if(!Validator::isValid($_GET)) {
	$showResult = false;
	require_once __DIR__ . '/form.php';
}

if($showResult) {
	
	if(!isset($_SESSION['gesundheitsdatenbefreier_session_request_counted'])) {
		$_SESSION['gesundheitsdatenbefreier_session_request_counted'] = 1;
		$counter = get_option('gesundheitsdatenbefreier_counter', 0);
		update_option('gesundheitsdatenbefreier_counter', $counter + 1);
	}

	require_once __DIR__ . '/../includes/krankenkassen.php';
	$krankenkasse = $gesundheitsdatenbefreier_krankenkassen->get($_GET);

?>

<p>
    <b>An: <?php echo htmlentities($krankenkasse->name); ?></b>
</p>

<?php

$mailText = get_mail_text($_GET);
echo wpautop($mailText);

?>

<div class="mail-text">
<?php

echo $mailText;

?>
</div>

<div class="actions">
	<input class="back button" type="button" value="&lt; Zurück" />
<?php
	if($krankenkasse->canSendLetter()) {
?>
    <form target="_blank" action="/wp-json/gesundheitsdatenbefreier/pdf" method="post">
        <input type="hidden" name="gp_name" value="<?php echo htmlentities($_GET['gp_name']); ?>">
        <input type="hidden" name="gp_strasse" value="<?php echo htmlentities($_GET['gp_strasse']); ?>">
        <input type="hidden" name="gp_plz" value="<?php echo htmlentities($_GET['gp_plz']); ?>">
        <input type="hidden" name="gp_ort" value="<?php echo htmlentities($_GET['gp_ort']); ?>">
        <input type="hidden" name="gp_kasse" value="<?php echo htmlentities($_GET['gp_kasse']); ?>">
        <input type="hidden" name="gp_kk_name" value="<?php echo htmlentities($_GET['gp_kk_name']); ?>">
        <input type="hidden" name="gp_kk_strasse" value="<?php echo htmlentities($_GET['gp_kk_strasse']); ?>">
        <input type="hidden" name="gp_kk_plz" value="<?php echo htmlentities($_GET['gp_kk_plz']); ?>">
        <input type="hidden" name="gp_kk_ort" value="<?php echo htmlentities($_GET['gp_kk_ort']); ?>">
        <input type="hidden" name="gp_kk_mail" value="<?php echo htmlentities($_GET['gp_kk_mail']); ?>">		
        <input type="hidden" name="gp_nummer" value="<?php echo htmlentities($_GET['gp_nummer']); ?>">
        <input class="submit-pdf" type="submit" value="PDF öffnen" />
    </form>
<?php
	}
	if($krankenkasse->canSendMail()) {
?>
    <input class="submit-mail" type="button" value="Mail öffnen" />
	<input class="next button" type="button" value="Weiter &gt;" />
<?php
	}
?>
</div>

<script type="text/javascript">
jQuery(document).ready(function() {
    jQuery('.submit-mail').click(function() {
        var gp_kk_email = "<?php echo htmlentities($krankenkasse->email); ?>";
        var gp_nummer = "<?php echo htmlentities($_GET['gp_nummer']); ?>";
        var gp_name = "<?php echo htmlentities($_GET['gp_name']); ?>";
        window.open("mailto:" + gp_kk_email + "?subject=Datenschutzauskunft&body=" + encodeURIComponent(jQuery('.mail-text').text()));
	});
	jQuery('.back.button').click(function() {
		window.location = "?gp=infos";
	});
	jQuery('.next.button').click(function() {
		window.location = "?gp=good-bye";
	});
});
</script>

<style type="text/css">
.mail-text {
	display: none;
}
.actions {
    display: flex;
    justify-content: space-between;
	margin-top: 20px;
}
.actions > * {
    display: inline-block;
}
</style>
<?php
}