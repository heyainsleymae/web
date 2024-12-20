<?
use Safe\DateTimeImmutable;

/**
 * @property User $User
 */
class Patron{
	use Traits\Accessor;

	public int $UserId;
	public bool $IsAnonymous;
	public ?string $AlternateName = null;
	public bool $IsSubscribedToEmails;
	public DateTimeImmutable $Created;
	public ?DateTimeImmutable $Ended = null;

	protected User $_User;


	// *******
	// METHODS
	// *******

	public function Create(): void{
		$this->Created = NOW;
		Db::Query('
			INSERT into Patrons (Created, UserId, IsAnonymous, AlternateName, IsSubscribedToEmails)
			values(?,
			       ?,
			       ?,
			       ?,
			       ?)
		', [$this->Created, $this->UserId, $this->IsAnonymous, $this->AlternateName, $this->IsSubscribedToEmails]);

		Db::Query('
			INSERT into Benefits (UserId, CanVote, CanAccessFeeds, CanBulkDownload)
			values (?,
			        true,
			        true,
			        true) on duplicate key
			update CanVote = true,
			       CanAccessFeeds = true,
			       CanBulkDownload = true
		', [$this->UserId]);

		// If this is a patron for the first time, send the first-time patron email.
		// Otherwise, send the returning patron email.
		$isReturning = Db::QueryInt('
				SELECT count(*)
				from Patrons
				where UserId = ?
			', [$this->UserId]) > 1;

		$this->SendWelcomeEmail($isReturning);
	}

	private function SendWelcomeEmail(bool $isReturning): void{
		if($this->User !== null){
			$em = new Email();
			$em->To = $this->User->Email ?? '';
			$em->ToName = $this->User->Name ?? '';
			$em->From = EDITOR_IN_CHIEF_EMAIL_ADDRESS;
			$em->FromName = EDITOR_IN_CHIEF_NAME;
			$em->Subject = 'Thank you for supporting Standard Ebooks!';
			$em->Body = Template::EmailPatronsCircleWelcome(['isAnonymous' => $this->IsAnonymous, 'isReturning' => $isReturning]);
			$em->TextBody = Template::EmailPatronsCircleWelcomeText(['isAnonymous' => $this->IsAnonymous, 'isReturning' => $isReturning]);
			$em->Send();

			$em = new Email();
			$em->To = ADMIN_EMAIL_ADDRESS;
			$em->From = ADMIN_EMAIL_ADDRESS;
			$em->Subject = 'New Patrons Circle member';
			$em->Body = Template::EmailAdminNewPatron(['patron' => $this, 'payment' => $this->User->Payments[0]]);
			$em->TextBody = Template::EmailAdminNewPatronText(['patron' => $this, 'payment' => $this->User->Payments[0]]);;
			$em->Send();
		}
	}


	// ***********
	// ORM METHODS
	// ***********

	/**
	 * @throws Exceptions\PatronNotFoundException
	 */
	public static function Get(?int $userId): Patron{
		if($userId === null){
			throw new Exceptions\PatronNotFoundException();
		}

		$result = Db::Query('
			SELECT *
			from Patrons
			where UserId = ?
			', [$userId], Patron::class);

		return $result[0] ?? throw new Exceptions\PatronNotFoundException();;
	}

	/**
	 * @throws Exceptions\PatronNotFoundException
	 */
	public static function GetByEmail(?string $email): Patron{
		if($email === null){
			throw new Exceptions\PatronNotFoundException();
		}

		$result = Db::Query('
			SELECT p.*
			from Patrons p
			inner join Users u using(UserId)
			where u.Email = ?
		', [$email], Patron::class);

		return $result[0] ?? throw new Exceptions\PatronNotFoundException();
	}
}
