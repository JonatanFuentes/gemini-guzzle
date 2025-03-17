<?php
require 'vendor/autoload.php';
use GuzzleHttp\Client;

function extractTextFromElement($element) {
    $text = '';
    if ($element instanceof \PhpOffice\PhpWord\Element\Text) {
        $text .= $element->getText();
    } elseif ($element instanceof \PhpOffice\PhpWord\Element\TextRun) {
        foreach ($element->getElements() as $childElement) {
            $text .= extractTextFromElement($childElement);
        }
    } elseif (method_exists($element, 'getElements')) {
        foreach ($element->getElements() as $childElement) {
            $text .= extractTextFromElement($childElement);
        }
    }
    return $text;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $question = isset($_POST['question']) ? $_POST['question'] : '';
    $fileText = '';
    if (isset($_FILES['document']) && $_FILES['document']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['document']['tmp_name'];
        $fileName = $_FILES['document']['name'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));
        $allowedExtensions = ['pdf', 'docx'];
        if (in_array($fileExtension, $allowedExtensions)) {
            if ($fileExtension === 'pdf') {
                $parser = new \Smalot\PdfParser\Parser();
                $pdf = $parser->parseFile($fileTmpPath);
                $fileText = $pdf->getText();
            } elseif ($fileExtension === 'docx') {
                $phpWord = \PhpOffice\PhpWord\IOFactory::load($fileTmpPath);
                $text = '';
                foreach ($phpWord->getSections() as $section) {
                    foreach ($section->getElements() as $element) {
                        $text .= extractTextFromElement($element) . "\n";
                    }
                }
                $fileText = $text;
            }
        } else {
            echo "Formato de archivo no permitido. Solo se permiten PDF y DOCX.";
            exit;
        }
    }
    if ($fileText) {
        $prompt = "NO UTILICES ASTERISCOS *:\n\n{$fileText}\n\nPregunta: {$question}";
    } else {
        $prompt = $question;
    }
    $apiKey = 'AIzaSyD7J6lc3asaKsFpRLeCAs0YKscNBIZDzNE';
    $endpoint = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash-thinking-exp-01-21:generateContent?key={$apiKey}";
    $client = new Client([
        'headers' => ['Content-Type' => 'application/json']
    ]);

    try {
        $response = $client->post($endpoint, [
            'json' => [
                'contents' => [
                    ['parts' => [['text' => $prompt]]]
                ],
                'generationConfig' => [
                    'temperature' => 0.8
                ]
            ]
        ]);
        $body = json_decode($response->getBody(), true);
        echo $body['candidates'][0]['content']['parts'][0]['text'] ?? 'Error en la respuesta de la API';
    } catch (Exception $e) {
        echo 'Error: ' . $e->getMessage();
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP IA</title>
    <style><?php include('css/style.css'); ?></style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <H1>Gemini en PHP</H1>
    <h4>Este sistema está desarrollado exclusivamente con PHP 8 y Guzzle, la biblioteca de cliente HTTP proporcionada por Google para usar su IA, sin depender de otras 
        <br>librerías externas que puedan restringir o limitar los modelos a utilizar de Gemini.</h4>
    <form id="questionForm" method="post" enctype="multipart/form-data">
        <textarea name="question" id="question" rows="4" placeholder="Pregunta lo que que quieras"></textarea>
        <br>
        <label for="audio_file">Subir documento docx o pdf para referencias</label><br>
        <input type="file" name="document" id="document" accept=".pdf, .docx">
        <br>
        <input type="submit" value="Generar">
    </form>
    <div class="response" id="responseDiv">
        <p>Tu respuesta aparecerá aquí...</p>
    </div>
    <br>
    <br>
    <footer>Desarrollado por <a href="mailto:fuentescerezojonatan@gmail.com">Jonatan Fuentes Cerezo</a></footer>

    <br>
    <br>
    <script>
        $(document).ready(function() {
            $('#questionForm').submit(function(e) {
                e.preventDefault();
                var formData = new FormData(this);

                $('#responseDiv').html(`
                   <p>Procesando...</p>
                   <img class="loading-gif" src="https://media.tenor.com/On7kvXhzml4AAAAj/loading-gif.gif" alt="Cargando...">
               `);
                $.ajax({
                    url: '',
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
    $('#responseDiv').html('<p>' + response.replace(/\n/g, '<br>') + '</p>');
},

                    error: function() {
                        $('#responseDiv').html('<p class="error">Error al obtener respuesta.</p>');
                    }
                });
            });
        });
    </script>
</body>
</html>
