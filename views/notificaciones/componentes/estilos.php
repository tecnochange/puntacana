<?php
// Estilos de las pantallas de notificaciones (bandeja, detalle y administración).
// Se incluye una vez por página.
?>
<style>
    /* Un solo ancho para todas las pantallas del módulo: cambiar de pantalla no mueve el contenido. */
    .notif { max-width: 80rem; margin: 0 auto; padding: 0 .75rem 2.5rem; }
    /* Reserva el espacio de la barra de desplazamiento: las pantallas largas no se corren respecto a las cortas. */
    html { scrollbar-gutter: stable; }
    .notif-encabezado { display: flex; justify-content: space-between; align-items: center; gap: 1rem; flex-wrap: wrap; margin-bottom: 1.25rem; }
    .notif-encabezado h3 { margin: 0; font-weight: 700; }
    .notif-encabezado p { margin: .125rem 0 0; color: #6c757d; }

    .notif-resumen { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 1rem; margin-bottom: 1.25rem; }
    .notif-tarjeta { background: #fff; border-radius: .875rem; padding: 1rem 1.125rem; display: flex; align-items: center; gap: .875rem;
        box-shadow: 0 .125rem .625rem rgba(16, 24, 40, .06); border: .0625rem solid #eef0f4; }
    .notif-tarjeta__icono { width: 2.875rem; height: 2.875rem; border-radius: .75rem; display: grid; place-items: center; font-size: 1.375rem; flex-shrink: 0; }
    .notif-tarjeta__valor { font-size: 1.5rem; font-weight: 700; line-height: 1; }
    .notif-tarjeta__etiqueta { color: #6c757d; font-size: .8125rem; margin-top: .25rem; }
    .notif-tarjeta--pendientes .notif-tarjeta__icono { background: #e7f0ff; color: #0d6efd; }
    .notif-tarjeta--pronto .notif-tarjeta__icono { background: #fff4e5; color: #f08c00; }
    .notif-tarjeta--sin-leer .notif-tarjeta__icono { background: #f1ecff; color: #7048e8; }
    button.notif-tarjeta { width: 100%; text-align: left; cursor: pointer; transition: box-shadow .15s, transform .15s, border-color .15s; }
    button.notif-tarjeta:hover { box-shadow: 0 .375rem 1.125rem rgba(16, 24, 40, .1); transform: translateY(-.0625rem); border-color: #dbe4ff; }

    .notif-panel { background: #fff; border-radius: 1rem; box-shadow: 0 .125rem .625rem rgba(16, 24, 40, .06); border: .0625rem solid #eef0f4; }
    .notif-barra { display: flex; justify-content: space-between; align-items: center; gap: .75rem; flex-wrap: wrap; padding: .875rem 1rem; border-bottom: .0625rem solid #eef0f4; }
    .notif-pestanas { display: inline-flex; background: #f3f5f9; border-radius: .75rem; padding: .25rem; gap: .125rem; }
    .notif-pestanas .nav-link { border: 0; border-radius: .5625rem; color: #495057; padding: .4375rem .875rem; font-size: .875rem; display: flex; align-items: center; gap: .375rem; }
    .notif-pestanas .nav-link.active { background: #fff; color: #0d6efd; box-shadow: 0 .0625rem .25rem rgba(16, 24, 40, .12); font-weight: 600; }
    .notif-contador { background: #e9ecef; color: #495057; border-radius: 1.25rem; padding: 0 .5rem; font-size: .75rem; line-height: 1.25rem; }
    .nav-link.active .notif-contador { background: #e7f0ff; color: #0d6efd; }
    .notif-barra__derecha { display: flex; gap: .5rem; align-items: center; flex-wrap: wrap; }
    .notif-buscador { position: relative; min-width: 17.5rem; }
    .notif-boton-filtros { border-radius: .625rem; display: flex; align-items: center; gap: .375rem; }
    .notif-boton-filtros .notif-contador { background: #0d6efd; color: #fff; }

    .notif-filtros { display: flex; flex-wrap: wrap; gap: .75rem 1rem; align-items: flex-end; padding: .75rem 1rem; background: #fafbfd; border-bottom: .0625rem solid #eef0f4; }
    .notif-filtro { display: flex; flex-direction: column; gap: .25rem; min-width: 10.625rem; }
    .notif-filtro__etiqueta { font-size: .7188rem; font-weight: 600; text-transform: uppercase; letter-spacing: .04em; color: #868e96; margin: 0; }
    .notif-filtro .form-select { border-radius: .5625rem; }
    .notif-segmentos { display: inline-flex; background: #eef1f6; border-radius: .5625rem; padding: .1875rem; }
    .notif-segmentos button { white-space: nowrap; border: 0; background: transparent; border-radius: .4375rem; padding: .1875rem .75rem; font-size: .8125rem; color: #495057; line-height: 1.5; }
    .notif-segmentos button.active { background: #fff; color: #0d6efd; font-weight: 600; box-shadow: 0 .0625rem .1875rem rgba(16, 24, 40, .12); }
    .notif-activos { display: flex; align-items: center; flex-wrap: wrap; gap: .5rem; padding: .625rem 1rem 0; }
    .notif-activos__resultado { font-size: .8125rem; color: #495057; font-weight: 500; }
    .notif-activos__chips { display: flex; flex-wrap: wrap; gap: .375rem; }
    .notif-chip--filtro { border: 0; background: #e7f0ff; color: #0d6efd; font-size: .7812rem; cursor: pointer; }
    .notif-chip--filtro:hover { background: #d0e2ff; }
    .notif-chip--filtro i { vertical-align: -.125rem; }
    .notif-buscador i { position: absolute; left: .75rem; top: 50%; transform: translateY(-50%); color: #adb5bd; }
    .notif-buscador input { padding-left: 2.125rem; border-radius: .625rem; }

    .notif-contenido { padding: .375rem 1rem 1rem; }
    .notif-grupo__titulo { font-size: .75rem; font-weight: 600; text-transform: uppercase; letter-spacing: .04em; color: #868e96; margin: .875rem .25rem .5rem; }
    .notif-item { display: flex; gap: .875rem; align-items: flex-start; padding: .875rem; border-radius: .75rem; border: .0625rem solid #eef0f4;
        margin-bottom: .5rem; transition: background .15s, box-shadow .15s; border-left: .25rem solid transparent; }
    .notif-item { position: relative; cursor: pointer; }
    .notif-item:hover { box-shadow: 0 .25rem .875rem rgba(16, 24, 40, .08); }
    /* El enlace del título cubre toda la tarjeta: cualquier clic abre el detalle. */
    .notif-item__enlace::after { content: ""; position: absolute; inset: 0; border-radius: inherit; }
    .notif-item__acciones { align-self: center; }
    .notif-item__ir { width: 2.25rem; height: 2.25rem; border-radius: 50%; display: grid; place-items: center; font-size: 1.5rem;
        color: #adb5bd; background: #f8f9fb; transition: background .15s, color .15s, transform .15s; }
    .notif-item:hover .notif-item__ir { background: #e7f0ff; color: #0d6efd; transform: translateX(.125rem); }
    .notif-item--no-leida { background: #f8faff; border-left-color: var(--notif-color); }
    .notif-item__icono { width: 2.625rem; height: 2.625rem; border-radius: 50%; display: grid; place-items: center; font-size: 1.25rem; flex-shrink: 0;
        color: var(--notif-color); background: #f1f3f5; background: color-mix(in srgb, var(--notif-color) 12%, #fff); }
    .notif-item__cuerpo { flex: 1; min-width: 0; }
    .notif-item__titulo { display: flex; align-items: center; gap: .5rem; }
    .notif-item__titulo a { color: #212529; text-decoration: none; }
    .notif-item__titulo a:hover { color: #0d6efd; }
    .notif-item--no-leida .notif-item__titulo a { font-weight: 600; }
    .notif-punto { width: .5rem; height: .5rem; border-radius: 50%; background: #0d6efd; flex-shrink: 0; }
    .notif-item__mensaje { margin: .25rem 0 0; color: #6c757d; font-size: .875rem; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
    .notif-item__meta { display: flex; flex-wrap: wrap; align-items: center; gap: .5rem .75rem; margin-top: .5rem; font-size: .7812rem; color: #868e96; }
    .notif-item__meta i { vertical-align: -.125rem; }
    .notif-chip { display: inline-flex; align-items: center; gap: .25rem; white-space: nowrap; border-radius: 1.25rem; padding: .125rem .625rem; font-weight: 500; }
    .notif-chip--normal { background: #f1f3f5; color: #495057; }
    .notif-chip--pronto { background: #fff4e5; color: #d9480f; }
    .notif-chip--urgente { background: #ffe3e3; color: #c92a2a; }
    .notif-item__acciones { display: flex; gap: .375rem; align-items: center; flex-shrink: 0; }

    .notif-vacio { text-align: center; padding: 3rem 1rem; color: #6c757d; }
    .notif-vacio__icono { width: 4.75rem; height: 4.75rem; border-radius: 50%; background: #e7f0ff; color: #0d6efd; display: grid;
        place-items: center; font-size: 2.25rem; margin: 0 auto .875rem; }
    .notif-vacio h5 { color: #343a40; font-weight: 600; margin-bottom: .25rem; }
    .notif-sin-resultados { text-align: center; color: #868e96; padding: 2rem 0; }
    .notif-sin-resultados > i { font-size: 2rem; display: block; margin-bottom: .375rem; }
    .notif-sin-resultados p { margin-bottom: .625rem; }
    .notif-item__tipo { color: #6c757d; }

    /* Detalle */
    .notif-detalle-nav { display: flex; justify-content: space-between; align-items: center; gap: .5rem; flex-wrap: wrap; margin-bottom: 1rem; }
    .notif-detalle-cabecera { position: relative; overflow: hidden; display: flex; gap: 1.125rem; align-items: flex-start; padding: 1.5rem; margin-bottom: 1rem;
        background: #fff; border-radius: 1rem; border: .0625rem solid #eef0f4; box-shadow: 0 .125rem .625rem rgba(16, 24, 40, .06); }
    .notif-detalle-cabecera::before { content: ""; position: absolute; top: 0; left: 0; right: 0; height: .25rem; background: var(--notif-color); }
    .notif-detalle-cabecera__icono { width: 3.75rem; height: 3.75rem; border-radius: 1rem; display: grid; place-items: center; font-size: 1.875rem; flex-shrink: 0;
        color: var(--notif-color); background: #f1f3f5; background: color-mix(in srgb, var(--notif-color) 12%, #fff); }
    .notif-detalle-cabecera__cuerpo { flex: 1; min-width: 0; }
    .notif-detalle-cabecera__tipo { font-size: .7812rem; font-weight: 600; text-transform: uppercase; letter-spacing: .04em; color: var(--notif-color); }
    .notif-detalle-cabecera__tipo i { vertical-align: -.125rem; }
    .notif-separador { color: #ced4da; margin: 0 .25rem; }
    .notif-detalle-cabecera__titulo { font-weight: 700; margin: .375rem 0 .625rem; line-height: 1.3; overflow-wrap: anywhere; }
    .notif-detalle-cabecera__meta { display: flex; flex-wrap: wrap; align-items: center; gap: .5rem .875rem; font-size: .8125rem; color: #868e96; }
    .notif-detalle-cabecera__meta i { vertical-align: -.125rem; }
    /* Franja para cerrar la tarea, al pie del mensaje: leer, ir al registro y volver a marcarla. */
    .notif-resolver { margin-top: 1.125rem; padding: .875rem 1rem; border-radius: .75rem; background: #f4fbf6; border: .0625rem solid #d3f9d8; transition: background .15s, border-color .15s; }
    .notif-resolver--confirmando { background: #fff9db; border-color: #ffec99; }
    .notif-resolver__paso { display: flex; align-items: center; gap: .875rem; }
    .notif-resolver__paso[hidden] { display: none; }
    .notif-resolver__icono { width: 2.5rem; height: 2.5rem; border-radius: .625rem; background: #d3f9d8; color: #2b8a3e; display: grid; place-items: center; font-size: 1.25rem; flex-shrink: 0; }
    .notif-resolver__icono--atencion { background: #ffec99; color: #e67700; }
    .notif-resolver__texto { flex: 1; min-width: 0; }
    .notif-resolver__texto strong { display: block; color: #343a40; font-weight: 600; }
    .notif-resolver__texto small { display: block; color: #868e96; font-size: .7812rem; }
    .notif-resolver .btn { border-radius: .625rem; white-space: nowrap; display: inline-flex; align-items: center; gap: .375rem; font-weight: 500; }
    .notif-resolver .btn i { font-size: 1.125rem; }
    .notif-resolver__boton { background: #fff; color: #2b8a3e; border: .0625rem solid #8ce99a; }
    .notif-resolver__boton:hover, .notif-resolver__boton:focus { background: #2b8a3e; color: #fff; border-color: #2b8a3e; }
    .notif-resolver__botones { display: flex; gap: .5rem; flex-shrink: 0; }
    .notif-resolver__error { margin-top: .625rem; font-size: .8125rem; color: #c92a2a; }
    .notif-detalle-bloque { padding: 1.25rem; margin-bottom: 1rem; }
    .notif-detalle-mensaje { font-size: .9688rem; line-height: 1.65; color: #343a40; margin-bottom: 0; overflow-wrap: anywhere; }
    .notif-detalle-enlace { display: flex; align-items: center; gap: .875rem; margin-top: 1.125rem; padding: .875rem 1rem; border-radius: .75rem; border: .0625rem solid #e3e8f0;
        color: #212529; text-decoration: none; transition: border-color .15s, background .15s; }
    .notif-detalle-enlace:hover { border-color: #0d6efd; background: #f8faff; color: #0d6efd; }
    .notif-detalle-enlace small { display: block; color: #868e96; font-size: .7812rem; }
    .notif-detalle-enlace__icono { width: 2.5rem; height: 2.5rem; border-radius: .625rem; background: #e7f0ff; color: #0d6efd; display: grid; place-items: center; font-size: 1.25rem; flex-shrink: 0; }

    .notif-linea { list-style: none; margin: 0; padding: 0; }
    .notif-linea__hito { position: relative; display: flex; gap: .875rem; padding-bottom: 1.125rem; }
    .notif-linea__hito:last-child { padding-bottom: 0; }
    .notif-linea__hito:not(:last-child)::before { content: ""; position: absolute; left: .9375rem; top: 2.125rem; bottom: .125rem; width: .125rem; background: #eef0f4; }
    .notif-linea__punto { width: 2rem; height: 2rem; border-radius: 50%; display: grid; place-items: center; font-size: 1rem; flex-shrink: 0; background: #f1f3f5; color: #495057; }
    .notif-linea__hito--primaria .notif-linea__punto { background: #e7f0ff; color: #0d6efd; }
    .notif-linea__hito--exito .notif-linea__punto { background: #d3f9d8; color: #2b8a3e; }
    .notif-linea__hito--peligro .notif-linea__punto { background: #ffe3e3; color: #c92a2a; }
    .notif-linea__hito--futura .notif-linea__punto { background: #fff; color: #f08c00; border: .125rem dashed #ffd8a8; }
    .notif-linea__texto { font-weight: 500; color: #343a40; padding-top: .3125rem; }
    .notif-linea__hito--futura .notif-linea__texto { color: #868e96; }
    .notif-linea__fecha { font-size: .7812rem; color: #868e96; }

    .notif-plazo__valor { font-size: 1.375rem; font-weight: 700; color: #343a40; }
    .notif-plazo__valor--pronto { color: #d9480f; }
    .notif-plazo__valor--urgente { color: #c92a2a; }
    .notif-plazo__barra { height: .5rem; border-radius: .5rem; background: #f1f3f5; overflow: hidden; margin: .625rem 0 .75rem; }
    .notif-plazo__barra span { display: block; height: 100%; border-radius: .5rem; background: #0d6efd; }
    .notif-plazo__barra .notif-plazo__avance--pronto { background: #f08c00; }
    .notif-plazo__barra .notif-plazo__avance--urgente { background: #e03131; }
    .notif-plazo__extremos { display: flex; justify-content: space-between; font-size: .7812rem; color: #868e96; }
    .notif-plazo__extremos strong { color: #343a40; font-weight: 600; }
    .notif-datos { display: grid; grid-template-columns: auto 1fr; gap: .625rem 1rem; margin: 0; font-size: .875rem; }
    .notif-datos dt { color: #868e96; font-weight: 500; }
    .notif-datos dd { margin: 0; color: #343a40; text-align: right; overflow-wrap: anywhere; }

    /* Administración */
    .notif-tabla { width: 100%; }
    .notif-tabla th { font-size: .75rem; text-transform: uppercase; letter-spacing: .04em; color: #868e96; font-weight: 600; border-bottom: .0625rem solid #eef0f4; padding: .625rem .75rem; }
    .notif-tabla td { padding: .75rem; border-bottom: .0625rem solid #f1f3f5; vertical-align: middle; font-size: .875rem; }
    .notif-tabla tr:last-child td { border-bottom: 0; }
    .notif-tabla .btn { white-space: nowrap; }
    .notif-tabla td.notif-canales { min-width: 11rem; }
    .notif-codigo { font-family: SFMono-Regular, Consolas, monospace; font-size: .7812rem; background: #f1f3f5; border-radius: .375rem; padding: .125rem .5rem; color: #495057; }
    .notif-seccion { padding: 1.125rem 1.25rem; border-bottom: .0625rem solid #eef0f4; }
    .notif-seccion:last-child { border-bottom: 0; }
    .notif-seccion h6 { font-weight: 600; margin-bottom: .25rem; }
    .notif-seccion .notif-ayuda { color: #868e96; font-size: .8125rem; margin-bottom: .875rem; }
    .notif-seccion .form-label { font-size: .8125rem; font-weight: 500; color: #495057; margin-bottom: .25rem; }
    .notif-seccion .form-control, .notif-seccion .form-select { border-radius: .625rem; }
    .notif-marcadores { display: flex; flex-wrap: wrap; gap: .375rem; }
    .notif-marcadores code { background: #e7f0ff; color: #0d6efd; border-radius: .375rem; padding: .125rem .5rem; font-size: .7812rem; cursor: copy; }
    .notif-interruptor { display: flex; align-items: flex-start; gap: .625rem; padding: .75rem .875rem; border: .0625rem solid #eef0f4; border-radius: .75rem; height: 100%; }
    .notif-interruptor .form-check-input { margin: .1875rem 0 0; float: none; flex-shrink: 0; }
    .notif-interruptor.form-switch { padding-left: .875rem; }
    .notif-interruptor small { display: block; color: #868e96; }
    .notif-previa { position: sticky; top: 5.625rem; }
    .notif-previa__bandeja { padding: 1rem; border-bottom: .0625rem solid #eef0f4; }
    .notif-previa iframe { width: 100%; height: 26.25rem; border: 0; border-radius: 0 0 1rem 1rem; background: #f4f6f8; }
    .notif-previa__etiqueta { font-size: .75rem; font-weight: 600; text-transform: uppercase; letter-spacing: .04em; color: #868e96; margin-bottom: .5rem; }
    .notif-acciones-pie { display: flex; justify-content: flex-end; gap: .5rem; padding: 1rem 1.25rem; border-top: .0625rem solid #eef0f4; }
    .notif-aviso-alcance { background: #f8f9fb; border: .0625rem dashed #dee2e6; border-radius: .75rem; padding: .625rem .875rem; font-size: .8125rem; color: #6c757d; }

    @media (max-width: 48rem) {
        .notif-resumen { gap: .5rem; }
        .notif-tarjeta { flex-direction: column; align-items: flex-start; gap: .5rem; padding: .75rem; }
        .notif-tarjeta__icono { width: 2.25rem; height: 2.25rem; font-size: 1.125rem; }
        .notif-tarjeta__etiqueta { font-size: .75rem; }
        .notif-pestanas { display: flex; width: 100%; overflow-x: auto; flex-wrap: nowrap; scrollbar-width: none; }
        .notif-pestanas::-webkit-scrollbar { display: none; }
        .notif-pestanas .nav-link { white-space: nowrap; padding: .4375rem .625rem; }
        .notif-filtros { display: grid; grid-template-columns: 1fr 1fr; gap: .75rem; }
        .notif-filtro:first-child { grid-column: 1 / -1; }
        .notif-segmentos { display: flex; }
        .notif-segmentos button { flex: 1; }
        .notif-barra__derecha, .notif-buscador { min-width: 0; width: 100%; }
        .notif-buscador { flex: 1; }
        .notif-filtro { min-width: 0; flex: 1 1 8.75rem; }
        .notif-detalle-cabecera { flex-wrap: wrap; padding: 1.125rem; }
        .notif-resolver__paso { flex-wrap: wrap; }
        .notif-resolver__texto { flex-basis: calc(100% - 3.375rem); }
        .notif-resolver__boton, .notif-resolver__botones { width: 100%; justify-content: center; }
        .notif-resolver__botones .btn { flex: 1; justify-content: center; }
        .notif-detalle-cabecera__titulo { font-size: 1.375rem; }
        .notif-item { flex-wrap: nowrap; }
        .notif-item__acciones { width: auto; }
    }
</style>
