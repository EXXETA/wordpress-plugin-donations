import {
    PanelBody,
    SelectControl,
} from '@wordpress/components';
import {registerBlockType} from '@wordpress/blocks';
import {__} from '@wordpress/i18n';

const campaigns = [
    {
        label: __('Artenschutz', 'wp-donations-plugin'),
        value: 'protect_species_coin',
    }, {
        label: __('Meeresschutz', 'wp-donations-plugin'),
        value: 'protect_ocean_coin',
    }, {
        label: __('Waldschutz', 'wp-donations-plugin'),
        value: 'protect_forest_coin',
    }, {
        label: __('Kinder- und Jugendschutz', 'wp-donations-plugin'),
        value: 'protect_children_youth_coin',
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
    // TODO compare with current post

    registerBlockType('wp-donations-plugin/checkout-banner', {
        title: __('Spendemünzen', 'wp-donations-plugin'),
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
            const {setAttributes, isSelected} = props;
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
