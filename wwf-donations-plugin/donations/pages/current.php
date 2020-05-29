<?php
$today = new DateTime('now');
$defaultDaysInPast = \donations\SettingsManager::getOptionLiveReportDaysInPast();

// handle vars
if (isset($_GET['donation_report_start_date'])) {
    $sanitizedDate = preg_replace("([^0-9-])", "", $_GET['donation_report_start_date']);
    try {
        $startDate = new \DateTime($sanitizedDate);
    } catch (\Exception $ex) {
        $startDate = (new \DateTime('now'))->sub(new DateInterval('P' . $defaultDaysInPast . 'D'));
    }
} else {
    $startDate = (new \DateTime('now'))->sub(new DateInterval('P' . $defaultDaysInPast . 'D'));
}
$dateDifference = intval($today->diff($startDate)->days);

// handle post request to generate report
$reportTriggered = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['action'] === 'generate_report') {
        \donations\Plugin::do_report_generate($startDate, $today);
        $reportTriggered = true;
    }
}
?>
<div class="wrap">
    <h2>Live-Spendenbericht</h2>
    <?php if ($reportTriggered): ?>
        <div class="notice notice-success">
            <p>
                Es wurde ein Bericht generiert vom <?php echo $startDate->format('Y-m-d'); ?> bis heute.
            </p>
        </div>
    <?php endif ?>
    <div class="notice notice-info">
        <p>
            Hier können Sie die aktuelle Auswertung der Spendeneinnahmen über einen bestimmten Zeitraum betrachten.<br/>
            <strong>Standardzeitraum:</strong> <?php esc_attr_e($defaultDaysInPast) ?> <?php echo $defaultDaysInPast === 1 ? 'Tag' : 'Tage' ?>
        </p>
    </div>
    <div class="notice notice-warning">
        <p>
            <strong>WICHTIG:</strong>
            In großen Shops mit vielen Bestellungen kann der folgende Aufruf sehr rechen- und zeitintensiv sein!<br/>
            Bitte wählen Sie dementsprechend den Startzeitpunkt mit Vorsicht.
        </p>
    </div>
    <form action="<?php echo admin_url('admin.php'); ?>" method="get">
        <input type="hidden" name="page" value="wwf-donations-current"/>
        <label for="donation_report_start_date">
            Startzeitpunkt:
            <input id="donation_report_start_date" name="donation_report_start_date"
                   type="date" value="<?php echo $startDate->format('Y-m-d') ?>" required
                   min="<?php echo (clone $today)->sub(new DateInterval('P5Y'))->format('Y-m-d') ?>"
                   max="<?php echo $today->format('Y-m-d') ?>"/>
        </label>
        <input type="submit" value="Aktualisieren" class="button-primary"/>
    </form>
    <?php if (!$reportTriggered): ?>
        <form action="" method="post" style="margin-top: 16px; margin-bottom: 25px;">
            <input type="hidden" name="action" value="generate_report"/>
            <input type="submit" value="Bericht generieren seit <?php echo $startDate->format('Y-m-d') ?>"
                   class="button-secondary"/>
        </form>
    <?php endif ?>

    <table class="widefat fixed" style="margin-top: 1rem;">
        <tr class="alternate">
            <td><strong>Startzeitpunkt</strong></td>
            <td><?php echo $startDate->format('d.m.Y') ?></td>
        </tr>
        <tr class="alternate">
            <td><strong>Endzeitpunkt</strong></td>
            <td>
                <?php echo $today->format('d.m.Y') ?>
                <?php if ($dateDifference > 0): ?>
                    (<?php echo $dateDifference ?>&nbsp;<?php echo $dateDifference === 1 ? 'Tag' : 'Tage' ?>)
                <?php endif ?>
            </td>
        </tr>
        <?php $sum = 0;
        $totalOrderCounter = 0; ?>
        <?php foreach (\donations\CampaignManager::getAllCampaigns() as $charityCampaign): ?>
            <?php
            $report = \donations\CampaignManager::getRevenueOfCampaignInTimeRange($charityCampaign->getSlug(), $startDate, $today);
            $revenue = $report->getAmount();
            $totalOrderCounter = $report->getOrderCountTotal();
            $sum += $revenue;
            ?>
            <tr>
                <td><strong>Kampagne: <?php echo $charityCampaign->getName() ?></strong></td>
                <td><?php echo number_format($revenue, 2) ?> &euro;</td>
            </tr>
        <?php endforeach ?>
        <tr class="alternate">
            <td><strong>Summe</strong></td>
            <td>
                <strong><?php echo number_format($sum, 2); ?> &euro;</strong>
            </td>
        </tr
        <tr class="alternate">
            <td><strong>Bestellungen insgesamt im Zeitraum</strong></td>
            <td>
                <strong><?php echo $totalOrderCounter; ?></strong>
            </td>
        </tr>
    </table>
</div>