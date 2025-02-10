<?php
/**
 * Script para generar documentación automáticamente.
 * 
 * Este archivo procesa las carpetas y archivos del proyecto, generando documentación
 * en la carpeta `docs` a partir de los docstrings y comentarios en los archivos fuente.
 * 
 * Autor: franHR
 */

session_start();
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    echo "Intento de acceso incorrecto";
    exit();
}
// Lista de carpetas y archivos a excluir
$EXCLUDED_DIRS = [
    '.git',
    'docs',
    'node_modules',
    'vendor'
];

// Función auxiliar para eliminar directorio y contenido
function deleteDirectory($dir) {
    if (!file_exists($dir)) return true;
    if (!is_dir($dir)) return unlink($dir);
    foreach (scandir($dir) as $item) {
        if ($item == '.' || $item == '..') continue;
        if (!deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) return false;
    }
    return rmdir($dir);
}

function processFolder($source, $target) {
    global $EXCLUDED_DIRS;

    if (!file_exists($target)) {
        if (!@mkdir($target, 0777, true)) {
            throw new Exception("No se pudo crear el directorio: $target");
        }
    }

    $items = scandir($source);
    
    foreach ($items as $item) {
        // Skip excluded directories and special directories
        if (in_array($item, $EXCLUDED_DIRS) || $item === '.' || $item === '..') {
            continue;
        }

        $sourcePath = $source . DIRECTORY_SEPARATOR . $item;
        $targetPath = $target . DIRECTORY_SEPARATOR . $item;
        
        // Convertir rutas a relativas para la documentación
        $relativePath = str_replace($_SERVER['DOCUMENT_ROOT'], '', $sourcePath);
        $relativePath = str_replace('\\', '/', $relativePath);

        if (is_dir($sourcePath)) {
            if (!file_exists($targetPath)) {
                @mkdir($targetPath, 0777, true);
            }
            
            // Crear archivo de descripción de carpeta
            $txtFilePath = $targetPath . DIRECTORY_SEPARATOR . 'folder_info.txt';
            $folderInfo = "Carpeta: $item\n";
            $folderInfo .= "Ruta Relativa: $relativePath\n";
            file_put_contents($txtFilePath, $folderInfo);

            processFolder($sourcePath, $targetPath);
        } else if (is_file($sourcePath)) {
            $fileInfo = pathinfo($sourcePath);
            $docFolderPath = $target . DIRECTORY_SEPARATOR . $fileInfo['filename'];
            
            if (!file_exists($docFolderPath)) {
                @mkdir($docFolderPath, 0777, true);
            }

            // Crear archivo de documentación
            $txtFilePath = $docFolderPath . DIRECTORY_SEPARATOR . 'doc.txt';
            $docContent = "Archivo: {$fileInfo['basename']}\n";
            $docContent .= "Ruta Relativa: $relativePath\n\n";
            $docContent .= "Documentación:\n";
            $docContent .= extractDocstring($sourcePath);
            
            file_put_contents($txtFilePath, $docContent);
        }
    }
}

function extractDocstring($filePath) {
    $content = file_get_contents($filePath);
    $extension = pathinfo($filePath, PATHINFO_EXTENSION);
    $docstring = '';

    switch ($extension) {
        case 'php':
            // Buscar docstrings de clase
            if (preg_match('/<\\?php\\s*\\/\\*\\*(.*?)\\*\\//s', $content, $matches)) {
                $docstring .= trim($matches[1]) . "\n\n";
            }
            
            // Buscar docstrings de funciones
            preg_match_all('/\/\*\*(.*?)\*\/\s*function\s+(\w+)/s', $content, $matches);
            if (!empty($matches[1])) {
                foreach ($matches[1] as $index => $doc) {
                    $funcName = $matches[2][$index];
                    $docstring .= "Función: $funcName\n" . trim($doc) . "\n\n";
                }
            }
            break;

        case 'js':
            // Buscar docstrings JSDoc
            preg_match_all('/\/\*\*(.*?)\*\//s', $content, $matches);
            if (!empty($matches[1])) {
                $docstring = trim(implode("\n\n", $matches[1]));
            }
            break;

        case 'css':
        case 'java':
        case 'c':
        case 'cpp':
            // Match comments enclosed in /* ... */
            if (preg_match('/\\/\\*(.*?)\\*\\//s', $content, $matches)) {
                return trim($matches[1]);
            }
            break;

        case 'py':
            // Match Python docstrings (""" ... """ or single-line # comments)
            if (preg_match('/^\s*"""(.*?)"""/s', $content, $matches)) {
                return trim($matches[1]);
            } elseif (preg_match('/^\s*#\s*(.+)$/m', $content, $matches)) {
                return trim($matches[1]);
            }
            break;

        case 'html':
        case 'htm':
            // Match HTML comments <!-- ... -->
            if (preg_match('/<!--(.*?)-->/s', $content, $matches)) {
                return trim($matches[1]);
            }
            break;

        default:
            return '';
    }

    // Si no hay docstring, intentar extraer una descripción básica
    if (empty($docstring)) {
        $docstring = "Archivo: " . basename($filePath) . "\n";
        $docstring .= "Tipo: " . strtoupper($extension) . "\n";
        $docstring .= "Ruta: " . str_replace('\\', '/', $filePath) . "\n";
    }

    return $docstring;
}

// Configuración de rutas relativas
$baseDir = dirname(dirname(dirname(__FILE__))); 
$sourceFolder = $baseDir; 
$targetFolder = __DIR__ . DIRECTORY_SEPARATOR . 'docs';

// Verificar permisos y crear carpeta docs
if (!file_exists($targetFolder)) {
    // Verificar permisos del directorio padre
    if (!is_writable(__DIR__)) {
        header("Location: panel.php?status=error&message=" . urlencode("Error: No hay permisos de escritura en el directorio " . __DIR__));
        exit;
    }
    
    // Intentar crear el directorio con permisos completos
    if (!mkdir($targetFolder, 0777, true)) {
        header("Location: panel.php?status=error&message=" . urlencode("Error: No se pudo crear el directorio docs. Verifique los permisos"));
        exit;
    }
    
    // Asegurarse que los permisos se establezcan correctamente
    chmod($targetFolder, 0777);
}

// Verificar si el directorio es escribible
if (!is_writable($targetFolder)) {
    header("Location: panel.php?status=error&message=" . urlencode("Error: El directorio docs no tiene permisos de escritura"));
    exit;
}

try {
    processFolder($sourceFolder, $targetFolder);
    header("Location: panel.php?status=success&message=¡Documentación generada correctamente!");
} catch (Exception $e) {
    header("Location: panel.php?status=error&message=" . urlencode($e->getMessage()));
}
exit;
