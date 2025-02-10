# Generador de Documentación

Sistema de generación automática de documentación basado en docstrings y comentarios del código fuente.

## Requisitos Previos

- PHP 7.0 o superior
- Servidor web (Apache recomendado)
- Permisos de escritura en la carpeta de documentación
- Sesión de usuario iniciada (requiere autenticación)

## Estructura de Carpetas

```

├── generar.php
├── index.php
├── panel.php
└── README.md
```

## Configuración

1. Asegúrate de tener un servidor web configurado y funcionando (por ejemplo, XAMPP).
2. Coloca los archivos del proyecto en el directorio raíz de tu servidor web.
3. Verifica que la carpeta `docs` tenga permisos de escritura. Si no existe, el script intentará crearla automáticamente.

## Uso

1. Inicia sesión en tu aplicación web.
2. Accede al `panel.php` para ver el panel de control de documentación.
3. Haz clic en "Generar Documentación" para ejecutar el script `generar.php` que procesará las carpetas y archivos del proyecto.
4. Una vez generada la documentación, puedes verla accediendo a `index.php`.

## Detalles Técnicos

- `generar.php`: Procesa las carpetas y archivos del proyecto, generando documentación en la carpeta `docs`.
- `index.php`: Muestra la estructura del proyecto y la documentación generada.
- `panel.php`: Proporciona una interfaz para generar y ver la documentación.

## Exclusiones

El script excluye las siguientes carpetas de la generación de documentación:
- `.git`
- `documentacion`
- `docs`
- `node_modules`
- `vendor`

## Notas

- Asegúrate de que el servidor web tenga permisos de escritura en la carpeta `docs`.
- La documentación se genera automáticamente a partir de los docstrings y comentarios en los archivos fuente.

## Ejemplo de Uso

1. Accede a `http://localhost/util/documentacion/panel.php`.
2. Haz clic en "Generar Documentación".
3. Una vez completado, haz clic en "Ver Documentación" para revisar la documentación generada.

## Contacto

Para cualquier duda o problema, contacta al administrador del sistema.
