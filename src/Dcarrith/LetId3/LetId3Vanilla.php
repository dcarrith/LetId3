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
	
	// This is the default array of tags we care about (not sure if we care about "filename", "filepath", and "filenamepath" yet)
	private $_essentialTags = array( "TIT2", "TPE1", "TALB", "TRCK", "APIC", "artist", "album", "track", "picture" );

	// Array to store the essential tag data of a given id3 array that's being parsed
	private $_essentialTagData = array();

	// GetId3 seems to parse the ID3 tags into a flat array structure, so we need to keep track of when we find an essential tag so we can then grab
	// the value that's stored in the "data" element later on in the iteration of the array
	private $_currentTagKey = null;

        function __construct() { 

		parent::__construct();
	}

	public function getAlbumArtData( $id3 ) {

		// Make sure there's nothing left over from a previous parse
		$this->_essentialTagData = null;

		// Parse out only the "APIC" and "picture" data into the essentialTagData array
		$this->parseEssentialTagData($id3, array( "APIC", "picture" ));

		// Check the keys we know of that could contain album art
		if( isset($this->_essentialTagData['picture']) ) {

			return $this->_essentialTagData['picture'];
		
		} else if( isset($this->_essentialTagData['APIC']) ) {

			return $this->_essentialTagData['APIC'];
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

						$this->_essentialTagData[ $this->_currentTagKey ] = $value;
						
						$this->_currentTagKey = null;
					}
					
				} else {

					if ( in_array( $key, $essentialTags )) {

						$this->_essentialTagData[ $key ] = $value;
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
}
