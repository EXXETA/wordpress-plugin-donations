<div class="wrap">
    <h2>Spenden-Einstellungen</h2>

    <?php
    if (!current_user_can('manage_options')) {
        echo "Diesem Benutzer fehlen die Berechtigungen, um die Spendeneinstellungen zu ändern.";
        return;
    }
    $currentReportingInterval = \donations\SettingsManager::getOptionCurrentReportingInterval();
    $currentLiveReportsDaysInPast = \donations\SettingsManager::getOptionLiveReportDaysInPast();
    $reportRecipient = \donations\SettingsManager::getOptionReportRecipientMail();
    $isBannerShownInMiniCart = \donations\SettingsManager::getOptionMiniBannerIsShownInMiniCart();
    $currentMiniBannerCampaign = \donations\SettingsManager::getOptionMiniBannerCampaign();
    ?>
    <form action="options.php" method="post">
        <?php settings_fields(\donations\Plugin::$pluginSlug); ?>

        <table class="form-table" role="presentation">
            <tbody>
            <tr>
                <th scope="row">Reporting-Einstellungen</th>
                <td>
                    <fieldset>
                        <legend class="screen-reader-text">
                            <span>Reporting-Einstellungen</span>
                        </legend>

                        <label for="wwf_donations_reporting_interval_select">
                            Regelmäßige Spendenberichte generieren
                        </label>
                        <select id="wwf_donations_reporting_interval_select"
                                class="postform"
                                name="wwf_donations_reporting_interval">
                            <?php foreach (\donations\SettingsManager::getReportingIntervals() as $value => $label): ?>
                                <option value="<?php esc_attr_e($value) ?>"
                                    <?php
                                    echo $currentReportingInterval == $value ? ' selected="selected"' : '';
                                    ?>
                                ><?php esc_attr_e($label) ?></option>
                            <?php endforeach ?>
                        </select>
                        <br/>
                        <br/>

                        <label for="wwf_donations_reporting_live_days_in_past_input">
                            Live-Berichte &ndash; Tage in die Vergangenheit
                        </label>
                        <input type="number" id="wwf_donations_reporting_live_days_in_past_input"
                               name="wwf_donations_reporting_live_days_in_past" min="1" max="150" step="1"
                               class="small-text"
                               value="<?php echo $currentLiveReportsDaysInPast ?>"/>
                        <br/>
                        <br/>

                        <label for="wwf_donations_reporting_recipient_field">
                            Empfangsadresse der Spendenberichte
                        </label>
                        <input id="wwf_donations_reporting_recipient_field" type="email" readonly
                            <?php
                            // do not make it too easy to change mail:
                            // name="wwf_donations_reporting_recipient"
                            ?> size="75" maxlength="250" value="<?php echo $reportRecipient ?>"/>
                    </fieldset>
                </td>
            </tr>
            <tr>
                <th scope="row">Mini-Banner-Einstellungen</th>
                <td>
                    <fieldset>
                        <legend class="screen-reader-text">
                            <span>Banner-Einstellungen</span>
                        </legend>

                        <label for="wwf_donations_mini_banner_show_mini_cart">
                            Kleines Banner im "Mini-Cart" anzeigen
                        </label>
                        <input id="wwf_donations_mini_banner_show_mini_cart" type="checkbox"
                               value="1"
                               <?php checked(true, $isBannerShownInMiniCart, true) ?>
                               name="wwf_donations_mini_banner_show_mini_cart" />
                        <br/>
                        <br/>
                        <label for="wwf_donations_mini_banner_campaign">
                            Kampagne des Mini-Banners
                        </label>
                        <select id="wwf_donations_mini_banner_campaign"
                                class="postform"
                                name="wwf_donations_mini_banner_campaign">
                                <option value="null">Keine Auswahl (Standard: Biologische Artenvielfalt)</option>
                            <?php foreach (\donations\CampaignManager::getAllCampaigns() as $campaign): ?>
                                <? /* @var \donations\CharityCampaign $campaign */ ?>
                                <option value="<?php esc_attr_e($campaign->getSlug()) ?>"
                                    <?php
                                    echo $campaign->getSlug() == $currentMiniBannerCampaign ? ' selected="selected"' : '';
                                    ?>
                                ><?php esc_attr_e($campaign->getName()) ?></option>
                            <?php endforeach ?>
                        </select>
                        <br/>
                        <br/>
                    </fieldset>
                </td>
            </tr>
            </tbody>
        </table>
        <?php submit_button('Einstellungen speichern'); ?>
    </form>
</div>