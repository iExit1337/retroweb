<?php foreach ($files['css'] as $file) { ?>
    <link href="<?= $file ?>" rel="stylesheet">
<?php } ?>

<?php foreach ($files['js'] as $file) { ?>
    <script src="<?= $file ?>" type="text/javascript"></script>
<?php } ?>