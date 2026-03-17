<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulta DNI API - Premium</title>
    <link rel="stylesheet" href="style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h1>Consulta DNI API</h1>
        <p class="subtitle">Obtén nombres y apellidos mediante scrapeo de eldni.com</p>
        
        <div class="search-group">
            <input type="text" id="dniInput" placeholder="Ingresa DNI (8 dígitos)" maxlength="8" autofocus>
            <button id="searchBtn">Consultar</button>
        </div>

        <div id="loader" class="loader">Consultando...</div>
        <div id="error" class="error"></div>

        <div id="resultCard" class="result-card">
            <div class="result-item">
                <div class="label">Nombres</div>
                <div id="resNombres" class="value">-</div>
            </div>
            <div class="result-item">
                <div class="label">Apellido Paterno</div>
                <div id="resPaterno" class="value">-</div>
            </div>
            <div class="result-item">
                <div class="label">Apellido Materno</div>
                <div id="resMaterno" class="value">-</div>
            </div>
        </div>

        <div class="api-footer">
            <p><strong>Endpoint API:</strong> <code>/api.php?dni={numero}</code></p>
        </div>
    </div>

    <script>
        const elements = {
            dniInput: document.getElementById('dniInput'),
            searchBtn: document.getElementById('searchBtn'),
            loader: document.getElementById('loader'),
            error: document.getElementById('error'),
            resultCard: document.getElementById('resultCard'),
            resNombres: document.getElementById('resNombres'),
            resPaterno: document.getElementById('resPaterno'),
            resMaterno: document.getElementById('resMaterno')
        };

        const updateUI = (state, data = {}) => {
            elements.loader.style.display = state === 'loading' ? 'block' : 'none';
            elements.error.style.display = state === 'error' ? 'block' : 'none';
            elements.resultCard.style.display = state === 'success' ? 'block' : 'none';
            
            if (state === 'error') elements.error.textContent = data.message;
            if (state === 'success') {
                elements.resNombres.textContent = data.nombres || '-';
                elements.resPaterno.textContent = data.apellidoPaterno || '-';
                elements.resMaterno.textContent = data.apellidoMaterno || '-';
            }
        };

        elements.searchBtn.addEventListener('click', async () => {
            const dni = elements.dniInput.value.trim();
            if (!/^\d{8}$/.test(dni)) {
                updateUI('error', { message: 'Por favor, ingresa un DNI válido de 8 dígitos.' });
                return;
            }

            updateUI('loading');

            try {
                const response = await fetch(`api.php?dni=${dni}`);
                const result = await response.json();

                if (result.success) {
                    updateUI('success', result.data);
                } else {
                    updateUI('error', { message: result.message || 'Error al consultar el DNI.' });
                }
            } catch (e) {
                updateUI('error', { message: 'Error de conexión con el servidor.' });
            }
        });

        elements.dniInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') elements.searchBtn.click();
        });
    </script>
</body>
</html>
