<?php

defined('ABSPATH') or die('');

$counter = get_option('gesundheitsdatenbefreier_counter', 0);
$threshold = get_option('gesundheitsdatenbefreier_threshold', 0);

echo wpautop(get_option('gesundheitsdatenbefreier_info_text'));

if($counter >= $threshold) {
	if($counter == 1) {
		echo '<p>Es wurde bereits eine Datenabfrage erstellt.</p>';
	} else {
		echo '<p>Es wurden bereits ' . $counter . ' Datenabfragen erstellt.</p>';
	}
}

?>

<input class="button" type="button" value="Frage deine Daten ab!" />

<script type="text/javascript">
jQuery(document).ready(function() {
	jQuery('.button').click(function() {
		window.location = "?gp=form";
	});
});
</script>