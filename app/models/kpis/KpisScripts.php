<script>
    class KpisScripts {

        constructor() {
            // Espera a que el DOM esté cargado antes de crear el contenedor
            document.addEventListener("DOMContentLoaded", () => {
                this._crearContenedorToasts();
            });
        }

        EditarTipoIntegrante(id_Kpi_Colaborador, tipo) {
            
            jQuery.ajax({
                    url: "api/kpis/editar_tipo_integrante.php",
                    type: "POST",
                    data: {
                        id_Kpi_Colaborador,
                        tipo
                    },
                    dataType: "json"
                })
                .done((resp) => {
                    const query = window.location.search;
                    window.location.href = query
                    //this.mostrarToast("El tipo de integrante se ha ctualizado correctamente");
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
                cont.style.zIndex = "5000";
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
                <div>
                    <div class="toast-body">${mensaje}</div>
                </div>
            `;

            contenedor.appendChild(toast);

            setTimeout(() => toast.remove(), 5000);
        }
    }
</script>