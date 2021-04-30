<?php

namespace WWFDonationPlugin\Controller;

use exxeta\wwf\banner\model\CharityProduct;
use Monolog\Logger;
use Shopware\Core\Checkout\Cart\Cart;
use Shopware\Core\Checkout\Cart\LineItemFactoryRegistry;
use Shopware\Core\Checkout\Cart\SalesChannel\CartService;
use Shopware\Core\Content\Product\ProductEntity;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Controller\StorefrontController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
class WWFCartController extends StorefrontController
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
     * @Route("/wwfdonation/add-donation-line-item-ajax", name="frontend.action.wwfdonationplugin.wwf-add-donation-line-item-ajax-action", methods={"GET"}, defaults={"XmlHttpRequest"=true})
     * @param Request $request
     * @param SalesChannelContext $salesChannelContext
     */
    public function wwfAddDonationLineItemAjax(Request $request, SalesChannelContext $salesChannelContext): Response
    {
        return $this->handleRequest(true, $request, $salesChannelContext);
    }

    /**
     * @RouteScope(scopes={"storefront"})
     * @Route("/wwfdonation/add-donation-line-item", name="frontend.action.wwfdonationplugin.wwf-add-donation-line-item-action", methods={"GET"})
     * @param Request $request
     * @param SalesChannelContext $salesChannelContext
     */
    public function wwfAddDonationLineItem(Request $request, SalesChannelContext $salesChannelContext): Response
    {
        return $this->handleRequest(false, $request, $salesChannelContext);
    }

    /**
     * @param int|null $maxQuantity
     * @param int $quantity
     * @return bool
     */
    protected function isProductQuantityValid(?int $maxQuantity, int $quantity): bool
    {
        return $maxQuantity != null && $maxQuantity > 0 && $quantity > 0 && $quantity <= $maxQuantity;
    }

    protected function handleRequest(bool $isAjax, Request $request, SalesChannelContext $salesChannelContext)
    {
        if ($isAjax) {
            $redirectResponse = $this->json(['ERROR']);
        } else {
            $redirectResponse = $this->redirectToRoute('frontend.checkout.cart.page');
        }

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
        $productEntity = $this->productService->getProductBySlug($charityProduct->getSlug(), $salesChannelContext->getContext());
        if (!$productEntity instanceof ProductEntity) {
            $this->logger->err('Could not find sw product entity for charity campaign. Cancelled request.');
            // could not find product
            return $redirectResponse;
        }
        $maxQuantity = $productEntity->getMaxPurchase();
        if ($this->isProductQuantityValid($maxQuantity, $quantity)) {
            // ok, all data seems valid -> add product to cart
            $this->logger->debug('OK, all inputs to add charity products to the cart seem valid. Start of adding line items.');
            if (!$salesChannelContext) {
                $salesChannelContext = SalesChannelContext::createFrom(\Shopware\Core\Framework\Context::createDefaultContext());
            }
            try {
                $cartToken = $salesChannelContext->getToken();
            } catch (\TypeError $typeError) {
                $cartToken = UUID::randomHex();
            }
            $cart = $this->cartService->getCart($cartToken, $salesChannelContext);
            if (!$cart instanceof Cart) {
                // this should never happen
                $this->logger->err('Could not retrieve cart object with a valid sw cart token.');
                return $redirectResponse;
            }
            // get possible existing line item quantity
            $existingQuantity = 0;
            $existingLineItem = null;
            foreach ($cart->getLineItems() as $singleLineItem) {
                if ($singleLineItem->getReferencedId() == $productEntity->getId()) {
                    $existingQuantity = $singleLineItem->getQuantity();
                    $existingLineItem = $singleLineItem;
                    break;
                }
            }
            // new quantity check
            $quantity += $existingQuantity;
            $quantity = min($quantity, $maxQuantity);

            // ok, everything still seems to be valid
            if ($existingLineItem == null) {
                // re-use existing line item
                $charityLineItem = $this->lineItemFactoryRegistry->create([
                    'type' => 'product',
                    'referencedId' => $productEntity->getId(),
                    'quantity' => $quantity
                ], $salesChannelContext);
                $cart->add($charityLineItem);
            } else {
                // line item already exists, so just increase its quantity
                $charityLineItem = $existingLineItem;
                $charityLineItem->setQuantity($quantity);
            }
            // this persists the cart with the new line item
            $this->cartService->recalculate($cart, $salesChannelContext);

            $this->logger->debug('Added charity products to the cart successfully.');

            if ($isAjax) {
                $redirectResponse = $this->json(['OK']);
            }
            // strip GET params by redirecting in this way
            return $redirectResponse;
        }

        $this->logger->info('Invalid quantity received for charity line item. Skipping request.');
        return $redirectResponse;
    }
}