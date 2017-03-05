<?php

use Phalcon\Mvc\Model;

class Users extends Model
{

	public $id;

	public $name;

	public $email;

	public function getSource()
	{
		return 'users';
	}

}
