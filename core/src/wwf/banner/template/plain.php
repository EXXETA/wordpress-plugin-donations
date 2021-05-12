<?php
// template vars $args
// - 'subject' - string
// - 'counter' - int = report id
// - 'revenues' - array with campaignSlug => revenue, string => float
// - 'startDate' - \DateTime
// - 'endDate' - \DateTime
// - 'sum' - sum of all campaign revenues, float
// - 'isRegular' - boolean, indicate if report was triggered manually
// - 'totalOrderCount' - float
// - 'pluginInstance' - an object instance of a DonationPluginInterface
// - 'shopName' - string
// - 'shopUrl' - string
// - 'shopSystem' - string
/* @var $args array */
?>
<?php echo $args['subject'] ?>

    Zeitraum - Von: <?php echo $args['startDate']->format(DateTime::RFC2822) ?>

    Zeitraum - Ende: <?php echo $args['endDate']->format(DateTime::RFC2822) ?>

    Bestellungen insgesamt im Zeitraum: <?php echo $args['totalOrderCount'] ?>

    Bericht #<?php echo $args['counter'] ?>

<?php foreach ($args['revenues'] as $slug => $revenue) : ?>
    <?php echo $args['pluginInstance']->getCharityProductManagerInstance()->getCampaignBySlug($slug)->getName() ?>: <?php echo number_format($revenue, 2) ?> €

<?php endforeach ?>

    Summe: <?php echo number_format($args['sum'], 2) ?> €


    Bericht erstellt am: <?php echo gmdate('F j, Y H:i:s') ?>

    Bericht manuell erstellt: <?php echo $args['isRegular'] ? 'Nein' : 'Ja' ?>

    Bericht erstellt mit: <?php echo $args['shopSystem'] ?>

    Link zum Shop | <?php echo $args['shopName'] ?>