<?php

namespace Webkul\Installer\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\File;
use Webkul\Installer\Helpers\DatabaseManager;
use Webkul\Installer\Helpers\EnvironmentManager;
use Webkul\Installer\Helpers\ServerRequirements;

class InstallerController extends Controller
{
    /**
     * Const Variable For Min PHP Version
     *
     * @var string
     */
    const MIN_PHP_VERSION = '8.1.0';

    /**
     * Const Variable for Static Customer Id
     *
     * @var int
     */
    const USER_ID = 1;

    /**
     * Create a new controller instance
     *
     * @return void
     */
    public function __construct(
        protected ServerRequirements $serverRequirements,
        protected EnvironmentManager $environmentManager,
        protected DatabaseManager $databaseManager
    ) {}

    /**
     * Installer View Root Page
     */
    public function index()
    {
        $phpVersion = $this->serverRequirements->checkPHPversion(self::MIN_PHP_VERSION);

        $requirements = $this->serverRequirements->validate();

        if (request()->has('locale')) {
            return redirect()->route('installer.index');
        }

        return view('installer::installer.index', compact('requirements', 'phpVersion'));
    }

    /**
     * ENV File Setup
     */
    public function envFileSetup(Request $request): JsonResponse
    {
        $message = $this->environmentManager->generateEnv($request);

        return new JsonResponse(['data' => $message]);
    }

    /**
     * Run Migration
     */
    public function runMigration(): mixed
    {
        return $this->databaseManager->migration();
    }

    /**
     * Run Seeder.
     *
     * @return void|string
     */
    public function runSeeder()
    {
        $allParameters = request()->allParameters;

        $parameter = [
            'parameter' => [
                'default_locales'    => $allParameters['app_locale'] ?? null,
                'default_currency'   => $allParameters['app_currency'] ?? null,
            ],
        ];

        $response = $this->environmentManager->setEnvConfiguration(request()->allParameters);

        if ($response) {
            $seeder = $this->databaseManager->seeder($parameter);

            return $seeder;
        }
    }

    /**
     * Admin Configuration Setup.
     */
    public function adminConfigSetup(): bool
    {
        $password = password_hash(request()->input('password'), PASSWORD_BCRYPT, ['cost' => 10]);

        try {
            DB::table('users')->updateOrInsert(
                [
                    'id' => self::USER_ID,
                ], [
                    'name'     => request()->input('admin'),
                    'email'    => request()->input('email'),
                    'password' => $password,
                    'role_id'  => 1,
                    'status'   => 1,
                ]
            );

            $this->smtpConfigSetup();

            return true;
        } catch (\Throwable $th) {
            report($th);

            return false;
        }
    }

    /**
     * SMTP connection setup for Mail
     */
    private function smtpConfigSetup()
    {
        $filePath = storage_path('installed');

        File::put($filePath, 'Your Krayin App is Successfully Installed');

        Event::dispatch('krayin.installed');

        return $filePath;
    }
}
