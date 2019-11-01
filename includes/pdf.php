<?php

defined('ABSPATH') or die('');

function gesundheitsdatenbefreier_pdf() {

	require_once __DIR__ . '/../libs/tcpdf/tcpdf.php';
    
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('AK Vorratsdatenspeicherung');
    $pdf->SetTitle('Datenschutzauskunft');
    $pdf->SetSubject('Datenschutzauskunft');
    
	$pdf->SetMargins(22, 27, 20);
    $pdf->SetPrintHeader(false);
    $pdf->SetPrintFooter(false);
    $pdf->AddPage();

	require_once __DIR__ . '/krankenkassen.php';
	$krankenkasse = $gesundheitsdatenbefreier_krankenkassen->get($_POST);
	
    $gp_name = htmlentities($_POST['gp_name']);
    $gp_strasse = htmlentities($_POST['gp_strasse']);
    $gp_plz = htmlentities($_POST['gp_plz']);
    $gp_ort = htmlentities($_POST['gp_ort']);

    $html = <<<TXT
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<font size="8">{$gp_name} - {$gp_strasse} - {$gp_plz} {$gp_ort}<br/></font><br/>
<b>{$krankenkasse->name}</b><br/>
{$krankenkasse->strasse}<br/>
{$krankenkasse->plz} {$krankenkasse->ort}
<br/>
<br/>
<br/>
<br/>
<br/>
TXT;
	
	$html .= wpautop(get_mail_text($_POST));

    $pdf->writeHTML($html, true, false, true, false, '');
    
	$lineStyle = array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0));
	$pdf->Line(7, 105, 11, 105, $lineStyle);
	$pdf->Line(7, 210, 11, 210, $lineStyle);
	
    $pdf->Output('Auskunft.pdf', 'I');
}