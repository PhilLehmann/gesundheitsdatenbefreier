<?php

defined('ABSPATH') or die('');

$showResult = true;

require_once __DIR__ . '/../includes/validator.php';
if(!gesundheitsdatenbefreier_Validator::isValid($_GET)) {
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
<div class="gesundheitsdatenbefreier result">
	<p>
		<b>{$krankenkasse->name}</b><br/>
		{$krankenkasse->strasse}<br/>
		{$krankenkasse->plz} {$krankenkasse->ort}<br/>
		<i>{$krankenkasse->email}</i>
	</p>
	
	<p>
		<b><?php echo esc_html(get_option('gesundheitsdatenbefreier_mail_subject', 'Datenschutzauskunft')); ?></b>
	</p>

	<?php

	$mailText = gesundheitsdatenbefreier_get_mail_text($_GET);
	echo wpautop($mailText);

	?>

	<div class="mail-to">
		<?php echo esc_html($krankenkasse->email); ?>
	</div>
	<div class="mail-subject">
		<?php echo esc_html(get_option('gesundheitsdatenbefreier_mail_subject', 'Datenschutzauskunft')); ?>
	</div>
	<div class="mail-text">
		<?php echo $mailText; ?>
	</div>

	<div class="actions">
		<input class="infos button" type="button" value="&lt; Zurück" />
		<?php
		if($krankenkasse->canSendLetter()) {
		?>
		<form target="_blank" action="/wp-json/gesundheitsdatenbefreier/pdf" method="post">
			<input type="hidden" name="gp_name" value="<?php echo esc_attr($_GET['gp_name']); ?>">
			<input type="hidden" name="gp_strasse" value="<?php echo esc_attr($_GET['gp_strasse']); ?>">
			<input type="hidden" name="gp_plz" value="<?php echo esc_attr($_GET['gp_plz']); ?>">
			<input type="hidden" name="gp_ort" value="<?php echo esc_attr($_GET['gp_ort']); ?>">
			<input type="hidden" name="gp_kasse" value="<?php echo esc_attr($_GET['gp_kasse']); ?>">
			<input type="hidden" name="gp_kk_name" value="<?php echo esc_attr($_GET['gp_kk_name']); ?>">
			<input type="hidden" name="gp_kk_strasse" value="<?php echo esc_attr($_GET['gp_kk_strasse']); ?>">
			<input type="hidden" name="gp_kk_plz" value="<?php echo esc_attr($_GET['gp_kk_plz']); ?>">
			<input type="hidden" name="gp_kk_ort" value="<?php echo esc_attr($_GET['gp_kk_ort']); ?>">
			<input type="hidden" name="gp_kk_mail" value="<?php echo esc_attr($_GET['gp_kk_mail']); ?>">		
			<input type="hidden" name="gp_nummer" value="<?php echo esc_attr($_GET['gp_nummer']); ?>">
			<input class="submit-pdf button" type="submit" value="PDF öffnen" />
		</form>
		<?php
		}
		if($krankenkasse->canSendMail()) {
		?>
		<input class="submit-mail button" type="button" value="Mail öffnen" />
		<?php
		}
		?>
		<input class="good-bye button" type="button" value="Weiter &gt;" />
	</div>
</div>

<?php
}