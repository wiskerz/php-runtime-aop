<?php
class Demo_Security
{
    private static $_securityAspect = null;
    public static function disableLargeMoneyOperations()
    {
        //Match Credit and Debit functions of Demo_Account
        $h = new Aspect_Hook(array(
                new Aspect_JoinPoint_Regex_Function('/debit/'),
                new Aspect_JoinPoint_Regex_Function('/credit/')
        ), array('Demo_Account'));
        self::$_securityAspect = new Aspect_Function($h, 
				'Demo_Security::blockMoneyTransfer');
        Aspect_Handler::wrapAroundBefore(self::$_securityAspect);
    }
    public static function enableLargeMoneyOperations()
    {
        if(self::$_securityAspect === null) return;
        Aspect_Handler::unwrapAroundBefore (self::$_securityAspect);
        self::$_securityAspect = null;
    }
    public static function blockMoneyTransfer($meta, $amount)
    {
        if($amount > 5000)
            return new Aspect_Result_Stop (false);
    }
}
