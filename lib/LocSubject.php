<?
class LocSubject{
	public int $LocSubjectId;
	public string $Name;

	// *******
	// METHODS
	// *******

	/**
	 * @throws Exceptions\ValidationException
	 */
	public function Validate(): void{
		$error = new Exceptions\ValidationException();

		if(isset($this->Name)){
			$this->Name = trim($this->Name);

			if($this->Name == ''){
				$error->Add(new Exceptions\LocSubjectNameRequiredException());
			}

			if(strlen($this->Name) > EBOOKS_MAX_STRING_LENGTH){
				$error->Add(new Exceptions\StringTooLongException('LoC subject: '. $this->Name));
			}
		}
		else{
			$error->Add(new Exceptions\LocSubjectNameRequiredException());
		}

		if($error->HasExceptions){
			throw $error;
		}
	}

	/**
	 * @throws Exceptions\ValidationException
	 */
	public function Create(): void{
		$this->Validate();

		Db::Query('
			INSERT into LocSubjects (Name)
			values (?)
		', [$this->Name]);
		$this->LocSubjectId = Db::GetLastInsertedId();
	}

	/**
	 * @throws Exceptions\ValidationException
	 */
	public function GetByNameOrCreate(string $name): LocSubject{
		$result = Db::Query('
				SELECT *
				from LocSubjects
				where Name = ?
			', [$name], LocSubject::class);

		if(isset($result[0])){
			return $result[0];
		}
		else{
			$this->Create();
			return $this;
		}
	}
}
