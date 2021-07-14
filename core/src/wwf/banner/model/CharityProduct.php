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

namespace exxeta\wwf\banner\model;

/**
 * Class CharityProduct
 *
 * @package exxeta\wwf\banner\model
 */
class CharityProduct
{
    /**
     * @var string
     */
    private $slug;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $description;

    /**
     * @var float
     */
    private $price;

    /**
     * filename of a file in "images" directory of this plugin
     *
     * @var string
     */
    private $imagePath;

    /**
     * CharityProduct constructor.
     *
     * @param string $slug
     * @param string $name
     * @param string $description
     * @param float $price
     * @param string $imagePath
     */
    public function __construct(string $slug, string $name, string $description, float $price, string $imagePath)
    {
        $this->slug = $slug;
        $this->name = $name;
        $this->description = $description;
        $this->price = $price;
        $this->imagePath = $imagePath;
    }

    /**
     * @return string
     */
    public function getProductIdSettingKey(): string
    {
        return $this->getSlug() . "_product_id";
    }

    /**
     * @return string
     */
    public function getImageIdSettingKey(): string
    {
        return $this->getSlug() . "_image_id";
    }

    /**
     * @return string
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return float
     */
    public function getPrice(): float
    {
        return $this->price;
    }

    /**
     * @return string
     */
    public function getImagePath(): string
    {
        return $this->imagePath;
    }
}