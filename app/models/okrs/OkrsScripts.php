<script>
    class OkrsScripts {

        constructor() {
            // Espera a que el DOM esté cargado antes de crear el contenedor
            document.addEventListener("DOMContentLoaded", () => {
                this._crearContenedorToasts();
            });
        }

        EditarSeguimiento(entidad, id_registro, valor) {
            jQuery.ajax({
                    url: "api/okrs/editar_seguimiento.php",
                    type: "POST",
                    data: {
                        entidad,
                        id_registro,
                        valor
                    },
                    dataType: "json"
                })
                .done((resp) => { 
                    //window.location.reload();
                    console.log(resp);
                    
                    //this.mostrarToast("Seguimiento actualizado correctamente");
                })
                .fail((xhr) => {
                    console.error("✗ Error en el servidor", xhr.responseText);
                    this.mostrarToast("Error al guardar", "danger");
                });
        }

        EditarTipoIntegrante(id_Okrs_Equipos, tipo) {
            jQuery.ajax({
                    url: "api/okrs/editar_tipo_integrante.php",
                    type: "POST",
                    data: {
                        id_Okrs_Equipos,
                        tipo
                    },
                    dataType: "json"
                })
                .done((resp) => {
                    window.location.href = "?pg=okrs/okr/integrantes"
                    //this.mostrarToast("Tipo de integrante actualizado correctamente");
                })
                .fail((xhr) => {
                    console.error("✗ Error en el servidor", xhr.responseText);
                    this.mostrarToast("Error al guardar", "danger");
                });
        }


        _crearContenedorToasts() {
            if (!document.getElementById("toast-container-custom")) {
                const cont = document.createElement("div");
                cont.id = "toast-container-custom";
                cont.style.position = "fixed";
                cont.style.bottom = "20px";
                cont.style.right = "20px";
                cont.style.zIndex = "100";
                cont.style.display = "flex";
                cont.style.flexDirection = "column";
                cont.style.gap = "10px";
                document.body.appendChild(cont);
            }
        }

        mostrarToast(mensaje, tipo = "success") {
            const color = tipo === "success" ? "bg-success" : "bg-danger";

            const contenedor = document.getElementById("toast-container-custom");
            if (!contenedor) return; // ← Safety check para evitar errores

            const toast = document.createElement("div");
            toast.className = `toast align-items-center text-white ${color} show`;
            toast.role = "alert";

            toast.style.minWidth = "260px";
            toast.style.boxShadow = "0 4px 12px rgba(0,0,0,0.2)";
            toast.style.borderRadius = "8px";

            toast.innerHTML = `
                <div class="d-flex">
                    <div class="toast-body">${mensaje}</div>
                </div>
            `;

            contenedor.appendChild(toast);

            setTimeout(() => toast.remove(), 2000);
        }
    }
</script>