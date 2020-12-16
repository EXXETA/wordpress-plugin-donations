<?php
// template vars $args
// - 'subject' - string
// - 'content' - string (html)
/* @var $args array */
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="de">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <title><?php echo $args['subject'] ?> | <?php echo $args['shopName'] ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
</head>
<body style="margin: 0 auto; padding: 6px;background-color: #eeeeff;max-width: 800px;width: 60%;">
<?php echo $args['content'] ?>
</body>
</html>