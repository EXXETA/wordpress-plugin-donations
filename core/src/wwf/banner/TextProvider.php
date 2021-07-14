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


namespace exxeta\wwf\banner;

/**
 * Class TextProvider
 * Common texts shared across all shop plugins.
 *
 * @package exxeta\wwf\banner
 */
class TextProvider
{
    /**
     * Method to get the general info text each plugin should display at least once.
     * If possible - the affected product IDs (of the donation products a plugin creates) should be given.
     *
     * @param array|null $allProductIds
     * @return string
     */
    public static function getGeneralInfoText(?array $allProductIds): string
    {
        $output = '<p>';
        $output .= 'Dieses Plugin erweitert den Shop mit mehreren Produkten, um Gelder für 
                    Wohltätigkeitsorganisationen zu sammeln.<br/>';
        if ($allProductIds && sizeof($allProductIds) > 0) {
            $output .= sprintf('Produkt-IDs: <strong>%s</strong>', join(', ', $allProductIds)) . '<br/>';
        }
        $output .= 'Bitte überweisen Sie in regelmäßigen Abständen die Beträge der eingenommenen Spenden 
                    unter Angabe des jeweilig gewünschten Spendenzwecks zusätzlich zum angegebenen Verwendungszweck
                    auf folgendes Konto:<br/><br/>';
        $output .= '<strong>IBAN:</strong> DE06 5502 0500 0222 2222 22<br/>';
        $output .= '<strong>BIC:</strong> BFSWDE33MNZ &ndash; Bank für Sozialwirtschaft<br/>';
        $output .= '<strong>Verwendungszweck:</strong> 20ISAZ2002';
        $output .= '</p>';
        return $output;
    }
}