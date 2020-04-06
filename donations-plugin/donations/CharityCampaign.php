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
     * CharityCampaign constructor.
     * @param string $slug
     * @param string $description
     * @param string $detailURL
     */
    public function __construct(string $slug, string $description, string $detailURL, string $name)
    {
        $this->slug = $slug;
        $this->description = $description;
        $this->detailURL = $detailURL;
        $this->name = $name;
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
}