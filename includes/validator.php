<?php

defined('ABSPATH') or die('');

class gesundheitsdatenbefreier_Validator {
	
	private static $errors = null;
	
	private static $messages = array(
		'not_empty'  => 'Bitte geben Sie einen Wert ein.',
		'plz'  => 'Bitte geben Sie eine gültige Postleitzahl ein.',
		'email' => 'Bitte geben Sie eine gültige E-Mail-Adresse ein.',
		'versichertennummer' => 'Bitte geben Sie eine gültige Versichertennummer ein.'
	);
	
	private static $basicValidations = array(
		'gp_name' => array('not_empty'),
		'gp_strasse' => array('not_empty'),
		'gp_plz' => array('not_empty', 'plz'),
		'gp_ort' => array('not_empty'),
		'gp_kasse' => array('not_empty'),
		'gp_nummer' => array('not_empty', 'versichertennummer')
	);
	
	// Used when "other" health insurance is picked
	private static $otherKKValidation = array(
		'gp_kk_name' => array('not_empty')
	);
	
	// If "other" health insurance is picked, the user can either provide mail...
	private static $otherKKMailValidation = array(
		'gp_kk_mail' => array('not_empty', 'email')
	);
	
	// ... or snail mail contact information
	private static $otherKKAddressValidation = array(
		'gp_kk_strasse' => array('not_empty'),
		'gp_kk_plz' => array('not_empty', 'plz'),
		'gp_kk_ort' => array('not_empty')
	);
	
	// validation functions
	private static function is_not_empty($value) {
		$trimmedText = trim($value);
		return !empty($trimmedText);
	}
	
	private static function is_plz($value) {
		return is_string($value) && is_numeric($value) && strlen($value) === 5;
	}
	
	private static function is_email($value) {
		return (bool) filter_var($value, FILTER_VALIDATE_EMAIL);
	}
	
	private static function is_versichertennummer($value) {
		return is_string($value) && strlen($value) === 10 && self::modified_luhn($value);
	}
	
	private static function modified_luhn($number) {		
		$firstLetterValue = ord(strtoupper(substr($number, 0, 1))) - ord('A') + 1;
		if($firstLetterValue <= 9) {
			$firstLetterValue = '0' . $firstLetterValue;
		}
		$number = $firstLetterValue . substr($number, 1);
		$total = 0;
		$digits = str_split($number);
		foreach($digits as $index => $digit) {
			if (!is_numeric($digit)) return false;
			if($index == 10) {
				break;
			}
			if ($index % 2 == 1) {
				$digit *= 2;
			}
			if ($digit >= 10) {
				$digit_parts = str_split($digit);
				$digit = $digit_parts[0] + $digit_parts[1];
			}
			$total += $digit;
		}
		$checksum = $total;
		if ($checksum >= 10) {
			$checksum = $checksum % 10;
		}
		return $checksum == substr($number, 10);
	}
	
	// Validate data against a specific set of rules
	private static function validateRules($data, $validations, $errors = []) {
		foreach($validations as $field => $checks) {
			// trim all parameters
			if(isset($data[$field]) && $data[$field] != trim($data[$field])) {
				$data[$field] = trim($data[$field]);
			}			
			foreach($checks as $check) {
				$checkFunction = 'is_' . $check;
				if(isset($data[$field])) {
					if(!self::$checkFunction($data[$field])) {
						$errors[$field] = self::$messages[$check];
						break;
					}
				} elseif($check == 'not_empty') {
					$errors[$field] = self::$messages[$check];
					break;
				}
			}
		}
		return $errors;
	}
	
	// Validate complete set of data
	public static function validatePost() {
		if(self::$errors !== null) {
			return self::$errors;
		}
		
		self::$errors = self::validateRules($_POST, self::$basicValidations);
		if($_POST['gp_kasse'] == 'other') {
			self::$errors = self::validateRules($_POST, self::$otherKKValidation, self::$errors);
			
			if(self::is_not_empty($_POST['gp_kk_mail'])) {
				self::$errors = self::validateRules($_POST, self::$otherKKMailValidation, self::$errors);
			} else {
				self::$errors = self::validateRules($_POST, self::$otherKKAddressValidation, self::$errors);
			}
		}
		return self::$errors;
	}
	
	public static function isValidPost() {
		return count(self::validatePost()) === 0;
	}
	
	public static function hasError($name) {
		return self::$errors !== null && array_key_exists($name, self::$errors);
	}
	
	public static function getError($name) {
		return self::$errors[$name];
	}
}
