<?php

namespace Framework\Libs;

// Most part of this class functionalities has been taken from
// http://kuwamoto.org/2007/12/17/improved-pluralizing-in-php-actionscript-and-ror
// and other few sources, with my own additional methods to archive my aim

class Inflection
{
    static $plural = array(
        '/(quiz)$/i'               => "$1zes",
        '/^(ox)$/i'                => "$1en",
        '/([m|l])ouse$/i'          => "$1ice",
        '/(matr|vert|ind)ix|ex$/i' => "$1ices",
        '/(x|ch|ss|sh)$/i'         => "$1es",
        '/([^aeiouy]|qu)y$/i'      => "$1ies",
        '/(hive)$/i'               => "$1s",
        '/(?:([^f])fe|([lr])f)$/i' => "$1$2ves",
        '/(shea|lea|loa|thie)f$/i' => "$1ves",
        '/sis$/i'                  => "ses",
        '/([ti])um$/i'             => "$1a",
        '/(tomat|potat|ech|her|vet)o$/i'=> "$1oes",
        '/(bu)s$/i'                => "$1ses",
        '/(alias)$/i'              => "$1es",
        '/(octop)us$/i'            => "$1i",
        '/(ax|test)is$/i'          => "$1es",
        '/(us)$/i'                 => "$1es",
        '/s$/i'                    => "s",
        '/$/'                      => "s"
    );

    static $singular = array(
        '/(quiz)zes$/i'             => "$1",
        '/(matr)ices$/i'            => "$1ix",
        '/(vert|ind)ices$/i'        => "$1ex",
        '/^(ox)en$/i'               => "$1",
        '/(alias)es$/i'             => "$1",
        '/(octop|vir)i$/i'          => "$1us",
        '/(cris|ax|test)es$/i'      => "$1is",
        '/(shoe)s$/i'               => "$1",
        '/(o)es$/i'                 => "$1",
        '/(bus)es$/i'               => "$1",
        '/([m|l])ice$/i'            => "$1ouse",
        '/(x|ch|ss|sh)es$/i'        => "$1",
        '/(m)ovies$/i'              => "$1ovie",
        '/(s)eries$/i'              => "$1eries",
        '/([^aeiouy]|qu)ies$/i'     => "$1y",
        '/([lr])ves$/i'             => "$1f",
        '/(tive)s$/i'               => "$1",
        '/(hive)s$/i'               => "$1",
        '/(li|wi|kni)ves$/i'        => "$1fe",
        '/(shea|loa|lea|thie)ves$/i'=> "$1f",
        '/(^analy)ses$/i'           => "$1sis",
        '/((a)naly|(b)a|(d)iagno|(p)arenthe|(p)rogno|(s)ynop|(t)he)ses$/i'  => "$1$2sis",
        '/([ti])a$/i'               => "$1um",
        '/(n)ews$/i'                => "$1ews",
        '/(h|bl)ouses$/i'           => "$1ouse",
        '/(corpse)s$/i'             => "$1",
        '/(us)es$/i'                => "$1",
        '/s$/i'                     => ""
    );

    static $irregular = array(
        'move'   => 'moves',
        'foot'   => 'feet',
        'goose'  => 'geese',
        'sex'    => 'sexes',
        'child'  => 'children',
        'man'    => 'men',
        'tooth'  => 'teeth',
        'person' => 'people',
        'admin'  => 'admin',
        'class'  => 'classes'
    );

    static $uncountable = array(
        'sheep',
        'fish',
        'deer',
        'series',
        'species',
        'money',
        'rice',
        'information',
        'equipment'
    );

    public static function pluralize( $string )
    {
		global $irregularWords;
		
		// save some time in the case that singular and plural are the same
        if ( in_array( strtolower( $string ), self::$uncountable ) )
            return $string;

        // check for irregular singular forms
        foreach ( $irregularWords as $pattern => $result )
        {
            $pattern = '/' . $pattern . '$/i';

            if ( preg_match( $pattern, $string ) )
                return preg_replace( $pattern, $result, $string);
        }

        // check for irregular singular forms
        foreach ( self::$irregular as $pattern => $result )
        {
            $pattern = '/' . $pattern . '$/i';

            if ( preg_match( $pattern, $string ) )
                return preg_replace( $pattern, $result, $string);
        }

        // check for matches using regular expressions
        foreach ( self::$plural as $pattern => $result )
        {
            if ( preg_match( $pattern, $string ) )
                return preg_replace( $pattern, $result, $string );
        }

        return $string;
    }

    public static function singularize( $string )
    {	
		global $irregularWords;
        // save some time in the case that singular and plural are the same
        if ( in_array( strtolower( $string ), self::$uncountable ) )
            return $string;

	    // check for irregular words
        foreach ( $irregularWords as $result => $pattern )
        {
            $pattern = '/' . $pattern . '$/i';

            if ( preg_match( $pattern, $string ) )
                return preg_replace( $pattern, $result, $string);
        }
		
		// check for irregular plural forms
        foreach ( self::$irregular as $result => $pattern )
        {
            $pattern = '/' . $pattern . '$/i';

            if ( preg_match( $pattern, $string ) )
                return preg_replace( $pattern, $result, $string);
        }

	    // check for matches using regular expressions
        foreach ( self::$singular as $pattern => $result )
        {
            if ( preg_match( $pattern, $string ) )
                return preg_replace( $pattern, $result, $string );
        }

        return $string;
    }

    public static function pluralize_if($count, $string)
    {
        if ($count == 1)
            return "1 $string";
        else
            return $count . " " . self::pluralize($string);
    }
    /**
     * Returns the input lower_case_delimited_string as a CamelCasedString. [borrowed from CakePHP]
     *
     * @param string $string String to camelize
     * @param string $delimiter the delimiter in the input string
     * @return string CamelizedStringLikeThis.
     * @link http://book.cakephp.org/3.0/en/core-libraries/inflector.html#creating-camelcase-and-under-scored-forms
     */
    public static function camelize($string, $delimiter = '_')
    {
        $result = str_replace(' ', '', self::humanize($string, $delimiter));

        return $result;
    }

    /**
     * Returns the input CamelCasedString as an underscored_string.
     *
     * Also replaces dashes with underscores. [borrowed from CakePHP]
     *
     * @param string $string CamelCasedString to be "underscorized"
     * @return string underscore_version of the input string
     * @link http://book.cakephp.org/3.0/en/core-libraries/inflector.html#creating-camelcase-and-under-scored-forms
     */
    public static function underscore($string)
    {
        return self::delimit(str_replace('-', '_', $string), '_');
    }
    
    /**
	* Expects a CamelCasedInputString and produces a lower case string delimited with space. 
	* @param string $string
	* 
	* @return Returns a space delimited string
	*/
    public static function spacirize($string) 
    {
		$result = mb_strtolower(preg_replace('/(?<=\\w)([A-Z])/', ' ' . '\\1', $string));

        return $result;
	}

    /**
     * Returns the input CamelCasedString as an dashed-string.
     *
     * Also replaces underscores with dashes. [borrowed from CakePHP]
     *
     * @param string $string The string to dasherize.
     * @return string Dashed version of the input string
     */
    public static function dasherize($string)
    {
        return self::delimit(str_replace('_', '-', $string), '-');
    }

    /**
     * Returns the input lower_case_delimited_string as 'A Human Readable String'.
     * (Underscores are replaced by spaces and capitalized following words.). [borrowed from CakePHP]
     *
     * @param string $string String to be humanized
     * @param string $delimiter the character to replace with a space
     * @return string Human-readable string
     * @link http://book.cakephp.org/3.0/en/core-libraries/inflector.html#creating-human-readable-forms
     */
    public static function humanize($string, $delimiter = '_')
    {
        $result = explode(' ', str_replace($delimiter, ' ', $string));
        foreach ($result as &$word) {
            $word = mb_strtoupper(mb_substr($word, 0, 1)) . mb_substr($word, 1);
        }
        $result = implode(' ', $result);

        return $result;
    }    
    
	/**
	* Expects a CamelCasedInputString, and produces a lower_case_delimited_string. [borrowed from CakePHP]
	*
	* @param string $string String to delimit
	* @param string $delimiter the character to use as a delimiter
	* @return string delimited string
	*/
    public static function delimit($string, $delimiter = '_')
    {
        $result = mb_strtolower(preg_replace('/(?<=\\w)([A-Z])/', $delimiter . '\\1', $string));

        return $result;
    }
}