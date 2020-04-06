<?php

namespace donations;

/**
 * Class CharityProduct
 */
class CharityProduct extends AbstractProduct
{
    /**
     * CharityProduct constructor.
     * @param string $slug
     * @param string $name
     * @param string $description
     * @param float $price
     */
    public function __construct(string $slug, string $name, string $description, float $price)
    {
        parent::__construct($slug, $name, $description, $price);
    }
}