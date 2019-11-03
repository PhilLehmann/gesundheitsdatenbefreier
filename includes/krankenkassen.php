<?php

defined('ABSPATH') or die('');

class gesundheitsdatenbefreier_Krankenkassenliste {
	private $data = [];
	
	function add($name, $plz, $ort, $strasse, $email) {
		array_push($this->data, new gesundheitsdatenbefreier_Krankenkasse($name, $plz, $ort, $strasse, $email));
	}
	
	function get($requestData) {
		if(!isset($requestData['gp_kasse'])) {
			wp_die('Parameter "gp_kasse" fehlt.');
		}
		
		$name = $requestData['gp_kasse'];
		foreach($this->data as $krankenkasse) {
			if($krankenkasse->name == $name) {
				return $krankenkasse;
			}
		}
		
		if(!isset($requestData['gp_kk_name'])) {
			wp_die('Parameter "gp_kk_name" fehlt.');
		}
		if(!isset($requestData['gp_kk_mail']) && (!isset($requestData['gp_kk_plz']) || !isset($requestData['gp_kk_ort']) || !isset($requestData['gp_kk_strasse']))) {
			wp_die('Parameter "gp_kk_mail" und eine komplette Addresse (Parameter "gp_kk_plz", "gp_kk_ort" und "gp_kk_strasse") fehlen.');
		}
		return new gesundheitsdatenbefreier_Krankenkasse($requestData['gp_kk_name'], $requestData['gp_kk_plz'], $requestData['gp_kk_ort'], $requestData['gp_kk_strasse'], $requestData['gp_kk_mail']);
	}
	
	function printOptions() {
		foreach($this->data as $krankenkasse) {
			echo '<option value="' . esc_attr($krankenkasse->name) . '">' . esc_html($krankenkasse->name) . '</option>';
		}
	}
}

class gesundheitsdatenbefreier_Krankenkasse {
    public $name;
    public $plz;
    public $ort;
    public $strasse;
    public $email;
   
    function __construct($name, $plz, $ort, $strasse, $email) {
		$this->name = $name;
		$this->plz = $plz;
		$this->ort = $ort;
		$this->strasse = $strasse;
		$this->email = $email;
    }
	
	function canSendLetter() {
		return !empty($this->plz) && !empty($this->ort) && !empty($this->strasse);
	}
	
	function canSendMail() {
		return !empty($this->email);
	}
}

$gesundheitsdatenbefreier_krankenkassen = new gesundheitsdatenbefreier_Krankenkassenliste();

$gesundheitsdatenbefreier_krankenkassen->add('BARMER', '10969', 'Berlin', 'Axel-Springer-Straße 44', 'service@barmer.de');
$gesundheitsdatenbefreier_krankenkassen->add('DAK Gesundheit', '20097', 'Hamburg', 'Nagelsweg 27-31', 'service@dak.de');
$gesundheitsdatenbefreier_krankenkassen->add('Techniker Krankenkasse (TK)', '22305', 'Hamburg', 'Bramfelder Straße 140', 'service@tk.de');
$gesundheitsdatenbefreier_krankenkassen->add('HEK - Hanseatische Krankenkasse', '22041', 'Hamburg', 'Wandsbeker Zollstraße 86-90', 'kontakt@hek.de');
$gesundheitsdatenbefreier_krankenkassen->add('hkk Krankenkasse', '28195', 'Bremen', 'Martinistraße 26', 'info@hkk.de');
$gesundheitsdatenbefreier_krankenkassen->add('KKH Kaufmännische Krankenkasse', '30625', 'Hannover', 'Karl-Wiechert-Allee 61', 'service@kkh.de');
$gesundheitsdatenbefreier_krankenkassen->add('KNAPPSCHAFT', '44781', 'Bochum', 'Dez. I.1 -Kundenservice-', 'kundenservice-info@knappschaft.de');
$gesundheitsdatenbefreier_krankenkassen->add('AOK Baden-Württemberg', '70191', 'Stuttgart', 'Presselstr. 19', 'info@bw.aok.de');
$gesundheitsdatenbefreier_krankenkassen->add('AOK Bayern', '81739', 'München', 'Carl-Wery-Straße 28', 'infoprivatkunden@service.by.aok.de');
$gesundheitsdatenbefreier_krankenkassen->add('AOK Bremen/Bremerhaven', '28195', 'Bremen', 'Bürgermeister-Smidt-Straße 95', 'info@hb.aok.de');
$gesundheitsdatenbefreier_krankenkassen->add('AOK Hessen', '61352', 'Bad Homburg', 'Basler Straße 2', 'service@he.aok.de');
$gesundheitsdatenbefreier_krankenkassen->add('AOK Niedersachsen', '30519', 'Hannover', 'Hildesheimer Straße 273', 'AOK.Service@nds.aok.de');
$gesundheitsdatenbefreier_krankenkassen->add('AOK Nordost', '14467', 'Potsdam', 'Behlertstr. 33A', 'service@nordost.aok.de');
$gesundheitsdatenbefreier_krankenkassen->add('AOK Nordwest', '44269', 'Dortmund', 'Kopenhagener Straße 1', 'kontakt@nw.aok.de');
$gesundheitsdatenbefreier_krankenkassen->add('AOK PLUS', '01067', 'Dresden', 'Sternplatz 7', 'service@plus.aok.de');
$gesundheitsdatenbefreier_krankenkassen->add('AOK Rheinland-Pfalz/Saarland', '67304', 'Eisenberg', 'Virchowstraße 30', 'service@rps.aok.de');
$gesundheitsdatenbefreier_krankenkassen->add('AOK Rheinland/Hamburg', '40213', 'Düsseldorf', 'Kasernenstraße 61', 'aok@rh.aok.de');
$gesundheitsdatenbefreier_krankenkassen->add('AOK Sachsen-Anhalt', '39106', 'Magdeburg', 'Lüneburger Straße 4', 'service@san.aok.de');
$gesundheitsdatenbefreier_krankenkassen->add('IKK classic', '01099', 'Dresden', 'Tannenstr. 4b', 'info@ikk-classic.de');
$gesundheitsdatenbefreier_krankenkassen->add('BKK Mobil Oil', '80639', 'München', 'Friedenheimer Brücke 29', 'info@service.bkk-mobil-oil.de');
$gesundheitsdatenbefreier_krankenkassen->add('SBK', '80339', 'München', 'Heimeranstr. 31', 'info@sbk.org');




