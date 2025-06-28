<?php
// Paso 1: Configuración inicial y seguridad
header('Content-Type: application/json');

// ¡MUY IMPORTANTE! NO PONGAS TU API KEY DIRECTAMENTE AQUÍ EN UN PROYECTO REAL.
// Usa variables de entorno para máxima seguridad.
$apiKey = 'AIzaSyCE8VlW6Ry9v0-cFoxXnkYDZLtWjwGovXw'; // <-- ¡REEMPLAZA ESTO CON TU API KEY REAL!

// --- MODIFICACIÓN 1: LEER EL HISTORIAL DE CHAT COMPLETO ---
// En lugar de leer una sola 'question', ahora leemos el array 'messages'.
$requestData = json_decode(file_get_contents('php://input'), true);
$messagesFromFrontend = $requestData['messages'] ?? [];

if (empty($messagesFromFrontend)) {
    echo json_encode(['reply' => 'Error: No se recibió el historial de mensajes.']);
    exit;
}

// --- MODIFICACIÓN 2: ELIMINAR EL PROMPT DEL SISTEMA DE AQUÍ ---
// El prompt del sistema ($systemPrompt) ya no es necesario en este archivo.
// JavaScript lo añade como el primer mensaje del historial, que es una práctica más limpia.
// Así, este script se enfoca solo en la comunicación con la API.


// --- MODIFICACIÓN 3: TRANSFORMAR EL HISTORIAL PARA LA API DE GEMINI ---
// La API de Gemini necesita un formato específico de 'roles' (solo 'user' y 'model').
// Este código convierte nuestro historial (que usa 'system', 'user', 'assistant') al formato de Gemini.
$geminiContents = [];
foreach ($messagesFromFrontend as $message) {
    $role = $message['role'];
    $content = $message['content'];
    $mappedRole = '';

    // Convertimos nuestros roles a los roles que Gemini entiende ('user', 'model')
    if ($role === 'system') {
        // El prompt del sistema se trata como el primer mensaje del usuario.
        // Esto es una práctica estándar para la API de Gemini.
        $mappedRole = 'user';
    } elseif ($role === 'assistant') {
        // El rol de nuestro asistente se mapea al rol 'model' de Gemini.
        $mappedRole = 'model';
    } else { // 'user'
        $mappedRole = 'user';
    }

    // Añadimos el mensaje formateado al array que enviaremos a Gemini.
    $geminiContents[] = [
        'role' => $mappedRole,
        'parts' => [['text' => $content]]
    ];
}


// Paso 3: Preparar la llamada a la API de Gemini con el historial completo
$geminiApiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=' . $apiKey;

$dataPayload = [
    'contents' => $geminiContents, // <-- ¡Aquí usamos el historial formateado!
    'safetySettings' => [
        ['category' => 'HARM_CATEGORY_HARASSMENT', 'threshold' => 'BLOCK_ONLY_HIGH'],
        ['category' => 'HARM_CATEGORY_HATE_SPEECH', 'threshold' => 'BLOCK_ONLY_HIGH'],
    ]
];

// Paso 4: Ejecutar la llamada a la API con cURL (sin cambios aquí)
$ch = curl_init($geminiApiUrl);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($dataPayload));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // No recomendado para producción

$response = curl_exec($ch);
$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// Paso 5: Procesar la respuesta y enviarla al frontend
$responseData = json_decode($response, true);
$botReply = '';

// Verificamos si la respuesta de la API fue exitosa (código 200) y si contiene el texto.
if ($httpcode == 200 && isset($responseData['candidates'][0]['content']['parts'][0]['text'])) {
    $botReply = $responseData['candidates'][0]['content']['parts'][0]['text'];
} else {
    // Si hay un error, lo capturamos para dar un mensaje más claro.
    $error_message = $responseData['error']['message'] ?? 'Respuesta desconocida de la API.';
    $botReply = 'Lo siento, ha ocurrido un error: ' . $error_message;
    // Opcional: registrar el error completo para depuración
    // error_log("Error de la API de Gemini: " . $response);
}

// Enviar la respuesta final en formato JSON al JavaScript
echo json_encode(['reply' => $botReply]);