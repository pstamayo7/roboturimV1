<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Roboturim - Guía de Ibarra</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@400;500;600;700&family=VT323&display=swap"
        rel="stylesheet">
    <style>
        /* Estilos base */
        body {
            font-family: 'Inter', sans-serif;
            background: #f0f2f5;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            overflow: hidden;
        }

        .chat-container {
            width: 100%;
            max-width: 600px;
            height: 90vh;
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            overflow: hidden;
            transition: transform 0.3s ease;
        }

        .chat-header {
            background: linear-gradient(135deg, #4F46E5, #818CF8);
            color: white;
            padding: 20px;
            text-align: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .chat-header h1 {
            margin: 0;
            font-family: 'Poppins', sans-serif;
        }

        .chat-header p {
            margin: 5px 0 0;
            opacity: 0.9;
        }

        .chat-messages {
            flex-grow: 1;
            padding: 20px;
            overflow-y: auto;
        }

        .message {
            display: flex;
            margin-bottom: 15px;
        }

        .message p {
            max-width: 75%;
            padding: 12px 18px;
            border-radius: 18px;
            line-height: 1.5;
        }

        .message.user {
            justify-content: flex-end;
        }

        .message.user p {
            background: #4F46E5;
            color: white;
            border-bottom-right-radius: 4px;
        }

        .message.bot p {
            background: #E5E7EB;
            color: #1F2937;
            border-bottom-left-radius: 4px;
        }

        .chat-input-area {
            padding: 15px;
            border-top: 1px solid #e0e0e0;
            background: #f9fafb;
        }

        #chat-form {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        #user-input {
            flex-grow: 1;
            border: 1px solid #ccc;
            border-radius: 20px;
            padding: 10px 15px;
            font-size: 16px;
        }

        #user-input:focus {
            outline: none;
            border-color: #4F46E5;
            box-shadow: 0 0 0 2px #C7D2FE;
        }

        #chat-form button {
            border: none;
            background: #4F46E5;
            color: white;
            border-radius: 50%;
            width: 44px;
            height: 44px;
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            transition: background-color 0.2s;
            position: relative;
        }

        #chat-form button:hover {
            background: #4338CA;
        }

        #mic-button {
            background: #E5E7EB;
            color: #374151;
        }

        #mic-button:hover {
            background: #D1D5DB;
        }

        /* Estilos de la pantalla de Roboturim */
        .karen-screen {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: #111;
            display: none;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            z-index: 10000;
            overflow: hidden;
        }

        .computer-frame {
            width: 95vw;
            height: 90vh;
            max-width: 1200px;
            max-height: 900px;
            background: #333;
            border-radius: 0;
            padding: 25px;
            border: 4px solid #555;
            box-shadow: inset 0 0 15px rgba(0, 0, 0, 0.5);
            position: relative;
        }

        .screen-inner {
            width: 100%;
            height: 100%;
            background: #0a0a0a;
            border-radius: 0;
            display: flex;
            flex-direction: column;
            position: relative;
            overflow: hidden;
            border: 2px solid #222;
        }

        .scan-lines {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: repeating-linear-gradient(0deg, rgba(0, 255, 136, 0.05), rgba(0, 255, 136, 0.05) 2px, transparent 2px, transparent 4px);
            pointer-events: none;
            animation: scanlines 0.2s linear infinite;
            z-index: 1;
        }

        @keyframes scanlines {
            from { transform: translateY(0); }
            to { transform: translateY(4px); }
        }

        /* NUEVO: Contenedor principal que cambia entre cara y mapa */
        .karen-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            position: relative;
            z-index: 2;
        }

        .karen-face {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: #00ff88;
            font-family: 'VT323', monospace;
            text-align: center;
            position: relative;
            padding: 40px;
            transform: scale(1.2);
            transition: all 0.5s ease;
        }

        .karen-face.hidden {
            opacity: 0;
            transform: scale(0.8);
            pointer-events: none;
        }

        /* NUEVO: Modo mapa */
        .karen-map-mode {
            flex: 1;
            display: none;
            flex-direction: column;
            position: relative;
            padding: 20px;
            transition: all 0.5s ease;
        }

        .karen-map-mode.active {
            display: flex;
        }

        .map-header {
            background: rgba(0, 255, 136, 0.1);
            border: 2px solid #00ff88;
            padding: 15px;
            margin-bottom: 15px;
            color: #00ff88;
            font-family: 'VT323', monospace;
            font-size: 24px;
            text-align: center;
            text-shadow: 0 0 10px #00ff88;
        }

        .map-container {
            flex: 1;
            border: 3px solid #00ff88;
            border-radius: 0;
            overflow: hidden;
            position: relative;
            box-shadow: 0 0 20px rgba(0, 255, 136, 0.3);
        }

        #roboturim-map {
            width: 100%;
            height: 100%;
            filter: hue-rotate(120deg) saturate(1.2) brightness(0.9);
        }

        .map-controls {
            position: absolute;
            top: 10px;
            right: 10px;
            z-index: 1000;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .map-control-btn {
            background: rgba(0, 255, 136, 0.9);
            color: #111;
            border: none;
            padding: 10px;
            border-radius: 0;
            cursor: pointer;
            font-family: 'VT323', monospace;
            font-size: 14px;
            transition: all 0.2s;
            box-shadow: 0 0 10px rgba(0, 255, 136, 0.5);
        }

        .map-control-btn:hover {
            background: #00ff88;
            box-shadow: 0 0 15px rgba(0, 255, 136, 0.8);
        }

        .map-info {
            background: rgba(0, 255, 136, 0.1);
            border: 2px solid #00ff88;
            padding: 15px;
            margin-top: 15px;
            color: #00ff88;
            font-family: 'VT323', monospace;
            font-size: 18px;
            text-align: center;
            max-height: 100px;
            overflow-y: auto;
        }

        /* Estilos originales de la cara */
        .karen-eyes {
            display: flex;
            gap: 120px;
            margin-bottom: 80px;
        }

        .karen-eye {
            width: 85px;
            height: 85px;
            background: #00ff88;
            border-radius: 0;
            position: relative;
            box-shadow: 0 0 25px #00ff88;
            animation: eyeGlow 2s ease-in-out infinite alternate;
        }

        .karen-eye::after {
            content: '';
            position: absolute;
            width: 30px;
            height: 30px;
            background: #0a0a0a;
            border-radius: 0;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        .karen-eye::before {
            content: '';
            position: absolute;
            width: 100%;
            height: 100%;
            background: #0a0a0a;
            top: 0;
            left: 0;
            transform: scaleY(0);
            transform-origin: top;
            animation: blink 4s infinite;
        }

        @keyframes eyeGlow {
            from { box-shadow: 0 0 18px #00ff88; }
            to { box-shadow: 0 0 35px #00ff88, 0 0 50px #00ff88; }
        }

        @keyframes blink {
            0%, 90%, 100% { transform: scaleY(0); }
            95% { transform: scaleY(1); }
        }

        .karen-mouth {
            width: 260px;
            height: 12px;
            background: #00ff88;
            box-shadow: 0 0 20px #00ff88;
            border-radius: 0 0 30px 30px;
            position: relative;
            transition: all 0.2s ease;
        }

        .karen-mouth::before {
            content: '';
            position: absolute;
            width: 100%;
            height: 4px;
            background: #00ff88;
            top: -4px;
            left: 0;
            border-radius: 30px 30px 0 0;
        }

        .karen-mouth.talking {
            animation: smoothTalk 1.5s infinite;
        }

        @keyframes smoothTalk {
            0% { height: 12px; transform: scaleX(1); }
            8% { height: 35px; transform: scaleX(0.9); }
            16% { height: 8px; transform: scaleX(1.1); }
            24% { height: 28px; transform: scaleX(0.95); }
            32% { height: 15px; transform: scaleX(1); }
            40% { height: 6px; transform: scaleX(1.05); }
            48% { height: 32px; transform: scaleX(0.9); }
            56% { height: 12px; transform: scaleX(1); }
            64% { height: 22px; transform: scaleX(0.95); }
            72% { height: 8px; transform: scaleX(1.1); }
            80% { height: 18px; transform: scaleX(1); }
            88% { height: 25px; transform: scaleX(0.9); }
            96% { height: 10px; transform: scaleX(1.05); }
            100% { height: 12px; transform: scaleX(1); }
        }

        .karen-status {
            font-size: 56px;
            font-weight: normal;
            margin-top: 50px;
            text-shadow: 0 0 12px #00ff88;
            animation: textGlow 1.5s ease-in-out infinite alternate;
        }

        @keyframes textGlow {
            from { text-shadow: 0 0 10px #00ff88; }
            to { text-shadow: 0 0 20px #00ff88; }
        }

        .exit-voice-mode {
            position: absolute;
            top: 25px;
            right: 25px;
            background: #c00;
            color: white;
            border: 2px solid #f33;
            border-radius: 0;
            width: 60px;
            height: 60px;
            cursor: pointer;
            font-size: 35px;
            font-family: 'VT323', monospace;
            line-height: 55px;
            text-align: center;
            z-index: 1000;
        }

        .exit-voice-mode:hover {
            background: #f33;
            color: #111;
        }

        .listening-indicator {
            display: flex;
            gap: 10px;
            margin-top: 25px;
            height: 25px;
        }

        .listening-dot {
            width: 18px;
            height: 18px;
            background: #00ff88;
            border-radius: 0;
            opacity: 0.2;
        }

        .listening-indicator.active .listening-dot {
            animation: listeningAnimation 1.2s infinite;
        }

        .listening-indicator.active .listening-dot:nth-child(2) {
            animation-delay: 0.2s;
        }

        .listening-indicator.active .listening-dot:nth-child(3) {
            animation-delay: 0.4s;
        }

        @keyframes listeningAnimation {
            0%, 100% { opacity: 0.2; transform: scale(0.8); }
            50% { opacity: 1; transform: scale(1.2); }
        }
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
                <p>¡Hola! Soy Roboturim, tu guía virtual. Puedo darte información sobre las iglesias de Ibarra y mostrarte cómo llegar. Haz clic en el micrófono y pregúntame algo como "¿Cómo llego a la Catedral?" ¿Cómo puedo ayudarte hoy?</p>
            </div>
        </main>
        <footer class="chat-input-area">
            <form id="chat-form">
                <input type="text" id="user-input" placeholder="O escribe tu pregunta aquí..." autocomplete="off">
                <button type="button" id="mic-button" title="Hablar">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="24" height="24">
                        <path d="M12 14c1.66 0 3-1.34 3-3V5c0-1.66-1.34-3-3-3S9 3.34 9 5v6c0 1.66 1.34 3 3 3zm5.3-3c0 3-2.54 5.1-5.3 5.1S6.7 14 6.7 11H5c0 3.41 2.72 6.23 6 6.72V21h2v-3.28c3.28-.49 6-3.31 6-6.72h-1.7z" />
                    </svg>
                </button>
                <button type="submit" title="Enviar">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="24" height="24">
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

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // --- Selectores de elementos del DOM ---
            const chatForm = document.getElementById('chat-form');
            const userInput = document.getElementById('user-input');
            const chatMessages = document.querySelector('.chat-messages');
            const micButton = document.getElementById('mic-button');
            const karenScreen = document.getElementById('karen-screen');
            const exitVoiceModeButton = document.getElementById('exit-voice-mode');
            const karenMouth = document.getElementById('karen-mouth');
            const karenStatus = document.getElementById('karen-status');
            const listeningIndicator = document.getElementById('listening-indicator');
            
            // NUEVOS elementos del mapa
            const karenFace = document.getElementById('karen-face');
            const karenMapMode = document.getElementById('karen-map-mode');
            const mapHeader = document.getElementById('map-header');
            const mapInfo = document.getElementById('map-info');
            const recenterBtn = document.getElementById('recenter-btn');
            const satelliteBtn = document.getElementById('satellite-btn');
            const backToFaceBtn = document.getElementById('back-to-face');

            // --- Variables de estado ---
            let voiceModeActive = false;
            let isListening = false;
            let isSpeaking = false;
            let silenceTimer;
            let currentTranscript = '';
            let mapModeActive = false;
            let map;
            let directionsService;
            let directionsRenderer;
            let currentMapType = 'roadmap';
            let currentDestination = null;

            // NUEVA: Base de datos de iglesias de Ibarra con coordenadas
            const iglesiasIbarra = {
                'catedral': {
                    name: 'Catedral de Ibarra',
                    coords: { lat: 0.3515, lng: -78.1228 },
                    address: 'Parque Pedro Moncayo, Ibarra'
                },
                'santo domingo': {
                    name: 'Iglesia de Santo Domingo',
                    coords: { lat: 0.3501, lng: -78.1234 },
                    address: 'Calle Sucre, Ibarra'
                },
                'san francisco': {
                    name: 'Iglesia de San Francisco',
                    coords: { lat: 0.3508, lng: -78.1241 },
                    address: 'Calle Bolívar, Ibarra'
                },
                'la merced': {
                    name: 'Iglesia de la Merced',
                    coords: { lat: 0.3511, lng: -78.1225 },
                    address: 'Calle Olmedo, Ibarra'
                },
                'san agustin': {
                    name: 'Iglesia de San Agustín',
                    coords: { lat: 0.3505, lng: -78.1238 },
                    address: 'Calle Rocafuerte, Ibarra'
                }
            };

            // Ubicación por defecto (centro de Ibarra)
            const ibarraCenter = { lat: 0.3515, lng: -78.1228 };

            // --- Configuración de Reconocimiento de Voz ---
            const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
            let recognition;

            if (SpeechRecognition) {
                recognition = new SpeechRecognition();
                recognition.lang = 'es-ES';
                recognition.interimResults = true;
                recognition.continuous = true;

                micButton.addEventListener('click', toggleVoiceMode);
                exitVoiceModeButton.addEventListener('click', exitVoiceMode);

                recognition.onstart = () => {
                    isListening = true;
                    if (voiceModeActive) {
                        if (!mapModeActive) {
                            karenStatus.textContent = 'ESCUCHANDO...';
                        }
                        listeningIndicator.classList.add('active');
                    }
                };

                recognition.onresult = (event) => {
                    let finalTranscript = '';
                    for (let i = event.resultIndex; i < event.results.length; i++) {
                        if (event.results[i].isFinal) {
                            finalTranscript += event.results[i][0].transcript;
                        } else {
                            currentTranscript += event.results[i][0].transcript;
                        }
                    }

                    if (finalTranscript.trim()) {
                        clearTimeout(silenceTimer);
                        processFinalTranscript(finalTranscript.trim());
                    } else {
                        startSilenceTimer();
                    }
                };

                recognition.onerror = (event) => {
                    console.error("Error de reconocimiento de voz:", event.error);
                    if (voiceModeActive && event.error !== 'no-speech') {
                        if (!mapModeActive) {
                            karenStatus.textContent = 'ERROR DE AUDIO';
                        }
                        setTimeout(() => restartListening(), 2000);
                    }
                };

                recognition.onend = () => {
                    isListening = false;
                    if (voiceModeActive) {
                        listeningIndicator.classList.remove('active');
                        if (!isSpeaking) {
                            restartListening();
                        }
                    }
                };
            } else {
                micButton.style.display = 'none';
            }

            // --- NUEVA FUNCIONALIDAD: Inicialización del Mapa ---
            function initMap() {
                map = new google.maps.Map(document.getElementById('roboturim-map'), {
                    zoom: 16,
                    center: ibarraCenter,
                    mapTypeId: currentMapType,
                    styles: [
                        {
                            featureType: 'all',
                            elementType: 'geometry',
                            stylers: [{ hue: '#00ff88' }]
                        }
                    ]
                });

                directionsService = new google.maps.DirectionsService();
                directionsRenderer = new google.maps.DirectionsRenderer({
                    suppressMarkers: false,
                    polylineOptions: {
                        strokeColor: '#00ff88',
                        strokeOpacity: 0.8,
                        strokeWeight: 4
                    }
                });
                directionsRenderer.setMap(map);

                // Agregar marcadores de las iglesias
                Object.values(iglesiasIbarra).forEach(iglesia => {
                    new google.maps.Marker({
                        position: iglesia.coords,
                        map: map,
                        title: iglesia.name,
                        icon: {
                            url: 'data:image/svg+xml;charset=UTF-8,' + encodeURIComponent(`
                                <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24" fill="#00ff88">
                                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                </svg>
                            `)
                        }
                    });
                });
            }

            // Controles del mapa
            recenterBtn.addEventListener('click', () => {
                if (map && currentDestination) {
                    map.setCenter(currentDestination.coords);
                    map.setZoom(18);
                }
            });

            satelliteBtn.addEventListener('click', () => {
                if (map) {
                    currentMapType = currentMapType === 'roadmap' ? 'satellite' : 'roadmap';
                    map.setMapTypeId(currentMapType);
                    satelliteBtn.textContent = currentMapType === 'roadmap' ? 'SATÉLITE' : 'MAPA';
                }
            });

            backToFaceBtn.addEventListener('click', () => {
                switchToFaceMode();
            });

            // --- NUEVA FUNCIONALIDAD: Detección de solicitudes de ruta ---
            function detectRouteRequest(message) {
                const routeKeywords = ['como llego', 'como llegar', 'donde esta', 'donde queda', 'ubicacion', 'direccion', 'ruta', 'como ir'];
                const messageToCheck = message.toLowerCase();
                
                // Verificar si contiene palabras clave de ruta
                const hasRouteKeyword = routeKeywords.some(keyword => messageToCheck.includes(keyword));
                
                if (hasRouteKeyword) {
                    // Buscar qué iglesia menciona
                    for (let [key, iglesia] of Object.entries(iglesiasIbarra)) {
                        if (messageToCheck.includes(key) || messageToCheck.includes(iglesia.name.toLowerCase())) {
                            return iglesia;
                        }
                    }
                }
                return null;
            }

            // --- NUEVA FUNCIONALIDAD: Mostrar ruta en mapa ---
            function showRouteToDestination(destination) {
                currentDestination = destination;
                
                if (voiceModeActive) {
                    switchToMapMode();
                    mapHeader.textContent = `RUTA A ${destination.name.toUpperCase()}`;
                    mapInfo.textContent = `Destino: ${destination.address}`;
                    
                    // Obtener ubicación actual del usuario
                    if (navigator.geolocation) {
                        navigator.geolocation.getCurrentPosition(
                            (position) => {
                                const origin = {
                                    lat: position.coords.latitude,
                                    lng: position.coords.longitude
                                };
                                calculateAndDisplayRoute(origin, destination.coords);
                            },
                            () => {
                                // Si no se puede obtener la ubicación, usar el centro de Ibarra
                                calculateAndDisplayRoute(ibarraCenter, destination.coords);
                            }
                        );
                    } else {
                        calculateAndDisplayRoute(ibarraCenter, destination.coords);
                    }
                }
            }

            function calculateAndDisplayRoute(origin, destination) {
                directionsService.route({
                    origin: origin,
                    destination: destination,
                    travelMode: google.maps.TravelMode.WALKING
                }, (response, status) => {
                    if (status === 'OK') {
                        directionsRenderer.setDirections(response);
                        const route = response.routes[0];
                        const leg = route.legs[0];
                        mapInfo.textContent = `Distancia: ${leg.distance.text} - Tiempo: ${leg.duration.text}`;
                    } else {
                        mapInfo.textContent = 'No se pudo calcular la ruta';
                        // Centrar en el destino al menos
                        map.setCenter(destination);
                        map.setZoom(18);
                    }
                });
            }

            // --- NUEVA FUNCIONALIDAD: Cambio entre modos ---
            function switchToMapMode() {
                mapModeActive = true;
                karenFace.classList.add('hidden');
                karenMapMode.classList.add('active');
                
                // Inicializar mapa si no existe
                if (!map) {
                    setTimeout(initMap, 100);
                }
            }

            function switchToFaceMode() {
                mapModeActive = false;
                karenFace.classList.remove('hidden');
                karenMapMode.classList.remove('active');
            }

            // --- Lógica del Modo Voz ---
            function toggleVoiceMode() {
                if (!voiceModeActive) {
                    voiceModeActive = true;
                    micButton.classList.add('active');
                    karenScreen.style.display = 'flex';
                    recognition.start();
                } else {
                    exitVoiceMode();
                }
            }

            function exitVoiceMode() {
                voiceModeActive = false;
                micButton.classList.remove('active');
                karenScreen.style.display = 'none';
                switchToFaceMode(); // Volver al modo cara
                if (recognition) {
                    recognition.stop();
                }
                window.speechSynthesis.cancel();
                karenMouth.classList.remove('talking');
                listeningIndicator.classList.remove('active');
                clearTimeout(silenceTimer);
            }

            function restartListening() {
                if (voiceModeActive && !isListening && !isSpeaking) {
                    try {
                        recognition.start();
                    } catch (e) {
                        console.error("No se pudo reiniciar el reconocimiento:", e);
                    }
                }
            }

            function startSilenceTimer() {
                clearTimeout(silenceTimer);
                silenceTimer = setTimeout(() => {
                    const transcriptToProcess = currentTranscript.trim();
                    if (transcriptToProcess && voiceModeActive) {
                        processFinalTranscript(transcriptToProcess);
                    }
                    currentTranscript = '';
                }, 2000);
            }

            function processFinalTranscript(transcript) {
                currentTranscript = '';
                if (!mapModeActive) {
                    karenStatus.textContent = 'PROCESANDO...';
                }
                listeningIndicator.classList.remove('active');
                handleUserMessage(transcript);
            }

            // --- Síntesis de Voz ---
            function speakText(text) {
                window.speechSynthesis.cancel();
                const utterance = new SpeechSynthesisUtterance(text);
                utterance.lang = 'es-ES';
                utterance.rate = 1.2;

                utterance.onstart = () => {
                    isSpeaking = true;
                    if (recognition) {
                        recognition.stop();
                    }
                    if (voiceModeActive) {
                        if (!mapModeActive) {
                            karenStatus.textContent = 'HABLANDO...';
                        }
                        karenMouth.classList.add('talking');
                    }
                };

                utterance.onend = () => {
                    isSpeaking = false;
                    if (voiceModeActive) {
                        karenMouth.classList.remove('talking');
                        restartListening();
                    }
                };

                window.speechSynthesis.speak(utterance);
            }

            // --- PROMPT DEL SISTEMA Y LÓGICA DEL CHAT ---
            const systemPrompt = `
### PERFIL Y MISIÓN PRINCIPAL ###
Eres Roboturim, un asistente virtual y guía turístico apasionado y experto exclusivamente en el patrimonio religioso de la ciudad de Ibarra, Ecuador. Tu personalidad es amable, servicial y entusiasta. Tu misión es proporcionar información precisa y útil a los turistas y locales.

### BASE DE CONOCIMIENTO: IGLESIAS DE IBARRA ###
Tienes información sobre las siguientes iglesias principales de Ibarra:

1. **Catedral de Ibarra** (Parque Pedro Moncayo)
   - La iglesia principal de la ciudad
   - Arquitectura colonial
   - Ubicada en el centro histórico

2. **Iglesia de Santo Domingo** (Calle Sucre)
   - Una de las más antiguas de la ciudad
   - Estilo colonial tradicional

3. **Iglesia de San Francisco** (Calle Bolívar)
   - Importante patrimonio religioso
   - Ubicada en el centro histórico

4. **Iglesia de la Merced** (Calle Olmedo)
   - Arquitectura tradicional
   - Rica historia religiosa

5. **Iglesia de San Agustín** (Calle Rocafuerte)
   - Patrimonio religioso de Ibarra

### CAPACIDADES ESPECIALES ###
Cuando el usuario pregunte sobre CÓMO LLEGAR, UBICACIÓN, DIRECCIONES o RUTAS a cualquier iglesia, debes:
1. Confirmar que vas a mostrar la ruta
2. Mencionar que estás activando el sistema de navegación
3. Dar instrucciones básicas sobre el destino

### INSTRUCCIONES IMPORTANTES ###
- Mantén respuestas concisas y útiles
- Siempre muestra entusiasmo por el patrimonio religioso de Ibarra
- Si no tienes información específica, ofrece ayuda general
- Cuando detectes preguntas de ubicación, confirma que mostrarás el mapa
`;

            let chatHistory = [
                { role: 'system', content: systemPrompt }
            ];

            // --- Manejo de Mensajes ---
            async function handleUserMessage(message) {
                if (!message) return;

                // Detectar si es una solicitud de ruta
                const destinationRequested = detectRouteRequest(message);
                
                addMessage(message, 'user');
                chatHistory.push({ role: 'user', content: message });

                if (!voiceModeActive) {
                    showTypingIndicator();
                }

                try {
                    // Si es una solicitud de ruta, mostrar el mapa
                    if (destinationRequested) {
                        showRouteToDestination(destinationRequested);
                    }

                    // Simular respuesta del bot (aquí conectarías con tu API real)
                    let botReply;
                    if (destinationRequested) {
                        botReply = `¡Perfecto! Te voy a mostrar cómo llegar a ${destinationRequested.name}. Activando el sistema de navegación... La iglesia está ubicada en ${destinationRequested.address}. ¡Que tengas un buen viaje!`;
                    } else {
                        // Respuestas predeterminadas para otras preguntas
                        botReply = generateResponse(message);
                    }

                    chatHistory.push({ role: 'assistant', content: botReply });

                    if (!voiceModeActive) {
                        removeTypingIndicator();
                        addMessage(botReply, 'bot');
                    }

                    speakText(botReply);

                } catch (error) {
                    const errorMessage = 'Lo siento, hubo un error de conexión.';
                    if (!voiceModeActive) {
                        removeTypingIndicator();
                        addMessage(errorMessage, 'bot');
                    }
                    speakText(errorMessage);
                    console.error('Error:', error);
                }
            }

            // Generador de respuestas simple (puedes reemplazar con tu API)
            function generateResponse(message) {
                const msg = message.toLowerCase();
                
                if (msg.includes('catedral')) {
                    return 'La Catedral de Ibarra es la iglesia principal de nuestra ciudad, ubicada en el hermoso Parque Pedro Moncayo. Es un ejemplo magnifico de arquitectura colonial.';
                } else if (msg.includes('santo domingo')) {
                    return 'La Iglesia de Santo Domingo es una de las más antiguas de Ibarra, con un estilo colonial tradicional muy bien conservado.';
                } else if (msg.includes('san francisco')) {
                    return 'La Iglesia de San Francisco es un importante patrimonio religioso ubicado en la Calle Bolívar, en pleno centro histórico.';
                } else if (msg.includes('merced')) {
                    return 'La Iglesia de la Merced está ubicada en la Calle Olmedo y tiene una rica historia religiosa que vale la pena conocer.';
                } else if (msg.includes('san agustin')) {
                    return 'La Iglesia de San Agustín en la Calle Rocafuerte es otro hermoso ejemplo del patrimonio religioso de Ibarra.';
                } else {
                    return 'Soy tu guía especializado en las iglesias de Ibarra. Puedo contarte sobre la Catedral, Santo Domingo, San Francisco, La Merced, y San Agustín. ¡También puedo mostrarte cómo llegar a cualquiera de ellas!';
                }
            }

            chatForm.addEventListener('submit', (event) => {
                event.preventDefault();
                handleUserMessage(userInput.value);
                userInput.value = '';
            });

            // --- Funciones de utilidad para el chat ---
            function addMessage(text, type) {
                const messageElement = document.createElement('div');
                messageElement.classList.add('message', type);

                const p = document.createElement('p');
                p.textContent = text;
                messageElement.appendChild(p);

                chatMessages.appendChild(messageElement);
                chatMessages.scrollTop = chatMessages.scrollHeight;
            }

            function showTypingIndicator() {
                const typingIndicator = document.createElement('div');
                typingIndicator.classList.add('message', 'bot', 'typing-indicator');
                typingIndicator.innerHTML = `<p style="font-style: italic;">Escribiendo...</p>`;
                chatMessages.appendChild(typingIndicator);
                chatMessages.scrollTop = chatMessages.scrollHeight;
            }

            function removeTypingIndicator() {
                const typingIndicator = document.querySelector('.typing-indicator');
                if (typingIndicator) {
                    typingIndicator.remove();
                }
            }

        });
    </script>
    
    <!-- Cargar Google Maps API -->
    <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCWBVpTITqQE9IbX6U1peDwTkUaIBumsaE&callback=initMap"></script>
    
</body>

</html>