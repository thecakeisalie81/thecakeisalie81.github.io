<?php
session_start();

$Host_Servidor   = 'localhost';
$Usuario_BD      = 'root';
$Password_BD     = '';
$Nombre_BD       = 'usuarios_db';
$Conexion        = mysqli_connect($Host_Servidor, $Usuario_BD, $Password_BD, $Nombre_BD);

if (!$Conexion) die('Error de conexión: ' . mysqli_connect_error());


if (isset($_POST['Registrar'])) {
    $Usuario_Input    = mysqli_real_escape_string($Conexion, $_POST['Usuario']);
    $Email_Input      = mysqli_real_escape_string($Conexion, $_POST['Email']);
    $Contrasena_Hash  = password_hash($_POST['Contrasena'], PASSWORD_DEFAULT);
    $Sql_Insert       = "
        INSERT INTO usuarios (usuario, email, contrasena)
        VALUES ('$Usuario_Input', '$Email_Input', '$Contrasena_Hash')
    ";
    $Mensaje = mysqli_query($Conexion, $Sql_Insert)
        ? 'Registro exitoso.'
        : 'Error: ' . mysqli_error($Conexion);
}

if (isset($_POST['Login'])) {
    $Usuario_Input   = mysqli_real_escape_string($Conexion, $_POST['Usuario']);
    $Contrasena_Input = $_POST['Contrasena'];
    $Sql_Select      = "
        SELECT id, contrasena
        FROM usuarios
        WHERE usuario = '$Usuario_Input'
        LIMIT 1
    ";
    $Resultado = mysqli_query($Conexion, $Sql_Select);
    $Fila = mysqli_fetch_assoc($Resultado);

    if ($Fila && password_verify($Contrasena_Input, $Fila['contrasena'])) {
        $_SESSION['Usuario_ID'] = $Fila['id'];
        $Mensaje = 'Login exitoso.';
    } else {
        $Mensaje = 'Usuario o contraseña incorrectos.';
    }

}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Login / Registro</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
</head>

<body>
    <?php if (!empty($Mensaje)): ?>
    <p><?= $Mensaje ?></p>
    <?php endif ?>

    <div class="d-flex justify-content-center align-items-center vh-100">
        <div class="contenidos">
            <div class="row">
                <div class="col rounded-3 border border-4">
                    <div class="container">
                        <h2 class="text-center mt-5">Registro</h2>
                        <form method="post" class="form-group"><div class="mb-3">
                            <label class="form-label fw-semibold">Usuario: <input class="form-control" type="text" name="Usuario" required></label><br>
                            <label class="form-label fw-semibold">Email: <input class="form-control" type="email" name="Email" required></label><br>
                            <label class="form-label fw-semibold">Contraseña: <input class="form-control" type="password" name="Contrasena" required></label><br>
                            <button class="btn btn-outline-primary" type="submit" name="Registrar">Registrar</button>
                        </div>
                        </form>
                    </div>
                </div>

                <div class="col rounded-3 border border-4">
                    <div class="container">
                        <h2 class="text-center mt-5">Login</h2>
                        <form method="post" class="form-group"><div class="mb-3">
                            <label class="form-label fw-semibold">Usuario: <input class="form-control" type="text" name="Usuario" required></label><br>
                            <label class="form-label fw-semibold">Contraseña: <input class="form-control" type="password" name="Contrasena" required></label><br>
                            <button class="btn btn-outline-primary" type="submit" name="Login">Ingresar</button>
                        </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>

</body>

</html>