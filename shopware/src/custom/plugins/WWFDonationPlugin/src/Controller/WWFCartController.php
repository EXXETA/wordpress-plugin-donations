<?php

namespace WWFDonationPlugin\Controller;

use exxeta\wwf\banner\model\CharityProduct;
use Monolog\Logger;
use Shopware\Core\Checkout\Cart\Cart;
use Shopware\Core\Checkout\Cart\LineItem\LineItemCollection;
use Shopware\Core\Checkout\Cart\LineItemFactoryRegistry;
use Shopware\Core\Checkout\Cart\SalesChannel\CartService;
use Shopware\Core\Content\Product\ProductEntity;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use WWFDonationPlugin\Service\CharityCampaignManager;
use WWFDonationPlugin\Service\ProductService;
use WWFDonationPlugin\WWFDonationPlugin;

/**
 * Class WWFCartController
 *
 * A classic Symfony controller implementation in the sw 6 context of this plugin.
 * Basically adding a csrf-protected route to the shop for adding the charity products to the cart.
 * The main banner of this plugin will fire a GET request to this controller submitting the campaign slug
 * and the quantity.
 *
 * @package WWFDonationPlugin\Controller
 */
class WWFCartController extends AbstractController
{
    /**
     * @var CartService
     */
    private $cartService;

    /**
     * @var LineItemFactoryRegistry
     */
    private $lineItemFactoryRegistry;

    /**
     * @var ProductService
     */
    private $productService;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * WWFCartController constructor.
     * @param CartService $cartService
     * @param ProductService $productService
     */
    public function __construct(CartService $cartService, LineItemFactoryRegistry $lineItemFactoryRegistry,
                                ProductService $productService, Logger $logger)
    {
        $this->cartService = $cartService;
        $this->lineItemFactoryRegistry = $lineItemFactoryRegistry;
        $this->productService = $productService;
        $this->logger = $logger;
    }

    /**
     * @RouteScope(scopes={"storefront"})
     * @Route("/wwfdonationplugin/add-donation-line-item", name="sales-channel-api.action.wwfdonationplugin.wwf-add-donation-line-item-action", methods={"GET"})
     */
    public function wwfAddDonationLineItem(Request $request, Context $context): RedirectResponse
    {
        $redirectResponse = $this->redirectToRoute('frontend.checkout.cart.page');

        $csrf = $request->query->get('banner_csrf_token');
        $isRequestValid = $this->isCsrfTokenValid(WWFDonationPlugin::CSRF_TOKEN_ID, $csrf);
        if (!$isRequestValid) {
            $this->logger->warn('Invalid CSRF token for wwf cart controller endpoint. Cancelled/Skipped request.');
            return $redirectResponse;
        }
        $productSlug = strval($request->query->get('donation'));
        $quantity = intval($request->query->get('quantity'));

        $charityCampaignManager = new CharityCampaignManager();
        if (!in_array($productSlug, $charityCampaignManager->getAllCharityProductSlugs()) || $quantity <= 0) {
            // invalid charity product slug!
            $this->logger->warn('Invalid charity product slug received. Cancelled request.');
            return $redirectResponse;
        }
        $charityProduct = $charityCampaignManager->getProductBySlug($productSlug);
        if (!$charityProduct instanceof CharityProduct) {
            $this->logger->warn('Could not find charity product object for campaign slug. Cancelled request.');
            // invalid charity product slug!
            return $redirectResponse;
        }
        $productEntity = $this->productService->getProductBySlug($charityProduct->getSlug(), $context);
        if (!$productEntity instanceof ProductEntity) {
            $this->logger->err('Could not find sw product entity for charity campaign. Cancelled request.');
            // could not find product
            return $redirectResponse;
        }
        $maxQuantity = $productEntity->getMaxPurchase();
        if ($maxQuantity != null && $quantity > 0 && $quantity <= $maxQuantity) {
            // ok, all data seems valid -> add product to cart
            $this->logger->debug('OK, all inputs to add charity products to the cart seem valid. Start of adding line items.');
            $salesChannelContext = SalesChannelContext::createFrom($context);
            try {
                $token = $salesChannelContext->getToken();
            } catch (\TypeError $typeError) {
                $token = UUID::randomHex();
                $this->cartService->createNew($token);
            }
            $charityLineItem = $this->lineItemFactoryRegistry->create([
                'type' => 'product',
                'referencedId' => $productEntity->getId(),
                'quantity' => $quantity
            ], $salesChannelContext);
            $cart = $this->cartService->getCart($token, $salesChannelContext);
            if (!$cart instanceof Cart) {
                // this should never happen
                $this->logger->err('Could not retrieve cart object with a valid sw cart token.');
                return $redirectResponse;
            }
            $cart->addLineItems(new LineItemCollection([$charityLineItem]));
            $this->logger->debug('Added charity products to the cart successfully.');
            // strip GET params by redirecting in this way
            return $this->redirect($redirectResponse->getTargetUrl());
        }
        $this->logger->info('Invalid quantity received for charity line item. Skipping request.');
        return $redirectResponse;
    }
}