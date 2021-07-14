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
 * Class CharityCampaign
 * representation of a campaign
 *
 * @package exxeta\wwf\banner\model
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
     * @param string $slug = id
     * @param string $description
     * @param string $fullText
     * @param string $headline
     * @param string $buttonDescription
     * @param string $name
     * @param string $class
     */
    public function __construct(string $slug, string $description, string $fullText, string $headline,
                                string $buttonDescription, string $name, string $class)
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