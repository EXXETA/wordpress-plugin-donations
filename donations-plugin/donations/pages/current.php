<?php
$today = new DateTime('now');
$defaultDaysInPast = 90;

if (isset($_GET['donation_report_start_date'])) {
    $sanitizedDate = preg_replace("([^0-9-])", "", $_GET['donation_report_start_date']);
    try {
        $startDate = new \DateTime($sanitizedDate);
    } catch (\Exception $ex) {
        $startDate = (new \DateTime('now'))->sub(new DateInterval('P' + $defaultDaysInPast + 'D'));
    }
} else {
    $startDate = (new \DateTime('now'))->sub(new DateInterval('P' + $defaultDaysInPast + 'D'));
}
?>
<div class="wrap">
    <h2>Aktueller Bericht</h2>
    <div class="notice notice-info">
        <p>
            Hier können Sie die aktuelle Auswertung der Spendeneinnahmen über einen bestimmten Zeitraum betrachten.
        </p>
    </div>
    <form action="<?php echo admin_url( 'admin.php' ); ?>" method="get">
        <input type="hidden" name="page" value="wp-donations-current" />
        <label for="donation_report_start_date">
            Start-Zeitpunkt:
            <input id="donation_report_start_date" name="donation_report_start_date"
                   type="date" value="<?php echo $startDate->format('Y-m-d') ?>" required
                   max="<?php echo $today->format('Y-m-d')?>" />
        </label>
        <input type="submit" value="Aktualisieren" />
    </form>

    <table class="widefat fixed" style="margin-top: 1rem;">
        <tr class="alternate">
            <td><strong>Startzeitpunkt</strong></td>
            <td><?php echo $startDate->format('d.m.Y') ?></td>
        </tr>
        <tr class="alternate">
            <td><strong>Endzeitpunkt</strong></td>
            <td><?php echo $today->format('d.m.Y') ?></td>
        </tr>
        <?php foreach (\donations\CampaignManager::getAllCampaigns() as $charityCampaign): ?>
            <?php
                $revenue = \donations\CampaignManager::getRevenueOfCampaignInTimeRange($charityCampaign->getSlug(), $startDate, $today);
            ?>
            <tr>
                <td><strong>Kampagne: <?php echo $charityCampaign->getSlug() ?></strong></td>
                <td><?php echo $revenue ?> &euro;</td>
            </tr>
        <?php endforeach ?>
    </table>
</div>