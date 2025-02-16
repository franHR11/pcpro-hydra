<!doctype html>
<html>
    <head>
        <title>Documentación del Sistema</title>
        <style>
            /* General styling */
            @page {
                margin: 1in;
            }

            body {
                font-family: 'Georgia', serif;
                line-height: 1.5;
                color: #333;
                margin: 0;
                padding: 0;
                background: #fff;
            }

            h1, h2, h3 {
                font-family: 'Times New Roman', serif;
                text-align: center;
                margin: 20px 0;
                color: #111;
            }

            h1 {
                font-size: 2.5em;
            }

            h2 {
                font-size: 1.8em;
            }

            h3 {
                font-size: 1.5em;
            }

            p {
                font-size: 12pt;
                margin: 10px 0;
                text-align: justify;
                text-indent: 1em;
            }

            ul {
                list-style-type: none;
                padding: 0;
                margin: 0;
            }

            li {
                margin: 5px 0;
            }

            pre {
                font-family: 'Courier New', monospace;
                font-size: 11pt;
                background: #f9f9f9;
                border: 1px solid #ddd;
                border-radius: 5px;
                padding: 10px;
                overflow-x: auto;
                margin-bottom: 10px;
            }

            /* Book-like styling */
            .container {
                margin-top: 60px;
                width: 8.5in;
                padding: 0.5in;
            }

            .page-break {
                page-break-before: always;
            }

            /* Table of contents styling */
            ul.toc {
                padding: 0;
                margin: 0 0 20px 0;
                list-style-type:none;
            }

            ul.toc li {
                font-size: 12pt;
                margin: 10px 0;
                list-style-type:none;
                display:block;
                padding-left:20px;
            }

            /* Special styling for inline notes and comments */
            .comment {
                font-style: italic;
                color: #555;
                background: #f1f1f1;
                border-left: 4px solid #ccc;
                padding: 5px 10px;
                margin: 10px 0;
            }

            /* Footer styling */
            footer {
                text-align: center;
                font-size: 10pt;
                color: #888;
                margin-top: 20px;
                border-top: 1px solid #eee;
                padding-top: 10px;
            }

            @media print{
                @page {
                    margin: 1cm;
                }
                body {
                    font-family: 'Georgia', serif;
                    line-height: 1.5;
                    color: #333;
                    margin: 0;
                    padding: 0;
                    background: white;
                }
                .container {
                    margin-top: 0;
                    width: 100%;
                    margin: auto;
                    padding:0px;
                    background: white;
                    box-shadow: none;
                }
                .nav-bar {
                    display: none;
                }
            }

            /* Añadir estilos para mejor visualización */
            .file-description {
                background: white;
                border-left: 4px solid #1a73e8;
                margin: 15px 0;
                transition: transform 0.2s;
            }

            .file-description:hover {
                transform: translateX(5px);
            }

            .folder-path {
                background: #f8f9fa;
                padding: 8px;
                border-bottom: 1px solid #dee2e6;
                color: #666;
                font-size: 0.9em;
                margin-bottom: 5px;
            }

            /* Nuevos estilos */
            .nav-bar {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                background: #1a73e8;
                padding: 10px;
                text-align: center;
                z-index: 1000;
            }

            .nav-bar a {
                color: white;
                text-decoration: none;
                padding: 8px 15px;
                border-radius: 5px;
                margin: 0 10px;
                background: rgba(255,255,255,0.1);
            }

            .nav-bar a:hover {
                background: rgba(255,255,255,0.2);
            }
        </style>
    </head>
    <body>
        <div class="nav-bar">
            <a href="panel.php">← Volver al Panel</a>
            <a href="javascript:window.print()">🖨️ Imprimir</a>
        </div>
        <div class="container">
            <h1>Documentación del Sistema</h1>
            <?php
            /**
             * Página principal para visualizar la documentación generada.
             * 
             * Este archivo muestra la estructura del proyecto y la documentación detallada
             * generada a partir de los docstrings y comentarios en los archivos fuente.
             * 
             * Autor: franHR
             */

            function processFolderForHtml($source,$indice) {
                $html = "<ul class='toc'>\n";
                $items = scandir($source);
                
                // Ordenar items: carpetas primero, luego archivos
                usort($items, function($a, $b) use ($source) {
                    $aIsDir = is_dir($source . DIRECTORY_SEPARATOR . $a);
                    $bIsDir = is_dir($source . DIRECTORY_SEPARATOR . $b);
                    if ($aIsDir === $bIsDir) return strcasecmp($a, $b);
                    return $bIsDir ? -1 : 1;
                });

                $baseDir = dirname(dirname(dirname(__FILE__))); // Ruta base del proyecto
                
                foreach ($items as $item) {
                    if ($item === '.' || $item === '..') {
                        continue;
                    }
                    $sourcePath = $source . DIRECTORY_SEPARATOR . $item;
                    // Convertir a ruta relativa desde la raíz del proyecto
                    $relativePath = str_replace($baseDir, '', str_replace('\\', '/', $sourcePath));
                    $relativePath = ltrim($relativePath, '/');
                    
                    if (is_dir($sourcePath)) {
                        $folderIcon = "📁";
                        $html .= "<li><div class='folder-path'>$folderIcon $relativePath</div>\n";

                        $html .= processFolderForHtml($sourcePath,$indice);
                        $html .= "</li>\n";
                    } else if (is_file($sourcePath) && pathinfo($item, PATHINFO_EXTENSION) === 'txt') {
                        if($indice == "no"){
                            $contents = htmlspecialchars(file_get_contents($sourcePath));
                            $html .= "<li class='file-description'><div class='folder-path'>📄 $relativePath</div>";
                            $html .= "<div class='content'>".nl2br(htmlspecialchars($contents))."</div></li>\n";
                        }
                    }
                }
                $html .= "</ul>\n";
                return $html;
            }

            $sourceFolder = 'docs'; // Replace with the path to your target folder
            ?>
            <h2>Estructura del Proyecto</h2>
            <?php
            echo processFolderForHtml($sourceFolder,"si");
            ?>
            <div class="page-break"></div>
            <h2>Documentación Detallada</h2>
            <?php
            echo processFolderForHtml($sourceFolder,"no");
            ?>
        </div>
        <footer>
            <p>&copy; <?php echo date('Y'); ?> Documentación del Sistema. Generado automáticamente.</p>
        </footer>
        <script src="https://aurora.pcprogramacion.es/tracking/script.js"></script>
    </body>
    
</html>