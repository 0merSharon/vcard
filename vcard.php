<?php

/**
 * VCard generator - can save to file or output as a download
 *
 * @author Jeroen Desloovere <jeroen@siesqo.be>
 */	
class VCard
{
	/**
	 * Filename
	 *
	 * @param string
	 */
	private $filename;

	/**
	 * Properties
	 *
	 * @param array
	 */
	private $properties;

	/**
	 * Add address
	 *
	 * @param string[optional] $name
	 * @param string[optional] $extended
	 * @param string[optional] $street
	 * @param string[optional] $city
	 * @param string[optional] $region
	 * @param string[optional] $zip
	 * @param string[optional] $country
	 * @param string[optional] $type $type may be DOM | INTL | POSTAL | PARCEL | HOME | WORK or any combination of these: e.g. "WORK;PARCEL;POSTAL"
	 */
	public function addAddress($name = '', $extended = '', $street = '', $city = '', $region = '', $zip = '', $country = '', $type = 'WORK;POSTAL')
	{
		// init value
		$value = $name . ';' . $extended . ';' . $street . ';' . $city . ';' . $region . ';' . $zip . ';' . $country;

		// set property
		$this->setProperty('ADR' . (($type != '') ? ';' . $type : ''), $value);
	}

	/**
	 * Add birthday
	 *
	 * @param string $date Format is YYYY-MM-DD
	 */
	public function addBirthday($date)
	{
		$this->setProperty('BDAY', $date);
	}

	/**
	 * Add company
	 *
	 * @param string $company
	 */
	public function addCompany($company)
	{
		$this->setProperty('ORG', $company);

		// if filename is empty, add to filename
		if(empty($this->filename)) $this->setFilename($company);
	}

	/**
	 * Add email
	 *
	 * @param string $date Format is YYYY-MM-DD
	 */
	public function addEmail($address)
	{
		$this->setProperty('EMAIL;INTERNET', $address);
	}

	/**
	 * Add jobtitle
	 *
	 * @param string $jobtitle The jobtitle for the person.
	 */
	public function addJobtitle($jobtitle)
	{ 
		$this->setProperty('TITLE', $jobtitle);
	}

	/**
	 * Add name
	 *
	 * @param string[optional] $lastname
	 * @param string[optional] $firstname
	 * @param string[optional] $additional
	 * @param string[optional] $prefix
	 * @param string[optional] $suffix
	 */
	public function addName($lastname = '', $firstname = '', $additional = '', $prefix = '', $suffix = '')
	{
		// define filename
		$this->setFilename(array($prefix, $firstname, $additional, $lastname, $suffix));

		// set property
		$this->setProperty('N', $lastname . ';' . $firstname . ';' . $additional . ';' . $prefix . ';' . $suffix);

		// is property FN set?
		if(!isset($this->properties['FN']) || $this->properties['FN'] == '')
		{
			// set property
			$this->setProperty('FN', trim($prefix . ' ' . $firstname . ' ' . $additional . ' ' . $lastname . ' ' . $suffix));	
		}
	}

	/**
	 * Add note
	 *
	 * @param string $note
	 */
	public function addNote($note)
	{
		$this->setProperty('NOTE', $note);
	}

	/**
	 * Add phone number
	 *
	 * @param string $number
	 * @param string[optional] $type Type may be PREF | WORK | HOME | VOICE | FAX | MSG | CELL | PAGER | BBS | CAR | MODEM | ISDN | VIDEO or any senseful combination, e.g. "PREF;WORK;VOICE"
	 */
	public function addPhoneNumber($number, $type = '')
	{
		$this->setProperty('TEL' . (($type != '') ? ';' . $type : ''), $number);
	}

	/**
	 * Add URL
	 *
	 * @param string $url
	 * @param string[optional] $type Type may be WORK | HOME
	 */
	public function addURL($url, $type = '')
	{
		$this->setProperty('URL' . (($type != '') ? ';' . $type : ''), $url);
	}

	/**
	 * Build VCard (.vcf)
	 *
	 * @return string
	 */
	protected function buildVCard()
	{
		// init string
		$string = "BEGIN:VCARD\r\n";
		$string .= "VERSION:3.0\r\n";
		$string .= "REV:" . date("Y-m-d") . "T" . date("H:i:s") . "Z\r\n";

		// loop all properties
		foreach($this->properties as $key => $value)
		{
			// add to string
			$string .= $key . ':' . $value . "\r\n";
		}

		// add to string
		$string .= "END:VCARD\r\n";

		// return
		return $string;
	}

	/**
	 * Build VCalender (.ics) - Safari (iOS) can not open .vcf files, so we build a workaround.
	 *
	 * @return string
	 */
	protected function buildVCalendar()
	{
		// init dates
		$dtstart = date("Ymd")."T".date("Hi")."00";		
		$dtend = date("Ymd")."T".date("Hi")."01";
	
		// init string
		$string = "BEGIN:VCALENDAR\n";
		$string .= "VERSION:2.0\n";
		$string .= "BEGIN:VEVENT\n";
		$string .= "DTSTART;TZID=Europe/London:" . $dtstart . "\n";	
		$string .= "DTEND;TZID=Europe/London:" . $dtend . "\n";
		$string .= "SUMMARY:Click attached contact below to save to your contacts\n";
		$string .= "DTSTAMP:" . $dtstart . "Z\n";
		$string .= "ATTACH;VALUE=BINARY;ENCODING=BASE64;FMTTYPE=text/directory;\n";
		$string .= " X-APPLE-FILENAME=" . $this->filename . ".vcf:\n";

		// base64 encode it so that it can be used as an attachemnt to the "dummy" calendar appointment
		$b64vcard = base64_encode($this->buildVCard());

		// chunk the single long line of b64 text in accordance with RFC2045
		// (and the exact line length determined from the original .ics file exported from Apple calendar
		$b64mline = chunk_split($b64vcard, 74, "\n");
		
		// need to indent all the lines by 1 space for the iphone (yes really?!!)
		$b64final = preg_replace('/(.+)/', ' $1', $b64mline);		
		$string .= $b64final;

		// output the correctly formatted encoded text
		$string .= "END:VEVENT\n";		
		$string .= "END:VCALENDAR\n";

		// return
		return $string;
	}

	/**
	 * Decode
	 *
	 * @param string $value
	 * @return string decoded
	 */
	private function decode($value)
	{
		return htmlspecialchars_decode((string) $value, ENT_QUOTES);
	}

	/**
	 * Download
	 */
	public function download()
	{
		// iOS devices
		if($this->isIOS())
		{
			// define output
			$output = $this->buildVCalendar();

			# Send correct headers
			header('Content-type: text/x-vcalendar; charset=utf-8');
			// header('Content-type: application/octet-stream; charset=utf-8');
			// Alternatively: application/octet-stream
			// Depending on the desired browser behaviour
			// Be sure to test thoroughly cross-browser
			header('Content-Disposition: attachment; filename=' . $this->filename . '.ics;');
		}

		// non-iOS devices
		else
		{
			// define output
			$output = $this->buildVCard();

			// output to file
			header('Content-type:text/x-vcard; charset=UTF-8');
			header('Content-Disposition: attachment; filename=' . $this->filename . '.vcf;');
		}

		header('Content-Length: ' . strlen($output));
		header('Connection: close');
		echo $output;
	}

	/**
	 * Get output as string
	 */
	public function get()
	{
		// return output for iOS devices or for non-iOS devices
		return ($this->isIOS()) ? $this->buildVCalendar() : $this->buildVCard();
	}

	/**
	 * Get filename
	 *
	 * @return string
	 */
	public function getFilename()
	{
		return $this->filename;
	}

	/**
	 * Is iOS?
	 *
	 * @return string ios
	 */
	public function isIOS()
	{
		// get user agent
		$browser = strtolower($_SERVER['HTTP_USER_AGENT']);

		// return bool
		return (strpos($browser,'iphone') || strpos($browser,'ipod') || strpos($browser,'ipad')) ? true : false;
	}

	/**
	 * Save
	 */
	public function save()
	{
		// iOS devices - save to file as .ics
		if($this->isIOS()) file_put_contents($this->filename . '.ics', $this->buildVCalendar());

		// non-iOS devices - save to file as .vcf
		else file_put_contents($this->filename . '.vcf', $this->buildVCard());
	}

	/**
	 * Set filename
	 *
	 * @param mixed $value
	 * @param bool $overwrite
	 * @param string[optional] $separator
	 */
	public function setFilename($value, $overwrite = true, $separator = '_')
	{
		// recast to string if $value is array
		if(is_array($value)) $value = implode($separator, $value);

		// trim unneeded values
		$value = trim($value, $separator);

		// remove all spaces
		$value = preg_replace('/\s+/', $separator, $value);

		// if value is empty, stop here
		if(empty($value)) return false;

		// decode value + lowercase the string
		$value = strtolower($this->decode($value));

		// overwrite filename or add to filename using a prefix in between
		$this->filename = ($overwrite) ? $value : $this->filename . $separator . $value;
	}

	/**
	 * Set property
	 *
	 * @param string $key
	 * @param string $value
	 */
	private function setProperty($key, $value)
	{
		// set decoded property
		$this->properties[$key] = $this->decode($value);
	}
}

/**
 * VCard Exception class
 *
 * @author Jeroen Desloovere <jeroen@siesqo.be>
 */
class VCardException extends Exception
{
}
