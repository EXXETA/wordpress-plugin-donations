<?php


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