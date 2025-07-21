<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Roboturim - Guía de Ibarra</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="stylesheet" href="css/principal.css">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@400;500;600;700&family=VT323&display=swap"
        rel="stylesheet">
    <style>
        /* Estilos base */
     
    </style>
</head>

<body>
    <div class="chat-container">
        <header class="chat-header">
            <h1>Roboturim</h1>
            <p>Tu Guía de Turismo Religioso en Ibarra</p>
        </header>
        <main class="chat-messages">
            <div class="message bot">
                <p>¡Hola! Soy Roboturim, tu guía virtual. Puedo darte información sobre las iglesias de Ibarra y
                    mostrarte cómo llegar. Haz clic en el micrófono y pregúntame algo como "¿Cómo llego a la Catedral?"
                    ¿Cómo puedo ayudarte hoy?</p>
            </div>
        </main>
        <footer class="chat-input-area">
            <form id="chat-form">
                <input type="text" id="user-input" placeholder="O escribe tu pregunta aquí..." autocomplete="off">
                <button type="button" id="mic-button" title="Hablar">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="24"
                        height="24">
                        <path
                            d="M12 14c1.66 0 3-1.34 3-3V5c0-1.66-1.34-3-3-3S9 3.34 9 5v6c0 1.66 1.34 3 3 3zm5.3-3c0 3-2.54 5.1-5.3 5.1S6.7 14 6.7 11H5c0 3.41 2.72 6.23 6 6.72V21h2v-3.28c3.28-.49 6-3.31 6-6.72h-1.7z" />
                    </svg>
                </button>
                <button type="submit" title="Enviar">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="24"
                        height="24">
                        <path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z" />
                    </svg>
                </button>
            </form>
        </footer>
    </div>

    <div class="karen-screen" id="karen-screen">
        <div class="computer-frame">
            <div class="screen-inner">
                <div class="scan-lines"></div>
                <div class="karen-content">
                    <!-- Modo cara normal -->
                    <div class="karen-face" id="karen-face">
                        <div class="karen-eyes">
                            <div class="karen-eye"></div>
                            <div class="karen-eye"></div>
                        </div>
                        <div class="karen-mouth" id="karen-mouth"></div>
                        <div class="karen-status" id="karen-status">ESCUCHANDO...</div>
                        <div class="listening-indicator" id="listening-indicator">
                            <div class="listening-dot"></div>
                            <div class="listening-dot"></div>
                            <div class="listening-dot"></div>
                        </div>
                    </div>

                    <!-- NUEVO: Modo mapa -->
                    <div class="karen-map-mode" id="karen-map-mode">
                        <div class="map-header" id="map-header">
                            CALCULANDO RUTA...
                        </div>
                        <div class="map-container">
                            <div class="map-controls">
                                <button class="map-control-btn" id="recenter-btn">CENTRAR</button>
                                <button class="map-control-btn" id="satellite-btn">SATÉLITE</button>
                                <button class="map-control-btn" id="back-to-face">VOLVER</button>
                            </div>
                            <div id="roboturim-map"></div>
                        </div>
                        <div class="map-info" id="map-info">
                            Mostrando ubicación...
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <button class="exit-voice-mode" id="exit-voice-mode" title="Salir del modo voz">X</button>
    </div>

    <script src="js/principal.js"></script>

    <!-- Cargar Google Maps API -->
    <script async defer
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCWBVpTITqQE9IbX6U1peDwTkUaIBumsaE&callback=initMap"></script>

</body>

</html>