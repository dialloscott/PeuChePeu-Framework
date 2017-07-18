<?php

namespace App\Shop\Actions;

use App\Auth\Entity\User;
use App\Auth\Table\UserTable;
use App\Shop\Entity\Product;
use App\Shop\Table\PurchaseTable;
use Framework\Payment\Stripe;

class PurchaseService
{
    /**
     * @var Stripe
     */
    private $stripe;

    /**
     * @var PurchaseTable
     */
    private $purchaseTable;
    /**
     * @var UserTable
     */
    private $userTable;

    public function __construct(Stripe $stripe, PurchaseTable $purchaseTable, UserTable $userTable)
    {
        $this->stripe = $stripe;
        $this->purchaseTable = $purchaseTable;
        $this->userTable = $userTable;
    }

    public function hasPurchased(User $user, Product $product): bool
    {
        $purchases = $this->purchaseTable->findAll('WHERE product_id = ? AND user_id = ?', [$product->id, $user->id]);

        return count($purchases) > 0;
    }

    public function process(User $user, Product $product, string $token)
    {
        if ($this->hasPurchased($user, $product)) {
            throw new \Exception('Cet utilisateur possède déjà ce produit');
        }
        if ($user->stripe_customer_id) {
            $card = $this->stripe->createCard($user->stripe_customer_id, $token);
        } else {
            $customer = $this->stripe->createCustomer(['email' => $user->email]);
            $card = $customer->sources->create(['source' => $token]);
            $this->userTable->update($user->id, ['stripe_customer_id' => $customer->id]);
        }
        [$vat] = $this->stripe->getVatFromCard($card, $product->price);
        $charge = $this->stripe->createCharge($user->stripe_customer_id, [
            'amount'      => ($product->price + $vat) * 100,
            'description' => 'Achat MonSite.fr : ' . $product->name,
            'currency'    => 'EUR'
        ]);
        $this->purchaseTable->create([
            'product_id' => $product->id,
            'user_id'    => $user->id,
            'price'      => $product->price,
            'vat'        => $vat,
            'stripe_id'  => $charge->id,
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }
}
