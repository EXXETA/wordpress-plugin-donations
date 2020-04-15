<?php


namespace donations;

/**
 * Class CharityCampaign
 * representation of a campaign
 *
 * @package donations
 */
class CharityCampaign
{
    /**
     * should correspond to a campaign product slug
     *
     * @var string
     */
    private $slug;

    /**
     * @var string
     */
    private $description;

    /**
     * @var string
     */
    private $detailURL;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $class;

    /**
     * CharityCampaign constructor.
     * @param string $slug
     * @param string $description
     * @param string $detailURL
     * @param string $name
     * @param string $class
     */
    public function __construct(string $slug, string $description, string $detailURL, string $name, string $class)
    {
        $this->slug = $slug;
        $this->description = $description;
        $this->detailURL = $detailURL;
        $this->name = $name;
        $this->class = $class;
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
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return string
     */
    public function getDetailURL(): string
    {
        return $this->detailURL;
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
    public function getClass(): string
    {
        return $this->class;
    }
}