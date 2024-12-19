<?php

return [
    'acl' => [
        'leads'           => 'Leads',
        'lead'            => 'Lead',
        'quotes'          => 'Devis',
        'mail'            => 'Mail',
        'inbox'           => 'Inbox',
        'draft'           => 'Draft',
        'outbox'          => 'Outbox',
        'sent'            => 'Envoyés',
        'trash'           => 'Corbeilles',
        'activities'      => 'Activités',
        'webhook'         => 'Webhook',
        'contacts'        => 'Contacts',
        'persons'         => 'Personnes',
        'organizations'   => 'Organisations',
        'products'        => 'Produits',
        'settings'        => 'Paramètres',
        'groups'          => 'Groupes',
        'roles'           => 'Rôles',
        'users'           => 'Utilisateurs',
        'user'            => 'Utilisateur',
        'automation'      => 'Automatisation',
        'attributes'      => 'Attributs',
        'pipelines'       => 'Pipelines',
        'sources'         => 'Sources',
        'types'           => 'Types',
        'email-templates' => 'Templates de mails',
        'workflows'       => 'Workflows',
        'other-settings'  => 'Autres paramètres',
        'tags'            => 'Tags',
        'configuration'   => 'Configuration',
        'create'          => 'Créer',
        'edit'            => 'Editer',
        'view'            => 'Voir',
        'print'           => 'Imprimer',
        'delete'          => 'Supprimer',
        'export'          => 'Exporter',
        'mass-delete'     => 'Supprimer en masse',
    ],

    'users' => [
        'activate-warning' => 'Votre compte n\'est pas encore activé, contactez votre administrateur.',
        'login-error'      => 'Votre identifiant ou votre mot de passe est erroné.',

        'login' => [
            'email'                => 'Adresse mail',
            'forget-password-link' => 'Mot de passe oublié ?',
            'password'             => 'Mot de passe',
            'submit-btn'           => 'Connexion',
            'title'                => 'Connexion',
        ],

        'forget-password' => [
            'create' => [
                'email'           => 'Votre mail',
                'email-not-exist' => 'Cet email n\'existe pas',
                'page-title'      => 'Mot de passe oublié',
                'reset-link-sent' => 'Mail de réinitialisation envoyé',
                'sign-in-link'    => 'Retour à la connexion ?',
                'submit-btn'      => 'Réinitialiser',
                'title'           => 'Réinitialiser son mot de passe',
            ],
        ],

        'reset-password' => [
            'back-link-title'  => 'Retour à la connexion ?',
            'confirm-password' => 'Confirmez le mot de passe',
            'email'            => 'Email enregistré',
            'password'         => 'Mot de passe',
            'submit-btn'       => 'Réinitialiser le mot de passe',
            'title'            => 'Réinitialiser le mot de passe',
        ],
    ],

    'account' => [
        'edit' => [
            'back-btn'          => 'Retour',
            'change-password'   => 'Changer de mot de passe',
            'confirm-password'  => 'Confirmez le mot de passe',
            'current-password'  => 'Mot de passe actuel',
            'email'             => 'Email',
            'general'           => 'General',
            'invalid-password'  => 'Le mot de passe renseigné est incorrect.',
            'name'              => 'Nom',
            'password'          => 'Mot de passe',
            'profile-image'     => 'Image de profil',
            'save-btn'          => 'Enregistrer le compte',
            'title'             => 'Mon compte',
            'update-success'    => 'Compte mis à jour avec succès',
            'upload-image-info' => 'Téléchargez une image de profil (110px X 110px) au format PNG ou JPG',
        ],
    ],

    'components' => [
        'activities' => [
            'actions' => [
                'mail' => [
                    'btn'          => 'Mail',
                    'title'        => 'Ecrire un mail',
                    'to'           => 'A',
                    'enter-emails' => 'Appuyez sur entrée ajouter un email',
                    'cc'           => 'CC',
                    'bcc'          => 'BCC',
                    'subject'      => 'Sujet',
                    'send-btn'     => 'Envoyer',
                    'message'      => 'Message',
                ],

                'file' => [
                    'btn'           => 'Fichier',
                    'title'         => 'Ajouter un fichier',
                    'title-control' => 'Titre',
                    'name'          => 'Nom',
                    'description'   => 'Description',
                    'file'          => 'Fichier',
                    'save-btn'      => 'Enregistrer le fichier',
                ],

                'note' => [
                    'btn'      => 'Note',
                    'title'    => 'Ajouter une note',
                    'comment'  => 'Commenter',
                    'save-btn' => 'Enregistrer la note',
                ],

                'activity' => [
                    'btn'           => 'Activité',
                    'title'         => 'Ajouter une activité',
                    'title-control' => 'Titre',
                    'description'   => 'Description',
                    'schedule-from' => 'Planifié de',
                    'schedule-to'   => 'Jusqu\'a',
                    'location'      => 'Localisation',
                    'call'          => 'Appel',
                    'meeting'       => 'Réunion',
                    'lunch'         => 'Déjeuner',
                    'save-btn'      => 'Enregistrer l\'activité',

                    'participants' => [
                        'title'       => 'Participants',
                        'placeholder' => 'Tapez pour rechercher les participants',
                        'users'       => 'Utilisateurs',
                        'persons'     => 'Personnes',
                        'no-results'  => 'Aucun résultat...',
                    ],
                ],
            ],

            'index' => [
                'from'         => 'De',
                'to'           => 'A',
                'cc'           => 'Cc',
                'bcc'          => 'Bcc',
                'all'          => 'Tous',
                'planned'      => 'Planifié',
                'calls'        => 'Appels',
                'meetings'     => 'Réunions',
                'lunches'      => 'Déjeuners',
                'files'        => 'Fichiers',
                'quotes'       => 'Devis',
                'notes'        => 'Notes',
                'emails'       => 'Emails',
                'change-log'   => 'Modifications',
                'by-user'      => 'Par :user',
                'scheduled-on' => 'Planifié le',
                'location'     => 'Localisation',
                'participants' => 'Participants',
                'mark-as-done' => 'Marquer comme fait',
                'delete'       => 'Supprimer',
                'edit'         => 'Editer',
                'view'         => 'Voir',
                'unlink'       => 'Supprimer le lien',
                'empty'        => 'Non renseigné',

                'empty-placeholders' => [
                    'all' => [
                        'title'       => 'Aucune activité trouvée',
                        'description' => 'Aucune activité trouvée, vous pouvez en ajouter en cliquant sur le bouton dans le panneau de gauche.',
                    ],

                    'planned' => [
                        'title'       => 'Aucune activité planifiée trouvée',
                        'description' => 'Aucune activité planifiée trouvée, vous pouvez en ajouter en cliquant sur le bouton dans le panneau de gauche.',
                    ],

                    'notes' => [
                        'title'       => 'Aucune note trouvée',
                        'description' => 'Aucune note trouvée, vous pouvez en ajouter en cliquant sur le bouton dans le panneau de gauche.',
                    ],

                    'calls' => [
                        'title'       => 'Aucun appel trouvé',
                        'description' => 'Aucun appel trouvée, vous pouvez en ajouter en cliquant sur le bouton dans le panneau de gauche.',
                    ],

                    'meetings' => [
                        'title'       => 'Aucune réunion trouvée',
                        'description' => 'Aucune réunion trouvée, vous pouvez en ajouter en cliquant sur le bouton dans le panneau de gauche.',
                    ],

                    'lunches' => [
                        'title'       => 'Aucun déjeuner trouvé',
                        'description' => 'Aucun déjeuner trouvé, vous pouvez en ajouter en cliquant sur le bouton dans le panneau de gauche.',
                    ],

                    'files' => [
                        'title'       => 'Aucun fichier trouvé',
                        'description' => 'Aucune activité planifiée trouvée, vous pouvez en ajouter en cliquant sur le bouton dans le panneau de gauche.',
                    ],

                    'emails' => [
                        'title'       => 'Aucun email trouvé',
                        'description' => 'Aucune activité planifiée trouvée, vous pouvez en ajouter en cliquant sur le bouton dans le panneau de gauche.',
                    ],

                    'system' => [
                        'title'       => 'Aucune modification trouvée',
                        'description' => 'Aucune modification trouvée.',
                    ],
                ],
            ],
        ],

        'media' => [
            'images' => [
                'add-image-btn'     => 'Ajouter une image',
                'ai-add-image-btn'  => 'Magic AI',
                'allowed-types'     => 'png, jpeg, jpg',
                'not-allowed-error' => 'Seuls les formats (.jpeg, .jpg, .png, ..) sont acceptés.',
                //TODO
                'placeholders' => [
                    'front'     => 'Front',
                    'next'      => 'Next',
                    'size'      => 'Size',
                    'use-cases' => 'Use Cases',
                    'zoom'      => 'Zoom',
                ],
            ],

            'videos' => [
                'add-video-btn'     => 'Ajouter une video',
                'allowed-types'     => 'mp4, webm, mkv',
                'not-allowed-error' => 'Seuls les formats (.mp4, .mov, .ogg ..) sont acceptés..',
            ],
        ],

        'datagrid' => [
            'index' => [
                'no-records-selected'              => 'Aucun enregistrement sélectionné.',
                'must-select-a-mass-action-option' => 'Vous devez sélectionner une option d\'une action de masse.',
                'must-select-a-mass-action'        => 'Vous devez sélectionner une action de masse.',
            ],

            'toolbar' => [
                'length-of' => ':length de',
                'of'        => 'de',
                'per-page'  => 'Par page',
                'results'   => ':total Resultats',
                'delete'    => 'Supprimer',
                'selected'  => ':total items Selectionnés',

                'mass-actions' => [
                    'submit'        => 'Envoyer',
                    'select-option' => 'Options',
                    'select-action' => 'Actions',
                ],

                'filter' => [
                    'apply-filters-btn' => 'Appliquer les filtres',
                    'back-btn'          => 'Retour',
                    'create-new-filter' => 'Créer un nouveau filtre',
                    'custom-filters'    => 'Filtre personnalisé',
                    'delete-error'      => 'Quelque chose s\'est mal passé durant la suppression du filtre, essayez à nouveau.',
                    'delete-success'    => 'Le filtre a été supprimé avec succès.',
                    'empty-description' => 'Il n\'y a pas de filtre sélectionné pour l\'enregistrement. Veuillez en sélectionner.',
                    'empty-title'       => 'Ajouter des filtres à enregistrer',
                    'name'              => 'Nom',
                    'quick-filters'     => 'Filtre rapide',
                    'save-btn'          => 'Enregistrer',
                    'save-filter'       => 'Enregistrer le filtre',
                    'saved-success'     => 'Le filtre a été enregistré avec succès.',
                    'selected-filters'  => 'Filtres sélectionnés',
                    'title'             => 'Filtre',
                    'update'            => 'Mise à jour',
                    'update-filter'     => 'Mise à jour du filtre',
                    'updated-success'   => 'Le filtre a été mis à jour avec succès.',
                ],

                'search' => [
                    'title' => 'Recherche',
                ],
            ],

            'filters' => [
                'select' => 'Selection',
                'title'  => 'Filtres',

                'dropdown' => [
                    'searchable' => [
                        'at-least-two-chars' => 'Tapez au moins 2 caractères...',
                        'no-results'         => 'Aucun résultat...',
                    ],
                ],

                'custom-filters' => [
                    'clear-all' => 'Tout effacer',
                    'title'     => 'Filtres personnalisés',
                ],

                'boolean-options' => [
                    'false' => 'Faux',
                    'true'  => 'Vrai',
                ],

                'date-options' => [
                    'last-month'        => 'Dernier mois',
                    'last-six-months'   => 'Derniers 6 Mois',
                    'last-three-months' => 'Derniers 3 Mois',
                    'this-month'        => 'Ce mois',
                    'this-week'         => 'Cette semaine',
                    'this-year'         => 'Cette année',
                    'today'             => 'Aujourd\'hui',
                    'yesterday'         => 'Hier',
                ],
            ],

            'table' => [
                'actions'              => 'Actions',
                'no-records-available' => 'Aucun enregistrement disponible.',
            ],
        ],

        'modal' => [
            'confirm' => [
                'agree-btn'    => 'Accepter',
                'disagree-btn' => 'Refuser',
                'message'      => 'Voulez vous vraiment réaliser cette action ?',
                'title'        => 'Etes vous sur ?',
            ],
        ],

        'tags' => [
            'index' => [
                'title'          => 'Tags',
                'added-tags'     => 'Tags ajoutés',
                'save-btn'       => 'Enregistrer le tag',
                'placeholder'    => 'Tapez pour rechercher des tags',
                'add-tag'        => 'Ajouter \":term\"...',
                'aquarelle-red'  => 'Aquarelle Red',
                'crushed-cashew' => 'Crushed Cashew',
                'beeswax'        => 'Beeswax',
                'lemon-chiffon'  => 'Lemon Chiffon',
                'snow-flurry'    => 'Snow Flurry',
                'honeydew'       => 'Honeydew',
            ],
        ],

        'layouts' => [
            'header' => [
                'mega-search' => [
                    'title'   => 'Rechercher',

                    'tabs' => [
                        'leads'    => 'Leads',
                        'quotes'   => 'Devis',
                        'persons'  => 'Personnes',
                        'products' => 'Produits',
                    ],

                    'explore-all-products'          => 'Rechercher parmi tous les produits',
                    'explore-all-leads'             => 'Rechercher parmi tous les leads',
                    'explore-all-contacts'          => 'Rechercher parmi tous les contacts',
                    'explore-all-quotes'            => 'Rechercher parmi tous les devis',
                    'explore-all-matching-products' => 'Rechercher parmi tous les produits correspondant à ":query" (:count)',
                    'explore-all-matching-leads'    => 'Rechercher parmi tous les leads correspondant à ":query" (:count)',
                    'explore-all-matching-contacts' => 'Rechercher parmi tous les contacts correspondant à ":query" (:count)',
                    'explore-all-matching-quotes'   => 'Rechercher parmi tous les devis correspondant à ":query" (:count)',
                ],
            ],
        ],

        'attributes' => [
            'edit'   => [
                'delete' => 'Supprimer',
            ],

            'lookup' => [
                'click-to-add'    => 'Cliquez pour ajouter',
                'search'          => 'Recherche',
                'no-result-found' => 'Aucun résultat',
                'search'          => 'Recherche...',
            ],
        ],

        'lookup' => [
            'click-to-add' => 'Cliquez pour ajouter',
            'no-results'   => 'Recherche',
            'add-as-new'   => 'Ajouter en tant que nouveau',
            'search'       => 'Recherche...',
        ],

        'flash-group' => [
            'success' => 'Succes',
            'error'   => 'Erreur',
            'warning' => 'Avertissement',
            'info'    => 'Info',
        ],
    ],

    'quotes' => [
        'index' => [
            'title'          => 'Devis',
            'create-btn'     => 'Créer un devis',
            'create-success' => 'Devis créé avec succès.',
            'update-success' => 'Devis mis à jour avec succès.',
            'delete-success' => 'Devis supprimé avec succès.',
            'delete-failed'  => 'Le devis ne peut pas être supprimé.',

            'datagrid' => [
                'subject'        => 'Sujet',
                'sales-person'   => 'Commercial',
                'expired-at'     => 'Expiré le',
                'created-at'     => 'Créé le',
                'expired-quotes' => 'Devis expirés',
                'person'         => 'Personne',
                'subtotal'       => 'Sous-total',
                'discount'       => 'Remise',
                'tax'            => 'Taxe',
                'adjustment'     => 'Ajustement',
                'grand-total'    => 'Total général',
                'edit'           => 'Modifier',
                'delete'         => 'Supprimer',
                'print'          => 'Imprimer',
            ],

            'pdf' => [
                'title'            => 'Devis',
                'grand-total'      => 'Total Général',
                'adjustment'       => 'Ajustement',
                'discount'         => 'Remise',
                'tax'              => 'Taxe',
                'sub-total'        => 'Sous-total',
                'amount'           => 'Montant',
                'quantity'         => 'Quantité',
                'price'            => 'Prix',
                'product-name'     => 'Nom du Produit',
                'sku'              => 'SKU',
                'shipping-address' => 'Adresse de Livraison',
                'billing-address'  => 'Adresse de Facturation',
                'expired-at'       => 'Expiré Le',
                'sales-person'     => 'Commercial',
                'date'             => 'Date',
                'quote-id'         => 'ID du Devis',
            ],
        ],

        'create' => [
            'title'             => 'Créer un devis',
            'save-btn'          => 'Enregistrer le devis',
            'quote-info'        => 'Informations sur le devis',
            'quote-info-info'   => 'Renseignez les informations de base du devis.',
            'address-info'      => 'Informations sur l’adresse',
            'address-info-info' => 'Informations sur l’adresse liée au devis.',
            'quote-items'       => 'Articles du devis',
            'search-products'   => 'Rechercher des produits',
            'link-to-lead'      => 'Lier à un lead',
            'quote-item-info'   => 'Ajouter une demande de produit pour ce devis.',
            'quote-name'        => 'Nom du devis',
            'quantity'          => 'Quantité',
            'price'             => 'Prix',

            'discount'          => 'Remise',
            'tax'               => 'Taxe',
            'total'             => 'Total',
            'amount'            => 'Montant',
            'add-item'          => '+ Ajouter un article',
            'sub-total'         => 'Sous-total (:symbol)',
            'total-discount'    => 'Remise (:symbol)',
            'total-tax'         => 'Taxe (:symbol)',
            'total-adjustment'  => 'Ajustement (:symbol)',
            'grand-total'       => 'Total général (:symbol)',
            'discount-amount'   => 'Montant de la remise',
            'tax-amount'        => 'Montant de la taxe',
            'adjustment-amount' => 'Montant de l’ajustement',
            'product-name'      => 'Nom du produit',
            'action'            => 'Action',
        ],

        'edit' => [
            'title'             => 'Modifier un devis',
            'save-btn'          => 'Enregistrer le devis',
            'quote-info'        => 'Informations sur le devis',
            'quote-info-info'   => 'Renseignez les informations de base du devis.',
            'address-info'      => 'Informations sur l’adresse',
            'address-info-info' => 'Informations sur l’adresse liée au devis.',
            'quote-items'       => 'Articles du devis',
            'link-to-lead'      => 'Lier à un lead',
            'quote-item-info'   => 'Ajouter une demande de produit pour ce devis.',
            'quote-name'        => 'Nom du devis',
            'quantity'          => 'Quantité',
            'price'             => 'Prix',
            'search-products'   => 'Rechercher des produits',
            'discount'          => 'Remise',
            'tax'               => 'Taxe',
            'total'             => 'Total',
            'amount'            => 'Montant',
            'add-item'          => '+ Ajouter un article',
            'sub-total'         => 'Sous-total (:symbol)',
            'total-discount'    => 'Remise (:symbol)',
            'total-tax'         => 'Taxe (:symbol)',
            'total-adjustment'  => 'Ajustement (:symbol)',
            'grand-total'       => 'Total général (:symbol)',
            'discount-amount'   => 'Montant de la remise',
            'tax-amount'        => 'Montant de la taxe',
            'adjustment-amount' => 'Montant de l’ajustement',
            'product-name'      => 'Nom du produit',
            'action'            => 'Action',
        ],
    ],

    'contacts' => [
        'persons' => [
            'index' => [
                'title'          => 'Personnes',
                'create-btn'     => 'Créer une personne',
                'create-success' => 'Personne créée avec succès.',
                'update-success' => 'Personne mise à jour avec succès.',
                'delete-success' => 'Personne supprimée avec succès.',
                'delete-failed'  => 'La personne ne peut pas être supprimée.',

                'datagrid' => [
                    'contact-numbers'   => 'Numéros de contact',
                    'delete'            => 'Supprimer',
                    'edit'              => 'Modifier',
                    'emails'            => 'E-mails',
                    'id'                => 'ID',
                    'view'              => 'Voir',
                    'name'              => 'Nom',
                    'organization-name' => 'Nom de l’organisation',
                ],
            ],

            'view' => [
                'title'              => ':name',
                'about-person'       => 'À propos de la personne',
                'about-organization' => 'À propos de l’organisation',

                'activities' => [
                    'index' => [
                        'all'          => 'Tout',
                        'calls'        => 'Appels',
                        'meetings'     => 'Réunions',
                        'lunches'      => 'Déjeuners',
                        'files'        => 'Fichiers',
                        'quotes'       => 'Devis',
                        'notes'        => 'Notes',
                        'emails'       => 'E-mails',
                        'by-user'      => 'Par :user',
                        'scheduled-on' => 'Planifié le',
                        'location'     => 'Lieu',
                        'participants' => 'Participants',
                        'mark-as-done' => 'Marquer comme terminé',
                        'delete'       => 'Supprimer',
                        'edit'         => 'Modifier',
                    ],

                    'actions' => [
                        'mail' => [
                            'btn'      => 'E-mail',
                            'title'    => 'Rédiger un e-mail',
                            'to'       => 'À',
                            'cc'       => 'CC',
                            'bcc'      => 'CCI',
                            'subject'  => 'Objet',
                            'send-btn' => 'Envoyer',
                            'message'  => 'Message',
                        ],

                        'file' => [
                            'btn'           => 'Fichier',
                            'title'         => 'Ajouter un fichier',
                            'title-control' => 'Titre',
                            'name'          => 'Nom du fichier',
                            'description'   => 'Description',
                            'file'          => 'Fichier',
                            'save-btn'      => 'Enregistrer le fichier',
                        ],

                        'note' => [
                            'btn'      => 'Note',
                            'title'    => 'Ajouter une note',
                            'comment'  => 'Commentaire',
                            'save-btn' => 'Enregistrer la note',
                        ],

                        'activity' => [
                            'btn'           => 'Activité',
                            'title'         => 'Ajouter une activité',
                            'title-control' => 'Titre',
                            'description'   => 'Description',
                            'schedule-from' => 'Planifié de',
                            'schedule-to'   => 'Planifié à',
                            'location'      => 'Lieu',
                            'call'          => 'Appel',
                            'meeting'       => 'Réunion',
                            'lunch'         => 'Déjeuner',
                            'save-btn'      => 'Enregistrer l’activité',
                        ],
                    ],
                ],
            ],

            'create' => [
                'title'    => 'Créer une personne',
                'save-btn' => 'Enregistrer la personne',
            ],

            'edit' => [
                'title'    => 'Modifier une personne',
                'save-btn' => 'Enregistrer la personne',
            ],
        ],

        'organizations' => [
            'index' => [
                'title'          => 'Organisations',
                'create-btn'     => 'Créer une organisation',
                'create-success' => 'Organisation créée avec succès.',
                'update-success' => 'Organisation mise à jour avec succès.',
                'delete-success' => 'Organisation supprimée avec succès.',
                'delete-failed'  => 'L’organisation ne peut pas être supprimée.',

                'datagrid' => [
                    'delete'        => 'Supprimer',
                    'edit'          => 'Modifier',
                    'id'            => 'ID',
                    'name'          => 'Nom',
                    'persons-count' => 'Nombre de personnes',
                ],
            ],

            'create' => [
                'title'    => 'Créer une organisation',
                'save-btn' => 'Enregistrer l’organisation',
            ],

            'edit' => [
                'title'    => 'Modifier une organisation',
                'save-btn' => 'Enregistrer l’organisation',
            ],
        ],
    ],

    'products' => [
        'index' => [
            'title'          => 'Produits',
            'create-btn'     => 'Créer un produit',
            'create-success' => 'Produit créé avec succès.',
            'update-success' => 'Produit mis à jour avec succès.',
            'delete-success' => 'Produit supprimé avec succès.',
            'delete-failed'  => 'Le produit ne peut pas être supprimé.',

            'datagrid'   => [
                'allocated' => 'Alloué',
                'delete'    => 'Supprimer',
                'edit'      => 'Modifier',
                'id'        => 'ID',
                'in-stock'  => 'En stock',
                'name'      => 'Nom',
                'on-hand'   => 'Disponible',
                'price'     => 'Prix',
                'sku'       => 'SKU',
                'view'      => 'Voir',
            ],
        ],

        'create' => [
            'save-btn'  => 'Enregistrer les produits',
            'title'     => 'Créer des produits',
            'general'   => 'Général',
            'price'     => 'Prix',
        ],

        'edit' => [
            'title'     => 'Modifier des produits',
            'save-btn'  => 'Enregistrer les produits',
            'general'   => 'Général',
            'price'     => 'Prix',
        ],

        'view' => [
            'sku'         => 'SKU',
            'all'         => 'Tout',
            'notes'       => 'Notes',
            'files'       => 'Fichiers',
            'inventories' => 'Inventaire',
            'change-logs' => 'Journaux de modifications',

            'attributes' => [
                'about-product' => 'À propos du produit',
            ],

            'inventory' => [
                'source'     => 'Source',
                'in-stock'   => 'En stock',
                'allocated'  => 'Alloué',
                'on-hand'    => 'Disponible',
                'actions'    => 'Actions',
                'assign'     => 'Attribuer',
                'add-source' => 'Ajouter une source',
                'location'   => 'Emplacement',
                'add-more'   => 'Ajouter plus',
                'save'       => 'Enregistrer',
            ],
        ],
    ],

    'settings' => [
        'title' => 'Paramètres',

        'groups' => [
            'index' => [
                'create-btn'        => 'Créer un groupe',
                'title'             => 'Groupes',
                'create-success'    => 'Groupe créé avec succès.',
                'update-success'    => 'Groupe mis à jour avec succès.',
                'destroy-success'   => 'Groupe supprimé avec succès.',
                'delete-failed'     => 'Le groupe ne peut pas être supprimé.',

                'datagrid'   => [
                    'delete'      => 'Supprimer',
                    'description' => 'Description',
                    'edit'        => 'Modifier',
                    'id'          => 'ID',
                    'name'        => 'Nom',
                ],

                'edit' => [
                    'title' => 'Modifier le groupe',
                ],

                'create' => [
                    'name'        => 'Nom',
                    'title'       => 'Créer un groupe',
                    'description' => 'Description',
                    'save-btn'    => 'Enregistrer le groupe',
                ],
            ],
        ],

        'roles' => [
            'index' => [
                'being-used'                => 'Le rôle ne peut pas être supprimé, car il est utilisé par l’utilisateur administrateur.',
                'create-btn'                => 'Créer des rôles',
                'create-success'            => 'Rôle créé avec succès.',
                'current-role-delete-error' => 'Impossible de supprimer le rôle attribué à l’utilisateur actuel.',
                'delete-failed'             => 'Le rôle ne peut pas être supprimé.',
                'delete-success'            => 'Rôle supprimé avec succès.',
                'last-delete-error'         => 'Au moins un rôle est requis.',
                'settings'                  => 'Paramètres',
                'title'                     => 'Rôles',
                'update-success'            => 'Rôle mis à jour avec succès.',
                'user-define-error'         => 'Impossible de supprimer le rôle système.',

                'datagrid'   => [
                    'all'             => 'Tout',
                    'custom'          => 'Personnalisé',
                    'delete'          => 'Supprimer',
                    'description'     => 'Description',
                    'edit'            => 'Modifier',
                    'id'              => 'ID',
                    'name'            => 'Nom',
                    'permission-type' => 'Type de permission',
                ],
            ],

            'create' => [
                'access-control' => 'Contrôle d\'accès',
                'all'            => 'Tout',
                'back-btn'       => 'Retour',
                'custom'         => 'Personnalisé',
                'description'    => 'Description',
                'general'        => 'Général',
                'name'           => 'Nom',
                'permissions'    => 'Permissions',
                'save-btn'       => 'Enregistrer le rôle',
                'title'          => 'Créer un rôle',
            ],

            'edit' => [
                'access-control' => 'Contrôle d\'accès',
                'all'            => 'Tout',
                'back-btn'       => 'Retour',
                'custom'         => 'Personnalisé',
                'description'    => 'Description',
                'general'        => 'Général',
                'name'           => 'Nom',
                'permissions'    => 'Permissions',
                'save-btn'       => 'Enregistrer le rôle',
                'title'          => 'Modifier le rôle',
            ],
        ],

        'types' => [
            'index' => [
                'create-btn'     => 'Créer un type',
                'create-success' => 'Type créé avec succès.',
                'delete-failed'  => 'Le type ne peut pas être supprimé.',
                'delete-success' => 'Type supprimé avec succès.',
                'title'          => 'Types',
                'update-success' => 'Type mis à jour avec succès.',

                'datagrid' => [
                    'delete'      => 'Supprimer',
                    'description' => 'Description',
                    'edit'        => 'Modifier',
                    'id'          => 'ID',
                    'name'        => 'Nom',
                ],

                'create' => [
                    'name'     => 'Nom',
                    'save-btn' => 'Enregistrer le type',
                    'title'    => 'Créer un type',
                ],

                'edit' => [
                    'title' => 'Modifier le type',
                ],
            ],
        ],

        'sources' => [
            'index' => [
                'create-btn'     => 'Créer une Source',
                'create-success' => 'Source créée avec succès.',
                'delete-failed'  => 'La source ne peut pas être supprimée.',
                'delete-success' => 'Source supprimée avec succès.',
                'title'          => 'Sources',
                'update-success' => 'Source mise à jour avec succès.',

                'datagrid' => [
                    'delete' => 'Supprimer',
                    'edit'   => 'Modifier',
                    'id'     => 'ID',
                    'name'   => 'Nom',
                ],

                'create' => [
                    'name'     => 'Nom',
                    'save-btn' => 'Enregistrer la Source',
                    'title'    => 'Créer une Source',
                ],

                'edit' => [
                    'title' => 'Modifier la Source',
                ],
            ],
        ],

        'workflows' => [
            'index' => [
                'title'          => 'Flux de travail',
                'create-btn'     => 'Créer un flux de travail',
                'create-success' => 'Flux de travail créé avec succès.',
                'update-success' => 'Flux de travail mis à jour avec succès.',
                'delete-success' => 'Flux de travail supprimé avec succès.',
                'delete-failed'  => 'Le flux de travail ne peut pas être supprimé.',
                'datagrid'       => [
                    'delete'      => 'Supprimer',
                    'description' => 'Description',
                    'edit'        => 'Modifier',
                    'id'          => 'ID',
                    'name'        => 'Nom',
                ],
            ],

            'helpers' => [
                'update-related-leads'       => 'Mettre à jour les prospects associés',
                'send-email-to-sales-owner'  => 'Envoyer un email au commercial',
                'send-email-to-participants' => 'Envoyer un email aux participants',
                'add-webhook'                => 'Ajouter un webhook',
                'update-lead'                => 'Mettre à jour le prospect',
                'update-person'              => 'Mettre à jour la personne',
                'send-email-to-person'       => 'Envoyer un email à la personne',
                'add-tag'                    => 'Ajouter un tag',
                'add-note-as-activity'       => 'Ajouter une note en tant qu\'activité',
                'update-quote'               => 'Mettre à jour le devis',
            ],

            'create' => [
                'title'                  => 'Créer un flux de travail',
                'event'                  => 'Événement',
                'back-btn'               => 'Retour',
                'save-btn'               => 'Enregistrer le flux de travail',
                'name'                   => 'Nom',
                'basic-details'          => 'Détails de base',
                'description'            => 'Description',
                'actions'                => 'Actions',
                'basic-details-info'     => 'Mettez les informations de base du flux de travail.',
                'event-info'             => 'Un événement déclenche, vérifie, conditions et effectue des actions prédéfinies.',
                'conditions'             => 'Conditions',
                'conditions-info'        => 'Les conditions sont des règles qui vérifient des scénarios, déclenchées lors de situations spécifiques.',
                'actions-info'           => 'Une action réduit non seulement la charge de travail, mais facilite également l\'automatisation CRM.',
                'value'                  => 'Valeur',
                'condition-type'         => 'Type de condition',
                'all-condition-are-true' => 'Toutes les conditions sont vraies',
                'any-condition-are-true' => 'Toutes les conditions sont vraies',
                'add-condition'          => 'Ajouter une condition',
                'add-action'             => 'Ajouter une action',
                'yes'                    => 'Oui',
                'no'                     => 'Non',
                'email'                  => 'Email',
                'is-equal-to'            => 'Est égal à',
                'is-not-equal-to'        => 'N\'est pas égal à',
                'equals-or-greater-than' => 'Est égal ou supérieur à',
                'equals-or-less-than'    => 'Est égal ou inférieur à',
                'greater-than'           => 'Supérieur à',
                'less-than'              => 'Inférieur à',
                'type'                   => 'Type',
                'contain'                => 'Contient',
                'contains'               => 'Contient',
                'does-not-contain'       => 'Ne contient pas',
            ],

            'edit' => [
                'title'                  => 'Modifier le flux de travail',
                'event'                  => 'Événement',
                'back-btn'               => 'Retour',
                'save-btn'               => 'Enregistrer le flux de travail',
                'name'                   => 'Nom',
                'basic-details'          => 'Détails de base',
                'description'            => 'Description',
                'actions'                => 'Actions',
                'type'                   => 'Type',
                'basic-details-info'     => 'Mettez les informations de base du flux de travail.',
                'event-info'             => 'Un événement déclenche, vérifie, conditions et effectue des actions prédéfinies.',
                'conditions'             => 'Conditions',
                'conditions-info'        => 'Les conditions sont des règles qui vérifient des scénarios, déclenchées lors de situations spécifiques.',
                'actions-info'           => 'Une action réduit non seulement la charge de travail, mais facilite également l\'automatisation CRM.',
                'value'                  => 'Valeur',
                'condition-type'         => 'Type de condition',
                'all-condition-are-true' => 'Toutes les conditions sont vraies',
                'any-condition-are-true' => 'Toutes les conditions sont vraies',
                'add-condition'          => 'Ajouter une condition',
                'add-action'             => 'Ajouter une action',
                'yes'                    => 'Oui',
                'no'                     => 'Non',
                'email'                  => 'Email',
                'is-equal-to'            => 'Est égal à',
                'is-not-equal-to'        => 'N\'est pas égal à',
                'equals-or-greater-than' => 'Est égal ou supérieur à',
                'equals-or-less-than'    => 'Est égal ou inférieur à',
                'greater-than'           => 'Supérieur à',
                'less-than'              => 'Inférieur à',
                'contain'                => 'Contient',
                'contains'               => 'Contient',
                'does-not-contain'       => 'Ne contient pas',
            ],
        ],

        'webforms' => [
            'index' => [
                'title'          => 'Formulaires Web',
                'create-btn'     => 'Créer un formulaire Web',
                'create-success' => 'Formulaire Web créé avec succès.',
                'update-success' => 'Formulaire Web mis à jour avec succès.',
                'delete-success' => 'Formulaire Web supprimé avec succès.',
                'delete-failed'  => 'Le formulaire Web ne peut pas être supprimé.',

                'datagrid'       => [
                    'id'     => 'ID',
                    'title'  => 'Titre',
                    'edit'   => 'Modifier',
                    'delete' => 'Supprimer',
                ],
            ],

            'create' => [
                'title'                    => 'Créer un Webform',
                'add-attribute-btn'        => 'Ajouter un bouton d\'attribut',
                'attribute-label-color'    => 'Couleur de l\'étiquette de l\'attribut',
                'attributes'               => 'Attributs',
                'attributes-info'          => 'Ajoutez des attributs personnalisés au formulaire.',
                'background-color'         => 'Couleur de fond',
                'create-lead'              => 'Créer un prospect',
                'customize-webform'        => 'Personnaliser le Webform',
                'customize-webform-info'   => 'Personnalisez votre formulaire web avec les couleurs des éléments de votre choix.',
                'description'              => 'Description',
                'display-custom-message'   => 'Afficher un message personnalisé',
                'form-background-color'    => 'Couleur de fond du formulaire',
                'form-submit-btn-color'    => 'Couleur du bouton de soumission du formulaire',
                'form-submit-button-color' => 'Couleur du bouton de soumission du formulaire',
                'form-title-color'         => 'Couleur du titre du formulaire',
                'general'                  => 'Général',
                'leads'                    => 'Prospects',
                'person'                   => 'Personne',
                'save-btn'                 => 'Sauvegarder le Webform',
                'submit-button-label'      => 'Libellé du bouton de soumission',
                'submit-success-action'    => 'Action après succès de la soumission',
                'redirect-to-url'          => 'Rediriger vers l\'URL',
                'choose-value'             => 'Choisir une valeur',
                'select-file'              => 'Sélectionner un fichier',
                'select-image'             => 'Sélectionner une image',
                'enter-value'              => 'Entrer une valeur',
            ],

            'edit' => [
                'title'                     => 'Modifier le Webform',
                'add-attribute-btn'         => 'Ajouter un bouton d\'attribut',
                'attribute-label-color'     => 'Couleur de l\'étiquette de l\'attribut',
                'attributes'                => 'Attributs',
                'attributes-info'           => 'Ajoutez des attributs personnalisés au formulaire.',
                'background-color'          => 'Couleur de fond',
                'code-snippet'              => 'Extrait de code',
                'copied'                    => 'Copié',
                'copy'                      => 'Copier',
                'create-lead'               => 'Créer un prospect',
                'customize-webform'         => 'Personnaliser le Webform',
                'customize-webform-info'    => 'Personnalisez votre formulaire web avec les couleurs des éléments de votre choix.',
                'description'               => 'Description',
                'display-custom-message'    => 'Afficher un message personnalisé',
                'embed'                     => 'Intégrer',
                'form-background-color'     => 'Couleur de fond du formulaire',
                'form-submit-btn-color'     => 'Couleur du bouton de soumission du formulaire',
                'form-submit-button-color'  => 'Couleur du bouton de soumission du formulaire',
                'form-title-color'          => 'Couleur du titre du formulaire',
                'general'                   => 'Général',
                'preview'                   => 'Aperçu',
                'person'                    => 'Personne',
                'public-url'                => 'URL publique',
                'redirect-to-url'           => 'Rediriger vers l\'URL',
                'save-btn'                  => 'Sauvegarder le Webform',
                'submit-button-label'       => 'Libellé du bouton de soumission',
                'submit-success-action'     => 'Action après succès de la soumission',
                'choose-value'              => 'Choisir une valeur',
                'select-file'               => 'Sélectionner un fichier',
                'select-image'              => 'Sélectionner une image',
                'enter-value'               => 'Entrer une valeur',
            ],
        ],

        'email-template' => [
            'index' => [
                'create-btn'     => 'Créer un modèle d\'email',
                'title'          => 'Modèles d\'email',
                'create-success' => 'Modèle d\'email créé avec succès.',
                'update-success' => 'Modèle d\'email mis à jour avec succès.',
                'delete-success' => 'Modèle d\'email supprimé avec succès.',
                'delete-failed'  => 'Le modèle d\'email ne peut pas être supprimé.',

                'datagrid'   => [
                    'delete'       => 'Supprimer',
                    'edit'         => 'Modifier',
                    'id'           => 'ID',
                    'name'         => 'Nom',
                    'subject'      => 'Sujet',
                ],
            ],

            'create'     => [
                'title'                => 'Créer un Modèle d\'E-mail',
                'save-btn'             => 'Enregistrer le Modèle d\'E-mail',
                'email-template'       => 'Modèle d\'E-mail',
                'subject'              => 'Sujet',
                'content'              => 'Contenu',
                'subject-placeholders' => 'Espaces Réservés pour le Sujet',
                'general'              => 'Général',
                'name'                 => 'Nom',
            ],

            'edit' => [
                'title'                => 'Modifier le Modèle d\'E-mail',
                'save-btn'             => 'Enregistrer le Modèle d\'E-mail',
                'email-template'       => 'Modèle d\'E-mail',
                'subject'              => 'Sujet',
                'content'              => 'Contenu',
                'subject-placeholders' => 'Espaces Réservés pour le Sujet',
                'general'              => 'Général',
                'name'                 => 'Nom',
            ],
        ],

        'tags' => [
            'index' => [
                'create-btn'     => 'Créer un tag',
                'title'          => 'Tags',
                'create-success' => 'Tag créé avec succès.',
                'update-success' => 'Tag mis à jour avec succès.',
                'delete-success' => 'Tag supprimé avec succès.',
                'delete-failed'  => 'Le tag ne peut pas être supprimé.',

                'datagrid' => [
                    'delete'      => 'Supprimer',
                    'edit'        => 'Éditer',
                    'id'          => 'ID',
                    'name'        => 'Nom',
                    'users'       => 'Utilisateurs',
                    'created-at'  => 'Créé le',
                ],

                'create' => [
                    'name'     => 'Nom',
                    'save-btn' => 'Enregistrer le tag',
                    'title'    => 'Créer un tag',
                    'color'    => 'Couleur',
                ],

                'edit' => [
                    'title' => 'Modifier le Tag',
                ],
            ],
        ],

        'users' => [
            'index' => [
                'create-btn'          => 'Créer un utilisateur',
                'create-success'      => 'Utilisateur créé avec succès.',
                'delete-failed'       => 'L\'utilisateur ne peut pas être supprimé.',
                'delete-success'      => 'Utilisateur supprimé avec succès.',
                'last-delete-error'   => 'Au moins un utilisateur est requis.',
                'mass-delete-failed'  => 'Les utilisateurs ne peuvent pas être supprimés.',
                'mass-delete-success' => 'Utilisateurs supprimés avec succès.',
                'mass-update-failed'  => 'Les utilisateurs ne peuvent pas être mis à jour.',
                'mass-update-success' => 'Utilisateurs mis à jour avec succès.',
                'title'               => 'Utilisateurs',
                'update-success'      => 'Utilisateur mis à jour avec succès.',
                'user-define-error'   => 'Impossible de supprimer un utilisateur système.',
                'active'              => 'Actif',
                'inactive'            => 'Inactif',

                'datagrid' => [
                    'active'        => 'Actif',
                    'created-at'    => 'Créé le',
                    'delete'        => 'Supprimer',
                    'edit'          => 'Éditer',
                    'email'         => 'Email',
                    'id'            => 'ID',
                    'inactive'      => 'Inactif',
                    'name'          => 'Nom',
                    'status'        => 'Statut',
                    'update-status' => 'Mettre à jour le statut',
                    'users'         => 'Utilisateurs',
                ],

                'create' => [
                    'confirm-password' => 'Confirmer le mot de passe',
                    'email'            => 'Email',
                    'general'          => 'Général',
                    'global'           => 'Global',
                    'group'            => 'Groupe',
                    'individual'       => 'Individuel',
                    'name'             => 'Nom',
                    'password'         => 'Mot de passe',
                    'permission'       => 'Permission',
                    'role'             => 'Rôle',
                    'save-btn'         => 'Enregistrer l\'utilisateur',
                    'status'           => 'Statut',
                    'title'            => 'Créer un utilisateur',
                    'view-permission'  => 'Voir la permission',
                ],

                'edit' => [
                    'title' => 'Modifier l\'utilisateur',
                ],
            ],
        ],

        'pipelines' => [
            'index' => [
                'title'                => 'Pipelines',
                'create-btn'           => 'Créer un pipeline',
                'create-success'       => 'Pipeline créé avec succès.',
                'update-success'       => 'Pipeline mis à jour avec succès.',
                'delete-success'       => 'Pipeline supprimé avec succès.',
                'delete-failed'        => 'Le pipeline ne peut pas être supprimé.',
                'default-delete-error' => 'Le pipeline par défaut ne peut pas être supprimé.',

                'datagrid' => [
                    'delete'      => 'Supprimer',
                    'edit'        => 'Modifier',
                    'id'          => 'ID',
                    'is-default'  => 'Est par défaut',
                    'name'        => 'Nom',
                    'no'          => 'Non',
                    'rotten-days' => 'Jours pérmimés',
                    'yes'         => 'Oui',
                ],
            ],

            'create' => [
                'title'                => 'Créer un pipeline',
                'save-btn'             => 'Enregistrer le pipeline',
                'name'                 => 'Nom',
                'rotten-days'          => 'Jours périmés',
                'mark-as-default'      => 'Définir comme valeur par défaut',
                'general'              => 'Général',
                'probability'          => 'Probabilité (%)',
                'new-stage'            => 'Nouveau',
                'won-stage'            => 'Gagné',
                'lost-stage'           => 'Perdu',
                'stage-btn'            => 'Ajouter une étape',
                'stages'               => 'Étapes',
                'duplicate-name'       => 'Le champ "Nom" ne peut pas être dupliqué',
                'delete-stage'         => 'Supprimer l’étape',
                'add-new-stages'       => 'Ajouter de nouvelles étapes',
                'add-stage-info'       => 'Ajoutez une nouvelle étape pour votre pipeline',
                'newly-added'          => 'Nouvellement ajouté',
                'stage-delete-success' => 'Étape supprimée avec succès',
            ],

            'edit'  => [
                'title'                => 'Modifier le pipeline',
                'save-btn'             => 'Enregistrer le pipeline',
                'name'                 => 'Nom',
                'rotten-days'          => 'Jours de péremption',
                'mark-as-default'      => 'Définir comme par défaut',
                'general'              => 'Général',
                'probability'          => 'Probabilité (%)',
                'new-stage'            => 'Nouveau',
                'won-stage'            => 'Gagné',
                'lost-stage'           => 'Perdu',
                'stage-btn'            => 'Ajouter une étape',
                'stages'               => 'Étapes',
                'duplicate-name'       => 'Le champ "Nom" ne peut pas être dupliqué',
                'delete-stage'         => 'Supprimer l\'étape',
                'add-new-stages'       => 'Ajouter de nouvelles étapes',
                'add-stage-info'       => 'Ajoutez une nouvelle étape à votre pipeline',
                'stage-delete-success' => 'Étape supprimée avec succès',
            ],
        ],

        'webhooks' => [
            'index' => [
                'title'          => 'Webhooks',
                'create-btn'     => 'Créer un webhook',
                'create-success' => 'Webhook créé avec succès.',
                'update-success' => 'Webhook mis à jour avec succès.',
                'delete-success' => 'Webhook supprimé avec succès.',
                'delete-failed'  => 'Le webhook ne peut pas être supprimé.',

                'datagrid' => [
                    'id'          => 'ID',
                    'delete'      => 'Supprimer',
                    'edit'        => 'Modifier',
                    'name'        => 'Nom',
                    'entity-type' => 'Type d\'entité',
                    'end-point'   => 'Point de terminaison',
                ],
            ],

            'create' => [
                'title'                 => 'Créer Webhook',
                'save-btn'              => 'Sauvegarder Webhook',
                'info'                  => 'Entrez les détails des webhooks',
                'url-and-parameters'    => 'URL et Paramètres',
                'method'                => 'Méthode',
                'post'                  => 'Post',
                'put'                   => 'Put',
                'url-endpoint'          => 'Point de terminaison URL',
                'parameters'            => 'Paramètres',
                'add-new-parameter'     => 'Ajouter un nouveau paramètre',
                'url-preview'           => 'Aperçu de l\'URL:',
                'headers'               => 'En-têtes',
                'add-new-header'        => 'Ajouter un nouvel en-tête',
                'body'                  => 'Corps',
                'default'               => 'Par défaut',
                'x-www-form-urlencoded' => 'x-www-form-urlencoded',
                'key-and-value'         => 'Clé et Valeur',
                'add-new-payload'       => 'Ajouter un nouveau payload',
                'raw'                   => 'Brut',
                'general'               => 'Général',
                'name'                  => 'Nom',
                'entity-type'           => 'Type d\'entité',
                'insert-placeholder'    => 'Insérer un espace réservé',
                'description'           => 'Description',
                'json'                  => 'Json',
                'text'                  => 'Texte',
            ],

            'edit' => [
                'title'                 => 'Modifier Webhook',
                'edit-btn'              => 'Sauvegarder Webhook',
                'save-btn'              => 'Sauvegarder Webhook',
                'info'                  => 'Entrez les détails des webhooks',
                'url-and-parameters'    => 'URL et Paramètres',
                'method'                => 'Méthode',
                'post'                  => 'Post',
                'put'                   => 'Put',
                'url-endpoint'          => 'Point de terminaison URL',
                'parameters'            => 'Paramètres',
                'add-new-parameter'     => 'Ajouter un nouveau paramètre',
                'url-preview'           => 'Aperçu de l\'URL :',
                'headers'               => 'En-têtes',
                'add-new-header'        => 'Ajouter un nouvel en-tête',
                'body'                  => 'Corps',
                'default'               => 'Par défaut',
                'x-www-form-urlencoded' => 'x-www-form-urlencoded',
                'key-and-value'         => 'Clé et Valeur',
                'add-new-payload'       => 'Ajouter un nouveau payload',
                'raw'                   => 'Brut',
                'general'               => 'Général',
                'name'                  => 'Nom',
                'entity-type'           => 'Type d\'entité',
                'insert-placeholder'    => 'Insérer un espace réservé',
                'description'           => 'Description',
                'json'                  => 'Json',
                'text'                  => 'Texte',
            ],
        ],

        'warehouses' => [
            'index' => [
                'title'          => 'Entrepôts',
                'create-btn'     => 'Créer un entrepôt',
                'create-success' => 'Entrepôt créé avec succès.',
                'name-exists'    => 'Le nom de l\'entrepôt existe déjà.',
                'update-success' => 'Entrepôt mis à jour avec succès.',
                'delete-success' => 'Entrepôt supprimé avec succès.',
                'delete-failed'  => 'L\'entrepôt ne peut pas être supprimé.',

                'datagrid' => [
                    'id'              => 'ID',
                    'name'            => 'Nom',
                    'contact-name'    => 'Nom du contact',
                    'delete'          => 'Supprimer',
                    'edit'            => 'Modifier',
                    'view'            => 'Voir',
                    'created-at'      => 'Créé le',
                    'products'        => 'Produits',
                    'contact-emails'  => 'E-mails de contact',
                    'contact-numbers' => 'Numéros de contact',
                ],
            ],

            'create' => [
                'title'         => 'Créer un entrepôt',
                'save-btn'      => 'Enregistrer l\'entrepôt',
                'contact-info'  => 'Informations de contact',
            ],

            'edit' => [
                'title'         => 'Modifier l\'entrepôt',
                'save-btn'      => 'Enregistrer l\'entrepôt',
                'contact-info'  => 'Informations de contact',
            ],

            'view' => [
                'all'         => 'Tout',
                'notes'       => 'Notes',
                'files'       => 'Fichiers',
                'location'    => 'Emplacement',
                'change-logs' => 'Journaux des modifications',

                'locations' => [
                    'action'         => 'Action',
                    'add-location'   => 'Ajouter un emplacement',
                    'create-success' => 'Emplacement créé avec succès.',
                    'delete'         => 'Supprimer',
                    'delete-failed'  => "L'emplacement ne peut pas être supprimé.",
                    'delete-success' => 'Emplacement supprimé avec succès.',
                    'name'           => 'Nom',
                    'save-btn'       => 'Enregistrer',
                ],

                'general-information' => [
                    'title' => 'Informations générales',
                ],

                'contact-information' => [
                    'title' => 'Informations de contact',
                ],
            ],
        ],

        'attributes' => [
            'index' => [
                'title'              => 'Attributs',
                'create-btn'         => 'Créer un attribut',
                'create-success'     => 'Attributs créés avec succès.',
                'update-success'     => 'Attributs mis à jour avec succès.',
                'delete-success'     => 'Attributs supprimés avec succès.',
                'delete-failed'      => 'Les attributs ne peuvent pas être supprimés.',
                'user-define-error'  => 'Impossible de supprimer un attribut système.',
                'mass-delete-failed' => 'Les attributs système ne peuvent pas être supprimés.',

                'datagrid' => [
                    'yes'         => 'Oui',
                    'no'          => 'Non',
                    'id'          => 'ID',
                    'code'        => 'Code',
                    'name'        => 'Nom',
                    'entity-type' => 'Type d\'entité',
                    'type'        => 'Type',
                    'is-default'  => 'Par défaut',
                    'edit'        => 'Modifier',
                    'delete'      => 'Supprimer',
                ],
            ],

            'create'  => [
                'title'                 => 'Créer un attribut',
                'save-btn'              => 'Enregistrer l\'attribut',
                'code'                  => 'Code',
                'name'                  => 'Nom',
                'entity-type'           => 'Type d\'entité',
                'type'                  => 'Type',
                'validations'           => 'Validations',
                'is-required'           => 'Est requis',
                'input-validation'      => 'Validation de saisie',
                'is-unique'             => 'Est unique',
                'labels'                => 'Étiquettes',
                'general'               => 'Général',
                'numeric'               => 'Numérique',
                'decimal'               => 'Décimal',
                'url'                   => 'Url',
                'options'               => 'Options',
                'option-type'           => 'Type d\'option',
                'lookup-type'           => 'Type de recherche',
                'add-option'            => 'Ajouter une option',
                'save-option'           => 'Enregistrer l\'option',
                'option-name'           => 'Nom de l\'option',
                'add-attribute-options' => 'Ajouter des options d\'attribut',
                'text'                  => 'Texte',
                'textarea'              => 'Zone de texte',
                'price'                 => 'Prix',
                'boolean'               => 'Booléen',
                'select'                => 'Sélectionner',
                'multiselect'           => 'Sélection multiple',
                'email'                 => 'Email',
                'address'               => 'Adresse',
                'phone'                 => 'Téléphone',
                'datetime'              => 'Date et heure',
                'date'                  => 'Date',
                'image'                 => 'Image',
                'file'                  => 'Fichier',
                'lookup'                => 'Recherche',
                'entity_type'           => 'Type d\'entité',
                'checkbox'              => 'Case à cocher',
                'is_required'           => 'Est requis',
                'is_unique'             => 'Est unique',
                'actions'               => 'Actions',
            ],

            'edit'  => [
                'title'                 => 'Modifier l\'attribut',
                'save-btn'              => 'Enregistrer l\'attribut',
                'code'                  => 'Code',
                'name'                  => 'Nom',
                'labels'                => 'Étiquettes',
                'entity-type'           => 'Type d\'entité',
                'type'                  => 'Type',
                'validations'           => 'Validations',
                'is-required'           => 'Est requis',
                'input-validation'      => 'Validation de saisie',
                'is-unique'             => 'Est unique',
                'general'               => 'Général',
                'numeric'               => 'Numérique',
                'decimal'               => 'Décimal',
                'url'                   => 'Url',
                'options'               => 'Options',
                'option-type'           => 'Type d\'option',
                'lookup-type'           => 'Type de recherche',
                'add-option'            => 'Ajouter une option',
                'save-option'           => 'Enregistrer l\'option',
                'option-name'           => 'Nom de l\'option',
                'add-attribute-options' => 'Ajouter des options d\'attribut',
                'text'                  => 'Texte',
                'textarea'              => 'Zone de texte',
                'price'                 => 'Prix',
                'boolean'               => 'Booléen',
                'select'                => 'Sélectionner',
                'multiselect'           => 'Sélection multiple',
                'email'                 => 'Email',
                'address'               => 'Adresse',
                'phone'                 => 'Téléphone',
                'datetime'              => 'Date et heure',
                'date'                  => 'Date',
                'image'                 => 'Image',
                'file'                  => 'Fichier',
                'lookup'                => 'Recherche',
                'entity_type'           => 'Type d\'entité',
                'checkbox'              => 'Case à cocher',
                'is_required'           => 'Est requis',
                'is_unique'             => 'Est unique',
                'actions'               => 'Actions',
            ],
        ],
    ],

    'activities' => [
        'index' => [
            'title'      => 'Activités',

            'datagrid' => [
                'comment'       => 'Commentaire',
                'created_at'    => 'Créé le',
                'created_by'    => 'Créé par',
                'edit'          => 'Modifier',
                'id'            => 'ID',
                'done'          => 'Terminé',
                'not-done'      => 'Non terminé',
                'lead'          => 'Lead',
                'mass-delete'   => 'Suppression en masse',
                'mass-update'   => 'Mise à jour en masse',
                'schedule-from' => 'Planifié depuis',
                'schedule-to'   => 'Planifié jusqu\'à',
                'schedule_from' => 'Planifié depuis',
                'schedule_to'   => 'Planifié jusqu\'à',
                'title'         => 'Titre',
                'is_done'       => 'Est terminé',
                'type'          => 'Type',
                'update'        => 'Mettre à jour',
                'call'          => 'Appel',
                'meeting'       => 'Réunion',
                'lunch'         => 'Déjeuner',
            ],
        ],

        'edit' => [
            'title'           => 'Modifier l\'activité',
            'back-btn'        => 'Retour',
            'save-btn'        => 'Enregistrer l\'activité',
            'type'            => 'Type d\'activité',
            'call'            => 'Appel',
            'meeting'         => 'Réunion',
            'lunch'           => 'Déjeuner',
            'schedule_to'     => 'Planifier jusqu\'à',
            'schedule_from'   => 'Planifier depuis',
            'location'        => 'Lieu',
            'comment'         => 'Commentaire',
            'lead'            => 'Lead',
            'participants'    => 'Participants',
            'general'         => 'Général',
            'persons'         => 'Personnes',
            'no-result-found' => 'Aucun enregistrement trouvé.',
            'users'           => 'Utilisateurs',
        ],

        'updated'              => ':attribute mis à jour ',
        'created'              => 'Créé',
        'duration-overlapping' => 'Les participants ont une autre réunion à ce moment. Voulez-vous continuer ?',
        'create-success'       => 'Activité créée avec succès.',
        'update-success'       => 'Activité mise à jour avec succès.',
        'overlapping-error'    => 'Les participants ont une autre réunion à ce moment.',
        'destroy-success'      => 'Activité supprimée avec succès.',
        'delete-failed'        => 'L\'activité ne peut pas être supprimée.',
        'mass-update-success'  => 'Activités mises à jour avec succès.',
        'mass-destroy-success' => 'Activités supprimées avec succès.',
        'mass-delete-failed'   => 'Les activités ne peuvent pas être supprimées.',
    ],

    'mail' => [
        'index' => [
            'compose'           => 'Composer',
            'draft'             => 'Brouillon',
            'inbox'             => 'Boîte de réception',
            'outbox'            => 'Boîte d’envoi',
            'sent'              => 'Envoyé',
            'trash'             => 'Corbeille',
            'compose-mail-btn'  => 'Écrire un mail',
            'btn'               => 'Mail',

            'mail'              => [
                'title'         => 'Rédiger un mail',
                'to'            => 'À',
                'enter-emails'  => 'Appuyez sur Entrée pour ajouter des e-mails',
                'cc'            => 'CC',
                'bcc'           => 'CCI',
                'subject'       => 'Objet',
                'send-btn'      => 'Envoyer',
                'message'       => 'Message',
                'draft'         => 'Brouillon',
            ],

            'datagrid' => [
                'id'            => 'ID',
                'from'          => 'De',
                'to'            => 'À',
                'subject'       => 'Objet',
                'tag-name'      => 'Nom de l’étiquette',
                'created-at'    => 'Créé le',
                'move-to-inbox' => 'Déplacé dans la boîte de réception',
                'edit'          => 'Modifier',
                'view'          => 'Voir',
                'delete'        => 'Supprimer',
            ],
        ],

        'create-success'      => 'E-mail envoyé avec succès.',
        'update-success'      => 'E-mail mis à jour avec succès.',
        'mass-update-success' => 'E-mails mis à jour avec succès.',
        'delete-success'      => 'E-mail supprimé avec succès.',
        'delete-failed'       => 'L’e-mail ne peut pas être supprimé.',

        'view' => [
            'title'                      => 'Mails',
            'subject'                    => ':subject',
            'link-mail'                  => 'Lier le Mail',
            'to'                         => 'À',
            'cc'                         => 'CC',
            'bcc'                        => 'BCC',
            'reply'                      => 'Répondre',
            'reply-all'                  => 'Répondre à Tous',
            'forward'                    => 'Transférer',
            'delete'                     => 'Supprimer',
            'enter-mails'                => 'Entrez une adresse e-mail',
            'rotten-days'                => 'Lead en attente depuis :days jours',
            'search-an-existing-lead'    => 'Rechercher un lead existant',
            'search-an-existing-contact' => 'Rechercher un contact existant',
            'message'                    => 'Message',
            'add-attachments'            => 'Ajouter des pièces jointes',
            'discard'                    => 'Annuler',
            'send'                       => 'Envoyer',
            'no-result-found'            => 'Aucun résultat trouvé',
            'add-new-contact'            => 'Ajouter un Nouveau Contact',
            'description'                => 'Description',
            'search'                     => 'Rechercher...',
            'add-new-lead'               => 'Ajouter un Nouveau Lead',
            'create-new-contact'         => 'Créer un Nouveau Contact',
            'save-contact'               => 'Enregistrer le Contact',
            'create-lead'                => 'Créer un Lead',
            'linked-contact'             => 'Contact Lié',
            'link-to-contact'            => 'Lier à un Contact',
            'link-to-lead'               => 'Lier à un Lead',
            'linked-lead'                => 'Lead Lié',
            'lead-details'               => 'Détails du Lead',
            'contact-person'             => 'Contact',
            'product'                    => 'Produit',

            'tags' => [
                'create-success'  => 'Tag créé avec succès.',
                'destroy-success' => 'Tag supprimé avec succès.',
            ],
        ],
    ],

    'common' => [
        'custom-attributes' => [
            'select-country' => 'Sélectionner un pays',
            'select-state'   => 'Sélectionner un état',
            'state'          => 'État',
            'city'           => 'Ville',
            'postcode'       => 'Code postal',
            'work'           => 'Travail',
            'home'           => 'Domicile',
            'add-more'       => 'Ajouter plus',
            'select'         => 'Sélectionner',
            'country'        => 'Pays',
            'address'        => 'Adresse',
        ],
    ],

    'leads' => [
        'create-success'    => 'Prospect créé avec succès.',
        'update-success'    => 'Prospects mis à jour avec succès.',
        'update-failed'     => 'Les prospects ne peuvent pas être supprimés.',
        'destroy-success'   => 'Prospect supprimé avec succès.',
        'destroy-failed'    => 'Le prospect ne peut pas être supprimé.',

        'index' => [
            'title'      => 'Prospects',
            'create-btn' => 'Créer un Prospect',

            'datagrid' => [
                'id'                  => 'ID',
                'sales-person'        => 'Commercial',
                'subject'             => 'Objet',
                'source'              => 'Source',
                'lead-value'          => 'Valeur du Prospect',
                'lead-type'           => 'Type de Prospect',
                'tag-name'            => 'Nom du Tag',
                'contact-person'      => 'Personne à Contacter',
                'stage'               => 'Étape',
                'rotten-lead'         => 'Prospect Périmé',
                'expected-close-date' => 'Date de Clôture Prévue',
                'created-at'          => 'Créé le',
                'no'                  => 'Non',
                'yes'                 => 'Oui',
                'delete'              => 'Supprimer',
                'mass-delete'         => 'Suppression de Masse',
                'mass-update'         => 'Mise à Jour de Masse',
            ],

            'kanban' => [
                'rotten-days'            => 'Le prospect est périmé depuis :days jours',
                'empty-list'             => 'Votre liste de prospects est vide',
                'empty-list-description' => 'Créez un prospect pour organiser vos objectifs.',
                'create-lead-btn'        => 'Créer un Prospect',

                'columns' => [
                    'contact-person'      => 'Contact',
                    'id'                  => 'ID',
                    'lead-type'           => 'Type de Prospect',
                    'lead-value'          => 'Valeur du Prospect',
                    'sales-person'        => 'Commercial',
                    'source'              => 'Source',
                    'title'               => 'Titre',
                    'tags'                => 'Étiquettes',
                    'expected-close-date' => 'Date de Clôture Prévue',
                    'created-at'          => 'Créé le',
                ],

                'toolbar' => [
                    'search' => [
                        'title' => 'Rechercher',
                    ],

                    'filters' => [
                        'apply-filters' => 'Appliquer les filtres',
                        'clear-all'     => 'Tout effacer',
                        'filter'        => 'Filtrer',
                        'filters'       => 'Filtres',
                        'select'        => 'Sélectionner',
                    ],
                ],
            ],

            'view-switcher' => [
                'all-pipelines'       => 'Tous les pipelines',
                'create-new-pipeline' => 'Créer un nouveau pipeline',
            ],
        ],

        'create' => [
            'title'          => 'Créer un lead',
            'save-btn'       => 'Enregistrer',
            'details'        => 'Détails',
            'details-info'   => 'Renseignez les informations de base du lead',
            'contact-person' => 'Contact',
            'contact-info'   => 'Informations sur le contact',
            'products'       => 'Produits',
            'products-info'  => 'Informations sur les produits',
        ],

        'edit' => [
            'title'          => 'Modifier un lead',
            'save-btn'       => 'Enregistrer',
            'details'        => 'Détails',
            'details-info'   => 'Renseignez les informations de base du lead',
            'contact-person' => 'Contact',
            'contact-info'   => 'Informations sur le contact',
            'products'       => 'Produits',
            'products-info'  => 'Informations sur les produits',
        ],

        'common' => [
            'contact' => [
                'name'           => 'Nom',
                'email'          => 'Email',
                'contact-number' => 'Numéro de contact',
                'organization'   => 'Organisation',
            ],

            'products' => [
                'product-name' => 'Nom du produit',
                'quantity'     => 'Quantité',
                'price'        => 'Prix',
                'amount'       => 'Montant',
                'action'       => 'Action',
                'add-more'     => 'Ajouter plus',
                'total'        => 'Total',
            ],
        ],

        'view' => [
            'title'       => 'Lead: :title',
            'rotten-days' => ':days jours',

            'tabs'        => [
                'description' => 'Description',
                'products'    => 'Produits',
                'quotes'      => 'Devis',
            ],

            'attributes' => [
                'title' => 'À propos du lead',
            ],

            'quotes'=> [
                'subject'         => 'Sujet',
                'expired-at'      => 'Date d\'expiration',
                'sub-total'       => 'Sous-total',
                'discount'        => 'Remise',
                'tax'             => 'Taxe',
                'adjustment'      => 'Ajustement',
                'grand-total'     => 'Total général',
                'delete'          => 'Supprimer',
                'edit'            => 'Modifier',
                'download'        => 'Télécharger',
                'destroy-success' => 'Devis supprimé avec succès.',
                'empty-title'     => 'Aucun devis trouvé',
                'empty-info'      => 'Aucun devis trouvée pour ce lead',
                'add-btn'         => 'Ajoute un devis',
            ],

            'products' => [
                'product-name' => 'Nom du produit',
                'quantity'     => 'Quantité',
                'price'        => 'Prix',
                'amount'       => 'Montant',
                'action'       => 'Action',
                'add-more'     => 'Ajouter Plus',
                'total'        => 'Total',
                'empty-title'  => 'Aucun produit trouvé',
                'empty-info'   => 'Aucun produit trouvé pour ce lead',
                'add-product'  => 'Ajouter un produit',
            ],

            'persons' => [
                'title'     => 'À propos des personnes',
                'job-title' => ':job_title chez :organization',
            ],

            'stages' => [
                'won-lost'       => 'Gagné/Perdu',
                'won'            => 'Gagné',
                'lost'           => 'Perdu',
                'need-more-info' => 'Besoin de plus de détails',
                'closed-at'      => 'Fermé à',
                'won-value'      => 'Valeur gagnée',
                'lost-reason'    => 'Raison de la perte',
                'save-btn'       => 'Sauvegarder',
            ],

            'tags' => [
                'create-success'  => 'Étiquette créée avec succès.',
                'destroy-success' => 'Étiquette supprimée avec succès.',
            ],
        ],
    ],

    'configuration' => [
        'index' => [
            'back'         => 'Retour',
            'save-btn'     => 'Enregistrer la configuration',
            'save-success' => 'Configuration enregistrée avec succès.',
            'search'       => 'Rechercher',
            'title'        => 'Configuration',

            'general'  => [
                'title'   => 'Général',
                'info'    => 'Configuration générale',

                'general' => [
                    'title'           => 'Général',
                    'info'            => 'Mettez à jour vos paramètres généraux ici.',

                    'locale-settings' => [
                        'title'       => 'Paramètres de langue',
                        'title-info'  => 'Définit la langue utilisée dans l\'interface utilisateur, telles que l\'arabe (ar), l\'anglais (en), l\'espagnol (es), le français(fr), le persan (fa) et le turc (tr).',
                    ],
                ],
            ],
        ],
    ],

    'dashboard' => [
        'index' => [
            'title' => 'Tableau de bord',

            'revenue' => [
                'lost-revenue' => 'Revenus Perdus',
                'won-revenue'  => 'Revenus Gagnés',
            ],

            'over-all' => [
                'average-lead-value'    => 'Valeur Moyenne des Leads',
                'total-leads'           => 'Total des Leads',
                'average-leads-per-day' => 'Moyenne des Leads Par Jour',
                'total-quotations'      => 'Total des Devis',
                'total-persons'         => 'Total des Personnes',
                'total-organizations'   => 'Total des Organisations',
            ],

            'total-leads' => [
                'title' => 'Leads',
                'total' => 'Total des Leads',
                'won'   => 'Leads Gagnés',
                'lost'  => 'Leads Perdus',
            ],

            'revenue-by-sources' => [
                'title'       => 'Revenu Par Sources',
                'empty-title' => 'Aucune Donnée Disponible',
                'empty-info'  => 'Aucune donnée disponible pour l\'intervalle sélectionné',
            ],

            'revenue-by-types' => [
                'title'       => 'Revenu Par Types',
                'empty-title' => 'Aucune Donnée Disponible',
                'empty-info'  => 'Aucune donnée disponible pour l\'intervalle sélectionné',
            ],

            'top-selling-products' => [
                'title'       => 'Produits Principaux',
                'empty-title' => 'Aucun Produit Trouvé',
                'empty-info'  => 'Aucun produit disponible pour l\'intervalle sélectionné',
            ],

            'top-persons' => [
                'title'       => 'Personnes Principales',
                'empty-title' => 'Aucune Personne Trouvée',
                'empty-info'  => 'Aucune personne disponible pour l\'intervalle sélectionné',
            ],

            'open-leads-by-states' => [
                'title'       => 'Leads Ouverts Par États',
                'empty-title' => 'Aucune Donnée Disponible',
                'empty-info'  => 'Aucune donnée disponible pour l\'intervalle sélectionné',
            ],
        ],
    ],

    'layouts' => [
        'app-version'          => 'Version : :version',
        'dashboard'            => 'Tableau de bord',
        'leads'                => 'Leads',
        'quotes'               => 'Devis',
        'quote'                => 'Devis',

        'mail'                 => [
            'title'   => 'Mail',
            'compose' => 'Composer',
            'inbox'   => 'Boîte de réception',
            'draft'   => 'Brouillons',
            'outbox'  => 'Boîte d\'envoi',
            'sent'    => 'Envoyé',
            'trash'   => 'Corbeille',
            'setting' => 'Paramètres',
        ],

        'activities'           => 'Activités',
        'contacts'             => 'Contacts',
        'persons'              => 'Personnes',
        'person'               => 'Personne',
        'organizations'        => 'Organisations',
        'organization'         => 'Organisation',
        'products'             => 'Produits',
        'product'              => 'Produit',
        'settings'             => 'Paramètres',
        'user'                 => 'Utilisateur',
        'user-info'            => 'Gérez tous vos utilisateurs et leurs autorisations dans le CRM, y compris ce qu’ils sont autorisés à faire.',
        'groups'               => 'Groupes',
        'groups-info'          => 'Ajoutez, modifiez ou supprimez des groupes dans le CRM.',
        'roles'                => 'Rôles',
        'role'                 => 'Rôle',
        'roles-info'           => 'Ajoutez, modifiez ou supprimez des rôles dans le CRM.',
        'users'                => 'Utilisateurs',
        'users-info'           => 'Ajoutez, modifiez ou supprimez des utilisateurs dans le CRM.',
        'lead'                 => 'Lead',
        'lead-info'            => 'Gérez tous vos paramètres liés aux leads dans le CRM.',
        'pipelines'            => 'Pipelines',
        'pipelines-info'       => 'Ajoutez, modifiez ou supprimez des pipelines dans le CRM.',
        'sources'              => 'Sources',
        'sources-info'         => 'Ajoutez, modifiez ou supprimez des sources dans le CRM.',
        'types'                => 'Types',
        'types-info'           => 'Ajoutez, modifiez ou supprimez des types dans le CRM.',
        'automation'           => 'Automatisation',
        'automation-info'      => 'Gérez tous vos paramètres d’automatisation dans le CRM.',
        'attributes'           => 'Attributs',
        'attribute'            => 'Attribut',
        'attributes-info'      => 'Ajoutez, modifiez ou supprimez des attributs dans le CRM.',
        'email-templates'      => 'Modèles d’e-mails',
        'email'                => 'E-mail',
        'email-templates-info' => 'Ajoutez, modifiez ou supprimez des modèles d’e-mails dans le CRM.',
        'workflows'            => 'Flux de travail',
        'workflows-info'       => 'Ajoutez, modifiez ou supprimez des flux de travail dans le CRM.',
        'webhooks'             => 'Webhooks',
        'webhooks-info'        => 'Ajoutez, modifiez ou supprimez des webhooks dans le CRM.',
        'other-settings'       => 'Autres Paramètres',
        'other-settings-info'  => 'Gérez tous vos paramètres supplémentaires dans le CRM.',
        'tags'                 => 'Étiquettes',
        'tags-info'            => 'Ajoutez, modifiez ou supprimez des étiquettes dans le CRM.',
        'my-account'           => 'Mon Compte',
        'sign-out'             => 'Se Déconnecter',
        'back'                 => 'Retour',
        'name'                 => 'Nom',
        'configuration'        => 'Configuration',
        'activities'           => 'Activités',
        'howdy'                => 'Salut !',
        'warehouses'           => 'Entrepôts',
        'warehouse'            => 'Entrepôt',
        'warehouses-info'      => 'Ajoutez, modifiez ou supprimez des entrepôts dans le CRM.',
    ],

    'user' => [
        'account' => [
            'name'                  => 'Nom',
            'email'                 => 'E-mail',
            'password'              => 'Mot de passe',
            'my_account'            => 'Mon compte',
            'update_details'        => 'Mettre à jour les informations',
            'current_password'      => 'Mot de passe actuel',
            'confirm_password'      => 'Confirmer le mot de passe',
            'password-match'        => 'Le mot de passe actuel ne correspond pas.',
            'account-save'          => 'Les modifications du compte ont été enregistrées avec succès.',
            'permission-denied'     => 'Permission refusée',
            'remove-image'          => 'Supprimer l’image',
            'upload_image_pix'      => 'Téléchargez une image de profil (100px x 100px)',
            'upload_image_format'   => 'au format PNG ou JPG',
            'image_upload_message'  => 'Seules les images (.jpeg, .jpg, .png, ...) sont autorisées.',
        ],
    ],

    'emails' => [
        'common' => [
            'dear'   => 'Cher(e) :name',
            'cheers' => 'Cordialement,</br>L\'équipe :app_name',

            'user'   => [
                'dear'           => 'Cher(e) :username',
                'create-subject' => 'Vous avez été ajouté en tant que membre.',
                'create-body'    => 'Félicitations ! Vous êtes maintenant membre de notre équipe.',

                'forget-password' => [
                    'subject'           => 'Réinitialisation du mot de passe du client',
                    'dear'              => 'Cher(e) :username',
                    'reset-password'    => 'Réinitialiser le mot de passe',
                    'info'              => 'Vous recevez cet e-mail car nous avons reçu une demande de réinitialisation de mot de passe pour votre compte.',
                    'final-summary'     => 'Si vous n\'avez pas demandé de réinitialisation, aucune autre action n\'est requise.',
                    'thanks'            => 'Merci !',
                ],
            ],
        ],
    ],

    'errors' => [
        'dashboard' => 'Tableau de bord',
        'go-back'   => 'Retourner',
        'support'   => 'Si le problème persiste, contactez-nous à <a href=":link" class=":class">:email</a> pour obtenir de l\'aide.',

        '404' => [
            'description' => 'Oups ! La page que vous recherchez est introuvable. Il semble que nous n\'ayons pas trouvé ce que vous cherchiez.',
            'title'       => 'Page 404 Introuvable',
        ],

        '401' => [
            'description' => 'Oups ! Il semble que vous n\'ayez pas l\'autorisation d\'accéder à cette page. Il semble que vous manquiez des informations d\'identification nécessaires.',
            'title'       => '401 Non Autorisé',
        ],

        '403' => [
            'description' => 'Oups ! Cette page est interdite. Il semble que vous n\'ayez pas les autorisations requises pour afficher ce contenu.',
            'title'       => '403 Interdit',
        ],

        '500' => [
            'description' => 'Oups ! Quelque chose a mal tourné. Il semble que nous rencontrions des difficultés pour charger la page que vous recherchez.',
            'title'       => '500 Erreur Interne du Serveur',
        ],

        '503' => [
            'description' => 'Oups ! Il semble que nous soyons temporairement hors service pour maintenance. Veuillez revenir dans quelques instants.',
            'title'       => '503 Service Indisponible',
        ],
    ],
];
