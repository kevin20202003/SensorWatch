<?php
session_start();
if (empty($_SESSION["id"])) {
    header("location: ../auth/login.php");
}
?>
<!doctype html>
<html lang="en">

<head>
    <title>Administrador</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Bootstrap CSS v5.2.1 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-iYQeCzEYFbKjA/T2uDLTpkwGzCiq6soy8tYaI1GyVh/UjpbCx/TYkiZhlZB6+fzT" crossorigin="anonymous">
    <link rel="icon" href="img/logoP.png" type="image/png">
    <!-- Incluye SweetAlert2 desde una CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="css/style.css">

    <style>

        .navbar-brand img {
            height: 20%;
            width: 40%;
            border-radius: 8px;
            margin-right: 40%;
        }

        .navbar-nav {
            display: flex;
            align-items: center;
            /* Alinea verticalmente los elementos */
        }

        .nav-item {
            margin-left: 10px;
            /* Espacio entre los elementos del menú */
        }

        .navbar-collapse {
            justify-content: flex-end;
            /* Alinea los elementos del menú a la derecha */
        }

        @media (max-width: 991.98px) {
            .navbar-nav {
                flex-direction: row !important;
                /* Mantiene los elementos en una fila */
                margin-top: -7%;
                margin-left: 80%;
                margin-right: 0;
            }

            .nav-item {
                margin-left: 0;
                /* Elimina el espacio entre los elementos */
            }

            .navbar-collapse {
                justify-content: flex-start;
                /* Alinea los elementos del menú a la izquierda */
            }

            .navbar-nav .dropdown .btn {
                font-size: 0.5em;
                /* Ajustar el tamaño de fuente para pantallas más pequeñas */
                padding: 0.3rem 0.5rem;
                /* Ajustar el padding para botones más pequeños */
            }
        }

        @media (max-width: 600px) {
            .navbar-nav {
                flex-direction: row !important;
                /* Mantiene los elementos en una fila */
                margin-top: -11%;
                margin-left: 50%;
            }

        }

        .dropdown-toggle::after {
            display: none;
        }

        .nav-item {
            height: 40px;
        }

        .navbar-nav .dropdown .btn {
            margin-right: 50px;
        }

        .navbar-nav {
            align-items: center;
        }

        .tranding-slide-img i {
            font-size: 158px;
            /* Tamaño del ícono */
            color: black;
            /* Color del ícono */
        }
    </style>
</head>

<body style="background-image: url(img/fondo14.jpg); background-repeat: no-repeat; background-size: cover; height: 100vh; margin: 0;">
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="#">
                <img src="img/logoF-removebg-preview.png" alt="Logo">
            </a>

            <div class="navbar-collapse justify-content-end">
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <button class="btn btn-dark dropdown-toggle" type="button" id="dropdownMenuButton2" data-bs-toggle="dropdown" aria-expanded="false">
                            <span style="font-size: 2em;"><?php echo $_SESSION["nombre"]; ?></span>
                            <i class="fas fa-user" style="font-size: 2em;"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="dropdownMenuButton2">
                            <li><a class="dropdown-item" href="#" style="font-size: 1.5em;">Cambiar Contraseña</a></li>
                            <li><a class="dropdown-item" href="controlador/controlador_cerrar_sesion.php" style="font-size: 1.5em;">Cerrar Sesión</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <br><br>
    <!-- Button trigger modal -->
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal" style="margin-left: 5%; font-size: medium;">
        <i class="fa-solid fa-plus"></i> Agregar usuario
    </button>

    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5 text-center" id="exampleModalLabel">Agregar Usuario</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Formulario de edición -->
                    <form id="plusUserForm" method="post" action="">
                        <div class="mb-3">
                            <label for="usernameModal" class="form-label">Nombre de Usuario</label>
                            <input type="text" class="form-control" id="usernameModal" name="nombre" style="font-size: medium;" required>
                        </div>
                        <div class="mb-3">
                            <label for="emailModal" class="form-label">Correo Electrónico</label>
                            <input type="email" class="form-control" id="emailModal" name="correo" style="font-size: medium;" required>
                        </div>
                        <div class="mb-3">
                            <label for="passwordModal" class="form-label">Contraseña</label>
                            <input type="password" class="form-control" id="passwordModal" name="contraseña" style="font-size: medium;" required>
                        </div>
                        <div class="mb-3">
                            <label for="roleModal" class="form-label">Rol</label>
                            <select class="form-select" id="roleModal" name="rol" required style="font-size: medium;">
                                <option value="" disabled selected>Selecciona un rol...</option>
                                <option value="Administrador">Administrador</option>
                                <option value="Cliente">Cliente</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="statusModal" class="form-label">Estado</label>
                            <select class="form-select" id="statusModal" name="estado" required style="font-size: medium;">
                                <option value="" disabled selected>Selecciona un estado...</option>
                                <option value="Activo">Activo</option>
                                <option value="Inactivo">Inactivo</option>
                            </select>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="font-size: medium;">Cerrar</button>
                            <input class="btn btn-primary" type="submit" value="Registrarse" name="btningresar" style="font-size: medium;">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de usuarios -->
    <br><br><br><br>
    <table class="table table-striped text-center">
        <thead>
            <tr>
                <th scope="col">ID</th>
                <th scope="col">NOMBRE_USUARIO</th>
                <th scope="col">CORREO ELECTRÓNICO</th>
                <th scope="col">CONTRASEÑA</th>
                <th scope="col">ROL</th>
                <th scope="col">ESTADO</th>
                <th scope="col">ACCIONES</th>
            </tr>
        </thead>
        <tbody>
            <?php
            include('modelo/conexion.php');

            $query = "SELECT * FROM usuarios";
            $result = mysqli_query($conexion, $query);

            while ($row = mysqli_fetch_assoc($result)) {
                $contraseña_encriptada = password_hash($row['contraseña'], PASSWORD_DEFAULT);
                echo "<tr>";
                echo "<td>{$row['id']}</td>";
                echo "<td>{$row['nombre']}</td>";
                echo "<td>{$row['correo_electronico']}</td>";
                echo "<td>{$contraseña_encriptada}</td>";
                echo "<td>{$row['rol']}</td>";
                echo "<td>{$row['estado']}</td>";
                echo "<td>
                <button class='btn btn-warning edit-btn' data-id='{$row['id']}' data-bs-toggle='modal' data-bs-target='#editModal'><i class='fa-solid fa-pen-to-square'></i></button>
                <button class='btn btn-danger delete-btn' data-id='{$row['id']}'><i class='fa-solid fa-trash'></i></button>
                </td>";
                echo "</tr>";
            }

            // Agrega un campo oculto para manejar la acción de actualización
            echo "<input type='hidden' id='action' name='action' value='update'>";
            ?>
        </tbody>
    </table>

    <!-- Modal de edición -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="editModalLabel">Editar Usuario</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Formulario de edición -->
                    <form id="editUserForm" method="post">
                        <input type="hidden" id="editUserId" name="id_usuario">
                        <div class="mb-3">
                            <label for="editUsername" class="form-label">Nombre de Usuario</label>
                            <input type="text" class="form-control" id="editUsername" name="nombre_usuario" required style="font-size: medium;">
                        </div>
                        <div class="mb-3">
                            <label for="editEmail" class="form-label">Correo Electrónico</label>
                            <input type="email" class="form-control" id="editEmail" name="correo" required style="font-size: medium;">
                        </div>
                        <div class="mb-3">
                            <label for="editPassword" class="form-label">Contraseña</label>
                            <input type="password" class="form-control" id="editPassword" name="contraseña" required style="font-size: medium;">
                        </div>
                        <div class="mb-3">
                            <label for="editRole" class="form-label">Rol</label>
                            <select class="form-select" id="editRole" name="rol" required style="font-size: medium;">
                                <option value="" disabled selected>Selecciona un rol...</option>
                                <option value="Administrador">Administrador</option>
                                <option value="Cliente">Cliente</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="editStatus" class="form-label">Estado</label>
                            <select class="form-select" id="editStatus" name="estado" required style="font-size: medium;">
                                <option value="" disabled selected>Selecciona un estado...</option>
                                <option value="Activo">Activo</option>
                                <option value="Inactivo">Inactivo</option>
                            </select>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="font-size: medium;">Cerrar</button>
                            <input class="btn btn-primary" type="submit" value="Guardar Cambios" style="font-size: medium;">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
     <!-- Footer -->
     <footer>
        <div class="left">
            <p>&copy; 2024 Kevin Chala. All rights reserved.</p>
        </div>
        <div class="right">
            <ul>
                <li class="item">
                    <a href="#">
                        <i class="fa-brands fa-instagram icon"></i>
                    </a>
                </li>
                <li class="item">
                    <a href="#">
                        <i class="fa-brands fa-linkedin icon"></i>
                    </a>
                </li>
                <li class="item">
                    <a href="#">
                        <i class="fa-brands fa-facebook icon"></i>
                    </a>
                </li>
                <li class="item">
                    <a href="#">
                        <i class="fa-brands fa-x-twitter icon"></i>
                    </a>
                </li>
            </ul>
        </div>
    </footer>

    <script>
        // Manejar la adición de usuario con AJAX
        document.getElementById('plusUserForm').addEventListener('submit', function(event) {
            event.preventDefault();
            const formData = new FormData(this);

            fetch('controlador/registro_usuario.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Usuario Agregado',
                            text: data.message,
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.message,
                        });
                    }
                })
                .catch(error => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Hubo un problema al agregar el usuario. Intenta de nuevo más tarde.',
                    });
                });
        });

        document.querySelectorAll('.edit-btn').forEach(button => {
            button.addEventListener('click', () => {
                const userId = button.getAttribute('data-id');
                fetch(`controlador/get_user.php?id=${userId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const user = data.data;
                            document.getElementById('editUserId').value = user.id;
                            document.getElementById('editUsername').value = user.nombre;
                            document.getElementById('editEmail').value = user.correo_electronico;
                            document.getElementById('editPassword').value = user.contraseña;
                            document.getElementById('editRole').value = user.rol;
                            document.getElementById('editStatus').value = user.estado;
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: data.message,
                            });
                        }
                    });
            });
        });

        // Manejar la actualización del usuario con AJAX
        document.getElementById('editUserForm').addEventListener('submit', function(event) {
            event.preventDefault();
            const formData = new FormData(this);

            fetch('controlador/actualizar_usuario.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Usuario Actualizado',
                            text: data.message,
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.message,
                        });
                    }
                })
                .catch(error => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Hubo un problema al actualizar el usuario. Intenta de nuevo más tarde.',
                    });
                });
        });

        // Manejar la eliminación del usuario con AJAX
        document.querySelectorAll('.delete-btn').forEach(button => {
            button.addEventListener('click', () => {
                const userId = button.getAttribute('data-id');
                Swal.fire({
                    title: '¿Estás seguro?',
                    text: "No podrás revertir esta acción",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, eliminarlo',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch(`controlador/eliminar_usuario.php?id=${userId}`, {
                                method: 'POST',
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    Swal.fire(
                                        'Eliminado!',
                                        data.message,
                                        'success'
                                    ).then(() => {
                                        location.reload();
                                    });
                                } else {
                                    Swal.fire(
                                        'Error!',
                                        data.message,
                                        'error'
                                    );
                                }
                            })
                            .catch(error => {
                                Swal.fire(
                                    'Error!',
                                    'Hubo un problema al eliminar el usuario. Intenta de nuevo más tarde.',
                                    'error'
                                );
                            });
                    }
                });
            });
        });
    </script>

    <!-- Bootstrap JavaScript Libraries -->
    <script src="https://kit.fontawesome.com/e9f58d382f.js" crossorigin="anonymous"></script>
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    <script src="https://unpkg.com/swiper@8/swiper-bundle.min.js"></script>
    <script src="js/script.js"></script>
    <!-- Bootstrap JavaScript Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/js/bootstrap.min.js" integrity="sha384-7VPbUDkoPSGFnVtYi0QogXtr74QeVeeIs99Qfg5YCF+TidwNdjvaKZX19NZ/e6oz" crossorigin="anonymous">
    </script>
</body>

</html>