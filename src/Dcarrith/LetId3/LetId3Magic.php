<?php namespace Dcarrith\LetId3;

use \GetId3\GetId3Core as GetId3;

class LetId3 extends GetId3 {

	/*
	 * ID3v1.x - http://en.wikipedia.org/wiki/ID3#ID3v1
	 *
	 * 	Title
	 * 	Artist
	 * 	Album
	 * 	Year
	 * 	Comment
	 * 	Track (only if last 2 bytes of Comment field are unused)
	 * 	Genre
	 *  
	 * ID3v2.x - http://id3.org/id3v2.3.0
	 *
	 * 	TIT2: The 'Title/Songname/Content description' frame is the actual name of the piece (e.g. "Adagio", "Hurricane Donna").
	 * 	TPE1: The 'Lead artist(s)/Lead performer(s)/Soloist(s)/Performing group' is used for the main artist(s). They are seperated with the "/" character. 
	 * 	TPE2: The 'Band/Orchestra/Accompaniment' frame is used for additional information about the performers in the recording. 
	 * 	TALB: The 'Album/Movie/Show title' frame is intended for the title of the recording(/source of sound) which the audio in the file is taken from. 
	 * 	TCON: The 'Content type' [Genre], which previously was stored as a one byte numeric value only, is now a numeric string. You may use one or several of the types as ID3v1.1 did or, since the category list would be impossible to maintain with accurate and up to date categories, define your own.     
	 * 	TRCK: The 'Track number/Position in set' frame is a numeric string containing the order number of the audio-file on its original recording. This may be extended with a "/" character and a numeric string containing the total numer of tracks/elements on the original recording. E.g. "4/9".
	 * 	TYER: The 'Year' frame is a numeric string with a year of the recording. This frames is always four characters long (until the year 10000). 
	 * 	APIC: Attached picture
	 * 	
	 */

	// Variable for storing properties available via PHP magic methods: __set(), __get(), __isset(), __unset()
	private $_data = array();

	// This is the default array of tags we care about (not sure if we care about "filename", "filepath", and "filenamepath" yet)
	private $_essentialTags = array( "TIT2", "TPE1", "TALB", "TRCK", "APIC", "artist", "album", "track", "picture" );
	
	// GetId3 seems to parse the ID3 tags into a flat array structure, so we need to keep track of when we find an essential tag so we can then grab
	// the value that's stored in the "data" element later on in the iteration of the array
	private $_currentTagKey = null;

        function __construct() { 

		parent::__construct();
	}

	public function getAlbumArtData( $id3 ) {

		// Parse out only the "APIC" and "picture" data into the essentialTagData array
		$this->parseEssentialTagData($id3, array( "APIC", "picture" ));

		// Check for a picture via the __isset() magic method
		if( isset($this->picture) ) {
	
			// Retrieve the picture via the __get() magic method
			return $this->picture;
		
		} else if( isset($this->APIC) ) {

			// Retrieve the APIC via the __get() magic method
			return $this->APIC;
		}

		// If we couldn't find a picture, then just return null so it'll serve up the default image
		return null;
	}
	
	public function parseEssentialTagData( $id3, $target = null ) {

		// Use the passed in target parameter if it's set, otherwise, just use the default list of all essential tags
		$essentialTags = (isset($target) ? $target : $this->_essentialTags);

		foreach( $id3 as $key => $value ) {

			if ( !is_array($value) ) {
			
				if( $key == "data" ) {

					if ( $this->_currentTagKey !== null ) {

						// Create a variable through __set() magic method and name it based on value of _currentTagKey
						$$this->_currentTagKey = $value;
	
						// Unset the _currentTagKey
						unset($this->_currentTagKey);
					}
					
				} else {

					if ( in_array( $key, $essentialTags )) {

						// Create a variable through __set() magic method and name it based on value of $key
						$$key = $value;
					}
				}

			} else {

				// If key is one that we care about, then let's save it
				if ( in_array( $key, $essentialTags )) {

					if ( $key !== 0 ) {

						$this->_currentTagKey = $key;
					}
				}
			
				// Recursively call getEssentialTagData on the value that's an array
				$this->parseEssentialTagData( $value, $target );
			}
		}
	}
	
	public function getEssentialTagData() {

		return $this->_essentialTagData;
	}

	public function __get($name) {

		if ( array_key_exists( $name, $this->_data )) {
			return $this->_data[$name];
		}

		$trace = debug_backtrace();

		trigger_error(	'Undefined property via __get(): ' . $name .
				' in ' . $trace[0]['file'] .
				' on line ' . $trace[0]['line'],
				E_USER_NOTICE	);
		
		return null;
	}

	public function __set( $name, $value ) {
		$this->_data[$name] = $value;
	}

	public function __isset( $name ) {
		return isset( $this->_data[$name] );
	}

	public function __unset( $name ) {
		unset( $this->_data[$name] );
	}
}
