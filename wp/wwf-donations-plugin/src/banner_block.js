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
import {PanelBody, SelectControl,} from '@wordpress/components';
import {registerBlockType} from '@wordpress/blocks';
import {__} from '@wordpress/i18n';

const campaigns = [
    {
        label: __('Artenschutz', 'wwf-donations-plugin'),
        value: 'protect_species_coin',
    }, {
        label: __('Meeresschutz', 'wwf-donations-plugin'),
        value: 'protect_ocean_coin',
    }, {
        label: __('Waldschutz', 'wwf-donations-plugin'),
        value: 'protect_forest_coin',
    }, {
        label: __('Klimaschutz', 'wwf-donations-plugin'),
        value: 'protect_climate_coin',
    }, {
        label: __('Schutz biologischer Vielfalt', 'wwf-donations-plugin'),
        value: 'protect_diversity_coin',
    },
];

export function bannerBlock() {
    const cartPageId = cart_page_id; // this value is provided by the wordpress plugin

    registerBlockType('wwf-donations-plugin/checkout-banner', {
        title: __('WWF-Spendenbanner', 'wwf-donations-plugin'),
        description: '',
        isPrivate: false,
        icon: 'cart',
        category: 'widgets',
        attributes: {
            donationMode: {
                type: 'string',
            }
        },
        keywords: ['donation', 'charity', 'cart', 'banner'],
        edit: (props) => {
            const {setAttributes, isSelected, attributes} = props;
            // set proper initial value
            if(!attributes['donationMode']) {
                attributes['donationMode'] = 'protect_species_coin';
            }
            return (
                <PanelBody
                    title={__(
                        'WWF-Spendenbanner',
                        'wwf-donations-plugin'
                    )}
                >
                    <SelectControl
                        label={__(
                            'Zielkampagne',
                            'wwf-donations-plugin'
                        )}
                        value={attributes['donationMode']}
                        options={campaigns}
                        onChange={(value) =>
                            setAttributes({
                                donationMode: value,
                            })
                        }
                    />
                </PanelBody>
            )
        },
    });
    registerBlockType('wwf-donations-plugin/banner-mini', {
        title: __('WWF-Spendenbanner (mini)', 'wwf-donations-plugin'),
        description: '',
        isPrivate: false,
        icon: 'cart',
        category: 'widgets',
        attributes: {
            donationMode: {
                type: 'string',
            }
        },
        keywords: ['donation', 'charity', 'cart', 'banner', 'mini'],
        edit: (props) => {
            const {setAttributes, isSelected, attributes} = props;
            // set proper initial value
            if(!attributes['donationMode']) {
                attributes['donationMode'] = 'protect_diversity_coin';
            }
            return (
                <PanelBody
                    title={__(
                        'WWF-Spendenbanner (mini)',
                        'wwf-donations-plugin'
                    )}
                >
                    <SelectControl
                        label={__(
                            'Zielkampagne',
                            'wwf-donations-plugin'
                        )}
                        value={attributes['donationMode']}
                        options={campaigns}
                        onChange={(value) =>
                            setAttributes({
                                donationMode: value,
                            })
                        }
                    />
                </PanelBody>
            )
        },
    });
}
