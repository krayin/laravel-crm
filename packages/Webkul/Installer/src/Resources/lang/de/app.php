<?php

return [
    'seeders' => [
        'attributes' => [
            'leads' => [
                'description'         => 'Beschreibung',
                'expected-close-date' => 'Voraussichtliches Abschlussdatum',
                'lead-value'          => 'Lead-Wert',
                'sales-owner'         => 'Vertriebsverantwortlicher',
                'source'              => 'Quelle',
                'title'               => 'Titel',
                'type'                => 'Typ',
                'pipeline'            => 'Pipeline',
                'stage'               => 'Phase',
            ],

            'persons' => [
                'contact-numbers' => 'Kontaktnummern',
                'emails'          => 'E-Mails',
                'job-title'       => 'Berufsbezeichnung',
                'name'            => 'Name',
                'organization'    => 'Organisation',
                'sales-owner'     => 'Vertriebsverantwortlicher',
            ],

            'organizations' => [
                'address'     => 'Adresse',
                'name'        => 'Name',
                'sales-owner' => 'Vertriebsverantwortlicher',
            ],

            'products' => [
                'description' => 'Beschreibung',
                'name'        => 'Name',
                'price'       => 'Preis',
                'quantity'    => 'Menge',
                'sku'         => 'SKU',
            ],

            'quotes' => [
                'adjustment-amount' => 'Anpassungsbetrag',
                'billing-address'   => 'Rechnungsadresse',
                'description'       => 'Beschreibung',
                'discount-amount'   => 'Rabattbetrag',
                'discount-percent'  => 'Rabattprozentsatz',
                'expired-at'        => 'Abgelaufen am',
                'grand-total'       => 'Gesamtsumme',
                'person'            => 'Person',
                'sales-owner'       => 'Vertriebsverantwortlicher',
                'shipping-address'  => 'Lieferadresse',
                'sub-total'         => 'Zwischensumme',
                'subject'           => 'Betreff',
                'tax-amount'        => 'Steuerbetrag',
            ],

            'warehouses' => [
                'contact-address' => 'Kontaktadresse',
                'contact-emails'  => 'Kontakt-E-Mails',
                'contact-name'    => 'Kontaktname',
                'contact-numbers' => 'Kontaktnummern',
                'description'     => 'Beschreibung',
                'name'            => 'Name',
            ],
        ],

        'email' => [
            'activity-created'      => 'Aktivität erstellt',
            'activity-modified'     => 'Aktivität geändert',
            'date'                  => 'Datum',
            'new-activity'          => 'Sie haben eine neue Aktivität, bitte finden Sie die Details unten',
            'new-activity-modified' => 'Sie haben eine geänderte Aktivität, bitte finden Sie die Details unten',
            'participants'          => 'Teilnehmer',
            'title'                 => 'Titel',
            'type'                  => 'Typ',
        ],

        'lead' => [
            'pipeline' => [
                'default' => 'Standard-Pipeline',

                'pipeline-stages' => [
                    'follow-up'   => 'Nachverfolgung',
                    'lost'        => 'Verloren',
                    'negotiation' => 'Verhandlung',
                    'new'         => 'Neu',
                    'prospect'    => 'Aussicht',
                    'won'         => 'Gewonnen',
                ],
            ],

            'source' => [
                'direct'   => 'Direkt',
                'email'    => 'E-Mail',
                'phone'    => 'Telefon',
                'web'      => 'Web',
                'web-form' => 'Web-Formular',
            ],

            'type' => [
                'existing-business' => 'Bestehendes Geschäft',
                'new-business'      => 'Neues Geschäft',
            ],
        ],

        'user' => [
            'role' => [
                'administrator-role' => 'Administrator-Rolle',
                'administrator'      => 'Administrator',
            ],
        ],

        'workflow' => [
            'email-to-participants-after-activity-updation' => 'E-Mails an Teilnehmer nach Aktivitätsaktualisierung',
            'email-to-participants-after-activity-creation' => 'E-Mails an Teilnehmer nach Aktivitätserstellung',
        ],
    ],

    'installer' => [
        'index' => [
            'create-administrator' => [
                'admin'            => 'Admin',
                'krayin'           => 'Krayin',
                'confirm-password' => 'Passwort bestätigen',
                'email'            => 'E-Mail',
                'email-address'    => 'admin@example.com',
                'password'         => 'Passwort',
                'title'            => 'Administrator erstellen',
            ],

            'environment-configuration' => [
                'title'            => 'Konfiguration des Shops',
                'default-currency' => 'Standardwährung',
                'default-locale'   => 'Standardsprache',
                'database-name'    => 'Datenbankname',
                'database-username'=> 'Datenbankbenutzername',
                'database-password'=> 'Datenbankpasswort',
                'database-hostname'=> 'Datenbank-Hostname',
                'database-port'    => 'Datenbank-Port',
                'database-prefix'  => 'Datenbank-Präfix',
                'default-url'      => 'Standard-URL',
                'default-timezone' => 'Standard-Zeitzone',
                'select-timezone'  => 'Zeitzone auswählen',
                'warning-message'  => 'Achtung! Die Einstellungen für Ihre Standardsprache und Standardwährung sind dauerhaft und können nach der Festlegung nicht mehr geändert werden.',
            ],

            'installation-processing' => [
                'krayin'       => 'Installation von Krayin',
                'krayin-info'  => 'Datenbanktabellen werden erstellt, dies kann einige Augenblicke dauern',
                'title'        => 'Installation',
            ],

            'installation-completed' => [
                'admin-panel'                => 'Admin-Panel',
                'krayin-forums'              => 'Krayin-Forum',
                'customer-panel'             => 'Kunden-Panel',
                'explore-krayin-extensions'  => 'Krayin-Erweiterungen entdecken',
                'title'                      => 'Installation abgeschlossen',
                'title-info'                 => 'Krayin wurde erfolgreich auf Ihrem System installiert.',
            ],

            'ready-for-installation' => [
                'install'              => 'Installation',
                'install-info'         => 'Krayin für die Installation bereit',
                'start-installation'   => 'Installation starten',
                'title'                => 'Bereit für die Installation',
            ],

            'start' => [
                'locale'        => 'Sprache',
                'main'          => 'Start',
                'select-locale' => 'Sprache auswählen',
                'title'         => 'Ihre Krayin-Installation',
                'welcome-title' => 'Willkommen bei Krayin',
            ],

            'server-requirements' => [
                'title'       => 'Systemanforderungen',
                'php-version' => '8.1 oder höher',
                'pdo'         => 'PDO',
                'openssl'     => 'OpenSSL',
                'xml'         => 'XML',
                'mbstring'    => 'mbstring',
                'json'        => 'JSON',
                'ctype'       => 'cType',
                'curl'        => 'cURL',
            ],

            'back'              => 'Zurück',
            'krayin'            => 'Krayin',
            'krayin-info'       => 'Ein Community-Projekt von',
            'continue'          => 'Weiter',
            'installation-info' => 'Wir freuen uns, Sie hier zu sehen!',
            'installation-title'=> 'Willkommen zur Installation',
            'title'             => 'Krayin Installer',
        ],
    ],
];
