<?php

defined('ABSPATH') or die('');

class gesundheitsdatenbefreier_Krankenkassenliste {
	private $data = [];
	
	function add($name, $plz, $ort, $strasse, $email) {
		array_push($this->data, new gesundheitsdatenbefreier_Krankenkasse($name, $plz, $ort, $strasse, $email));
	}
	
	function getFromPost() {
		if(!isset($_POST['gp_kasse'])) {
			wp_die('Parameter "gp_kasse" fehlt.');
		}
		
		$name = $_POST['gp_kasse'];
		foreach($this->data as $krankenkasse) {
			if($krankenkasse->name == $name) {
				return $krankenkasse;
			}
		}
		
		if(!isset($_POST['gp_kk_name'])) {
			wp_die('Parameter "gp_kk_name" fehlt.');
		}
		if(!isset($_POST['gp_kk_mail']) && (!isset($_POST['gp_kk_plz']) || !isset($_POST['gp_kk_ort']) || !isset($_POST['gp_kk_strasse']))) {
			wp_die('Parameter "gp_kk_mail" und eine komplette Addresse (Parameter "gp_kk_plz", "gp_kk_ort" und "gp_kk_strasse") fehlen.');
		}
		return new gesundheitsdatenbefreier_Krankenkasse($_POST['gp_kk_name'], $_POST['gp_kk_plz'], $_POST['gp_kk_ort'], $_POST['gp_kk_strasse'], $_POST['gp_kk_mail']);
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
$gesundheitsdatenbefreier_krankenkassen->add('actimonda BKK', '52068', 'Aachen', 'Hüttenstraße 1', '');
$gesundheitsdatenbefreier_krankenkassen->add('atlas BKK ahlmann', '28217', 'Bremen', 'Am Kaffee-Quartier 3', '');
$gesundheitsdatenbefreier_krankenkassen->add('Audi BKK', '85057', 'Ingolstadt', 'Ettinger Str. 70', '');
$gesundheitsdatenbefreier_krankenkassen->add('BAHN-BKK', '60486', 'Frankfurt', 'Franklinstrasse 54', '');
$gesundheitsdatenbefreier_krankenkassen->add('Bertelsmann BKK', '33335', 'Gütersloh', 'Carl-Miele-Str. 214', '');
$gesundheitsdatenbefreier_krankenkassen->add('Betriebskrankenkasse PricewaterhouseCoopers', '34212', 'Melsungen', 'Burgstr. 1-3', '');
$gesundheitsdatenbefreier_krankenkassen->add('BIG direkt gesund', '10969', 'Berlin', 'Markgrafenstrasse 62', '');
$gesundheitsdatenbefreier_krankenkassen->add('BKK Achenbach Buschhütten', '57223', 'Kreuztal', 'Siegener Str. 152', '');
$gesundheitsdatenbefreier_krankenkassen->add('BKK Akzo Nobel Bayern', '63785', 'Obernburg', 'Glanzstoffstraße', '');
$gesundheitsdatenbefreier_krankenkassen->add('BKK B. Braun | Aesculap', '34212', 'Melsungen', 'Grüne Straße 1', '');
$gesundheitsdatenbefreier_krankenkassen->add('BKK BPW Bergische Achsen KG', '51674', 'Wiehl', 'Ohler Berg 1', '');
$gesundheitsdatenbefreier_krankenkassen->add('BKK Deutsche Bank AG', '40212', 'Düsseldorf', 'Königsallee 60c', '');
$gesundheitsdatenbefreier_krankenkassen->add('BKK Diakonie', '33617', 'Bielefeld', 'Königsweg 8', '');
$gesundheitsdatenbefreier_krankenkassen->add('BKK EUREGIO', '52525', 'Heinsberg', 'Boos-Fremery-Straße 62', '');
$gesundheitsdatenbefreier_krankenkassen->add('BKK EVM', '56068', 'Koblenz', 'Schützenstr 80 - 82', '');
$gesundheitsdatenbefreier_krankenkassen->add('BKK EWE', '26122', 'Oldenburg', 'Staulinie 16-17', '');
$gesundheitsdatenbefreier_krankenkassen->add('BKK exklusiv', '31275', 'Lehrte', 'Zum Blauen See 7', '');
$gesundheitsdatenbefreier_krankenkassen->add('BKK Faber-Castell & Partner', '94209', 'Regen', 'Bahnhofstraße 45', '');
$gesundheitsdatenbefreier_krankenkassen->add('BKK firmus', '28237', 'Bremen', 'Gottlieb-Daimler Str. 11', '');
$gesundheitsdatenbefreier_krankenkassen->add('BKK Freudenberg', '69469', 'Weinheim', 'Höhnerweg 2 - 4', '');
$gesundheitsdatenbefreier_krankenkassen->add('BKK GILDEMEISTER SEIDENSTICKER', '33649', 'Bielefeld', 'Winterstr. 49', '');
$gesundheitsdatenbefreier_krankenkassen->add('BKK GRILLO-WERKE AG', '47169', 'Duisburg', 'Weseler Str. 1', '');
$gesundheitsdatenbefreier_krankenkassen->add('BKK Groz-Beckert', '72458', 'Albstadt', 'Unter dem Malesfelsen 72', '');
$gesundheitsdatenbefreier_krankenkassen->add('BKK Herford Minden Ravensberg', '32051', 'Herford', 'Am Kleinbahnhof 5', '');
$gesundheitsdatenbefreier_krankenkassen->add('BKK Herkules', '34117', 'Kassel', 'Jordanstr. 6', '');
$gesundheitsdatenbefreier_krankenkassen->add('BKK KARL MAYER', '63179', 'Obertshausen', 'Industriestraße 3', '');
$gesundheitsdatenbefreier_krankenkassen->add('BKK Linde', '65187', 'Wiesbaden', 'Konrad-Adenauer-Ring 33', '');
$gesundheitsdatenbefreier_krankenkassen->add('BKK MAHLE', '70376', 'Stuttgart', 'Pragstr. 26-46', '');
$gesundheitsdatenbefreier_krankenkassen->add('BKK Melitta Plus', '32425', 'Minden', 'Marienstr. 122', '');
$gesundheitsdatenbefreier_krankenkassen->add('BKK Miele', '33332', 'Gütersloh', 'Carl-Miele-Straße 29', '');
$gesundheitsdatenbefreier_krankenkassen->add('BKK MTU', '88045', 'Friedrichshafen', 'Hochstraße 40', '');
$gesundheitsdatenbefreier_krankenkassen->add('BKK PFAFF', '67655', 'Kaiserslautern', 'Pirmasenser Str. 132', '');
$gesundheitsdatenbefreier_krankenkassen->add('BKK Pfalz', '67059', 'Ludwigshafen', 'Lichtenberger Str. 16', '');
$gesundheitsdatenbefreier_krankenkassen->add('BKK ProVita', '85232', 'Bergkirchen', 'Münchner Weg 5', '');
$gesundheitsdatenbefreier_krankenkassen->add('BKK Public', '38226', 'Salzgitter', 'Thiestrasse 15', '');
$gesundheitsdatenbefreier_krankenkassen->add('BKK Rieker.RICOSTA.Weisser', '78532', 'Tuttlingen', 'Stockacher Str. 4-6', '');
$gesundheitsdatenbefreier_krankenkassen->add('BKK RWE', '29225', 'Celle', 'Welfenallee 32', '');
$gesundheitsdatenbefreier_krankenkassen->add('BKK Salzgitter', '38226', 'Salzgitter', 'Thiestrasse 15', '');
$gesundheitsdatenbefreier_krankenkassen->add('BKK Scheufelen', '73230', 'Kirchheim', 'Schöllkopfstr. 65', '');
$gesundheitsdatenbefreier_krankenkassen->add('BKK Schwarzwald-Baar-Heuberg', '78647', 'Trossingen', 'Löhrstraße 45', '');
$gesundheitsdatenbefreier_krankenkassen->add('BKK STADT AUGSBURG', '86153', 'Augsburg', 'Willy-Brandt-Platz 1', '');
$gesundheitsdatenbefreier_krankenkassen->add('BKK Technoform', '37079', 'Göttingen', 'August-Spindler-Straße 1', '');
$gesundheitsdatenbefreier_krankenkassen->add('BKK Textilgruppe Hof', '95028', 'Hof', 'Fabrikzeile 21', '');
$gesundheitsdatenbefreier_krankenkassen->add('BKK VDN', '58239', 'Schwerte', 'Rosenweg 15', '');
$gesundheitsdatenbefreier_krankenkassen->add('BKK VerbundPlus', '80333', 'München', 'Karloninenplatz 5', '');
$gesundheitsdatenbefreier_krankenkassen->add('BKK Verkehrsbau Union (VBU)', '10969', 'Berlin', 'Lindenstraße 67', '');
$gesundheitsdatenbefreier_krankenkassen->add('BKK Voralb HELLER*INDEX*LEUZE', '72622', 'Nürtingen', 'Neuffener Straße 54', '');
$gesundheitsdatenbefreier_krankenkassen->add('BKK Werra-Meissner', '37269', 'Eschwege', 'Straßburger Str. 5', '');
$gesundheitsdatenbefreier_krankenkassen->add('BKK Wirtschaft & Finanzen', '34212', 'Melsungen', 'Bahnhofstr. 19', '');
$gesundheitsdatenbefreier_krankenkassen->add('BKK Würth', '74653', 'Künzelsau', 'Gartenstraße 11', '');
$gesundheitsdatenbefreier_krankenkassen->add('BKK ZF & Partner', '56068', 'Koblenz', 'Am Wöllershof 12', '');
$gesundheitsdatenbefreier_krankenkassen->add('BKK_DürkoppAdler', '33605', 'Bielefeld', 'Sieghorster Str. 66', '');
$gesundheitsdatenbefreier_krankenkassen->add('BKK24', '31683', 'Obernkirchen', 'Sülbecker Brand 1', '');
$gesundheitsdatenbefreier_krankenkassen->add('BMW BKK', '84130', 'Dingolfing', 'Mengkofener Str.  6', '');
$gesundheitsdatenbefreier_krankenkassen->add('Bosch BKK', '70469', 'Stuttgart', 'Kruppstr. 19', '');
$gesundheitsdatenbefreier_krankenkassen->add('Continentale Betriebskrankenkasse', '22335', 'Hamburg', 'Sengelmannstrasse 120', '');
$gesundheitsdatenbefreier_krankenkassen->add('Daimler Betriebskrankenkasse', '70327', 'Stuttgart', 'Mercedesstraße  139', '');
$gesundheitsdatenbefreier_krankenkassen->add('Debeka BKK', '56072', 'Koblenz', 'Im Metternicher Feld 40', '');
$gesundheitsdatenbefreier_krankenkassen->add('DIE BERGISCHE KRANKENKASSE', '42719', 'Solingen', 'Heresbachstr. 29', '');
$gesundheitsdatenbefreier_krankenkassen->add('Die Schwenninger Betriebskrankenkasse', '78056', 'Villingen-Schwenningen', 'Spittelstr. 50', '');
$gesundheitsdatenbefreier_krankenkassen->add('energie-Betriebskrankenkasse', '30159', 'Hannover', 'Lange Laube 6', '');
$gesundheitsdatenbefreier_krankenkassen->add('Ernst & Young BKK', '80636', 'München', 'Arnulfstr. 59', '');
$gesundheitsdatenbefreier_krankenkassen->add('Heimat Krankenkasse', '33602', 'Bielefeld', 'Herforder Str. 23', '');
$gesundheitsdatenbefreier_krankenkassen->add('IKK Brandenburg und Berlin', '14480', 'Potsdam', 'Ziolkowskistr. 6', '');
$gesundheitsdatenbefreier_krankenkassen->add('IKK gesund plus', '39124', 'Magdeburg', 'Umfassungsstraße 85', '');
$gesundheitsdatenbefreier_krankenkassen->add('IKK Nord', '19061', 'Schwerin', 'Ellerried 1', '');
$gesundheitsdatenbefreier_krankenkassen->add('IKK Südwest', '66111', 'Saarbrücken', 'Berliner Promenade 1', '');
$gesundheitsdatenbefreier_krankenkassen->add('Koenig & Bauer BKK', '97080', 'Würzburg', 'Friedrich-Koenig-Str. 4', '');
$gesundheitsdatenbefreier_krankenkassen->add('Krones BKK', '93073', 'Neutraubling', 'Bayerwaldstr. 2L', '');
$gesundheitsdatenbefreier_krankenkassen->add('Merck BKK', '64293', 'Darmstadt', 'Frankfurter Straße 130', '');
$gesundheitsdatenbefreier_krankenkassen->add('mhplus Betriebskrankenkasse', '90411', 'Nürnberg', 'Nordostpark 14', '');
$gesundheitsdatenbefreier_krankenkassen->add('Novitas BKK', '47059', 'Duisburg', 'Schifferstraße 92-102', '');
$gesundheitsdatenbefreier_krankenkassen->add('pronova BKK', '67063', 'Ludwigshafen', 'Brunckstr. 47', '');
$gesundheitsdatenbefreier_krankenkassen->add('R+V Betriebskrankenkasse', '65205', 'Wiesbaden', 'Kreuzberger Ring 21', '');
$gesundheitsdatenbefreier_krankenkassen->add('Salus BKK', '81476', 'München', 'Züricher Straße 27', '');
$gesundheitsdatenbefreier_krankenkassen->add('SECURVITA BKK', '20099', 'Hamburg', 'Lübeckertordamm 1-3', '');
$gesundheitsdatenbefreier_krankenkassen->add('SIEMAG BKK', '57271', 'Hilchenbach', 'Hillnhütter Str. 89', '');
$gesundheitsdatenbefreier_krankenkassen->add('SKD BKK', '97421', 'Schweinfurt', 'Schultesstrasse 19a', '');
$gesundheitsdatenbefreier_krankenkassen->add('Sozialversicherung für Landwirtschaft', 'Forsten und Gartenbau (SVLFG)', '34131', 'Kassel', 'Weißensteinstraße 70-72', '');
$gesundheitsdatenbefreier_krankenkassen->add('Südzucker BKK', '68167', 'Mannheim', 'Joseph-Meyer-Str. 13-15', '');
$gesundheitsdatenbefreier_krankenkassen->add('TUI BKK', '30625', 'Hannover', 'Karl-Wiechert-Allee 23', '');
$gesundheitsdatenbefreier_krankenkassen->add('VIACTIV Krankenkasse', '44789', 'Bochum', 'Universitätsstraße 43', '');
$gesundheitsdatenbefreier_krankenkassen->add('Wieland BKK', '89079', 'Ulm', 'Graf-Arco-Str. 36', '');
$gesundheitsdatenbefreier_krankenkassen->add('WMF Betriebskrankenkasse', '73312', 'Geislingen', 'Eberhardstraße', '');
$gesundheitsdatenbefreier_krankenkassen->add('Allianz Private Krankenversicherungs-Aktiengesellschaft', '85774', 'Unterföhring', 'Dieselstraße 6-8', '');
$gesundheitsdatenbefreier_krankenkassen->add('ALTE OLDENBURGER Krankenversicherung AG', '49377', 'Vechta', 'Alte-Oldenburger-Platz 1', '');
$gesundheitsdatenbefreier_krankenkassen->add('ALTE OLDENBURGER Krankenversicherung von 1927 Versicherungsverein auf Gegenseitigkeit', '49377', 'Vechta', 'Alte-Oldenburger-Platz 1', '');
$gesundheitsdatenbefreier_krankenkassen->add('ARAG Krankenversicherungs-Aktiengesellschaft', '81829', 'München', 'Hollerithstraße 11', '');
$gesundheitsdatenbefreier_krankenkassen->add('Augenoptiker Ausgleichskasse VVaG (AKA)', '44225', 'Dortmund', 'Generationenweg 4', '');
$gesundheitsdatenbefreier_krankenkassen->add('AXA Krankenversicherung Aktiengesellschaft', '51067', 'Köln', 'Colonia Allee 10-20', '');
$gesundheitsdatenbefreier_krankenkassen->add('Barmenia Krankenversicherung AG', '42119', 'Wuppertal', 'Barmenia-Allee 1', '');
$gesundheitsdatenbefreier_krankenkassen->add('Bayerische Beamtenkrankenkasse Aktiengesellschaft', '80538', 'München', 'Maximilianstraße 53', '');
$gesundheitsdatenbefreier_krankenkassen->add('Central Krankenversicherung Aktiengesellschaft', '50670', 'Köln', 'Hansaring 40 - 50', '');
$gesundheitsdatenbefreier_krankenkassen->add('Concordia Krankenversicherungs-Aktiengesellschaft', '30625', 'Hannover', 'Karl-Wiechert-Allee 55', '');
$gesundheitsdatenbefreier_krankenkassen->add('Continentale Krankenversicherung a.G.', '44139', 'Dortmund', 'Ruhrallee 92', '');
$gesundheitsdatenbefreier_krankenkassen->add('Debeka Krankenversicherungsverein auf Gegenseitigkeit Sitz Koblenz am Rhein', '56073', 'Koblenz am Rhein', 'Ferdinand-Sauerbruch-Straße 18', '');
$gesundheitsdatenbefreier_krankenkassen->add('DEVK Krankenversicherungs-Aktiengesellschaft', '50735', 'Köln', 'Riehler Straße 190', '');
$gesundheitsdatenbefreier_krankenkassen->add('DKV Deutsche Krankenversicherung Aktiengesellschaft', '50933', 'Köln', 'Aachener Straße 300', '');
$gesundheitsdatenbefreier_krankenkassen->add('ENVIVAS Krankenversicherung Aktiengesellschaft', '50670', 'Köln', 'Gereonswall 68', '');
$gesundheitsdatenbefreier_krankenkassen->add('ERGO Krankenversicherung AG', '90762', 'Fürth', 'Bay', 'Nürnberger Straße 91-95', '');
$gesundheitsdatenbefreier_krankenkassen->add('Freie Arzt- und Medizinkasse der Angehörigen der Berufsfeuerwehr und der Polizei VVaG', '60327', 'Frankfurt am Main', 'Hansaallee 154', '');
$gesundheitsdatenbefreier_krankenkassen->add('Gothaer Krankenversicherung Aktiengesellschaft', '50969', 'Köln', 'Arnoldiplatz 1', '');
$gesundheitsdatenbefreier_krankenkassen->add('HALLESCHE Krankenversicherung auf Gegenseitigkeit', '70178', 'Stuttgart', 'Reinsburgstraße 10', '');
$gesundheitsdatenbefreier_krankenkassen->add('HanseMerkur Krankenversicherung AG', '20354', 'Hamburg', 'Siegfried-Wedells-Platz 1', '');
$gesundheitsdatenbefreier_krankenkassen->add('HanseMerkur Krankenversicherung auf Gegenseitigkeit', '20354', 'Hamburg', 'Siegfried-Wedells-Platz 1', '');
$gesundheitsdatenbefreier_krankenkassen->add('HanseMerkur Speziale Krankenversicherung AG', '20354', 'Hamburg', 'Siegfried-Wedells-Platz 1', '');
$gesundheitsdatenbefreier_krankenkassen->add('HUK-COBURG-Krankenversicherung AG', '96450', 'Coburg', 'Bahnhofsplatz', '');
$gesundheitsdatenbefreier_krankenkassen->add('INTER Krankenversicherung AG', '68165', 'Mannheim', 'Erzbergerstraße 9-15', '');
$gesundheitsdatenbefreier_krankenkassen->add('Krankenunterstützungskasse der Berufsfeuerwehr Hannover', '30625', 'Hannover', 'Karl-Wiechert-Allee 60 B', '');
$gesundheitsdatenbefreier_krankenkassen->add('Landeskrankenhilfe V.V.a.G.', '21335', 'Lüneburg', 'Uelzener Straße 120', '');
$gesundheitsdatenbefreier_krankenkassen->add('LIGA Krankenversicherung katholischer Priester Versicherungsverein auf Gegenseitigkeit Regensburg', '93055', 'Regensburg', 'Weißenburgstraße 17', '');
$gesundheitsdatenbefreier_krankenkassen->add('Lohnfortzahlungskasse Aurich VVaG', '26603', 'Aurich', 'Lambertistraße 16', '');
$gesundheitsdatenbefreier_krankenkassen->add('Lohnfortzahlungskasse Leer VVaG', '26789', 'Leer', 'Grosser Stein 5', 'i. Hs. Huneke GmbH', '');
$gesundheitsdatenbefreier_krankenkassen->add('LVM Krankenversicherungs-AG', '48151', 'Münster', 'Kolde-Ring 21', '');
$gesundheitsdatenbefreier_krankenkassen->add('Mecklenburgische Krankenversicherungs-Aktiengesellschaft', '30625', 'Hannover', 'Platz der Mecklenburgischen 1', '');
$gesundheitsdatenbefreier_krankenkassen->add('MÜNCHENER VEREIN Krankenversicherung a.G.', '80336', 'München', 'Pettenkoferstraße 19', '');
$gesundheitsdatenbefreier_krankenkassen->add('NÜRNBERGER Krankenversicherung Aktiengesellschaft', '90482', 'Nürnberg', 'Ostendstraße 100', '');
$gesundheitsdatenbefreier_krankenkassen->add('ottonova Krankenversicherung AG', '80333', 'München', 'Ottostraße 4', '');
$gesundheitsdatenbefreier_krankenkassen->add('praenatura Versicherungsverein auf Gegenseitigkeit (VVaG)', '65428', 'Rüsselsheim', 'Bahnhofsplatz 1', '');
$gesundheitsdatenbefreier_krankenkassen->add('Provinzial Krankenversicherung Hannover AG', '30159', 'Hannover', 'Schiffgraben 4', '');
$gesundheitsdatenbefreier_krankenkassen->add('R+V Krankenversicherung Aktiengesellschaft', '65189', 'Wiesbaden', 'Raiffeisenplatz 1', '');
$gesundheitsdatenbefreier_krankenkassen->add('SIGNAL IDUNA Krankenversicherung a.G.', '44139', 'Dortmund', 'Joseph-Scherer-Straße 3', '');
$gesundheitsdatenbefreier_krankenkassen->add('SONO Krankenversicherung a.G.', '46242', 'Bottrop', 'Westring 73', '');
$gesundheitsdatenbefreier_krankenkassen->add('St. Martinus Priesterverein der Diözese Rottenburg-Stuttgart- Kranken- und Sterbekasse (KSK) - Versicherungsverein auf Gegenseitigkeit (VVaG)', '70178', 'Stuttgart', 'Hohenzollernstraße 23', '');
$gesundheitsdatenbefreier_krankenkassen->add('Süddeutsche Krankenversicherung a.G.', '70736', 'Fellbach', 'Raiffeisenplatz 5', '');
$gesundheitsdatenbefreier_krankenkassen->add('UNION KRANKENVERSICHERUNG AKTIENGESELLSCHAFT', '66123', 'Saarbrücken', 'Peter-Zimmer-Straße 2', '');
$gesundheitsdatenbefreier_krankenkassen->add('uniVersa Krankenversicherung a.G.', '90489', 'Nürnberg', 'Sulzbacher Straße 1-7', '');
$gesundheitsdatenbefreier_krankenkassen->add('Versicherer im Raum der Kirchen Krankenversicherung AG', '32756', 'Detmold', 'Doktorweg 2-4', '');
$gesundheitsdatenbefreier_krankenkassen->add('vigo Krankenversicherung VVaG', '40210', 'Düsseldorf', 'Konrad-Adenauer-Platz 12', '');
$gesundheitsdatenbefreier_krankenkassen->add('Württembergische Krankenversicherung Aktiengesellschaft', '70176', 'Stuttgart', 'Gutenbergstraße 30', '');


