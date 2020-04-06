<div class="wrap">
    <h2>Spenden-Einstellungen</h2>

    <?php
    if ( !current_user_can( 'manage_options' ) ) {
        echo "Diesem Benutzer fehlen die Berechtigungen, um die Spendeneinstellungen zu ändern.";
        return;
    }
    $currentReportingInterval = \donations\SettingsManager::getOptionCurrentReportingInterval();
    $currentLiveReportsDaysInPast = \donations\SettingsManager::getOptionLiveReportDaysInPast();
    ?>
    <form action="options.php" method="post">
        <?php settings_fields('wp_donations'); ?>

        <table class="form-table" role="presentation">
            <tbody>
            <tr>
                <th scope="row">Reporting Einstellungen</th>
                <td>
                    <fieldset>
                        <legend class="screen-reader-text">
                            <span>Reporting Einstellungen</span>
                        </legend>

                        <label for="wp_donations_reporting_interval_select">
                            Regelmäßige Spendenberichte generieren
                            <select id="wp_donations_reporting_interval_select"
                                    name="wp_donations_reporting_interval">
                                <?php foreach (\donations\SettingsManager::getReportingIntervals() as $value => $label): ?>
                                    <option value="<?php esc_attr_e($value) ?>"
                                        <?php
                                        echo $currentReportingInterval == $value ? ' selected="selected"' : '';
                                        ?>
                                    ><?php esc_attr_e($label) ?></option>
                                <?php endforeach ?>
                            </select>
                        </label>
                        <br/>

                        <label for="wp_donations_reporting_live_days_in_past_input">
                            Live-Berichte &ndash; Tage in die Vergangenheit
                            <input type="number" id="wp_donations_reporting_live_days_in_past_input"
                                   name="wp_donations_reporting_live_days_in_past" min="1" max="150" step="1"
                                   value="<?php echo $currentLiveReportsDaysInPast ?>"/>
                        </label>

                    </fieldset>
                </td>
            </tr>
            </tbody>
        </table>
        <?php submit_button( 'Einstellungen speichern' ); ?>
    </form>
</div>