<?php

namespace ValueObjects\Money;

use Money\Money as BaseMoney;
use Money\Currency as BaseCurrency;
use ValueObjects\Number\Integer;
use ValueObjects\Number\Real;
use ValueObjects\Number\RoundingMode;
use ValueObjects\Util\Util;
use ValueObjects\ValueObjectInterface;

class Money implements ValueObjectInterface
{
    /** @var BaseMoney */
    protected $money;

    /** @var Currency */
    protected $currency;

    /**
     * Returns a Money object from native int amount and string currency code
     *
     * @param  int    $amount   Amount expressed in the smallest units of $currency (e.g. cents)
     * @param  string $currency Currency code of the money object
     * @return static
     */
    public static function fromNative()
    {
        $args = func_get_args();

        $amount   = new Integer($args[0]);
        $currency = Currency::fromNative($args[1]);

        return new static($amount, $currency);
    }

    /**
     * Returns a Money object
     *
     * @param Integer  $amount   Amount expressed in the smallest units of $currency (e.g. cents)
     * @param Currency $currency Currency of the money object
     */
    public function __construct(Integer $amount, Currency $currency)
    {
        $baseCurrency   = new BaseCurrency($currency->getCode()->getValue());
        $this->money    = new BaseMoney($amount->getValue(), $baseCurrency);
        $this->currency = $currency;
    }

    /**
     *  Tells whether two Currency are equal by comparing their amount and currency
     *
     * @param  ValueObjectInterface $money
     * @return bool
     */
    public function equals(ValueObjectInterface $money)
    {
        if (false === Util::classEquals($this, $money)) {
            return false;
        }

        return $this->getAmount()->equals($money->getAmount()) && $this->getCurrency()->equals($money->getCurrency());
    }

    /**
     * Returns money amount
     *
     * @return Integer
     */
    public function getAmount()
    {
        $amount = new Integer($this->money->getAmount());

        return $amount;
    }

    /**
     * Returns money currency
     *
     * @return Currency
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * Add an integer quantity to the amount and returns a new Money object.
     * Use a negative quantity for subtraction.
     *
     * @param  Integer $quantity Quantity to add
     * @return Money
     */
    public function add(Integer $quantity)
    {
        $amount = new Integer($this->getAmount()->getValue() + $quantity->getValue());
        $result = new self($amount, $this->getCurrency());

        return $result;
    }

    /**
     * Multiply the Money amount for a given number and returns a new Money object.
     * Use 0 < Real $multipler < 1 for division.
     *
     * @param  Real  $multiplier
     * @param  mixed $rounding_mode Rounding mode of the operation. Defaults to RoundingMode::HALF_UP.
     * @return Money
     */
    public function multiply(Real $multiplier, RoundingMode $rounding_mode = null)
    {
        if (null === $rounding_mode) {
            $rounding_mode = RoundingMode::HALF_UP();
        }

        $amount        = $this->getAmount()->getValue() * $multiplier->getValue();
        $roundedAmount = new Integer(round($amount, 0, $rounding_mode->getValue()));
        $result        = new self($roundedAmount, $this->getCurrency());

        return $result;
    }

    /**
     * Returns a string representation of the Money value in format "CUR AMOUNT" (e.g.: EUR 1000)
     *
     * @return string
     */
    public function __toString()
    {
        return \sprintf('%s %d', $this->getCurrency()->getCode(), $this->getAmount()->getValue());
    }
}
