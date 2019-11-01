<?php

defined('ABSPATH') or die('');

class Validator {
	
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
	
	private static $otherKKValidation = array(
		'gp_kk_name' => array('not_empty')
	);
	
	private static $otherKKMailValidation = array(
		'gp_kk_mail' => array('email')
	);
	
	private static $otherKKAddressValidation = array(
		'gp_kk_strasse' => array('not_empty'),
		'gp_kk_plz' => array('not_empty', 'plz'),
		'gp_kk_ort' => array('not_empty')
	);
	
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
		if($firstLetterValue < 9) {
			$firstLetterValue = '0' . $firstLetterValue;
		}
		$number = $firstLetterValue . substr($number, 1);
		$total = 0;
		$digits = str_split($number);
		foreach($digits as $index => $digit) {
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
	
	private static function validateRules($data, $validations, $errors = []) {
		foreach($validations as $field => $checks) {
			foreach($checks as $check) {
				$checkFunction = 'is_' . $check;
				if(!self::$checkFunction($data[$field])) {
					$errors[$field] = self::$messages[$check];
					break;
				}
			}
		}
		return $errors;
	}
	
	public static function validate($data) {
		if(self::$errors !== null) {
			return self::$errors;
		}
		
		self::$errors = self::validateRules($data, self::$basicValidations);
		if($data['gp_kasse'] == 'other') {
			self::$errors = self::validateRules($data, self::$otherKKValidation, self::$errors);
			
			if(self::is_not_empty($data['gp_kk_mail'])) {
				self::$errors = self::validateRules($data, self::$otherKKMailValidation, self::$errors);
			} else {
				self::$errors = self::validateRules($data, self::$otherKKAddressValidation, self::$errors);
			}
		}
		return self::$errors;
	}
	
	public static function isValid($data) {
		return count(self::validate($data)) === 0;
	}
	
	public static function hasError($name) {
		return self::$errors !== null && array_key_exists($name, self::$errors);
	}
	
	public static function getError($name) {
		return self::$errors[$name];
	}
}
