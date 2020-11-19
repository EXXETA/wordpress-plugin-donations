<?php

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