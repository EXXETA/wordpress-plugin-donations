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
    private $fullText;
	
	/**
     * @var string
     */
    private $headline;
	
	/**
     * @var string
     */
    private $buttonDescription;

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
     * @param string $fullText
     * @param string $name
     * @param string $class
     */
    public function __construct(string $slug, string $description, string $fullText, string $headline, string $buttonDescription, string $name, string $class)
    {
        $this->slug = $slug;
        $this->description = $description;
        $this->fullText = $fullText;
        $this->headline = $headline;
        $this->buttonDescription = $buttonDescription;
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
    public function getFullText(): string
    {
        return $this->fullText;
    }

    /**
     * @return string
     */
    public function getHeadline(): string
    {
        return $this->headline;
    }

    /**
     * @return string
     */
    public function getButtonDescription(): string
    {
        return $this->buttonDescription;
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