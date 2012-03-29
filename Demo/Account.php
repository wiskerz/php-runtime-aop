<?php
class Demo_Account
{
	private $_balance = 0;
	private $_no	  = 0;
	public function __construct($no, $balance) 
	{
		$this->_balance = $balance;
		$this->_no	= $no;
	}
	public function debit($amount)
	{
		if($amount > 0)
		{
			$this->_balance += $amount;
			return true;
		}
		else 	return false;
	}
	public function credit($amount)
	{
		if($amount > 0)
		{
			$this->_balance -= $amount;
			return true;
		}
		else 	return false;
	}
	public function printInfo() 
	{
		echo "Account #{$this->_no}: \${$this->_balance}\n";
	}
	public function getBalance() { return $this->_balance; }
	public function getNumber()  { return $this->_no;      }


}
