<?php
class Demo_Promotions
{
    private static $_promoAspect = null;

    public static function enablePromotion()
    {
        //Bind to function debit of Demo_Account
        $h = new Aspect_Hook(array(
                new Aspect_JoinPoint_Regex_Function('/debit/'),
            ), array('Demo_Account'));

        self::$_promoAspect = new Aspect_Function($h, 'Demo_Promotions::debitPromotion');
        Aspect_Handler::wrapAroundBefore(self::$_promoAspect);
    }

    public static function disablePromotion()
    {
        if(self::$_promoAspect === null) return;

        Aspect_Handler::unwrapAroundBefore (self::$_promoAspect);
        self::$_promoAspect = null;
    }
    // 10% More Cash for debit users
    public static function debitPromotion($meta, $amount)
    {
        //Replace amount by amount * 1.10
        return new Aspect_Result_Replace(array($amount * 1.10));
    }
}
