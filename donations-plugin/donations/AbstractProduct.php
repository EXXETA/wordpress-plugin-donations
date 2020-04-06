<?php

namespace donations;

/**
 * abstract Class AbstractProduct
 * common properties and methods of products handled with this plugin.
 * provides getter
 */
abstract class AbstractProduct
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
     * AbstractProduct constructor.
     * @param string $slug
     * @param string $name
     * @param string $description
     * @param float $price
     */
    public function __construct(string $slug, string $name, string $description, float $price)
    {
        $this->slug = $slug;
        $this->name = $name;
        $this->description = $description;
        $this->price = $price;
    }

    public function getProductIdOptionKey(): string
    {
        return $this->getSlug() . "_product_id";
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
}