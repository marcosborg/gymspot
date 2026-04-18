<?php

namespace App\Console\Commands;

use App\Support\EnvironmentFile;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use RuntimeException;

class SwitchEnvironment extends Command
{
    protected $signature = 'app:env-switch
        {environment : Target profile stored inside .env, for example sandbox or production}
        {--no-backup : Do not create a timestamped backup of the current .env file}';

    protected $description = 'Switch the active APP/DB values using the {ENVIRONMENT}_* profile stored inside .env and clear Laravel caches.';

    public function handle(): int
    {
        $environment = trim((string) $this->argument('environment'));

        if (!preg_match('/^[A-Za-z0-9._-]+$/', $environment)) {
            $this->error('The environment name may only contain letters, numbers, dots, underscores and dashes.');
            return self::FAILURE;
        }

        try {
            $targetPath = EnvironmentFile::activePath();

            if (!$this->option('no-backup') && is_file($targetPath)) {
                $backupPath = base_path('.env.backup.' . date('Ymd_His'));
                EnvironmentFile::write($backupPath, EnvironmentFile::read($targetPath));
                $this->line("Backup created: {$backupPath}");
            }

            EnvironmentFile::switchToProfile($environment);

            Artisan::call('optimize:clear');

            $this->info("Active environment switched to [{$environment}] using profile values stored inside [.env].");
            $this->line(trim(Artisan::output()));

            return self::SUCCESS;
        } catch (RuntimeException $exception) {
            $this->error($exception->getMessage());

            return self::FAILURE;
        }
    }
}
