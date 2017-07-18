<?php

namespace App\Shop\Payment;

use Stripe\ApiResource;
use Stripe\StripeObject;
use Stripe\Util\Util;

class Card extends ApiResource
{
    public static function create(string $customer, ?array $params = null, ?array $options = null): StripeObject
    {
        self::_validateParams($params);
        $url = '/v1/customers/' . $customer . '/sources';
        [$response, $opts] = static::_staticRequest('post', $url, $params, $options);
        $obj = Util::convertToStripeObject($response->json, $opts);
        $obj->setLastResponse($response);

        return $obj;
    }
}
