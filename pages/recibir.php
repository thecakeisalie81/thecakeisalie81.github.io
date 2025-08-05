<?php
require '../system/session.php';
require '../layout/header.php';
?>
<div class="mt-3">



    <div>
        <h5>Libros que estan prestados:</h5>
        <br>
        <div class="table-responsive">
            <table id="tablaLibros" class="display w-100">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Autor</th>
                        <th>Género</th>
                        <th>Año</th>
                        <th>Reserva ID</th>
                        <th>Cliente</th>
                        <th>Fecha reserva</th>
                        <th>Fecha devolucion</th>
                        <th>Acciones</th> <!-- Agrega esta -->
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $resultado = mysqli_query($conn, "SELECT id, nombre, autor, genero, anno FROM libro WHERE estado = 2");
                    $reservas = mysqli_query($conn, "SELECT id, libro_id, cliente_id, fecha_reserva, fecha_devolucion FROM reserva WHERE estado = 1");
                    while ($row = mysqli_fetch_assoc($resultado) and $reserva = mysqli_fetch_assoc($reservas)) {
                        $cliente_id = $reserva['cliente_id'];
                        $cliente_query = mysqli_query($conn, "SELECT nombre FROM cliente WHERE id = $cliente_id");
                        $cliente = mysqli_fetch_assoc($cliente_query);
                        $reserva['cliente_id'] = $cliente['nombre'];
                        echo "<tr id='libro-{$row['id']}'>
                                <td>{$row['nombre']}</td>
                                <td>{$row['autor']}</td>
                                <td>{$row['genero']}</td>
                                <td>{$row['anno']}</td>
                                <td>{$reserva['id']}</td>
                                <td>{$reserva['cliente_id']}</td>
                                <td>{$reserva['fecha_reserva']}</td>
                                <td>{$reserva['fecha_devolucion']}</td>
                                <td class='text-center'>
                                    <button class='btn btn-sm btn-danger' onclick='mostrarModalDevolucion({$row['id']},{$reserva['id']})'>Devolver <span data-feather=\"user-check\" class=\"align-text-bottom\"></span></button>
                                </td>
                              </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>


<div class="modal fade" id="modalDevolver" tabindex="-1" aria-labelledby="modalDevolverLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="modalDevolverLabel">¿Confirmar devolución del libro?</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <form onsubmit="confirmarDevolucion(); return false;">
                    <input type="hidden" id="libroADevolver" value="0">
                    <input type="hidden" id="reservaADevolver" value="0">
                    <div class="mb-3 text-center">
                        <button type="submit" class="btn btn-primary">Confirmar</button>
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<script>
    document.addEventListener('DOMContentLoaded', function () {
        new DataTable('#tablaLibros', {
            language: {
                url: 'https://cdn.datatables.net/plug-ins/2.0.7/i18n/es-ES.json'
            }
        });
    });

     function mostrarModalDevolucion(libroId, reservaId) {
        document.getElementById('libroADevolver').value = libroId;
        document.getElementById('reservaADevolver').value = reservaId;
        const modal = new bootstrap.Modal(document.getElementById('modalDevolver'));
        modal.show();
    }

     function confirmarDevolucion() {
        const libroId = document.getElementById('libroADevolver').value;
        const reservaId = document.getElementById('reservaADevolver').value;

        if (libroId == 0 || reservaId == 0) {
            alert('Datos inválidos para la devolución.');
            return;
        }

        fetch('../system/ajax.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                accion: 'devolver',
                libro_id: libroId,
                reserva_id: reservaId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const fila = document.getElementById('libro-' + libroId);
                fila.parentNode.removeChild(fila);
                const modal = bootstrap.Modal.getInstance(document.getElementById('modalDevolver'));
                modal.hide();
                Swal.fire({
                    title: "Éxito!",
                    text: data.message,
                    icon: "success"
                });
            } else {
                Swal.fire({
                    title: "Error!",
                    text: data.message,
                    icon: "error"
                });
            }
        })
        .catch(error => console.error('Error:', error));
    }
</script>

<?php
require '../layout/footer.php';
?>