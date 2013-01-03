<?php
/*
 * 
 * Copyright (c) 2012, Martin Vondra
 * All Rights Reserved.
 * DESCRIPTION
 */

namespace Leolos\Status;
 
/**
* Status
* @author Martin Vondra <martin.vondra@email.cz>
* Class with HTTP1.1 rfc2616 codes
* @see http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html
* 
* You need to configure apache configuration
* and use directive 'ErrorDocument' 
* @see http://httpd.apache.org/docs/2.2/custom-error.html
* 
* 
* Class includes tha base status set, if you need use special one, pls. 
* write it down  and commit :)
*/
class Status {
	
	static function printPage($pageString) {
		if ($pageString) echo $pageString;
	}

	/**
	* 1xx - not usefull yet
	*/

	
	/** 
	* 2xx status
	* 200 OK The most used status - only end script
	*/
	static function OK($page='', $expire=0) {
		header("HTTP/1.0 200 OK");
		// set main headers for caching at proxy
		if ($expire > 0) {
			header('Cache-Control: max-age='.$expire.', s-maxage='.$expire.', public, must-revalidate');
			header('Expires: '.gmdate('D, d M Y H:i:s', time()+$expire).' GMT');

		// dont cache
		} elseif ( $expire < 0) {
			header('Cache-Control: no-cache, max-age=0, s-maxage=0, public, must-revalidate');
			header('Expires: '.gmdate('D, d M Y H:i:s', time() - 3600*2).' GMT');
			header('Last-Modified: '.gmdate('D, d M Y H:i:s', time() - 3600*2).' GMT');
			header('Content-Length: '.strlen($page));
		}

		Status::printPage($page);
	}


	/**
	* 3xx status
	* Redirect - another usefull status
	*/
	static function REDIRECT($url) {
		header("HTTP/1.0 302 Found");
		header("Location: $url");
	}


	/**
	* 301 status
	* Moved Permanently - use this url in the future
	*/
	static function MOVED_PERMANENTLY($url) {
		header("HTTP/1.0 301 Moved Permanently");
		header("Location: $url");
	}

	
	/**
	* 4xx status
	* Forbidden, NotFound, MethodNotAllowed etc.
	*	  
	*/
	static function FORBIDDEN($page='') {
		header("HTTP/1.0 403 Forbidden");
		Status::printPage($page);
	}
	
	
	static function NOT_FOUND($page='') {
		header("HTTP/1.0 404 Not Found");
		Status::printPage($page);
	}


	static function METHOD_NOT_ALLOWED($page='') {
		header("HTTP/1.0 405 Method Not Allowed");
		Status::printPage($page);
	}


	/**
	* 5xx server errors
	* 
	*/
	static function INTERNAL_SERVER_ERROR($page='') {
		header("HTTP/1.0 500 Internal Server Error");
		Status::printPage($page);
	}
}
