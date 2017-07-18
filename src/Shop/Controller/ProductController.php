<?php

namespace App\Shop\Controller;

use App\Auth\Entity\User;
use App\Shop\Actions\PurchaseService;
use App\Shop\Entity\Product;
use App\Shop\Table\ProductTable;
use Framework\Controller;
use Framework\Database\Database;
use Framework\Payment\Stripe;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Http\Response;
use Slim\Http\Stream;
use Stripe\Card;

class ProductController extends Controller
{
    public function index(ProductTable $productTable)
    {
        $products = $productTable->findAll();

        return $this->render('@shop/index', compact('products'));
    }

    public function show(
        int $id,
        ServerRequestInterface $request,
        ProductTable $productTable,
        Stripe $stripe
    ) {
        $product = $productTable->find($id);

        if ($request->getMethod() === 'POST') {
            $params = $request->getParsedBody();
            $token = $params['stripeToken'];
            [$vat, $rate] = $stripe->getVatFromToken($token, $product->price);

            return $this->render('@shop/buy', compact(
                'product',
                'vat',
                'rate',
                'card',
                'token'
            ));
        }

        return $this->render('@shop/show', compact('product'));
    }

    public function buy(
        int $id,
        ProductTable $productTable,
        User $user,
        ServerRequestInterface $request,
        PurchaseService $purchase
    ) {
        $product = $productTable->find($id);
        $params = $request->getParsedBody();
        $purchase->process($user, $product, $params['token']);
        $this->flash('success', 'Merci pour votre achat');

        return $this->redirect('shop.show', ['id' => $product->id]);
    }

    public function download(int $id, Database $database, User $user)
    {
        $product = $database->fetch('
        SELECT pr.name, pr.id
        FROM purchases AS p
        LEFT JOIN products AS pr ON pr.id = p.product_id
        WHERE p.product_id = ? AND p.user_id = ?
        ', [$id, $user->id]);
        $file = $this->container->get('basepath') . '/downloads/' . $id . '.pdf';
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $stream = fopen($file, 'r');
        $response = (new Response())
            ->withAddedHeader('Content-Type', finfo_file($finfo, $file))
            ->withAddedHeader('Content-Disposition', 'attachment; filename="' . $product->name . '.pdf"')
            ->withAddedHeader('Expires', '0')
            ->withAddedHeader('Cache-Control', 'must-revalidate')
            ->withAddedHeader('Pragma', 'public')
            ->withAddedHeader('Content-Length', filesize($file))
            ->withBody(new Stream($stream));

        return $response;
    }
}
