import {PanelBody, SelectControl,} from '@wordpress/components';
import {registerBlockType} from '@wordpress/blocks';
import {__} from '@wordpress/i18n';

const campaigns = [
    {
        label: __('Artenschutz', 'wp-donations-plugin'),
        value: 'protect_species_coin',
    }, {
        label: __('Artenschutz HH DE', 'wp-donations-plugin'),
        value: 'protect_species_coin_hh_de',
    }, {
        label: __('Artenschutz HH EN', 'wp-donations-plugin'),
        value: 'protect_species_coin_hh_en',
    }, {
        label: __('Meeresschutz', 'wp-donations-plugin'),
        value: 'protect_ocean_coin',
    }, {
        label: __('Waldschutz', 'wp-donations-plugin'),
        value: 'protect_forest_coin',
    }, {
        label: __('Klimaschutz', 'wp-donations-plugin'),
        value: 'protect_climate_coin',
    }, {
        label: __('Biologischer Vielfaltsschutz', 'wp-donations-plugin'),
        value: 'protect_diversity_coin',
    },
];

export function bannerBlock() {
    const cartPageId = cart_page_id; // this value is provided by the wordpress plugin

    registerBlockType('wp-donations-plugin/checkout-banner', {
        title: __('Spendenbanner', 'wp-donations-plugin'),
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
            return (
                <PanelBody
                    title={__(
                        'Spende Banner',
                        'wp-donations-plugin'
                    )}
                >
                    <SelectControl
                        label={__(
                            'Zielkampagne',
                            'wp-donations-plugin'
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
