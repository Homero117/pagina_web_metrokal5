<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menú Minimalista</title>
    <style>
        /* Estilo general */
        body {
            margin: 0;
            font-family: 'Arial', sans-serif;
            /*background-color: #f4f4f9;*/
        }

        /* Contenedor del menú */
        .menu {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 50px 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); /* Sombra sutil */
            position: relative;
        }

        /* Logo */
        .logo {
            position: absolute;
            left: 20px; /* Mantenerlo a la izquierda */
        }

        .logo img {
            height: 95px;
        }

        /* Opciones del menú */
        .menu-options {
            display: flex;
            justify-content: center;
            width: 100%; /* Asegura el centrado del menú */
            gap: 80px; /* Espaciado entre opciones */
        }

        .menu-options a {
            text-decoration: none;
            color: #333; /* Color del texto */
            font-size: 1rem;
            font-weight: bold;
            position: relative;
            transition: color 0.3s ease;
        }

        .menu-options a:hover {
            color: #007BFF; /* Azul al pasar el cursor */
        }

        /* Efecto visual bajo el texto al pasar el cursor */
        .menu-options a::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: -5px;
            width: 0;
            height: 2px;
            background-color: #007BFF;
            transition: width 0.3s ease;
        }

        .menu-options a:hover::after {
            width: 100%; /* Subrayado animado */
        }

        /*-------- */
        /* Contenedor principal */
        .rectangle-container {
            width: 100%;
            height: 300px;
            display: flex;
            align-items: center;
            padding: 0;
            color: white;
            transition: background-color 1s ease;
            position: relative;
        }

        /* Imagen a la izquierda */
        .rectangle-image {
            width: 40%;
            height: 100%;
            overflow: hidden;
        }

        .rectangle-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* Contenido a la derecha */
        .rectangle-content {
    width: 60%;
    padding: 20px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    text-align: center;
    height: 100%; /* Asegura que ocupe toda la altura disponible */
}


        .rectangle-content h2 {
            margin: 0;
            font-size: 2rem;
        }

        .rectangle-content p {
            margin: 10px 0 0;
            font-size: 1rem;
        }
    </style>
</head>
<body>
    <!-- Menú principal -->
    <div class="menu">
        <!-- Logo -->
        <div class="logo">
            <img src="metrokal_logo.png" alt="Logo">
        </div>

        <!-- Opciones del menú -->
    <div class="menu-options">
        <a href="RegistroOrden.php">Generar Orden</a>
        <a href="CentroOrdenes.php">Órdenes Internas</a>
        <a href="Recepcion.php">Generar Recepción</a>
        <a href="usuarios.html">Usuarios</a>
    </div>

    </div>

    <marquee behavior="scroll" scrollamount="10" direction="left" style="font-size: 1.5rem; font-family: Arial, sans-serif; color: #333;">
    Bienvenido a Metrokal 😊----------Bienvenido a Metrokal 😊----------Bienvenido a Metrokal 😊
    </marquee>


    <!-- Contenedor del rectángulo -->
    <div class="rectangle-container" id="rectangle">
        <div class="rectangle-image">
            <img src="valor1.jpg" alt="Innovación">
        </div>
        <div class="rectangle-content">
            <h2>Innovación</h2>
            <p>Nos enfocamos en desarrollar soluciones creativas y tecnológicas.</p>
        </div>
    </div>

    <script>
        // Datos para los rectángulos
        const rectangles = [
            {
                title: "Innovación",
                description: "Nos enfocamos en desarrollar soluciones creativas y tecnológicas.",
                image: "valor3.jpg",
                color: "rgba(0, 123, 255, 0.8)"
            },
            {
                title: "Compromiso",
                description: "Estamos comprometidos con la excelencia en cada proyecto.",
                image: "valor2.jpg",
                color: "rgba(40, 167, 69, 0.8)"
            },
            {
                title: "Trabajo en Equipo",
                description: "Creemos en la colaboración para alcanzar grandes metas.",
                image: "valor1.jpg",
                color: "rgba(255, 193, 7, 0.8)"
            }
        ];

        let currentIndex = 0;

        const rectangleElement = document.getElementById("rectangle");
        const imageElement = rectangleElement.querySelector(".rectangle-image img");
        const titleElement = rectangleElement.querySelector("h2");
        const descriptionElement = rectangleElement.querySelector("p");

        // Función para actualizar el contenido del rectángulo
        function updateRectangle() {
            const { title, description, image, color } = rectangles[currentIndex];
            titleElement.textContent = title;
            descriptionElement.textContent = description;
            imageElement.src = image;
            rectangleElement.style.backgroundColor = color;

            currentIndex = (currentIndex + 1) % rectangles.length; // Rotar índices
        }

        // Cambiar contenido cada 5 segundos
        setInterval(updateRectangle, 5000);

        // Inicializar con el primer valor
        updateRectangle();
    </script>
</body>
</html>

