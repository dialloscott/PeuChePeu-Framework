<?php

namespace Framework\Payment;

use Staaky\VATRates\VATRates;
use Stripe\ApiResource;
use Stripe\Card;
use Stripe\Charge;
use Stripe\Customer;
use Stripe\Token;

class Stripe extends ApiResource
{
    public function __construct(string $secret_key)
    {
        \Stripe\Stripe::setApiKey($secret_key);
    }

    /**
     * @param string $token
     * @param float  $price
     *
     * @return float[]
     */
    public function getVatFromToken(string $token, float $price): array
    {
        $card = $this->getCardFromToken($token);

        return $this->getVatFromCard($card, $price);
    }

    /**
     * Récupère une carte depuis le token.
     *
     * @param string $token
     *
     * @return Card|Token
     */
    public function getCardFromToken(string $token): Card
    {
        return Token::retrieve($token)->card;
    }

    /***
     * Renvoie un Client depuis l'API
     * @param array $params
     * @return Customer
     */
    public function createCustomer(array $params): Customer
    {
        return Customer::create($params);
    }

    /**
     * Crée une carte pour ce client.
     *
     * @param string $customer
     * @param string $token
     *
     * @return Card
     */
    public function createCard(string $customer, string $token): Card
    {
        // On trouve la fingerprint du token
        $card = $this->getCardFromToken($token);

        // On cherche les sources de paiement du customer
        $url = '/v1/customers/' . $customer . '/sources';
        [$response] = static::_staticRequest('get', $url . '?object=card', [], null);
        $fingerprints = array_map(function ($source) {
            return $source['fingerprint'];
        }, $response->json['data']);

        // Si la carte n'existe pas on la crée
        if (!in_array($card->fingerprint, $fingerprints, true)) {
            $customer = Customer::retrieve($customer);

            return $customer->sources->create(['source' => $token]);
        }

        return $card;
    }

    public function createCharge(string $customer, $params): Charge
    {
        return Charge::create(array_merge([
            'customer' => $customer
        ], $params));
    }

    public function getVatFromCard(Card $card, float $price): array
    {
        $country = $card->country;
        $vatRates = new VATRates();
        $rate = $vatRates->getStandardRate($country) ?: 0;
        $vat = floor($price * $rate) / 100;

        return [$vat, $rate];
    }
}
