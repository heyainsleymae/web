<?
namespace Exceptions;

class ArtworkImageDimensionsTooSmallException extends AppException{
	protected $message;

	// This has to be initialized in a constructor because we use the number_format() function.
	public function __construct(){
		$this->message = 'Image dimensions are too small. The minimum image size is ' . number_format(COVER_ARTWORK_IMAGE_MINIMUM_WIDTH) . ' × ' . number_format(COVER_ARTWORK_IMAGE_MINIMUM_HEIGHT) . '.';
	}
}