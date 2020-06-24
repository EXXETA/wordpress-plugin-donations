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
    ?>
    <form action="options.php" method="post">
        <?php settings_fields(\donations\Plugin::$pluginSlug); ?>

        <table class="form-table" role="presentation">
            <tbody>
            <tr>
                <th scope="row">Reporting Einstellungen</th>
                <td>
                    <fieldset>
                        <legend class="screen-reader-text">
                            <span>Reporting Einstellungen</span>
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
            </tbody>
        </table>
        <?php submit_button('Einstellungen speichern'); ?>
    </form>
</div>