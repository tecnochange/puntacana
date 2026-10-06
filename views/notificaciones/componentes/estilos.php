<?php
// Estilos de las pantallas de notificaciones (bandeja, detalle y administración).
// Se incluye una vez por página.
?>
<style>
    .notif { max-width: 1100px; margin: 0 auto; padding: 0 12px 40px; }
    .notif-encabezado { display: flex; justify-content: space-between; align-items: center; gap: 16px; flex-wrap: wrap; margin-bottom: 20px; }
    .notif-encabezado h3 { margin: 0; font-weight: 700; }
    .notif-encabezado p { margin: 2px 0 0; color: #6c757d; }

    .notif-resumen { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 16px; margin-bottom: 20px; }
    .notif-tarjeta { background: #fff; border-radius: 14px; padding: 16px 18px; display: flex; align-items: center; gap: 14px;
        box-shadow: 0 2px 10px rgba(16, 24, 40, .06); border: 1px solid #eef0f4; }
    .notif-tarjeta__icono { width: 46px; height: 46px; border-radius: 12px; display: grid; place-items: center; font-size: 22px; flex-shrink: 0; }
    .notif-tarjeta__valor { font-size: 24px; font-weight: 700; line-height: 1; }
    .notif-tarjeta__etiqueta { color: #6c757d; font-size: 13px; margin-top: 4px; }
    .notif-tarjeta--pendientes .notif-tarjeta__icono { background: #e7f0ff; color: #0d6efd; }
    .notif-tarjeta--pronto .notif-tarjeta__icono { background: #fff4e5; color: #f08c00; }
    .notif-tarjeta--sin-leer .notif-tarjeta__icono { background: #f1ecff; color: #7048e8; }

    .notif-panel { background: #fff; border-radius: 16px; box-shadow: 0 2px 10px rgba(16, 24, 40, .06); border: 1px solid #eef0f4; }
    .notif-barra { display: flex; justify-content: space-between; align-items: center; gap: 12px; flex-wrap: wrap; padding: 14px 16px; border-bottom: 1px solid #eef0f4; }
    .notif-pestanas { display: inline-flex; background: #f3f5f9; border-radius: 12px; padding: 4px; gap: 2px; }
    .notif-pestanas .nav-link { border: 0; border-radius: 9px; color: #495057; padding: 7px 14px; font-size: 14px; display: flex; align-items: center; gap: 6px; }
    .notif-pestanas .nav-link.active { background: #fff; color: #0d6efd; box-shadow: 0 1px 4px rgba(16, 24, 40, .12); font-weight: 600; }
    .notif-contador { background: #e9ecef; color: #495057; border-radius: 20px; padding: 0 8px; font-size: 12px; line-height: 20px; }
    .nav-link.active .notif-contador { background: #e7f0ff; color: #0d6efd; }
    .notif-buscador { position: relative; min-width: 240px; }
    .notif-buscador i { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #adb5bd; }
    .notif-buscador input { padding-left: 34px; border-radius: 10px; }

    .notif-contenido { padding: 6px 16px 16px; }
    .notif-grupo__titulo { font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: .04em; color: #868e96; margin: 14px 4px 8px; }
    .notif-item { display: flex; gap: 14px; align-items: flex-start; padding: 14px; border-radius: 12px; border: 1px solid #eef0f4;
        margin-bottom: 8px; transition: background .15s, box-shadow .15s; border-left: 4px solid transparent; }
    .notif-item:hover { box-shadow: 0 4px 14px rgba(16, 24, 40, .08); }
    .notif-item--no-leida { background: #f8faff; border-left-color: var(--notif-color); }
    .notif-item__icono { width: 42px; height: 42px; border-radius: 50%; display: grid; place-items: center; font-size: 20px; flex-shrink: 0;
        color: var(--notif-color); background: #f1f3f5; background: color-mix(in srgb, var(--notif-color) 12%, #fff); }
    .notif-item__cuerpo { flex: 1; min-width: 0; }
    .notif-item__titulo { display: flex; align-items: center; gap: 8px; }
    .notif-item__titulo a { color: #212529; text-decoration: none; }
    .notif-item__titulo a:hover { color: #0d6efd; }
    .notif-item--no-leida .notif-item__titulo a { font-weight: 600; }
    .notif-punto { width: 8px; height: 8px; border-radius: 50%; background: #0d6efd; flex-shrink: 0; }
    .notif-item__mensaje { margin: 4px 0 0; color: #6c757d; font-size: 14px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
    .notif-item__meta { display: flex; flex-wrap: wrap; align-items: center; gap: 8px 12px; margin-top: 8px; font-size: 12.5px; color: #868e96; }
    .notif-item__meta i { vertical-align: -2px; }
    .notif-chip { border-radius: 20px; padding: 2px 10px; font-weight: 500; }
    .notif-chip--normal { background: #f1f3f5; color: #495057; }
    .notif-chip--pronto { background: #fff4e5; color: #d9480f; }
    .notif-chip--urgente { background: #ffe3e3; color: #c92a2a; }
    .notif-item__acciones { display: flex; gap: 6px; align-items: center; flex-shrink: 0; }

    .notif-vacio { text-align: center; padding: 48px 16px; color: #6c757d; }
    .notif-vacio__icono { width: 76px; height: 76px; border-radius: 50%; background: #e7f0ff; color: #0d6efd; display: grid;
        place-items: center; font-size: 36px; margin: 0 auto 14px; }
    .notif-vacio h5 { color: #343a40; font-weight: 600; margin-bottom: 4px; }
    .notif-sin-resultados { text-align: center; color: #868e96; padding: 28px 0; }

    /* Administración */
    .notif-tabla { width: 100%; }
    .notif-tabla th { font-size: 12px; text-transform: uppercase; letter-spacing: .04em; color: #868e96; font-weight: 600; border-bottom: 1px solid #eef0f4; padding: 10px 12px; }
    .notif-tabla td { padding: 12px; border-bottom: 1px solid #f1f3f5; vertical-align: middle; font-size: 14px; }
    .notif-tabla tr:last-child td { border-bottom: 0; }
    .notif-codigo { font-family: SFMono-Regular, Consolas, monospace; font-size: 12.5px; background: #f1f3f5; border-radius: 6px; padding: 2px 8px; color: #495057; }
    .notif-seccion { padding: 18px 20px; border-bottom: 1px solid #eef0f4; }
    .notif-seccion:last-child { border-bottom: 0; }
    .notif-seccion h6 { font-weight: 600; margin-bottom: 4px; }
    .notif-seccion .notif-ayuda { color: #868e96; font-size: 13px; margin-bottom: 14px; }
    .notif-seccion .form-label { font-size: 13px; font-weight: 500; color: #495057; margin-bottom: 4px; }
    .notif-seccion .form-control, .notif-seccion .form-select { border-radius: 10px; }
    .notif-marcadores { display: flex; flex-wrap: wrap; gap: 6px; }
    .notif-marcadores code { background: #e7f0ff; color: #0d6efd; border-radius: 6px; padding: 2px 8px; font-size: 12.5px; cursor: copy; }
    .notif-interruptor { display: flex; align-items: flex-start; gap: 10px; padding: 12px 14px; border: 1px solid #eef0f4; border-radius: 12px; height: 100%; }
    .notif-interruptor .form-check-input { margin: 3px 0 0; float: none; flex-shrink: 0; }
    .notif-interruptor.form-switch { padding-left: 14px; }
    .notif-interruptor small { display: block; color: #868e96; }
    .notif-previa { position: sticky; top: 90px; }
    .notif-previa__bandeja { padding: 16px; border-bottom: 1px solid #eef0f4; }
    .notif-previa iframe { width: 100%; height: 420px; border: 0; border-radius: 0 0 16px 16px; background: #f4f6f8; }
    .notif-previa__etiqueta { font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: .04em; color: #868e96; margin-bottom: 8px; }
    .notif-acciones-pie { display: flex; justify-content: flex-end; gap: 8px; padding: 16px 20px; border-top: 1px solid #eef0f4; }
    .notif-aviso-alcance { background: #f8f9fb; border: 1px dashed #dee2e6; border-radius: 12px; padding: 10px 14px; font-size: 13px; color: #6c757d; }

    @media (max-width: 768px) {
        .notif-resumen { grid-template-columns: 1fr; }
        .notif-buscador { min-width: 0; width: 100%; }
        .notif-item { flex-wrap: wrap; }
        .notif-item__acciones { width: 100%; justify-content: flex-end; }
    }
</style>
