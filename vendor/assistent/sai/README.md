# SAI (Sistema de Asistencia Inteligente) para Laravel

SAI es una librería de PHP diseñada específicamente para Laravel que te permite crear asistentes virtuales basados en ChatGPT para aplicaciones. Con SAI, puedes controlar las funciones de la aplicación a través del lenguaje natural, lo que permite a los usuarios realizar tareas complejas de manera más eficiente.

## Instalación

Para instalar SAI en tu proyecto Laravel, ejecuta el siguiente comando:

```bash
composer require assistent/sai
```

Una vez que hayas instalado la librería, debes ejecutar el siguiente comando para configurar SAI:

```bash
php artisan sai:install
```

A continuación, crea un enlace simbólico para los archivos de almacenamiento:

```bash
php artisan storage:link
```
Recuerda configurar las siguientes variables en tu archivo .env:

```bash
OPENAI_API_KEY="tu api key"
OPENAI_MODEL="gpt-3.5-turbo" # Puedes elegir cualquier modelo
OPENAI_MAX_TOKEN=200
```
## Uso

Con SAI, puedes crear un chatbot que se integra con ChatGPT y agregar funcionalidades personalizadas según tus necesidades. Para incluir el chat como una ventana flotante en tu vista, simplemente agrega el siguiente código:

```bash
@include('assistent.assistent')
```

## Principios

Puedes definir los principios que el servicio debe reconocer en el archivo app/Principles/Principles.php. Por ejemplo:

```bash
class Principles extends SaiPrinciples {
    public function __invoke()
    {
        return array_merge(
            array_map(function ($principle) {
                return $principle;
            }, $this->default),
            [
                'Eres el asistente virtual de la empresa gglass'
            ]
        );
    }
}
```
## Directivas

Define los métodos que deseas llamar y los mensajes que los usuarios pueden utilizar para invocar esas funciones en el archivo app/config/sai.php. Por ejemplo:

```bash
"methods": [
    {
        "method": "GetFunctions",
        "messages": [
            "¿Qué funcionalidades tienes?",
            "Muéstrame lo que puedes hacer",
            "Muéstrame tus funcionalidades"
        ]
    }
]
```

Luego, crea la directiva correspondiente en app/Directives/Methods.

## Descripción

SAI es una herramienta poderosa para crear asistentes virtuales que pueden entender el lenguaje natural y realizar tareas complejas en aplicaciones. Con su proceso de instalación sencillo y opciones de configuración flexibles, SAI permite a los desarrolladores crear chatbots inteligentes y eficientes que mejoran la experiencia del usuario.

## Contribución
Si deseas contribuir a este proyecto, ¡estamos abiertos a colaboraciones! Siéntete libre de enviar pull requests o informar problemas en el repositorio de GitHub.

## Licencia

Este proyecto se distribuye bajo la licencia MIT. Consulta el archivo LICENSE para obtener más información.