<?php

/**
* @package		jVitals Library
* @version		1.0
* @date			2013-09-11
* @copyright	(C) 2007 - 2013 jVitals Digital Technologies Inc. All rights reserved.
* @license    	http://www.gnu.org/copyleft/gpl.html GNU/GPLv3
* @link     	http://jvitals.com

* Based on Gravatar 1.1 (https://github.com/tylerchr/PHP-Gravatar) by Tyler Christensen <tyler9xp@gmail.com>
* Based on Gravatar 1.0 (http://www.phpclasses.org/browse/package/4227.html), Copyright (C) Lucas Araújo <araujo.lucas@gmail.com>
*/

class JvitalsGravatar {
    /**
     *    Gravatar's url
     */
    const GRAVATAR_URL = "http://www.gravatar.com/avatar/";

    /**
     *    Ratings available
     */
    private $GRAVATAR_RATING = array("G", "PG", "R", "X");
    
    /**
     *    Query string. key/value
     */
    protected $properties = array(
        "gravatar_id"       => NULL,
        "default"           => NULL,
        "size"              => 80,        // The default value
        "rating"            => NULL,
        "border"            => NULL,
        'd'                 => 404
    );

    /**
     *    E-mail. This will be converted to md5($email)
     */
    protected $email = "";

    /**
     *    Extra attributes to the IMG tag like ALT, CLASS, STYLE...
     */
    protected $extra = "";

    public function __construct($email=NULL, $default=NULL) {
        $this->setEmail($email);
        $this->setDefault($default);
    }
    
    public function gravatarExists() {
        $url = $this->gravatarLink();
        
        if ($headers = @get_headers($url)) {
            if (stripos($headers[0], '200 OK')) {
                return true;
            } else if (stripos($headers[0], '404 Not Found')) {
                return false;
            }
        } else {
            // Failed to access the Gravatar URL
            // echo "Could not determine, failed to access URL";
            
            // Safer to assume it does NOT exist
            return false;
        }
    }

    public function setEmail($email) {
        if ($this->isValidEmail($email)) {
            $this->email = $email;
            $this->properties['gravatar_id'] = md5(strtolower(trim($this->email)));
            return true;
        }
        return false;
    }

    public function setDefault($default) {
        $this->properties['default'] = $default;
        return true;
    }

    public function setRating($rating) {
        if (in_array($rating, $this->GRAVATAR_RATING)) {
            $this->properties['rating'] = $rating;
            return true;
        }
        return false;
    }

    public function setSize($size) {
        $size = (int) $size;
        if ($size <= 0)
            $size = NULL;        // Use the default size
        $this->properties['size'] = $size;
    }

    public function setExtra($extra) {
        $this->extra = $extra;
        return true;
    }

    public function isValidEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    /**
     *    Object property overloading
     */
    public function __get($var) {
        switch ($var) {
            case 'email':
                return @$this->email;
                break;
            default:
                return @$this->properties[$var];
                break;
        }
    }

    /**
     *    Object property overloading
     */
    public function __set($var, $value) {
        switch($var) {
            case "email":    return $this->setEmail($value);
            case "rating":    return $this->setRating($value);
            case "default":    return $this->setDefault($value);
            case "size":    return $this->setSize($value);
            // Cannot set gravatar_id
            case "gravatar_id": return;
        }
        return @$this->properties[$var] = $value;
    }

    /**
     *    Object property overloading
     */
    public function __isset($var) { return isset($this->properties[$var]); }

    /**
     *    Object property overloading
     */
    public function __unset($var) { return @$this->properties[$var] == NULL; }

    /**
     *    Get source
     */
    public function gravatarLink() {
        $url = self::GRAVATAR_URL . $this->properties['gravatar_id'] . '?';
        $first = true;
        foreach($this->properties as $key => $value) {
            if ($key != 'gravatar_id' && isset($value)) {
                if (!$first)
                    $url .= "&";
                $url .= $key."=".urlencode($value);
                $first = false;
            }
        }
        return $url;    
    }

    public function toHTML() {
        return     '<img src="'. $this->gravatarLink() .'"'
                .(!isset($this->size) ? "" : ' width="'.$this->size.'" height="'.$this->size.'"')
                .$this->extra
                .' />';    
    }

    public function __toString() { return $this->gravatarLink(); }
    
    public function imageTag() {
        return $this->toHTML();    
    }
}