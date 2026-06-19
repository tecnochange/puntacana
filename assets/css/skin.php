<style>

<?php if($skin_empresa["apariencia"]["cabecera"]){ ?>
.header {
    background-color: <?php echo $skin_empresa["apariencia"]["cabecera"] ?>;
}
<?php } ?>

<?php if($skin_empresa["apariencia"]["lateral"]){ ?>
.base_lateral {
    background-color: <?php echo $skin_empresa["apariencia"]["lateral"] ?>;
}
<?php } ?>

<?php if($skin_empresa["apariencia"]["lateral"]){ ?>
body {
    background-color: <?php echo $skin_empresa["apariencia"]["background"] ?>;
}
<?php } ?>















<?php if($skin_empresa["componentes"]["titulo"]){ ?>
h3 {
    color: <?php echo $skin_empresa["componentes"]["titulo"] ?>;
}
<?php } ?>




</style>