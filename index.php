

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
        /* Estilos base (sin cambios) */
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

        /* Estilos de la pantalla de Roboturim - MEJORADOS */
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
            max-width: 1000px;
            max-height: 750px;
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
        }

        @keyframes scanlines {
            from {
                transform: translateY(0);
            }

            to {
                transform: translateY(4px);
            }
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
            transform: scale(1.3);
        }

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
            from {
                box-shadow: 0 0 18px #00ff88;
            }

            to {
                box-shadow: 0 0 35px #00ff88, 0 0 50px #00ff88;
            }
        }

        @keyframes blink {

            0%,
            90%,
            100% {
                transform: scaleY(0);
            }

            95% {
                transform: scaleY(1);
            }
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
            /* Animación de boca más rápida */
        }

        @keyframes smoothTalk {
            0% {
                height: 12px;
                transform: scaleX(1);
            }

            8% {
                height: 35px;
                transform: scaleX(0.9);
            }

            16% {
                height: 8px;
                transform: scaleX(1.1);
            }

            24% {
                height: 28px;
                transform: scaleX(0.95);
            }

            32% {
                height: 15px;
                transform: scaleX(1);
            }

            40% {
                height: 6px;
                transform: scaleX(1.05);
            }

            48% {
                height: 32px;
                transform: scaleX(0.9);
            }

            56% {
                height: 12px;
                transform: scaleX(1);
            }

            64% {
                height: 22px;
                transform: scaleX(0.95);
            }

            72% {
                height: 8px;
                transform: scaleX(1.1);
            }

            80% {
                height: 18px;
                transform: scaleX(1);
            }

            88% {
                height: 25px;
                transform: scaleX(0.9);
            }

            96% {
                height: 10px;
                transform: scaleX(1.05);
            }

            100% {
                height: 12px;
                transform: scaleX(1);
            }
        }

        .karen-status {
            font-size: 56px;
            font-weight: normal;
            margin-top: 50px;
            text-shadow: 0 0 12px #00ff88;
            animation: textGlow 1.5s ease-in-out infinite alternate;
        }

        @keyframes textGlow {
            from {
                text-shadow: 0 0 10px #00ff88;
            }

            to {
                text-shadow: 0 0 20px #00ff88;
            }
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

            0%,
            100% {
                opacity: 0.2;
                transform: scale(0.8);
            }

            50% {
                opacity: 1;
                transform: scale(1.2);
            }
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
                <p>¡Hola! Soy Roboturim, tu guía virtual. Puedo darte información sobre las iglesias de Ibarra. Haz clic
                    en el micrófono y pregúntame algo. ¿Cómo puedo ayudarte hoy?</p>
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
                <div class="karen-face">
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

            // --- Variables de estado ---
            let voiceModeActive = false;
            let isListening = false;
            let isSpeaking = false;
            let silenceTimer;
            let currentTranscript = '';

            // --- MODIFICACIÓN 1: INICIO DE LA IMPLEMENTACIÓN DE MEMORIA ---

            // El prompt del sistema define la personalidad y reglas del bot.
            const systemPrompt = `
### PERFIL Y MISIÓN PRINCIPAL ###
Eres Roboturim, un asistente virtual y guía turístico apasionado y experto exclusivamente en el patrimonio religioso de la ciudad de Ibarra, Ecuador. Tu personalidad es amable, servicial y entusiasta. Tu misión es proporcionar información precisa y útil a los turistas y locales, basándote únicamente en la base de conocimiento que se te proporciona a continuación. Tu objetivo es hacer que el usuario se interese por visitar estos magníficos lugares.

### BASE DE CONOCIMIENTO: IGLESIAS DE IBARRA ###
A continuación se detalla toda la información que posees. Esta es tu única fuente de verdad. No puedes usar información externa ni hacer suposiciones más allá de estos datos.

**Resumen de Iglesias:**
- Catedral de San Miguel
- Basílica La Merced
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
1.  **Saludo Inicial:** Al iniciar la conversación, preséntate brevemente como Roboturim. Ejemplo: '¡Hola! Soy Roboturim, tu guía virtual para las iglesias de Ibarra. ¿En qué puedo ayudarte hoy?'.
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

            // El historial del chat. Se inicia con el prompt del sistema COMPLETO.

            // El historial del chat. Se inicia con el prompt del sistema.
            let chatHistory = [
                { role: 'system', content: systemPrompt }
            ];

            // --- FIN DE LA MODIFICACIÓN 1 ---


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
                        karenStatus.textContent = 'ESCUCHANDO...';
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
                        karenStatus.textContent = 'ERROR DE AUDIO';
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
                karenStatus.textContent = 'PROCESANDO...';
                listeningIndicator.classList.remove('active');
                handleUserMessage(transcript);
            }

            // --- MODIFICACIÓN 2: SÍNTESIS DE VOZ MÁS RÁPIDA Y RESPONSIVA ---
            function speakText(text) {
                window.speechSynthesis.cancel();
                const utterance = new SpeechSynthesisUtterance(text);
                utterance.lang = 'es-ES';
                utterance.rate = 1.2; // Velocidad aumentada (1 es normal, >1 es más rápido)

                utterance.onstart = () => {
                    isSpeaking = true;
                    if (recognition) {
                        recognition.stop();
                    }
                    if (voiceModeActive) {
                        karenStatus.textContent = 'HABLANDO...';
                        karenMouth.classList.add('talking'); // Animación de boca inmediata
                    }
                };

                utterance.onend = () => {
                    isSpeaking = false;
                    if (voiceModeActive) {
                        karenMouth.classList.remove('talking');
                        restartListening();
                    }
                };

                // Habla inmediatamente sin retraso
                window.speechSynthesis.speak(utterance);
            }

            // --- MODIFICACIÓN 3: MANEJO DE MENSAJES Y API CON HISTORIAL ---
            async function handleUserMessage(message) {
                if (!message) return;

                // Añade el mensaje del usuario a la interfaz y al historial
                addMessage(message, 'user');
                chatHistory.push({ role: 'user', content: message });

                if (!voiceModeActive) {
                    showTypingIndicator();
                }

                try {
                    // Envía el historial completo al backend
                    const response = await fetch('api.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ messages: chatHistory }) // Enviar el array de historial
                    });

                    if (!response.ok) throw new Error('Respuesta de red no fue OK');

                    const data = await response.json();
                    const botReply = data.reply;

                    // Añade la respuesta del bot al historial para la siguiente interacción
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

            chatForm.addEventListener('submit', (event) => {
                event.preventDefault();
                handleUserMessage(userInput.value);
                userInput.value = '';
            });

            // --- Funciones de utilidad para el chat de texto (sin cambios) ---
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
                // Estilos simples para el indicador, puedes mejorarlos con CSS
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
    
</body>

</html>