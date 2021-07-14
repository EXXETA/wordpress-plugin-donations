<?php declare(strict_types=1);
/*
 * Copyright 2020-2021 EXXETA AG, Marius Schuppert
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

namespace WWFDonationPlugin\Service;

use exxeta\wwf\banner\BannerHandlerInterface;
use exxeta\wwf\banner\model\CharityProduct;
use Monolog\Logger;
use Shopware\Components\CSRFTokenValidator;
use Shopware\Models\Article\Article;
use Shopware\Models\Media\Album;
use Shopware\Models\Media\Media;
use WWFDonationPlugin\WWFDonationPluginException;

/**
 * Class ShopwareBannerHandler
 *
 * shopware specific implementation details of the generic wwf banners
 *
 * @package WWFDonationPlugin\Service
 */
class ShopwareBannerHandler implements BannerHandlerInterface
{
    // injected via setter
    /**
     * @var MediaService
     */
    protected $mediaService;

    /**
     * @var ProductService
     */
    protected $productService;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var bool
     */
    protected $isAjax;

    /**
     * @var string|null
     */
    protected $targetPageId;

    /**
     * ShopwareBannerHandler constructor.
     * @param MediaService $mediaService
     * @param CSRFTokenValidator $csrfTokenManager
     * @param ProductService $productService
     * @param string|null $targetPageId
     * @param bool $isAjax
     * @param Logger $logger
     */
    public function __construct(MediaService $mediaService, ProductService $productService,
                                ?string $targetPageId, bool $isAjax,
                                Logger $logger)
    {
        $this->mediaService = $mediaService;
        $this->productService = $productService;
        $this->targetPageId = $targetPageId;
        $this->isAjax = $isAjax;
        $this->logger = $logger;
    }

    public function getLogoImageUrl(CharityProduct $charityProduct): string
    {
        $mediaAlbum = $this->mediaService->getOrCreateMediaAlbum();
        if (!$mediaAlbum instanceof Album) {
            $this->logger->addError(sprintf('Could not find wwf media album record for image name "%s"',
                $charityProduct->getImagePath()));
            return '<pre>Could not retrieve wwf media album record</pre>';
        }
        $mediaRecord = $this->mediaService->getMediaRecordByCharityProduct($charityProduct, $mediaAlbum);
        if (!$mediaRecord instanceof Media) {
            $this->logger->addError(sprintf('Could not find media record for image name "%s"',
                $charityProduct->getImagePath()));
            return '<pre>Could not retrieve wwf media item record</pre>';
        }
        return $this->mediaService->getAbsoluteUrlByMediaRecord($mediaRecord);
    }

    public function getCartImageUrl(): string
    {
        return $this->getBaseUrl() . '_resources/css/images/icon_cart.svg';
    }

    public function getProductId(CharityProduct $charityProduct): string
    {
        // this method is never used by the shopware implementation
        return '';
    }

    public function getBaseUrl(): string
    {
        return 'custom/plugins/WWFDonationPlugin/Resources/views/frontend/';
    }

    public function getCartUrl(): string
    {
        return '/checkout/addArticle';
    }

    public function getMiniBannerTargetPageUrl($pageId): string
    {
        return $this->targetPageId;
    }

    public function getCartPageId(): int
    {
        // method not used here in this shopware context
        return 0;
    }

    /**
     * build extra row for mini banner cart add form
     *
     * @param string $output
     * @param CharityProduct $charityProduct
     */
    public function applyMiniBannerCartRowHook(string &$output, CharityProduct $charityProduct): void
    {
        $output .= '<div class="donation-cart-row">';
        $output .= sprintf('<form class="mini-banner-add-to-cart-form" action="%s" name="sAddToBasket" method="POST" %s>',
            $this->getCartUrl(), $this->getFormAttributes());

        $this->applyCartFormHook($output, $charityProduct);

        $output .= sprintf('<div class="quantity-field"><input class="donation-campaign-mini-quantity-input" type="number" value="1" min="1" name="%s" /></div>', $this->getFormQuantityInputName());
        $output .= '<div class="button-field"><input type="submit" value="In den Warenkorb" class="add_to_cart_button" /></div>';
        $output .= '</form></div>'; //.donation-cart-row
    }

    public function applyCartFormHook(&$output, CharityProduct $charityProduct): void
    {
        $article = $this->productService->getShopwareProductBySlug($charityProduct->getSlug());
        if (!$article instanceof Article) {
            throw new WWFDonationPluginException('Could not find product record of wwf campaign product.');
        }
        $output .= '<input name="sActionIdentifier" type="hidden" value="" />';
        $output .= '<input name="sAddAccessories" type="hidden" value="" />';

        $output .= sprintf('<input name="sAdd" type="hidden" value="%s" />', $article->getMainDetail()->getNumber());
    }

    public function getFormQuantityInputName(): string
    {
        return 'sQuantity';
    }

    public function getFormMethod(): string
    {
        return 'POST';
    }

    public function getFormAttributes(): string
    {
        if ($this->isAjax) {
            return 'enctype="multipart/form-data" data-add-article="true" data-eventname="submit" data-showmodal="false" data-addarticleurl="/checkout/ajaxAddArticleCart"';
        }
        return 'enctype="multipart/form-data" data-add-article="false" data-eventname="submit"';
    }
}