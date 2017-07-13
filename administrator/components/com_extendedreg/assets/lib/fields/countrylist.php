<?php 
/**
 * @package		ExtendedReg
 * @version		2.03
 * @date		2013-11-18
 * @copyright	Copyright (C) 2007 - 2013 jVitals Digital Technologies Inc. All rights reserved.
 * @license		http://www.gnu.org/copyleft/gpl.html GNU/GPLv3 or later
 * @link		http://jvitals.com
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

class erFieldCountrylist extends erField implements erFieldInterface {

	function __construct($record) {
		parent::__construct($record);
		
		if (!(is_array($this->_options) && count($this->_options))) {
			$dbo = JFactory::getDBO();
			$query = $dbo->getQuery(true);
			
			$values = array(
				(int)$this->_fld->id . ', ' . $dbo->Quote('AFGHANISTAN') . ', 1',
				(int)$this->_fld->id . ', ' . $dbo->Quote('ÅLAND ISLANDS') . ', 2',
				(int)$this->_fld->id . ', ' . $dbo->Quote('ALBANIA') . ', 3',
				(int)$this->_fld->id . ', ' . $dbo->Quote('ALGERIA') . ', 4',
				(int)$this->_fld->id . ', ' . $dbo->Quote('AMERICAN SAMOA') . ', 5',
				(int)$this->_fld->id . ', ' . $dbo->Quote('ANDORRA') . ', 6',
				(int)$this->_fld->id . ', ' . $dbo->Quote('ANGOLA') . ', 7',
				(int)$this->_fld->id . ', ' . $dbo->Quote('ANGUILLA') . ', 8',
				(int)$this->_fld->id . ', ' . $dbo->Quote('ANTARCTICA') . ', 9',
				(int)$this->_fld->id . ', ' . $dbo->Quote('ANTIGUA AND BARBUDA') . ', 10',
				(int)$this->_fld->id . ', ' . $dbo->Quote('ARGENTINA') . ', 11',
				(int)$this->_fld->id . ', ' . $dbo->Quote('ARMENIA') . ', 12',
				(int)$this->_fld->id . ', ' . $dbo->Quote('ARUBA') . ', 13',
				(int)$this->_fld->id . ', ' . $dbo->Quote('AUSTRALIA') . ', 14',
				(int)$this->_fld->id . ', ' . $dbo->Quote('AUSTRIA') . ', 15',
				(int)$this->_fld->id . ', ' . $dbo->Quote('AZERBAIJAN') . ', 16',
				(int)$this->_fld->id . ', ' . $dbo->Quote('BAHAMAS') . ', 17',
				(int)$this->_fld->id . ', ' . $dbo->Quote('BAHRAIN') . ', 18',
				(int)$this->_fld->id . ', ' . $dbo->Quote('BANGLADESH') . ', 19',
				(int)$this->_fld->id . ', ' . $dbo->Quote('BARBADOS') . ', 20',
				(int)$this->_fld->id . ', ' . $dbo->Quote('BELARUS') . ', 21',
				(int)$this->_fld->id . ', ' . $dbo->Quote('BELGIUM') . ', 22',
				(int)$this->_fld->id . ', ' . $dbo->Quote('BELIZE') . ', 23',
				(int)$this->_fld->id . ', ' . $dbo->Quote('BENIN') . ', 24',
				(int)$this->_fld->id . ', ' . $dbo->Quote('BERMUDA') . ', 25',
				(int)$this->_fld->id . ', ' . $dbo->Quote('BHUTAN') . ', 26',
				(int)$this->_fld->id . ', ' . $dbo->Quote('BOLIVIA') . ', 27',
				(int)$this->_fld->id . ', ' . $dbo->Quote('BOSNIA AND HERZEGOVINA') . ', 28',
				(int)$this->_fld->id . ', ' . $dbo->Quote('BOTSWANA') . ', 29',
				(int)$this->_fld->id . ', ' . $dbo->Quote('BOUVET ISLAND') . ', 30',
				(int)$this->_fld->id . ', ' . $dbo->Quote('BRAZIL') . ', 31',
				(int)$this->_fld->id . ', ' . $dbo->Quote('BRITISH INDIAN OCEAN TERRITORY') . ', 32',
				(int)$this->_fld->id . ', ' . $dbo->Quote('BRUNEI DARUSSALAM') . ', 33',
				(int)$this->_fld->id . ', ' . $dbo->Quote('BULGARIA') . ', 34',
				(int)$this->_fld->id . ', ' . $dbo->Quote('BURKINA FASO') . ', 35',
				(int)$this->_fld->id . ', ' . $dbo->Quote('BURUNDI') . ', 36',
				(int)$this->_fld->id . ', ' . $dbo->Quote('CAMBODIA') . ', 37',
				(int)$this->_fld->id . ', ' . $dbo->Quote('CAMEROON') . ', 38',
				(int)$this->_fld->id . ', ' . $dbo->Quote('CANADA') . ', 39',
				(int)$this->_fld->id . ', ' . $dbo->Quote('CAPE VERDE') . ', 40',
				(int)$this->_fld->id . ', ' . $dbo->Quote('CAYMAN ISLANDS') . ', 41',
				(int)$this->_fld->id . ', ' . $dbo->Quote('CENTRAL AFRICAN REPUBLIC') . ', 42',
				(int)$this->_fld->id . ', ' . $dbo->Quote('CHAD') . ', 43',
				(int)$this->_fld->id . ', ' . $dbo->Quote('CHILE') . ', 44',
				(int)$this->_fld->id . ', ' . $dbo->Quote('CHINA') . ', 45',
				(int)$this->_fld->id . ', ' . $dbo->Quote('CHRISTMAS ISLAND') . ', 46',
				(int)$this->_fld->id . ', ' . $dbo->Quote('COCOS (KEELING) ISLANDS') . ', 47',
				(int)$this->_fld->id . ', ' . $dbo->Quote('COLOMBIA') . ', 48',
				(int)$this->_fld->id . ', ' . $dbo->Quote('COMOROS') . ', 49',
				(int)$this->_fld->id . ', ' . $dbo->Quote('CONGO') . ', 50',
				(int)$this->_fld->id . ', ' . $dbo->Quote('CONGO, THE DEMOCRATIC REPUBLIC OF THE') . ', 51',
				(int)$this->_fld->id . ', ' . $dbo->Quote('COOK ISLANDS') . ', 52',
				(int)$this->_fld->id . ', ' . $dbo->Quote('COSTA RICA') . ', 53',
				(int)$this->_fld->id . ', ' . $dbo->Quote('CÔTE D\'IVOIRE') . ', 54',
				(int)$this->_fld->id . ', ' . $dbo->Quote('CROATIA') . ', 55',
				(int)$this->_fld->id . ', ' . $dbo->Quote('CUBA') . ', 56',
				(int)$this->_fld->id . ', ' . $dbo->Quote('CYPRUS') . ', 57',
				(int)$this->_fld->id . ', ' . $dbo->Quote('CZECH REPUBLIC') . ', 58',
				(int)$this->_fld->id . ', ' . $dbo->Quote('DENMARK') . ', 59',
				(int)$this->_fld->id . ', ' . $dbo->Quote('DJIBOUTI') . ', 60',
				(int)$this->_fld->id . ', ' . $dbo->Quote('DOMINICA') . ', 61',
				(int)$this->_fld->id . ', ' . $dbo->Quote('DOMINICAN REPUBLIC') . ', 62',
				(int)$this->_fld->id . ', ' . $dbo->Quote('ECUADOR') . ', 63',
				(int)$this->_fld->id . ', ' . $dbo->Quote('EGYPT') . ', 64',
				(int)$this->_fld->id . ', ' . $dbo->Quote('EL SALVADOR') . ', 65',
				(int)$this->_fld->id . ', ' . $dbo->Quote('EQUATORIAL GUINEA') . ', 66',
				(int)$this->_fld->id . ', ' . $dbo->Quote('ERITREA') . ', 67',
				(int)$this->_fld->id . ', ' . $dbo->Quote('ESTONIA') . ', 68',
				(int)$this->_fld->id . ', ' . $dbo->Quote('ETHIOPIA') . ', 69',
				(int)$this->_fld->id . ', ' . $dbo->Quote('FALKLAND ISLANDS (MALVINAS)') . ', 70',
				(int)$this->_fld->id . ', ' . $dbo->Quote('FAROE ISLANDS') . ', 71',
				(int)$this->_fld->id . ', ' . $dbo->Quote('FIJI') . ', 72',
				(int)$this->_fld->id . ', ' . $dbo->Quote('FINLAND') . ', 73',
				(int)$this->_fld->id . ', ' . $dbo->Quote('FRANCE') . ', 74',
				(int)$this->_fld->id . ', ' . $dbo->Quote('FRENCH GUIANA') . ', 75',
				(int)$this->_fld->id . ', ' . $dbo->Quote('FRENCH POLYNESIA') . ', 76',
				(int)$this->_fld->id . ', ' . $dbo->Quote('FRENCH SOUTHERN TERRITORIES') . ', 77',
				(int)$this->_fld->id . ', ' . $dbo->Quote('GABON') . ', 78',
				(int)$this->_fld->id . ', ' . $dbo->Quote('GAMBIA') . ', 79',
				(int)$this->_fld->id . ', ' . $dbo->Quote('GEORGIA') . ', 80',
				(int)$this->_fld->id . ', ' . $dbo->Quote('GERMANY') . ', 81',
				(int)$this->_fld->id . ', ' . $dbo->Quote('GHANA') . ', 82',
				(int)$this->_fld->id . ', ' . $dbo->Quote('GIBRALTAR') . ', 83',
				(int)$this->_fld->id . ', ' . $dbo->Quote('GREECE') . ', 84',
				(int)$this->_fld->id . ', ' . $dbo->Quote('GREENLAND') . ', 85',
				(int)$this->_fld->id . ', ' . $dbo->Quote('GRENADA') . ', 86',
				(int)$this->_fld->id . ', ' . $dbo->Quote('GUADELOUPE') . ', 87',
				(int)$this->_fld->id . ', ' . $dbo->Quote('GUAM') . ', 88',
				(int)$this->_fld->id . ', ' . $dbo->Quote('GUATEMALA') . ', 89',
				(int)$this->_fld->id . ', ' . $dbo->Quote('GUERNSEY') . ', 90',
				(int)$this->_fld->id . ', ' . $dbo->Quote('GUINEA') . ', 91',
				(int)$this->_fld->id . ', ' . $dbo->Quote('GUINEA-BISSAU') . ', 92',
				(int)$this->_fld->id . ', ' . $dbo->Quote('GUYANA') . ', 93',
				(int)$this->_fld->id . ', ' . $dbo->Quote('HAITI') . ', 94',
				(int)$this->_fld->id . ', ' . $dbo->Quote('HEARD ISLAND AND MCDONALD ISLANDS') . ', 95',
				(int)$this->_fld->id . ', ' . $dbo->Quote('HOLY SEE (VATICAN CITY STATE)') . ', 96',
				(int)$this->_fld->id . ', ' . $dbo->Quote('HONDURAS') . ', 97',
				(int)$this->_fld->id . ', ' . $dbo->Quote('HONG KONG') . ', 98',
				(int)$this->_fld->id . ', ' . $dbo->Quote('HUNGARY') . ', 99',
				(int)$this->_fld->id . ', ' . $dbo->Quote('ICELAND') . ', 100',
				(int)$this->_fld->id . ', ' . $dbo->Quote('INDIA') . ', 101',
				(int)$this->_fld->id . ', ' . $dbo->Quote('INDONESIA') . ', 102',
				(int)$this->_fld->id . ', ' . $dbo->Quote('IRAN, ISLAMIC REPUBLIC OF') . ', 103',
				(int)$this->_fld->id . ', ' . $dbo->Quote('IRAQ') . ', 104',
				(int)$this->_fld->id . ', ' . $dbo->Quote('IRELAND') . ', 105',
				(int)$this->_fld->id . ', ' . $dbo->Quote('ISLE OF MAN') . ', 106',
				(int)$this->_fld->id . ', ' . $dbo->Quote('ISRAEL') . ', 107',
				(int)$this->_fld->id . ', ' . $dbo->Quote('ITALY') . ', 108',
				(int)$this->_fld->id . ', ' . $dbo->Quote('JAMAICA') . ', 109',
				(int)$this->_fld->id . ', ' . $dbo->Quote('JAPAN') . ', 110',
				(int)$this->_fld->id . ', ' . $dbo->Quote('JERSEY') . ', 111',
				(int)$this->_fld->id . ', ' . $dbo->Quote('JORDAN') . ', 112',
				(int)$this->_fld->id . ', ' . $dbo->Quote('KAZAKHSTAN') . ', 113',
				(int)$this->_fld->id . ', ' . $dbo->Quote('KENYA') . ', 114',
				(int)$this->_fld->id . ', ' . $dbo->Quote('KIRIBATI') . ', 115',
				(int)$this->_fld->id . ', ' . $dbo->Quote('KOREA, DEMOCRATIC PEOPLE\'S REPUBLIC OF') . ', 116',
				(int)$this->_fld->id . ', ' . $dbo->Quote('KOREA, REPUBLIC OF') . ', 117',
				(int)$this->_fld->id . ', ' . $dbo->Quote('KUWAIT') . ', 118',
				(int)$this->_fld->id . ', ' . $dbo->Quote('KYRGYZSTAN') . ', 119',
				(int)$this->_fld->id . ', ' . $dbo->Quote('LAO PEOPLE\'S DEMOCRATIC REPUBLIC') . ', 120',
				(int)$this->_fld->id . ', ' . $dbo->Quote('LATVIA') . ', 121',
				(int)$this->_fld->id . ', ' . $dbo->Quote('LEBANON') . ', 122',
				(int)$this->_fld->id . ', ' . $dbo->Quote('LESOTHO') . ', 123',
				(int)$this->_fld->id . ', ' . $dbo->Quote('LIBERIA') . ', 124',
				(int)$this->_fld->id . ', ' . $dbo->Quote('LIBYAN ARAB JAMAHIRIYA') . ', 125',
				(int)$this->_fld->id . ', ' . $dbo->Quote('LIECHTENSTEIN') . ', 126',
				(int)$this->_fld->id . ', ' . $dbo->Quote('LITHUANIA') . ', 127',
				(int)$this->_fld->id . ', ' . $dbo->Quote('LUXEMBOURG') . ', 128',
				(int)$this->_fld->id . ', ' . $dbo->Quote('MACAO') . ', 129',
				(int)$this->_fld->id . ', ' . $dbo->Quote('MACEDONIA, THE FORMER YUGOSLAV REPUBLIC OF') . ', 130',
				(int)$this->_fld->id . ', ' . $dbo->Quote('MADAGASCAR') . ', 131',
				(int)$this->_fld->id . ', ' . $dbo->Quote('MALAWI') . ', 132',
				(int)$this->_fld->id . ', ' . $dbo->Quote('MALAYSIA') . ', 133',
				(int)$this->_fld->id . ', ' . $dbo->Quote('MALDIVES') . ', 134',
				(int)$this->_fld->id . ', ' . $dbo->Quote('MALI') . ', 135',
				(int)$this->_fld->id . ', ' . $dbo->Quote('MALTA') . ', 136',
				(int)$this->_fld->id . ', ' . $dbo->Quote('MARSHALL ISLANDS') . ', 137',
				(int)$this->_fld->id . ', ' . $dbo->Quote('MARTINIQUE') . ', 138',
				(int)$this->_fld->id . ', ' . $dbo->Quote('MAURITANIA') . ', 139',
				(int)$this->_fld->id . ', ' . $dbo->Quote('MAURITIUS') . ', 140',
				(int)$this->_fld->id . ', ' . $dbo->Quote('MAYOTTE') . ', 141',
				(int)$this->_fld->id . ', ' . $dbo->Quote('MEXICO') . ', 142',
				(int)$this->_fld->id . ', ' . $dbo->Quote('MICRONESIA, FEDERATED STATES OF') . ', 143',
				(int)$this->_fld->id . ', ' . $dbo->Quote('MOLDOVA') . ', 144',
				(int)$this->_fld->id . ', ' . $dbo->Quote('MONACO') . ', 145',
				(int)$this->_fld->id . ', ' . $dbo->Quote('MONGOLIA') . ', 146',
				(int)$this->_fld->id . ', ' . $dbo->Quote('MONTENEGRO') . ', 147',
				(int)$this->_fld->id . ', ' . $dbo->Quote('MONTSERRAT') . ', 148',
				(int)$this->_fld->id . ', ' . $dbo->Quote('MOROCCO') . ', 149',
				(int)$this->_fld->id . ', ' . $dbo->Quote('MOZAMBIQUE') . ', 150',
				(int)$this->_fld->id . ', ' . $dbo->Quote('MYANMAR') . ', 151',
				(int)$this->_fld->id . ', ' . $dbo->Quote('NAMIBIA') . ', 152',
				(int)$this->_fld->id . ', ' . $dbo->Quote('NAURU') . ', 153',
				(int)$this->_fld->id . ', ' . $dbo->Quote('NEPAL') . ', 154',
				(int)$this->_fld->id . ', ' . $dbo->Quote('NETHERLANDS') . ', 155',
				(int)$this->_fld->id . ', ' . $dbo->Quote('NETHERLANDS ANTILLES') . ', 156',
				(int)$this->_fld->id . ', ' . $dbo->Quote('NEW CALEDONIA') . ', 157',
				(int)$this->_fld->id . ', ' . $dbo->Quote('NEW ZEALAND') . ', 158',
				(int)$this->_fld->id . ', ' . $dbo->Quote('NICARAGUA') . ', 159',
				(int)$this->_fld->id . ', ' . $dbo->Quote('NIGER') . ', 160',
				(int)$this->_fld->id . ', ' . $dbo->Quote('NIGERIA') . ', 161',
				(int)$this->_fld->id . ', ' . $dbo->Quote('NIUE') . ', 162',
				(int)$this->_fld->id . ', ' . $dbo->Quote('NORFOLK ISLAND') . ', 163',
				(int)$this->_fld->id . ', ' . $dbo->Quote('NORTHERN MARIANA ISLANDS') . ', 164',
				(int)$this->_fld->id . ', ' . $dbo->Quote('NORWAY') . ', 165',
				(int)$this->_fld->id . ', ' . $dbo->Quote('OMAN') . ', 166',
				(int)$this->_fld->id . ', ' . $dbo->Quote('PAKISTAN') . ', 167',
				(int)$this->_fld->id . ', ' . $dbo->Quote('PALAU') . ', 168',
				(int)$this->_fld->id . ', ' . $dbo->Quote('PALESTINIAN TERRITORY, OCCUPIED') . ', 169',
				(int)$this->_fld->id . ', ' . $dbo->Quote('PANAMA') . ', 170',
				(int)$this->_fld->id . ', ' . $dbo->Quote('PAPUA NEW GUINEA') . ', 171',
				(int)$this->_fld->id . ', ' . $dbo->Quote('PARAGUAY') . ', 172',
				(int)$this->_fld->id . ', ' . $dbo->Quote('PERU') . ', 173',
				(int)$this->_fld->id . ', ' . $dbo->Quote('PHILIPPINES') . ', 174',
				(int)$this->_fld->id . ', ' . $dbo->Quote('PITCAIRN') . ', 175',
				(int)$this->_fld->id . ', ' . $dbo->Quote('POLAND') . ', 176',
				(int)$this->_fld->id . ', ' . $dbo->Quote('PORTUGAL') . ', 177',
				(int)$this->_fld->id . ', ' . $dbo->Quote('PUERTO RICO') . ', 178',
				(int)$this->_fld->id . ', ' . $dbo->Quote('QATAR') . ', 179',
				(int)$this->_fld->id . ', ' . $dbo->Quote('RÉUNION') . ', 180',
				(int)$this->_fld->id . ', ' . $dbo->Quote('ROMANIA') . ', 181',
				(int)$this->_fld->id . ', ' . $dbo->Quote('RUSSIAN FEDERATION') . ', 182',
				(int)$this->_fld->id . ', ' . $dbo->Quote('RWANDA') . ', 183',
				(int)$this->_fld->id . ', ' . $dbo->Quote('SAINT BARTHÉLEMY') . ', 184',
				(int)$this->_fld->id . ', ' . $dbo->Quote('SAINT HELENA') . ', 185',
				(int)$this->_fld->id . ', ' . $dbo->Quote('SAINT KITTS AND NEVIS') . ', 186',
				(int)$this->_fld->id . ', ' . $dbo->Quote('SAINT LUCIA') . ', 187',
				(int)$this->_fld->id . ', ' . $dbo->Quote('SAINT MARTIN') . ', 188',
				(int)$this->_fld->id . ', ' . $dbo->Quote('SAINT PIERRE AND MIQUELON') . ', 189',
				(int)$this->_fld->id . ', ' . $dbo->Quote('SAINT VINCENT AND THE GRENADINES') . ', 190',
				(int)$this->_fld->id . ', ' . $dbo->Quote('SAMOA') . ', 191',
				(int)$this->_fld->id . ', ' . $dbo->Quote('SAN MARINO') . ', 192',
				(int)$this->_fld->id . ', ' . $dbo->Quote('SAO TOME AND PRINCIPE') . ', 193',
				(int)$this->_fld->id . ', ' . $dbo->Quote('SAUDI ARABIA') . ', 194',
				(int)$this->_fld->id . ', ' . $dbo->Quote('SENEGAL') . ', 195',
				(int)$this->_fld->id . ', ' . $dbo->Quote('SERBIA') . ', 196',
				(int)$this->_fld->id . ', ' . $dbo->Quote('SEYCHELLES') . ', 197',
				(int)$this->_fld->id . ', ' . $dbo->Quote('SIERRA LEONE') . ', 198',
				(int)$this->_fld->id . ', ' . $dbo->Quote('SINGAPORE') . ', 199',
				(int)$this->_fld->id . ', ' . $dbo->Quote('SLOVAKIA') . ', 200',
				(int)$this->_fld->id . ', ' . $dbo->Quote('SLOVENIA') . ', 201',
				(int)$this->_fld->id . ', ' . $dbo->Quote('SOLOMON ISLANDS') . ', 202',
				(int)$this->_fld->id . ', ' . $dbo->Quote('SOMALIA') . ', 203',
				(int)$this->_fld->id . ', ' . $dbo->Quote('SOUTH AFRICA') . ', 204',
				(int)$this->_fld->id . ', ' . $dbo->Quote('SOUTH GEORGIA AND THE SOUTH SANDWICH ISLANDS') . ', 205',
				(int)$this->_fld->id . ', ' . $dbo->Quote('SPAIN') . ', 206',
				(int)$this->_fld->id . ', ' . $dbo->Quote('SRI LANKA') . ', 207',
				(int)$this->_fld->id . ', ' . $dbo->Quote('SUDAN') . ', 208',
				(int)$this->_fld->id . ', ' . $dbo->Quote('SURINAME') . ', 209',
				(int)$this->_fld->id . ', ' . $dbo->Quote('SVALBARD AND JAN MAYEN') . ', 210',
				(int)$this->_fld->id . ', ' . $dbo->Quote('SWAZILAND') . ', 211',
				(int)$this->_fld->id . ', ' . $dbo->Quote('SWEDEN') . ', 212',
				(int)$this->_fld->id . ', ' . $dbo->Quote('SWITZERLAND') . ', 213',
				(int)$this->_fld->id . ', ' . $dbo->Quote('SYRIAN ARAB REPUBLIC') . ', 214',
				(int)$this->_fld->id . ', ' . $dbo->Quote('TAIWAN, PROVINCE OF CHINA') . ', 215',
				(int)$this->_fld->id . ', ' . $dbo->Quote('TAJIKISTAN') . ', 216',
				(int)$this->_fld->id . ', ' . $dbo->Quote('TANZANIA, UNITED REPUBLIC OF') . ', 217',
				(int)$this->_fld->id . ', ' . $dbo->Quote('THAILAND') . ', 218',
				(int)$this->_fld->id . ', ' . $dbo->Quote('TIMOR-LESTE') . ', 219',
				(int)$this->_fld->id . ', ' . $dbo->Quote('TOGO') . ', 220',
				(int)$this->_fld->id . ', ' . $dbo->Quote('TOKELAU') . ', 221',
				(int)$this->_fld->id . ', ' . $dbo->Quote('TONGA') . ', 222',
				(int)$this->_fld->id . ', ' . $dbo->Quote('TRINIDAD AND TOBAGO') . ', 223',
				(int)$this->_fld->id . ', ' . $dbo->Quote('TUNISIA') . ', 224',
				(int)$this->_fld->id . ', ' . $dbo->Quote('TURKEY') . ', 225',
				(int)$this->_fld->id . ', ' . $dbo->Quote('TURKMENISTAN') . ', 226',
				(int)$this->_fld->id . ', ' . $dbo->Quote('TURKS AND CAICOS ISLANDS') . ', 227',
				(int)$this->_fld->id . ', ' . $dbo->Quote('TUVALU') . ', 228',
				(int)$this->_fld->id . ', ' . $dbo->Quote('UGANDA') . ', 229',
				(int)$this->_fld->id . ', ' . $dbo->Quote('UKRAINE') . ', 230',
				(int)$this->_fld->id . ', ' . $dbo->Quote('UNITED ARAB EMIRATES') . ', 231',
				(int)$this->_fld->id . ', ' . $dbo->Quote('UNITED KINGDOM') . ', 232',
				(int)$this->_fld->id . ', ' . $dbo->Quote('UNITED STATES') . ', 233',
				(int)$this->_fld->id . ', ' . $dbo->Quote('UNITED STATES MINOR OUTLYING ISLANDS') . ', 234',
				(int)$this->_fld->id . ', ' . $dbo->Quote('URUGUAY') . ', 235',
				(int)$this->_fld->id . ', ' . $dbo->Quote('UZBEKISTAN') . ', 236',
				(int)$this->_fld->id . ', ' . $dbo->Quote('VANUATU') . ', 237',
				(int)$this->_fld->id . ', ' . $dbo->Quote('VENEZUELA') . ', 238',
				(int)$this->_fld->id . ', ' . $dbo->Quote('VIET NAM') . ', 239',
				(int)$this->_fld->id . ', ' . $dbo->Quote('VIRGIN ISLANDS, BRITISH') . ', 240',
				(int)$this->_fld->id . ', ' . $dbo->Quote('VIRGIN ISLANDS, U.S.') . ', 241',
				(int)$this->_fld->id . ', ' . $dbo->Quote('WALLIS AND FUTUNA') . ', 242',
				(int)$this->_fld->id . ', ' . $dbo->Quote('WESTERN SAHARA') . ', 243',
				(int)$this->_fld->id . ', ' . $dbo->Quote('YEMEN') . ', 244',
				(int)$this->_fld->id . ', ' . $dbo->Quote('ZAMBIA') . ', 245',
				(int)$this->_fld->id . ', ' . $dbo->Quote('ZIMBABWE') . ', 246',
			);
			$query->clear()->insert('#__extendedreg_fields_values')
				->columns(array($dbo->quoteName('field_id'), $dbo->quoteName('val'), $dbo->quoteName('ord')))
				->values($values);
			$dbo->setQuery($query);
			try {
				if (!$dbo->execute()) {
					throw new RuntimeException(JText::_('COM_EXTENDEDREG_DATABASE_ERROR'), 83111);
					jexit();
				}
			} catch (RuntimeException $e) {
				throw new RuntimeException(JText::_('COM_EXTENDEDREG_DATABASE_ERROR'), 83111);
				jexit();
			}
			$this->_options = $this->_model->getFieldOpts((int)$this->_fld->id);
		}
	}
	
	public function getSqlType() {
		return  "tinytext";
	}
	
	public function hasParams() {
		return true;
	}
	
	public function hasOptions() {
		return true;
	}
	
	public function isMultiselect() {
		return false;
	}
	
	public function serversideValidation(&$post) {
		$result = true;
		$errmsg = '';
		$post[$this->_fld->name] = trim($post[$this->_fld->name]);

		$filter = new JFilterInput();
		$post[$this->_fld->name] = $filter->clean($post[$this->_fld->name]);
		
		if ((int)$this->_fld->required && !mb_strlen($post[$this->_fld->name])) {
			$errmsg .= JText::sprintf('COM_EXTENDEDREG_IS_REQUIRED_ERROR', JText::_($this->_fld->title)) . "\n";
			$result = false;
		}
		if (mb_strlen($post[$this->_fld->name])) {
			$validations = erHelperAddons::loadAddons('validation');
			if ($validations) {
				$selval = (array)$this->_params->get('validations');
				foreach ($validations as $lib) {
					if ($selval && is_array($selval) && in_array((int)$lib->id, $selval)) {
						$obj = erHelperAddons::getFieldValidation($lib, $this->_fld);
						if (!$obj->validate($post[$this->_fld->name], $post)) {
							$errmsg .= $obj->getError() . "\n";
							$result = false;
						}
					}
				}
			}
		}
		if (!$result) {
			$this->setError($errmsg);
			return false;
		}
		return true;
	}
	
	public function getHtml($value, $id = null) {
		$conf = $this->_model->getConfObj();
		$class = $this->_params->get('input_size', trim($conf->css_default_input_class));
		if ((int)$this->_fld->required) $class .= ' required';
		if (!mb_strlen(trim($value))) $value = '';
		$value = html_entity_decode($value);
		$this->getJavascptValidation();
		
		$result = '<select' . (trim($class) ? ' class="' . $class . '"' : '') . (trim($id) ? ' id="' . $id . '"' : '') . ' name="' . $this->_fld->name . '">';
		if (is_array($this->_options) && count($this->_options)) {
			foreach ($this->_options as $opt) {
				$result .= '<option value="' . htmlspecialchars($opt->val) . '"' . ($value == $opt->val ? ' selected' : '') . '>' . JText::_($opt->val) . '</option>';
			}
		}
		$result .= '</select>';
		return $result;
	}
	
	public function getSearchHtml($value, $name = '') {
		if (!mb_strlen(trim($value))) $value = '';
		$value = html_entity_decode($value);
		if (!trim($name)) $name = $this->_fld->name;
		
		$result = '<select class="inputbox" name="' . $name . '">';
		$result .= '<option value=""' . ($value == '' ? ' selected' : '') . '>-</option>';
		if (is_array($this->_options) && count($this->_options)) {
			foreach ($this->_options as $opt) {
				$result .= '<option value="' . htmlspecialchars($opt->val) . '"' . ($value == $opt->val ? ' selected' : '') . '>' . JText::_($opt->val) . '</option>';
			}
		}
		$result .= '</select>';
		return $result;
	}
	
}