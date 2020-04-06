<?php
// template vars $args
// - 'subject' - string
// - 'revenues' - array with campaignSlug => revenue, string => float
// - 'startDate' - \DateTime
// - 'endDate' - \DateTime
// - 'sum' - sum of all campaign revenues, float
// - 'isRegular' - boolean, indicate if report was triggered manually
?>
<h1><?php echo $args['subject'] ?></h1>

<h3>Zeitraum</h3>
<table style="border: 2px solid #000;">
    <tbody>
    <tr>
        <td style="border: 1px solid #eee;padding-left: 12px;"><strong>Von</strong></td>
        <td style="border: 1px solid #eee;padding-left: 12px;"><?php echo $args['startDate']->format(DateTime::RFC2822) ?></td>
    </tr>
    <tr>
        <td style="border: 1px solid #eee;padding-left: 12px;"><strong>Ende</strong></td>
        <td style="border: 1px solid #eee;padding-left: 12px;"><?php echo $args['endDate']->format(DateTime::RFC2822) ?></td>
    </tr>
    </tbody>
</table>

<h3>Bericht</h3>
<table style="border: 2px solid #000;width: 100%;">
    <tbody>
    <?php foreach ($args['revenues'] as $slug => $revenue) : ?>
        <tr>
            <td style="border: 1px solid #eee;padding-left: 12px;" width="70%">
                <strong><?php echo \donations\CampaignManager::getCampaignBySlug($slug)->getName() ?></strong>
            </td>
            <td style="border: 1px solid #eee;padding-left: 12px;" width="30%">
                <?php echo number_format($revenue, 2) ?> &euro;
            </td>
        </tr>
    <?php endforeach ?>
    <tr style="font-size: 120%;">
        <td style="border: 1px solid #eee;border-top: 2px solid #000;padding-left: 12px;"><strong>Summe:</strong></td>
        <td style="border: 1px solid #eee;border-top: 2px solid #000;padding-left: 12px;">
            <strong><?php echo number_format($args['sum'], 2) ?> &euro;</strong>
        </td>
    </tr>
    </tbody>
</table>

<p style="margin-top: 12px;">
    <strong>Bericht erstellt am (Serverzeit): </strong>
    <?php echo (new DateTime('now'))->format(DateTime::RFC2822) ?><br/>
    <strong>Bericht manuell erstellt:</strong> <?php echo $args['isRegular'] ? 'Nein' : 'Ja' ?>
</p>

<p>
    <a href="<?php echo wp_guess_url() ?>">Link zum Shop | <?php echo get_bloginfo('name') ?></a><br/>
</p>