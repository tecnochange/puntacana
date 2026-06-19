-----------------------------------------
<div class="top_nav top-nav-fixed">
    <div class="nav_menu">
        <div class="nav toggle">
            <a id="menu_toggle"><i class="fas fa-bars" style="color: #59008e !important"></i></a>
        </div>
        <nav class="nav navbar-nav">
            <ul class=" navbar-right">
                <li role="presentation" class="nav-item dropdown open">
                    &nbsp;&nbsp;&nbsp;&nbsp;
                    <a href="<?php echo $url; ?>app/controllers/close_sesion.php">
                        <button type="button" id="sidebarCollapse" class="btn btn-danger btn-sm" style="border-radius: 30px" title="Cerrar Sesión">
                            <i class="fas fa-sign-out-alt pull-right"></i>
                        </button>
                    </a>
                </li>
                <li class="nav-item dropdown open" style="padding-left: 15px;">
                    <a href="javascript:;" class="user-profile dropdown-toggle" aria-haspopup="true" id="navbarDropdown" data-toggle="dropdown" aria-expanded="false" style="font-size: 1rem;text-transform: uppercase;">
                        <img loading="lazy" src="<?php echo $url . "recursos/" . $dtEmpleado["foto"]; ?>" alt="" class="foto_min"><?php echo $dtEmpleado["nombre"] . " " . $dtEmpleado["apellidos"]; ?>
                    </a>
                    <div class="dropdown-menu dropdown-usermenu pull-right" aria-labelledby="navbarDropdown" style="font-size: 0.8rem !important;">
                        <a class="dropdown-item" href="<?php echo $url; ?>?pg=cuenta"> Mi Cuenta</a>
                        <!-- <a class="dropdown-item" href="<?php //echo $url; ?>?pg=administrar/send_masivo"> Buzón </a> -->
                        <!-- <a class="dropdown-item" href="<?php //echo $url; 
                                                            ?>app/controllers/close_sesion.php"><i class="fas fa-sign-out-alt pull-right" style="color: black !important;float: inline-end;"></i>Salir</a> -->
                    </div>

                </li>
                <li role="presentation" class="nav-item dropdown open">

                    <h5><?php echo mb_strtoupper($txt_role); ?></h5>
                </li>
            </ul>
        </nav>
    </div>
</div>