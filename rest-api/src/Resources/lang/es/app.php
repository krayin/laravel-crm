<?php

return [
    'common' => [
        'auth' => [
            'login' => [
                'success' => 'Inicio de sesión exitoso.',
                'logout'  => 'Cierre de sesión exitoso.',
            ],
        ],

        'resource-not-found'    => 'No se pudo encontrar el recurso solicitado.',
        'forbidden-error'       => 'No tienes permiso para acceder a este recurso.',
        'unauthenticated'       => 'No estás autenticado. Por favor, inicia sesión para continuar.',
        'internal-server-error' => 'Ocurrió un error inesperado en el servidor. Por favor, inténtalo de nuevo más tarde.',
    ],

    'products' => [
        'create-success'           => 'Producto creado con éxito.',
        'updated-success'          => 'Producto actualizado con éxito.',
        'delete-success'           => 'Producto eliminado con éxito.',
        'delete-failed'            => 'La eliminación del producto falló.',
        'inventory-create-success' => 'Inventario almacenado con éxito.',
    ],

    'leads' => [
        'create-success'  => 'Lead creado con éxito.',
        'updated-success' => 'Lead actualizado con éxito.',
        'delete-success'  => 'Lead eliminado con éxito.',
        'delete-failed'   => 'La eliminación del lead falló.',
        'no-valid-files'  => 'No se encontraron archivos válidos.',

        'view' => [
            'tags' => [
                'create-success' => 'Etiqueta adjuntada con éxito.',
                'delete-success' => 'Etiqueta desadjuntada con éxito.',
            ],

            'quotes' => [
                'create-success' => 'Cotización adjuntada con éxito.',
                'delete-success' => 'Cotización desadjuntada con éxito.',
            ],
        ],
    ],

    'quotes' => [
        'create-success'  => 'Cotización creada con éxito.',
        'update-success'  => 'Cotización actualizada con éxito.',
        'delete-success'  => 'Cotización eliminada con éxito.',
        'delete-failed'   => 'La eliminación de la cotización falló.',
        'saved-to-draft'  => 'Cotización guardada como borrador.',
    ],

    'mail' => [
        'create-success' => 'Correo electrónico creado con éxito.',
        'update-success' => 'Correo electrónico actualizado con éxito.',
        'delete-success' => 'Correo electrónico eliminado con éxito.',
        'delete-failed'  => 'La eliminación del correo electrónico falló.',
        'saved-to-draft' => 'Correo electrónico guardado como borrador.',

        'view' => [
            'tags' => [
                'create-success' => 'Etiqueta adjuntada con éxito.',
                'delete-success' => 'Etiqueta desadjuntada con éxito.',
            ],
        ],
    ],

    'activities' => [
        'create-success' => 'Actividad creada con éxito.',
        'update-success' => 'Actividad actualizada con éxito.',
        'delete-success' => 'Actividad eliminada con éxito.',
        'delete-failed'  => 'La eliminación de la actividad falló.',
    ],

    'contacts' => [
        'persons' => [
            'create-success'  => 'Persona creada con éxito.',
            'update-success'  => 'Persona actualizada con éxito.',
            'delete-success'  => 'Persona eliminada con éxito.',
            'delete-failed'   => 'La eliminación de la persona falló.',

            'view' => [
                'tags' => [
                    'create-success' => 'Etiqueta adjuntada con éxito.',
                    'delete-success' => 'Etiqueta desadjuntada con éxito.',
                ],
            ],
        ],

        'organizations' => [
            'create-success'  => 'Organización creada con éxito.',
            'update-success'  => 'Organización actualizada con éxito.',
            'delete-success'  => 'Organización eliminada con éxito.',
            'delete-failed'   => 'La eliminación de la organización falló.',
        ],
    ],

    'settings' => [
        'tags' => [
            'create-success' => 'Etiqueta creada con éxito.',
            'update-success' => 'Etiqueta actualizada con éxito.',
            'delete-success' => 'Etiqueta eliminada con éxito.',
            'delete-failed'  => 'La eliminación de la etiqueta falló.',
        ],

        'web-forms' => [
            'create-success'  => 'Formulario web creado con éxito.',
            'updated-success' => 'Formulario web actualizado con éxito.',
            'delete-success'  => 'Formulario web eliminado con éxito.',
            'delete-failed'   => 'La eliminación del formulario web falló.',
        ],

        'attributes' => [
            'create-success'    => 'Atributo creado exitosamente.',
            'update-success'    => 'Atributo actualizado exitosamente.',
            'destroy-success'   => 'Atributo eliminado exitosamente.',
            'delete-failed'     => 'Fallo al eliminar el atributo.',
            'user-define-error' => 'No se puede eliminar un atributo definido por el usuario.',
        ],

        'groups' => [
            'create-success'  => 'Grupo creado con éxito.',
            'update-success'  => 'Grupo actualizado con éxito.',
            'destroy-success' => 'Grupo eliminado con éxito.',
            'delete-failed'   => 'La eliminación del grupo falló.',
        ],

        'marketing' => [
            'events' => [
                'create-success'  => 'Evento de marketing creado con éxito.',
                'update-success'  => 'Evento de marketing actualizado con éxito.',
                'destroy-success' => 'Evento de marketing eliminado con éxito.',
                'delete-failed'   => 'Error al eliminar el evento de marketing.',
            ],

            'campaigns' => [
                'create-success'  => 'Campaña de marketing creada con éxito.',
                'update-success'  => 'Campaña de marketing actualizada con éxito.',
                'destroy-success' => 'Campaña de marketing eliminada con éxito.',
                'delete-failed'   => 'Error al eliminar la campaña de marketing.',
            ],
        ],

        'roles' => [
            'create-success' => 'Rol creado con éxito.',
            'update-success' => 'Rol actualizado con éxito.',
            'delete-success' => 'Rol eliminado con éxito.',
            'delete-failed'  => 'La eliminación del rol falló.',
        ],

        'users' => [
            'create-success'      => 'Usuario creado con éxito.',
            'updated-success'     => 'Usuario actualizado con éxito.',
            'delete-success'      => 'Usuario eliminado con éxito.',
            'delete-failed'       => 'La eliminación del usuario falló.',
            'last-delete-error'   => 'Se requiere al menos un usuario.',
            'mass-delete-success' => 'Usuarios seleccionados eliminados con éxito.',
            'mass-delete-failed'  => 'La eliminación de los usuarios seleccionados falló.',
            'mass-update-success' => 'Usuarios seleccionados actualizados con éxito.',
            'mass-update-failed'  => 'La actualización de los usuarios seleccionados falló.',
        ],

        'pipelines' => [
            'create-success'       => 'Pipeline creado con éxito.',
            'updated-success'      => 'Pipeline actualizado con éxito.',
            'delete-success'       => 'Pipeline eliminado con éxito.',
            'default-delete-error' => 'No se puede eliminar el pipeline predeterminado.',
        ],

        'sources' => [
            'create-success' => 'Fuente creada con éxito.',
            'update-success' => 'Fuente actualizada con éxito.',
            'delete-success' => 'Fuente eliminada con éxito.',
            'delete-failed'  => 'La eliminación de la fuente falló.',
        ],

        'types' => [
            'create-success' => 'Tipo creado con éxito.',
            'update-success' => 'Tipo actualizado con éxito.',
            'delete-success' => 'Tipo eliminado con éxito.',
            'delete-failed'  => 'La eliminación del tipo falló.',
        ],

        'email-templates' => [
            'create-success' => 'Plantilla de correo electrónico creada con éxito.',
            'update-success' => 'Plantilla de correo electrónico actualizada con éxito.',
            'delete-success' => 'Plantilla de correo electrónico eliminada con éxito.',
            'delete-failed'  => 'La eliminación de la plantilla de correo electrónico falló.',
        ],

        'workflows' => [
            'create-success' => 'Flujo de trabajo creado con éxito.',
            'update-success' => 'Flujo de trabajo actualizado con éxito.',
            'delete-success' => 'Flujo de trabajo eliminado con éxito.',
            'delete-failed'  => 'La eliminación del flujo de trabajo falló.',
        ],

        'warehouses' => [
            'create-success' => 'Almacén creado con éxito.',
            'update-success' => 'Almacén actualizado con éxito.',
            'delete-success' => 'Almacén eliminado con éxito.',
            'delete-failed'  => 'La eliminación del almacén falló.',

            'view' => [
                'locations' => [
                    'create-success' => 'Ubicación creada con éxito.',
                    'update-success' => 'Ubicación actualizada con éxito.',
                    'delete-success' => 'Ubicación eliminada con éxito.',
                    'delete-failed'  => 'La eliminación de la ubicación falló.',
                ],

                'tags' => [
                    'create-success' => 'Etiqueta adjuntada con éxito.',
                    'delete-success' => 'Etiqueta desadjuntada con éxito.',
                ],
            ],
        ],

        'webhooks' => [
            'create-success' => 'Webhook creado con éxito.',
            'update-success' => 'Webhook actualizado con éxito.',
            'delete-success' => 'Webhook eliminado con éxito.',
            'delete-failed'  => 'La eliminación del webhook falló.',
        ],

        'data-transfer' => [
            'imports' => [
                'create-success'    => 'Importación creada con éxito.',
                'delete-failed'     => 'La eliminación de la importación falló inesperadamente.',
                'delete-success'    => 'Importación eliminada con éxito.',
                'not-valid'         => 'La importación no es válida.',
                'nothing-to-import' => 'No hay recursos para importar.',
                'setup-queue-error' => 'Cambie su controlador de cola a "database" o "redis" para iniciar el proceso de importación.',
                'update-success'    => 'Importación actualizada con éxito.',
            ],
        ],
    ],

    'configuration' => [
        'save-success' => 'Configuración guardada con éxito.',
    ],
];
