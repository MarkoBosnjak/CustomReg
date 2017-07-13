<?php
/**
* @package		jVitals Library
* @version		1.0
* @date			2013-09-11
* @copyright	(C) 2007 - 2013 jVitals Digital Technologies Inc. All rights reserved.
* @license    	http://www.gnu.org/copyleft/gpl.html GNU/GPLv3
* @link     	http://jvitals.com
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

class JvitalsServer {
	
	public static function request($url, $timeout = 90, $user_agent = 'Mozilla/5.0 (X11; Linux x86_64; rv:22.0) Gecko/20100101 Firefox/22.0') {
		// Add logger
		JLog::addLogger(array('text_file' => 'jvitalslib.errors.php'), JLog::ALL, 'jvitalslib');
		
		if (function_exists("curl_init")) {
			// First option cURL
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_AUTOREFERER, true);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($ch, CURLOPT_MAXREDIRS, 3);
			curl_setopt($ch, CURLOPT_HEADER, false);
			curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
			if ((int)$timeout) {
				curl_setopt($ch, CURLOPT_TIMEOUT, (int)$timeout);
			}
			curl_setopt($ch, CURLOPT_URL, $url);
			
			$data = curl_exec($ch);
			if (curl_errno($ch)) {
				JLog::add('JvitalsServer::request curl error: ' . curl_error($ch), JLog::WARNING, 'jvitalslib');
				$data = false;
			}
			curl_close($ch);
		} elseif (function_exists("fsockopen")) {
			// Second option fsockopen
			$u = JURI::getInstance($url);
			$port = $u->getPort();
			if (!$port) $port = 80;
			$sock_errno = '';
			$sock_errstr = '';
			if ((int)$timeout) {
				$sock = @fsockopen($u->getHost(), $port, $sock_errno, $sock_errstr, (int)$timeout);
			} else {
				$sock = @fsockopen($u->getHost(), $port, $sock_errno, $sock_errstr);
			}
			if (!$sock) {
				JLog::add('JvitalsServer::request fsockopen failed', JLog::WARNING, 'jvitalslib');
				$data = false;
			} else {
				$request = "GET " . $u->toString(array('path', 'query', 'fragment')) . " HTTP/1.1\r\n";
				$request .= "Host: " . $u->getHost() . "\r\n";
				$request .= "User-Agent: " . $user_agent . "\r\n";
				$request .= "Referer: http://" . $_SERVER['HTTP_HOST'] . "\r\n";
				$request .= "Connection: Close\r\n\r\n";
				fputs($sock, $request);
				
				$page = array();
				while (!feof($sock)) {
					$page[] = fgets($sock, 1024);
				}
				@fclose($sock);
				
				if (!count($page)) {
					JLog::add('JvitalsServer::request fsockopen could not read the page', JLog::WARNING, 'jvitalslib');
					$data = false;
				} else {
					$data = implode(PHP_EOL, $page);
				}
			}
		} elseif (ini_get("allow_url_fopen")) {
			// Last option file_get_contents
			$options = array('http' => array(
				'user_agent' => $user_agent,
				'max_redirects' => 3
			));
			if ((int)$timeout) {
				$options['http']['timeout'] = (int)$timeout;
			}
			$context = stream_context_create($options);
			$data = @file_get_contents($url, false, $context);
			if (!$data) {
				JLog::add('JvitalsServer::request file_get_contents could not read the page', JLog::WARNING, 'jvitalslib');
				$data = false;
			}
		} else {
			JLog::add('JvitalsServer::request All request methods failed', JLog::WARNING, 'jvitalslib');
			$data = false;
		}
		
		return $data;
	}
	
	public static function download($url, $savePath, $timeout = 90, $user_agent = 'Mozilla/5.0 (X11; Linux x86_64; rv:22.0) Gecko/20100101 Firefox/22.0') {
		// Add logger
		JLog::addLogger(array('text_file' => 'jvitalslib.errors.php'), JLog::ALL, 'jvitalslib');
		
		if (function_exists("curl_init")) {
			// First option cURL
			$fp = @fopen($savePath, "w+b");
			@flock($fp, LOCK_EX);
			
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_AUTOREFERER, true);
			curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
			if ((int)$timeout) {
				curl_setopt($ch, CURLOPT_TIMEOUT, (int)$timeout);
			}
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_FILE, $fp);
			curl_exec($ch);
			$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			curl_close($ch);
			
			@flock($fp, LOCK_UN);
			@fclose($fp);
			
			if ((int)$httpCode != 200) {
				JLog::add('JvitalsServer::download curl error: ' . $httpCode, JLog::WARNING, 'jvitalslib');
				unlink($savePath);
				return false;
			}
		} elseif (function_exists("fsockopen")) {
			// Second option fsockopen
			$u = JURI::getInstance($url);
			$port = $u->getPort();
			if (!$port) $port = 80;
			$sock_errno = '';
			$sock_errstr = '';
			if ((int)$timeout) {
				$sock = @fsockopen($u->getHost(), $port, $sock_errno, $sock_errstr, (int)$timeout);
			} else {
				$sock = @fsockopen($u->getHost(), $port, $sock_errno, $sock_errstr);
			}
			if (!$sock) {
				JLog::add('JvitalsServer::download fsockopen failed', JLog::WARNING, 'jvitalslib');
				$data = false;
			} else {
				$request = "GET " . $u->toString(array('path', 'query', 'fragment')) . " HTTP/1.1\r\n";
				$request .= "Host: " . $u->getHost() . "\r\n";
				$request .= "User-Agent: " . $user_agent . "\r\n";
				$request .= "Referer: http://" . $_SERVER['HTTP_HOST'] . "\r\n";
				$request .= "Connection: Close\r\n\r\n";
				fputs($sock, $request);
				
				$fp = @fopen($savePath, "w+b");
				@flock($fp, LOCK_EX);
				
				if ((int)$timeout) {
					stream_set_timeout($sock, (int)$timeout);
				}
				// first ignore headers
				while (!feof($sock) && fgets($sock) != "\r\n"); 
				while (!feof($sock)) {
					$contents = fgets($sock, 4096);
					fwrite($fp, $contents);
					if ((int)$timeout) {
						$info = stream_get_meta_data($sock);
						if ($info['timed_out']) { break; }
					}
				}
				
				@flock($fp, LOCK_UN);
				@fclose($fp);
				@fclose($sock);
				
				if ((int)$timeout && $info['timed_out']) {
					JLog::add('JvitalsServer::download fsockopen timed out', JLog::WARNING, 'jvitalslib');
					unlink($savePath);
					return false;
				}
			}
		} elseif (ini_get("allow_url_fopen")) {
			// Last option fopen and fwrite
			$fpRemote  = @fopen($url, "rb");
			$fp = @fopen($savePath, "w+b");
			@flock($fp, LOCK_EX);
			
			while (!feof($fpRemote)) {
				fwrite($fp, fread($fpRemote, 4096));
			}
			
			@flock($fp, LOCK_UN);
			@fclose($fp);
			@fclose($fpRemote);
		} else {
			JLog::add('JvitalsServer::download All request methods failed', JLog::WARNING, 'jvitalslib');
			return false;
		}
		
		return true;
	}
	
}



