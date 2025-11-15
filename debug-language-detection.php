<?php
/**
 * Language Detection - Debug Log Viewer
 *
 * Affiche les logs de détection de langue en temps réel
 * URL: /debug-language-detection.php
 *
 * ⚠️ À SUPPRIMER EN PRODUCTION
 */

$logFile = __DIR__ . '/logs/language-detection-debug.log';

// Check if log file exists
if (!file_exists($logFile)) {
    $logContent = "Aucun log disponible encore.\n\nLe fichier de log sera créé lors de votre première visite.\n\nAccédez à / (racine) pour déclencher la détection.";
} else {
    $logContent = file_get_contents($logFile);
}

// Count entries
$logLines = explode("\n", trim($logContent));
$entryCount = substr_count($logContent, "=== LANGUAGE DETECTION START ===");

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calcuze - Language Detection Debug Logs</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Monaco', 'Courier New', monospace;
            background: #1e1e1e;
            color: #d4d4d4;
            padding: 2rem;
            line-height: 1.6;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
        }

        h1 {
            color: #4ec9b0;
            margin-bottom: 0.5rem;
            font-size: 2rem;
        }

        .subtitle {
            color: #858585;
            margin-bottom: 2rem;
            font-size: 0.9rem;
        }

        .controls {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
            flex-wrap: wrap;
        }

        button {
            padding: 0.75rem 1.5rem;
            background: #007acc;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-family: 'Monaco', 'Courier New', monospace;
            font-size: 0.9rem;
            transition: background 0.2s;
        }

        button:hover {
            background: #005a9e;
        }

        button.danger {
            background: #d16969;
        }

        button.danger:hover {
            background: #b85555;
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .stat {
            background: #252526;
            border-left: 4px solid #007acc;
            padding: 1rem;
            border-radius: 4px;
        }

        .stat-value {
            font-size: 2rem;
            font-weight: bold;
            color: #4ec9b0;
        }

        .stat-label {
            font-size: 0.85rem;
            color: #858585;
            margin-top: 0.5rem;
        }

        .log-viewer {
            background: #1e1e1e;
            border: 1px solid #3e3e42;
            border-radius: 4px;
            padding: 1.5rem;
            overflow-x: auto;
            max-height: 600px;
            overflow-y: auto;
        }

        .log-line {
            white-space: pre-wrap;
            word-wrap: break-word;
            margin-bottom: 0.2rem;
        }

        .log-header {
            color: #4ec9b0;
            font-weight: bold;
            margin-top: 1rem;
            margin-bottom: 0.5rem;
        }

        .log-header:first-child {
            margin-top: 0;
        }

        .log-section-title {
            color: #9cdcfe;
        }

        .log-key {
            color: #9cdcfe;
        }

        .log-value {
            color: #ce9178;
        }

        .log-success {
            color: #6a9955;
        }

        .log-error {
            color: #d16969;
        }

        .log-warning {
            color: #dcdcaa;
        }

        .log-info {
            color: #6a9955;
        }

        .footer {
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid #3e3e42;
            color: #858585;
            font-size: 0.85rem;
        }

        .footer a {
            color: #007acc;
            text-decoration: none;
        }

        .footer a:hover {
            text-decoration: underline;
        }

        .empty-state {
            color: #858585;
            text-align: center;
            padding: 3rem;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Language Detection Debug Logs</h1>
        <p class="subtitle">Suivi en temps réel de la détection de langue et des redirections</p>

        <div class="controls">
            <button onclick="location.reload()">🔄 Actualiser</button>
            <button onclick="clearLogs()" class="danger">🗑️ Effacer les logs</button>
            <button onclick="downloadLogs()">📥 Télécharger</button>
            <button onclick="copyToClipboard()">📋 Copier tout</button>
        </div>

        <div class="stats">
            <div class="stat">
                <div class="stat-value"><?php echo $entryCount; ?></div>
                <div class="stat-label">Détections totales</div>
            </div>
            <div class="stat">
                <div class="stat-value"><?php echo count($logLines); ?></div>
                <div class="stat-label">Lignes de log</div>
            </div>
            <div class="stat">
                <div class="stat-value"><?php echo date('Y-m-d H:i:s', filemtime($logFile) ?? time()); ?></div>
                <div class="stat-label">Dernière mise à jour</div>
            </div>
        </div>

        <div class="log-viewer" id="logViewer">
            <?php if (strpos($logContent, "Aucun log") === 0): ?>
                <div class="empty-state">
                    <?php echo htmlspecialchars($logContent); ?>
                </div>
            <?php else: ?>
                <?php
                // Color-code the log output
                $formattedLog = htmlspecialchars($logContent);

                // Highlight different types of lines
                $formattedLog = preg_replace(
                    '/^(=== .* ===)$/m',
                    '<span class="log-header">$1</span>',
                    $formattedLog
                );

                $formattedLog = preg_replace(
                    '/^(--- .* ---)$/m',
                    '<span class="log-section-title">$1</span>',
                    $formattedLog
                );

                $formattedLog = preg_replace(
                    '/^([A-Za-z ]+): (NOT SET|YES|NO|VALID|INVALID|.+)$/m',
                    '<span class="log-key">$1</span>: <span class="log-value">$2</span>',
                    $formattedLog
                );

                $formattedLog = preg_replace(
                    '/(REDIRECT EXECUTED)/m',
                    '<span class="log-warning">$1</span>',
                    $formattedLog
                );

                $formattedLog = preg_replace(
                    '/(VALID|YES|true)/m',
                    '<span class="log-success">$1</span>',
                    $formattedLog
                );

                $formattedLog = preg_replace(
                    '/(INVALID|NO|false|NOT SET)/m',
                    '<span class="log-error">$1</span>',
                    $formattedLog
                );
                ?>
                <div class="log-line"><?php echo $formattedLog; ?></div>
            <?php endif; ?>
        </div>

        <div class="footer">
            <p>
                <strong>💡 Comment utiliser:</strong>
            </p>
            <ul style="margin-left: 2rem; margin-top: 0.5rem;">
                <li>Accédez à <code>/</code> (racine) avec votre navigateur français</li>
                <li>Vérifiez les logs ci-dessus pour voir la détection</li>
                <li>Cherchez "REDIRECT EXECUTED" pour voir si la redirection a eu lieu</li>
                <li>Vérifiez les valeurs de Accept-Language header</li>
                <li>Cherchez "Detection Source" pour voir d'où vient la détection (cookie, url, accept-language, fallback)</li>
            </ul>

            <p style="margin-top: 1.5rem;">
                <strong>⚠️ Important:</strong> Ce fichier doit être supprimé en production.<br>
                <a href="/">← Retour à Calcuze</a>
            </p>
        </div>
    </div>

    <script>
        function clearLogs() {
            if (confirm('Êtes-vous sûr? Tous les logs seront supprimés.')) {
                fetch('<?php echo $_SERVER['PHP_SELF']; ?>?action=clear', {method: 'POST'})
                    .then(() => location.reload());
            }
        }

        function downloadLogs() {
            const logContent = document.getElementById('logViewer').innerText;
            const element = document.createElement('a');
            element.setAttribute('href', 'data:text/plain;charset=utf-8,' + encodeURIComponent(logContent));
            element.setAttribute('download', 'language-detection-logs.txt');
            element.style.display = 'none';
            document.body.appendChild(element);
            element.click();
            document.body.removeChild(element);
        }

        function copyToClipboard() {
            const logContent = document.getElementById('logViewer').innerText;
            navigator.clipboard.writeText(logContent).then(() => {
                alert('Logs copiés dans le presse-papiers!');
            });
        }
    </script>
</body>
</html>

<?php
// Handle clear action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'clear') {
    $logFile = __DIR__ . '/logs/language-detection-debug.log';
    if (file_exists($logFile)) {
        unlink($logFile);
        header('Content-Type: application/json');
        echo json_encode(['status' => 'success']);
        exit;
    }
}
?>

