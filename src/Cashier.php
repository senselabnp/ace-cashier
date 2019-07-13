<?php

namespace Acelle\Cashier;

use Illuminate\Support\ServiceProvider;

class Cashier
{
    
    /**
     * Get payment gateway.
     *
     * @var array
     */
    public static function getPaymentGateway($name=null, $fields=null)
    {
        if (isset($name)) {
            $config = config('cashier.gateways.' . $name);
        } else {
            $config = config('cashier.gateways.' . config('cashier.gateway'));
        }
        
        // overide fields
        if (isset($fields)) {
            $config['fields'] = $fields;
        }
        
        switch ($config['name']) {
            case 'direct':
                return new \Acelle\Cashier\Services\DirectPaymentGateway($config['fields']['notice']);
            case 'stripe':
                return new \Acelle\Cashier\Services\StripePaymentGateway($config['fields']['secret_key'], $config['fields']['publishable_key']);
            case 'braintree':
                return new \Acelle\Cashier\Services\BraintreePaymentGateway(
                    $config['fields']['environment'],
                    $config['fields']['merchant_id'],
                    $config['fields']['public_key'],
                    $config['fields']['private_key']
                );
            case 'coinpayments':
                return new \Acelle\Cashier\Services\CoinpaymentsPaymentGateway(
                    $config['fields']['merchant_id'],
                    $config['fields']['public_key'],
                    $config['fields']['private_key'],
                    $config['fields']['ipn_secret']
                );
            default:
                return false;
        }
    }
    
    /**
     * user want to change plan.
     *
     * @return bollean
     */
    public static function calcChangePlan($subscription, $plan)
    {
        $newEndsAt = $subscription->ends_at;
        
        $remainDays = $subscription->ends_at->diffInDays(\Carbon\Carbon::now());

        // amout per day of current plan
        $currentAmount = $subscription->plan->getBillableAmount();
        $periodDays = $subscription->ends_at->diffInDays($subscription->periodStartAt());
        $remainDays = $subscription->ends_at->diffInDays(\Carbon\Carbon::now());
        $currentPerDayAmount = ($currentAmount/$periodDays);
        $newAmount = ($plan->price/$periodDays)*$remainDays;
        $remainAmount = $currentPerDayAmount*$remainDays;

        $amount = $newAmount - $remainAmount;
        
        // if amount < 0
        if ($amount < 0) {
            $days = (int) ceil(-($amount/$currentPerDayAmount));
            $amount = 0;
            $newEndsAt->addDays($days);
        }

        return [
            'amount' => $amount,
            'endsAt' => $newEndsAt,
        ];
    }
    
    /**
     * Get renew url.
     *
     * @return bollean
     */
    public static function getRenewUrl($subscription, $returnUrl='/')
    {
        return action("\Acelle\Cashier\Controllers\\" . ucfirst(config('cashier.gateway')) . "Controller@renew", [
            'subscription_id' => $subscription->uid,
            'return_url' => $returnUrl,
        ]);
    }
    
    /**
     * Get renew url.
     *
     * @return bollean
     */
    public static function getChangePlanUrl($subscription, $returnUrl='/')
    {
        return action("\Acelle\Cashier\Controllers\\" . ucfirst(config('cashier.gateway')) . "Controller@changePlan", [
            'subscription_id' => $subscription->uid,
            'return_url' => $returnUrl,
        ]);
    }
    
    /**
     * Get renew url.
     *
     * @return bollean
     */
    public static function getPendingUrl($subscription, $returnUrl='/')
    {
        return action("\Acelle\Cashier\Controllers\\" . ucfirst(config('cashier.gateway')) . "Controller@pending", [
            'subscription_id' => $subscription->uid,
            'return_url' => $returnUrl,
        ]);
    }
}