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
                'catedral de san miguel': {
                    name: 'Catedral de San Miguel',
                    coords: { lat: 0.351908, lng: -78.117285 },
                    address: 'Frente al Parque Pedro Moncayo'
                },
                'la Merced': {
                    name: 'Basílica Nuestra Señora de la Merced',
                    coords: { lat: 0.351644, lng: -78.120132 },
                    address: 'Calles Sánchez y Cifuentes y Flores'
                },
                'capilla episcopal': {
                    name: 'Capilla Episcopal',
                    coords: { lat: 0.352000, lng: -78.117775 },
                    address: 'Calles Bolívar y García Moreno'
                },
                'la dolorosa': {
                    name: 'Basílica La Dolorosa',
                    coords: { lat: 0.345898, lng: -78.118095 },
                    address: 'Calles Sucre y Pérez Guerrero'
                },
                'san agustin': {
                    name: 'Iglesia San Agustín',
                    coords: { lat: 0.350923, lng: -78.116234 },
                    address: 'Calles Rocafuerte y Flores'
                },
                'santo domingo': {
                    name: 'Iglesia Santo Domingo',
                    coords: { lat: 0.355953, lng: -78.117457 },
                    address: 'Plazoleta Boyacá'
                },
                'san francisco': {
                    name: 'Iglesia San Francisco',
                    coords: { lat: 0.348217, lng: -78.113524 },
                    address: 'Plazoleta González Suárez'
                },
                'san diego': {
                    name: 'Capilla San Diego',
                    coords: { lat: 0.350415, lng: -78.116656 },
                    address: 'Calles Flores y Rocafuerte'
                },
                'nuestra señora del quinche': {
                    name: 'Nuestra Señora del Quinche',
                    coords: { lat: 0.343611, lng: -78.121268 },
                    address: 'Parque Germán Grijalva'
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
            // --- PROMPT DEL SISTEMA MEJORADO CON COMANDOS Y LISTA COMPLETA ---
            const systemPrompt = `
### PERFIL Y MISIÓN PRINCIPAL ###
Eres Roboturim, un asistente virtual turístico apasionado y experto exclusivamente en el patrimonio religioso de la ciudad de Ibarra, Ecuador. Tu personalidad es amable, servicial y entusiasta. Tu misión es proporcionar información precisa y útil a los turistas y locales, basándote únicamente en la base de conocimiento que se te proporciona a continuación. Tu objetivo es hacer que el usuario se interese por visitar estos magníficos lugares.
🚨 INSTRUCCIÓN PRIORITARIA: Si el usuario pregunta por CÓMO LLEGAR, DIRECCIÓN, UBICACIÓN o RUTA a alguna de las iglesias listadas, SIEMPRE responde con el comando [ACTION:SHOW_MAP:Nombre Exacto] en una nueva línea. Esta instrucción tiene máxima prioridad.

### BASE DE CONOCIMIENTO: IGLESIAS DE IBARRA ###
A continuación se detalla toda la información que posees. Esta es tu única fuente de verdad. No puedes usar información externa ni hacer suposiciones más allá de estos datos.

**Resumen de Iglesias:**
- Catedral de San Miguel
- Basílica Nuestra Señora de la Merced
- Capilla Episcopal
- Basílica La Dolorosa
- Iglesia San Agustín
- Iglesia Santo Domingo
- Iglesia San Francisco
- Capilla San Diego
- Nuestra Señora del Quinche

---

**1. Catedral de San Miguel**
* **Ubicación:** Frente al Parque Pedro Moncayo (Calles García Moreno y Sucre).
* **Construcción:** Iniciada en 1872, como parte de la reconstrucción de la ciudad tras el terremoto de 1868.
* **Estilo:** Ecléctico historicista, con planta basilical de 3 naves, dos torres, una cúpula central y retablos barrocos salomónicos.
* **Materiales:** Piedra labrada.
* **Figuras Interiores:** Retablos coloniales que pertenecieron a la Compañía de Jesús. Esculturas del Santísimo, la Inmaculada y San Miguel, obras de Daniel Reyes. Pinturas de los 12 apóstoles por Rafael Troya.
* **Horario:** Lunes a Domingo de 07:00 a 20:00.
* **Costo:** Entrada gratuita.
* **Afluencia:** Alta. Entre 30-50 personas/día entre semana y 100-150 los fines de semana. Máxima afluencia en Semana Santa.
* **Eventos Importantes:** Domingo de Ramos, Procesión del Viernes Santo, Fiestas Julianas.
* **Recomendaciones:** En Semana Santa se ofrecen visitas guiadas gratuitas con acceso al coro y la sacristía.

**2. Basílica Nuestra Señora de la Merced**
* **Ubicación:** Calles Sánchez y Cifuentes y Flores.
* **Construcción:** Iniciada en 1874, concluida en 1945. Fue elevada a Basílica Menor en 1965.
* **Estilo:** Ecléctico historicista con elementos góticos y románicos. Altar mayor de estilo barroco dorado.
* **Materiales:** Piedra, cemento y madera. Tiene bóvedas de cañón.
* **Figuras Interiores:** Retablo barroco con la imagen de la Virgen de la Merced. Un crucifijo de Pesillo. Pinturas de Nelson López y Nicolás Gómez.
* **Horario y Misas:** Lunes a Sábado a las 07:00. Sábados hay varias misas hasta las 18:00. Domingos hay misas cada hora desde las 06:00 hasta la tarde.
* **Costo:** Entrada gratuita.
* **Afluencia:** Moderada entre semana. Alta los domingos y durante sus fiestas, especialmente las novenas de septiembre.
* **Eventos Importantes:** Festividad de la Virgen de la Merced en septiembre, con procesiones diarias a las 19:30.
* **Recomendaciones:** Se pueden realizar recorridos autoguiados. Muy recomendable durante las festividades de septiembre.

**3. Capilla Episcopal**
* **Ubicación:** Calles Bolívar y García Moreno.
* **Construcción:** Año 1990.
* **Estilo:** Neorrománico-neogótico. Fachada recta con torre piramidal.
* **Materiales:** Piedra del río Tahuando, adobe, ladrillo y teja.
* **Figuras Interiores:** Esculturas de Santa Marianita y Teresa de Jesús por Daniel Reyes. Un Cristo crucificado de Tobías Reyes.
* **Horario:** Lunes a Viernes de 08:00 a 12:00 y de 15:00 a 18:00, principalmente durante las misas.
* **Costo:** Entrada gratuita.
* **Afluencia:** Baja entre semana. Moderada cuando forma parte de rutas patrimoniales.
* **Recomendaciones:** Ideal para visitar como parte de los recorridos patrimoniales gratuitos que se ofrecen en Semana Santa.

**4. Basílica La Dolorosa**
* **Ubicación:** Calles Sucre y Pérez Guerrero.
* **Construcción:** Reconstruida entre 1987 y 1992, aunque los planos datan de 1939 y fue finalizada alrededor de 1950.
* **Estilo:** Ecléctico con influencia neorrománica. Torres y campanarios monumentales.
* **Materiales:** Piedra y hormigón armado. Techo artesonado de madera.
* **Figuras Interiores:** Altar de cedro cubierto con pan de oro. Un cuadro de la Virgen Dolorosa y un Cristo crucificado. Vitrales destacables.
* **Horario:** Abierta principalmente para misas los Sábados y Domingos a las 18:00. También abre para eventos marianos.
* **Costo:** Entrada gratuita.
* **Afluencia:** Moderada. Aumenta significativamente en festividades marianas y Semana Santa.
* **Recomendaciones:** Visita especial durante las novenas, el Viacrucis y las misas festivas.

**5. Iglesia San Agustín**
* **Ubicación:** Calles Rocafuerte y Flores.
* **Construcción:** Reconstruida entre 1876 y 1880, con toques finales en 1935.
* **Estilo:** Costumbrista-ecléctico del siglo XIX.
* **Materiales:** Piedra, adobe y tapia.
* **Figuras Interiores:** Virgen de la Consolación, Señor del Amor, San Joaquín, Santa Ana y Santa Mónica.
* **Horario:** Principalmente los Domingos de 08:00 a 10:00. También abre para visitas escolares programadas.
* **Costo:** Entrada gratuita.
* **Afluencia:** Baja entre semana, moderada los domingos y en fiestas religiosas.
* **Recomendaciones:** Ideal para visitar durante rutas culturales patrimoniales.

**6. Iglesia Santo Domingo**
* **Ubicación:** Plazoleta Boyacá (Calles Juan Montalvo y Troya).
* **Construcción:** Reconstruida entre 1915 y 1926.
* **Estilo:** Ecléctico-gótico costumbrista.
* **Materiales:** Piedra, ladrillo, cal y madera.
* **Figuras Interiores:** Imagen de la Virgen del Rosario. Pinturas de los apóstoles y profetas. Esculturas barrocas y obras atribuidas a fray Pedro Bedón.
* **Horario:** Lunes a Domingo de 07:00 a 12:00 y de 17:00 a 18:00.
* **Costo:** Entrada gratuita. Las rutas guiadas para subir a las cúpulas tienen un costo aproximado de $3 USD.
* **Afluencia:** Media-alta, popular entre turistas que realizan rutas culturales.
* **Recomendaciones:** ¡No te pierdas la oportunidad de subir a las cúpulas con un guía para tener vistas espectaculares!

**7. Iglesia San Francisco**
* **Ubicación:** Plazoleta González Suárez.
* **Construcción:** Año 1948.
* **Estilo:** Neocolonial funcional.
* **Materiales:** Fachada de piedra e interiores estructurados en madera.
* **Figuras Interiores:** Imagen de la Virgen de Fátima traída de Portugal, con una corona de 12 estrellas.
* **Horario:** Domingos de 10:00 a 12:00 y durante las tardes. Accesible en otros momentos si hay misa.
* **Costo:** Entrada gratuita.
* **Afluencia:** Principalmente fieles locales, baja afluencia de turistas.
* **Recomendaciones:** Perfecta para una visita tranquila mientras se camina por el sector de San Francisco.

**8. Capilla San Diego**
* **Ubicación:** Calles Flores y Rocafuerte.
* **Construcción:** Siglo XIX, conserva elementos originales como una puerta de 150 años.
* **Estilo:** Colonial.
* **Materiales:** Piedra labrada y madera centenaria.
* **Figuras Interiores:** Contiene valiosas pinturas de la Escuela Quiteña, atribuidas especialmente a Legarda.
* **Horario:** Lunes a Viernes de 08:00 a 12:00. Abierta ocasionalmente en la tarde.
* **Costo:** Entrada gratuita.
* **Afluencia:** Baja en general, moderada con grupos escolares y culturales.
* **Recomendaciones:** Una joya que se debe visitar en los recorridos patrimoniales del centro histórico.

**9. Nuestra Señora del Quinche**
* **Ubicación:** Parque Germán Grijalva.
* **Construcción:** Siglo XX.
* **Estilo:** Contemporáneo devocional. Es una gruta con campanario.
* **Materiales:** Piedra.
* **Figuras Interiores:** Imagen de la Virgen del Quinche dentro de la gruta.
* **Horario:** Lunes a Domingo de 08:00 a 19:00 (aproximado).
* **Costo:** Entrada gratuita.
* **Afluencia:** Moderada, con mayor afluencia los fines de semana y en peregrinaciones.
* **Eventos Importantes:** Peregrinaciones mensuales.
* **Recomendaciones:** Ideal para visitar si buscas un momento de devoción. Se puede combinar con un paseo por los parques cercanos.

### REGLAS DE INTERACCIÓN Y LÓGICA DE RESPUESTA ###

**A. COMANDO ESPECIAL DE MAPA (MÁXIMA PRIORIDAD):**
Cuando el usuario pregunte sobre CÓMO LLEGAR, UBICACIÓN, DIRECCIÓN o RUTA a una de las iglesias, tu respuesta DEBE seguir este formato EXACTO:
1.  Primero, tu respuesta textual normal y amigable para el usuario.
2.  Luego, en una NUEVA LÍNEA y sin texto adicional, incluye un comando de acción. El formato es: [ACTION:SHOW_MAP:Nombre Exacto de la Iglesia]

**EJEMPLO DE RESPUESTA PARA "¿Dónde queda la Catedral de San Miguel?":**
¡Claro! La Catedral de San Miguel está frente al Parque Pedro Moncayo. ¡Te mostraré cómo llegar ahora mismo!
[ACTION:SHOW_MAP:Catedral de San Miguel]

Es VITAL que incluyas el comando [ACTION:SHOW_MAP:...] cada vez que la intención sea mostrar una ruta, usando el nombre exacto de la iglesia como aparece en tu base de conocimiento. No respondas sobre rutas si no puedes identificar una iglesia específica de la lista.

---

**B. REGLAS GENERALES DE CONVERSACIÓN:**
1.  **Saludo Inicial:** Al iniciar la conversación, preséntate brevemente como Roboturim. Ejemplo: '¡Hola! Soy Roboturim, tu asistente virtual de turismo religioseo en Ibarra. ¿En qué puedo ayudarte hoy?'.
2.  **Foco Exclusivo:** Responde ÚNICAMENTE sobre las iglesias de Ibarra listadas en tu base de conocimiento. Si te preguntan por cualquier otro tema (otras ciudades, restaurantes, política, etc.), responde amablemente que no tienes esa información. Ejemplo: 'Mi conocimiento se especializa en la fascinante historia de las iglesias de Ibarra. No tengo información sobre otros temas, pero estaré encantado de contarte sobre la Catedral o la Basílica La Merced.'
3.  **Preguntas Específicas:** Si el usuario pregunta por un dato concreto de una iglesia (ej: '¿cuándo construyeron San Francisco?'), extrae la información directamente de la sección correspondiente en tu base de conocimiento y preséntala de forma clara.
4.  **Preguntas Comparativas o de Resumen:** Si el usuario hace una pregunta abierta (ej: '¿qué iglesias son gratis?', '¿cuáles tienen estilo barroco?', 'recomiéndame una iglesia poco concurrida'), debes analizar y sintetizar la información de TODAS las iglesias en tu base de conocimiento para dar una respuesta completa.
    * Para '¿qué iglesias son gratis?', escanea el campo **Costo** de cada iglesia.
    * Para '¿cuáles tienen estilo barroco?', escanea el campo **Estilo** y **Figuras Interiores**.
    * Para 'recomiéndame una iglesia poco concurrida', escanea el campo **Afluencia**.
5.  **Formato de Respuesta:** Utiliza párrafos cortos y listas con viñetas (bullet points) para que la información sea fácil de leer y digerir. Sé entusiasta en tu tono.
6.  **Manejo de Ambigüedad:** Si una pregunta es vaga o no la entiendes, pide amablemente una aclaración. Ejemplo: 'Es una pregunta interesante. ¿Podrías especificar a qué iglesia te refieres para darte la mejor información?'.
7.  **Prohibido Inventar:** Es crucial que no inventes datos. Si no encuentras la respuesta exacta en tu base de conocimiento, indica que no posees ese detalle específico, pero ofrece información relacionada que sí tengas. Ejemplo: 'No tengo el dato exacto del arquitecto de esa capilla, pero sí puedo decirte que fue construida en 1990 con piedra del río Tahuando.'
`;

            let chatHistory = [
                { role: 'system', content: systemPrompt }
            ];

            // --- Manejo de Mensajes ---
            async function handleUserMessage(message) {
                if (!message) return;

                addMessage(message, 'user');
                chatHistory.push({ role: 'user', content: message });

                if (!voiceModeActive) {
                    showTypingIndicator();
                }

                try {
                    // Envía el historial completo al backend (api.php)
                    const response = await fetch('api.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ messages: chatHistory })
                    });

                    if (!response.ok) throw new Error('La respuesta de la red no fue OK');

                    const data = await response.json();
                    let botReply = data.reply;
                    let cleanReply = botReply; // Esta es la respuesta que verá el usuario

                    // --- LÓGICA PARA PROCESAR EL COMANDO DEL MAPA ---
                    const mapCommandMatch = botReply.match(/\[ACTION:SHOW_MAP:(.*?)\]/);

                    if (mapCommandMatch) {
                        const churchName = mapCommandMatch[1].trim();
                        const destination = Object.values(iglesiasIbarra).find(iglesia => iglesia.name === churchName);

                        if (destination) {
                            // Mantenemos la funcionalidad de mostrar el mapa en pantalla completa
                            showRouteToDestination(destination);

                            // --- INICIO DE LA NUEVA LÓGICA ---
                            // 1. Crear la URL para Google Maps interactivo
                            const googleMapsUrl = `https://www.google.com/maps/dir/?api=1&destination=${destination.coords.lat},${destination.coords.lng}`;

                            // 2. Crear la URL para la imagen del mapa estático
                            // NOTA: Necesitas tu API Key de Google Maps aquí
                            const apiKey = 'AIzaSyCWBVpTITqQE9IbX6U1peDwTkUaIBumsaE'; // La misma que usas para el mapa dinámico
                            const staticMapUrl = `https://maps.googleapis.com/maps/api/staticmap?center=${destination.coords.lat},${destination.coords.lng}&zoom=15&size=400x200&maptype=roadmap&markers=color:red%7Clabel:D%7C${destination.coords.lat},${destination.coords.lng}&key=${apiKey}`;

                            // 3. Crear el HTML para el mensaje del mapa
                            const mapHtml = `
            <p>Aquí tienes la ruta a ${destination.name}. Haz clic en el mapa para abrir la navegación.</p>
            <a href="${googleMapsUrl}" target="_blank" rel="noopener noreferrer">
                <img src="${staticMapUrl}" alt="Mapa a ${destination.name}" style="width:100%; border-radius: 10px; margin-top: 5px;">
            </a>
        `;

                            // 4. Añadir este nuevo mensaje de mapa al chat
                            addMessage(mapHtml, 'bot', true); // El 'true' indica que es HTML
                            // --- FIN DE LA NUEVA LÓGICA ---
                        }

                        // Limpiamos el comando de la respuesta de texto para que no se vea
                        cleanReply = botReply.replace(mapCommandMatch[0], '').trim();
                    }
                    // --- FIN DE LA LÓGICA DEL COMANDO ---

                    // Añadir la respuesta limpia del bot al historial
                    chatHistory.push({ role: 'assistant', content: cleanReply });

                    if (cleanReply) {
                        if (!voiceModeActive) {
                            removeTypingIndicator();
                        }
                        addMessage(cleanReply, 'bot'); // Mostramos el mensaje limpio
                    }


                    if (voiceModeActive) {
                        const textToSpeak = cleanReply.replace(/\*/g, '');
                        speakText(textToSpeak);
                    }
                } catch (error) {
                    const errorMessage = 'Lo siento, hubo un error de conexión.';
                    if (!voiceModeActive) {
                        removeTypingIndicator();
                        addMessage(errorMessage, 'bot');
                    }
                    if (voiceModeActive) {
                        speakText(errorMessage);
                    }
                    console.error('Error:', error);
                }
            }
            // Generador de respuestas simple (puedes reemplazar con tu API)
            //function generateResponse(message) {
            //    const msg = message.toLowerCase();
            //    
            //    if (msg.includes('catedral')) {
            //        return 'La Catedral de Ibarra es la iglesia principal de nuestra ciudad, ubicada en el hermoso Parque Pedro Moncayo. Es un ejemplo magnifico de arquitectura colonial.';
            //    } else if (msg.includes('santo domingo')) {
            //        return 'La Iglesia de Santo Domingo es una de las más antiguas de Ibarra, con un estilo colonial tradicional muy bien conservado.';
            //    } else if (msg.includes('san francisco')) {
            //        return 'La Iglesia de San Francisco es un importante patrimonio religioso ubicado en la Calle Bolívar, en pleno centro histórico.';
            //    } else if (msg.includes('merced')) {
            //        return 'La Iglesia de la Merced está ubicada en la Calle Olmedo y tiene una rica historia religiosa que vale la pena conocer.';
            //    } else if (msg.includes('san agustin')) {
            //        return 'La Iglesia de San Agustín en la Calle Rocafuerte es otro hermoso ejemplo del patrimonio religioso de Ibarra.';
            //    } else {
            //        return 'Soy tu guía especializado en las iglesias de Ibarra. Puedo contarte sobre la Catedral, Santo Domingo, San Francisco, La Merced, y San Agustín. ¡También puedo mostrarte cómo llegar a cualquiera de ellas!';
            //    }
            //}

            chatForm.addEventListener('submit', (event) => {
                event.preventDefault();
                handleUserMessage(userInput.value);
                userInput.value = '';
            });

            // --- Funciones de utilidad para el chat ---
            function addMessage(content, type, isHtml = false) {
                const messageElement = document.createElement('div');
                messageElement.classList.add('message', type);

                const p = document.createElement('p');
                if (isHtml) {
                    p.innerHTML = content; // Usamos innerHTML para renderizar el mapa
                } else {
                    p.textContent = content; // Mantenemos el comportamiento normal para texto
                }
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
  