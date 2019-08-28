<?php

/**
 * Created by PhpStorm.
 * User: khan
 * Date: 5/19/14
 * Time: 1:21 PM
 */
class CurrencyService
{


    private $__allCurrenciesWithArrayKey = null;

    function getCurrency($withArrayKey = false)
    {
        if ($withArrayKey && !empty($this->__allCurrenciesWithArrayKey))
            return $this->__allCurrenciesWithArrayKey;

        ///home/khan/www/softverk-webportal-remaxth/libraries/webportal/services/currency/currency_data.json
        $currencies = json_decode(file_get_contents(JPATH_ROOT . "/libraries/webportal/services/currency/currency_data.json"));
        $currencies = get_object_vars($currencies);

        $preferred_currencies = $this->getPreferredCurrencies();


//reorder the currencies!

        $currency_preferred = array();
        $currency_others = array();

        foreach ($currencies as $c) {
            $symbol = "";
            $c->symbol = trim($c->symbol);
            if (!empty($c->symbol)) {
                $symbol = " ( " . $c->symbol . " ) ";
            }
            $c->display = $c->name . $symbol;
            $c->disabled = false;
            if (in_array($c->code, $preferred_currencies)) {
                $currency_preferred[] = $c;
            } else
                $currency_others[] = $c;
        }

        ksort($currency_others);
        $dummy = new stdClass();
        $dummy->code = $dummy->symbol = $dummy->name = $dummy->display = "──────────────────────";
        $dummy->disabled = true;
        $dummyArray = array();
        $dummyArray[] = $dummy;


        $array = array_merge($currency_preferred, $dummyArray, $currency_others);

        if ($withArrayKey) {
            $array_temp = array();
            foreach ($array as $a) {
                $array_temp[$a->code] = $a;
            }
            $array = $array_temp;
            $this->__allCurrenciesWithArrayKey = $array;
        }

        return $array;

    }

    function getPreferredCurrencies()
    {
        $preferred_currencies = WFactory::getConfig()->getWebportalConfigurationArray();
        $preferred_currencies = $preferred_currencies['preferred_currencies'];
        return $preferred_currencies;
    }

    function setPreferredCurrency($currencyCode)
    {
        if (is_array($currencyCode)) {
            $currencyCode = $currencyCode['currencyCode'];
        }
        WFactory::getHelper()->setSessionVariable("PreferredCurrency", $currencyCode);

        return $this->getPreferredCurrency();
    }

    function getBaseCurrency()
    {
        $currencies = $this->getPreferredCurrencies();
        return $currencies[0];
    }

    function getPreferredCurrency()
    {
        $value = WFactory::getHelper()->getSessionVariable("PreferredCurrency");
        if ($value === null) {
            $prefferedCurrency = $this->getBaseCurrency();
            $this->setPreferredCurrency($prefferedCurrency);
            return $this->getPreferredCurrency();
        }
        return $value;
    }

    /**
     * @var NumberFormatter
     */
    private $formatter = null;

    /**
     * @return NumberFormatter
     */
    private function getFormatter()
    {
        if (!$this->formatter) {

            $locale = WFactory::getConfig()->getWebportalConfigurationArray();
            $locale = $locale['locale'];
            if ($locale === null) {
                $locale = "en_US";
            }

            $this->formatter = new \NumberFormatter($locale, \NumberFormatter::CURRENCY);
        }

     //   WFactory::getLogger()->debug("Convert currency formatter is : " . json_encode($this->formatter));

        return $this->formatter;
    }

    function formatCurrency($input, $currency = null)
    {

        if (!function_exists("numfmt_format_currency")) {
            WFactory::getLogger()->warn("php_intl NOT installed!", __LINE__, __FILE__);
            return $input;
        }

        if ($currency === null)
            $currency = $this->getPreferredCurrency();
    //    WFactory::getLogger()->info("Before Converted & Formatted currency : $input => $currency");
        $data = numfmt_format_currency($this->getFormatter(), $input, $currency);
   //     WFactory::getLogger()->info("Converted & Formatted currency : $input => $currency : data $data" );
        if (__COUNTRY === 'TH' || __COUNTRY=='PH') {
            preg_match_all('/\d.*/', $data, $matches);

            $money = trim($matches[0][0]);
            //$symbol = trim(str_replace($money, "", $data));
            if (strpos($money, ".") !== false)
                $money = substr($money, 0, strrpos($money, "."));
        }
        if (__COUNTRY === 'IS') {
            $money = trim(preg_replace('/[a-zA-Z]+/', '', $data));
        }


        $symbol = $this->getCurrency(true);
        $symbol = $symbol[$currency];
        $symbol = $symbol->symbol;

        return trim("$symbol $money");
    }


    /**
     * Convert from base currency (
     * @param $input
     * @param $currency
     * @return string
     */
    function convertCurrency($input, $currency = null)
    {
        if (is_array($input)) {
            $currency = $input['currency'];
            $input = $input['price'];
        }
        if (WFactory::getHelper()->isNullOrEmptyString($currency)) {
            $currency = $this->getPreferredCurrency();
        }

        $output = $this->__convertCurrencyUsingYahooFinance($input, $this->getBaseCurrency(), $currency);

        WFactory::getLogger()->debug("Converted $input from " . $this->getBaseCurrency() . " -> $currency , result is $output");

        if ($output == 0) {
            return JText::_("CALL FOR PRICE");
        } else {
            return $this->formatCurrency($output, $currency);
        }
    }

    private function __convertCurrencyUsingGoogleFinanceCalculator($amount, $from, $to)
    {
        if ($from == $to)
            return $amount;

        $hash = "CONVERT_{$from}_{$to}";

        $conversionValue = WFactory::getHelper()->getSessionVariable($hash);

        if (WFactory::getHelper()->isNullOrEmptyString($conversionValue)) {


            $url = "https://www.google.com/finance/converter?a=1&from=$from&to=$to";
            $data = file_get_contents($url);
            preg_match("/<span class=bld>(.*)<\/span>/", $data, $converted);
            $conversionValue = preg_replace("/[^0-9.]/", "", $converted[1]);

            WFactory::getHelper()->setSessionVariable($hash, $conversionValue);
        }

        return $amount * $conversionValue;

    }


    private function __convertCurrencyUsingYahooFinance($amount, $from, $to)
    {


        if ($from == $to)
            return $amount;

        $hash = "CONVERT_{$from}_{$to}";

        $conversionValue = WFactory::getHelper()->getSessionVariable($hash);

        if (WFactory::getHelper()->isNullOrEmptyString($conversionValue)) {

            $url = 'http://finance.yahoo.com/d/quotes.csv?e=.csv&f=sl1d1t1&s=' . $from . $to . '=X';

            $filehandler = @fopen($url, 'r');

            if ($filehandler) {
                $data = fgets($filehandler, 4096);
                fclose($filehandler);
            }

            $InfoData = explode(',', $data);
            $conversionValue = $InfoData[1];
            WFactory::getHelper()->setSessionVariable($hash, $conversionValue);
        }

        return $amount * $conversionValue;

    }


}