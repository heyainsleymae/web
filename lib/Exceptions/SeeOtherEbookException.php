<?
namespace Exceptions;

class SeeOtherEbookException extends AppException{
	public $Url;

	public function __construct(string $url = ''){
		$this->Url = $url;
		parent::__construct('This ebook is at a different URL: ' . $url);
	}
}
