<?php
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


namespace WWFDonationPlugin\Twig;


use Shopware\Core\Content\Seo\SeoUrlPlaceholderHandlerInterface;
use Shopware\Core\Framework\Adapter\Twig\Extension\SeoUrlFunctionExtension;
use Symfony\Bridge\Twig\Extension\RoutingExtension;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Class SeoUrlNullableExtension
 *
 * This is a copy of the original {@link SeoUrlFunctionExtension} with a check for the 'navigationId' to be set.
 * If not, an empty string is returned instead of an exception.
 *
 * @package WWFDonationPlugin\Twig
 */
class SeoUrlNullableExtension extends AbstractExtension
{
    /**
     * @var AbstractExtension
     */
    private $routingExtension;

    /**
     * @var SeoUrlPlaceholderHandlerInterface
     */
    private $seoUrlReplacer;

    public function __construct(RoutingExtension $extension, SeoUrlPlaceholderHandlerInterface $seoUrlReplacer)
    {
        $this->routingExtension = $extension;
        $this->seoUrlReplacer = $seoUrlReplacer;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('seoUrlNullable', [$this, 'seoUrl'], ['is_safe_callback' => [$this->routingExtension, 'isUrlGenerationSafe']]),
        ];
    }

    public function seoUrl(string $name, array $parameters = []): string
    {
        if (!isset($parameters['navigationId']) || empty(trim($parameters['navigationId']))) {
            return '';
        }
        return $this->seoUrlReplacer->generate($name, $parameters);
    }
}

