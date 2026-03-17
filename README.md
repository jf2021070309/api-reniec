# Consulta DNI API (RENIEC)

Herramienta ligera en PHP para obtener nombres y apellidos de ciudadanos peruanos mediante el scraping de `eldni.com`. Proporciona tanto una interfaz web moderna como un endpoint API para integraciones sencillas.

## Características
- 🚀 **Ligero**: Implementación simple en PHP sin dependencias pesadas.
- 🎨 **Interfaz Premium**: Diseño moderno, responsivo y minimalista utilizando la fuente Inter.
- 🔗 **API Ready**: Respuestas en formato JSON para una fácil integración con otros sistemas.
- 🛡️ **Seguro**: Manejo de tokens de seguridad (CSRF) para las consultas externas.

## Estructura del Proyecto
- `api.php`: Núcleo del proyecto. Maneja la lógica de scraping, extracción de datos y respuestas JSON.
- `index.php`: Interfaz de usuario intuitiva para consultas manuales.
- `style.css`: Hoja de estilos con enfoque en estética moderna y usabilidad.

## Uso

### Vía API
Puede integrar la consulta en sus propios proyectos realizando una petición GET:

**Endpoint:** `api.php?dni={8_DIGITOS}`

**Ejemplo de Respuesta:**
```json
{
  "success": true,
  "data": {
    "nombres": "JUAN CARLOS",
    "apellidoPaterno": "PEREZ",
    "apellidoMaterno": "RODRIGUEZ"
  }
}
```

### Vía Web
1. Coloque los archivos en su servidor local (ej. XAMPP en `htdocs/api-reniec`).
2. Acceda a `http://localhost/api-reniec/index.php` desde su navegador.
3. Ingrese el número de DNI y haga clic en "Consultar".

## Requisitos
- PHP 7.4 o superior.
- Extensión `curl` de PHP habilitada.

---
*Nota: Este proyecto realiza scraping de fuentes públicas y debe ser utilizado de manera responsable.*
