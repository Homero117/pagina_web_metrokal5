<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #f0f0f0;
        }
        .login-container {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .login-container h2 {
            margin-bottom: 20px;
            text-align: center;
        }
        .login-container input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .login-container button {
            width: 100%;
            padding: 10px;
            background-color: #007BFF;
            border: none;
            color: white;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
        }
        .login-container button:hover {
            background-color: #0056b3;
        }
        .error-message {
            color: red;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Inicio de Sesión</h2>
        <form id="loginForm">
            <input type="text" id="username" placeholder="Usuario" required>
            <input type="password" id="password" placeholder="Contraseña" required>
            <button type="submit">Iniciar Sesión</button>
        </form>
        <p class="error-message" id="errorMessage"></p>
    </div>

    <script>
        // Manejar el evento de envío del formulario
        document.getElementById('loginForm').addEventListener('submit', function (event) {
            event.preventDefault(); // Evitar el envío tradicional del formulario

            const username = document.getElementById('username').value;
            const password = document.getElementById('password').value;

            // Validar credenciales
            if (username === "kikin" && password === "12345678") {
                // Redirigir a OrdenInterna.php si las credenciales son correctas
                window.location.href = "RegistroOrden.php";
            } else {
                // Mostrar mensaje de error si las credenciales son incorrectas
                document.getElementById('errorMessage').textContent = "Usuario o contraseña incorrectos.";
            }
        });
    </script>
</body>
</html>
