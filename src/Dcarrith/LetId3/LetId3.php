<?php namespace Dcarrith\LetId3;

use \GetId3\GetId3Core as GetId3;

class LetId3 extends GetId3 {

        function __construct() { 

		parent::__construct();
	}

	public function getAlbumArtData( $id3 ) {

		if (isset($id3['fileformat'])) {

			if ($id3['fileformat'] == 'ogg') {

				$art_file_decoded = null;
				$art_file_decoded_truncated = null;
				$art_file_image_data = null;

				if (isset($id3['comments']['metadata_block_picture'][0])) {

					// Get the art_file data from the ogg vorbis metadata_block_picture element
					$art_file_image_data = $id3['comments']['metadata_block_picture'][0]; 

				} else if (isset($id3['tags']['vorbis_comment']['metadata_block_picture'][0])) {

					// Get the art_file data from the ogg vorbis metadata_block_picture element
					$art_file_image_data = $id3['tags']['vorbis_comment']['metadata_block_picture'][0]; 

				} else if (isset($id3['ogg']['comments']['metadata_block_picture'][0])) {

					// Get the art_file data from the ogg vorbis metadata_block_picture element
					$art_file_image_data = $id3['ogg']['comments']['metadata_block_picture'][0]; 

				} else {

					// don't know of any other cases yet
				}

				if (isset($art_file_image_data)) {

					// Base64 decode it
					$art_file_decoded = base64_decode($art_file_image_data);

				if (($art_file_decoded !== false) && ($art_file_decoded != '') && ($art_file_decoded !== null) && ($art_file_decoded !== NULL) && ($art_file_decoded !== "NULL") && (isset($art_file_decoded))) {

					if (strpos($art_file_decoded, "image/jpeg")) {

						// Need to truncate off the mimetype info since it doesn't need to be written with the file
						$art_file_decoded_truncated = substr($art_file_decoded, 42, (strlen($art_file_decoded) - 42));

					} else if (strpos($art_file_decoded, "image/png")) {

						// Need to truncate off the mimetype info since it doesn't need to be written with the file
						$art_file_decoded_truncated = substr($art_file_decoded, 41, (strlen($art_file_decoded) - 41));

					} else {

						// Need to truncate off the mimetype info since it doesn't need to be written with the file
						$art_file_decoded_truncated = substr($art_file_decoded, 42, (strlen($art_file_decoded) - 42));
					}

					return $art_file_decoded_truncated;
				} 

				return $art_file_image_data;

			} else { // must be mp3
		    
				return $id3['comments']['picture'][0]['data'];
			}
		}
	}
}
