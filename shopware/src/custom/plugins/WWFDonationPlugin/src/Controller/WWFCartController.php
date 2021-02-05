<?php

namespace WWFDonationPlugin\Controller;

use Enqueue\Util\UUID;
use Shopware\Core\Checkout\Cart\SalesChannel\CartService;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\VarDumper\VarDumper;

class WWFCartController extends AbstractController
{

    /**
     * @var CartService
     * @Autow
     */
    private $cartService;

    /**
     * WWFCartController constructor.
     * @param CartService $cartService
     */
    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * @RouteScope(scopes={"storefront"})
     * @Route("/wwfdonationplugin/add-donation-line-item", name="sales-channel-api.action.wwfdonationplugin.wwf-add-donation-line-item-action", methods={"GET"})
     */
    public function wwfAddDonationLineItem(Request $request, Context $context): RedirectResponse
    {
        $redirectResponse = $this->redirectToRoute('frontend.checkout.cart.page');

        $csrf = $request->query->get('banner_csrf_token');
        $csrfTokenManager = $this->container->get('security.csrf.token_manager');
        // TODO extract constant across bannerhandler and this controller
        $isRequestValid = $this->isCsrfTokenValid('wwf-banner', $csrf);
        if (!$isRequestValid) {
            // TODO log this case - potential security problem detected!
            return $redirectResponse;
        }
        $csrfTokenManager->removeToken('wwf-banner');
        $productNumber = $request->query->get('productNumber');
        $quantity = $request->query->get('quantity');

        VarDumper::dump($productNumber);
        VarDumper::dump($quantity);
        // TODO do proper input validation and replace the product number with the campaign slug
        exit;

        $salesChannelContext = SalesChannelContext::createFrom($context);
        try {
            $token = $salesChannelContext->getToken();
        } catch (\TypeError $typeError) {
            $token = UUID::generate();
            $this->cartService->createNew($token);
        }
        $cart = $this->cartService->getCart($token, $salesChannelContext);
        // TODO add item to the cart

        // remove URL GET params before redirecting
        $request->query->remove('product_number');

        // strip GET params
        return $this->redirect($redirectResponse->getTargetUrl());
    }
}