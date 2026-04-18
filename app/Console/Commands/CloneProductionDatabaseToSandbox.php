<?php

namespace App\Console\Commands;

use App\Support\EnvironmentFile;
use Illuminate\Console\Command;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Throwable;

class CloneProductionDatabaseToSandbox extends Command
{
    protected $signature = 'db:clone-production-to-sandbox
        {--mysqldump=mysqldump : Path to the mysqldump binary}
        {--mysql=mysql : Path to the mysql binary}
        {--keep-dump : Keep the generated SQL dump file in storage/app/tmp}';

    protected $description = 'Clone the database configured in PRODUCTION_DB_* into the database configured in SANDBOX_DB_* inside .env.';

    public function handle(): int
    {
        try {
            $production = EnvironmentFile::databaseConfig('production');
            $sandbox = EnvironmentFile::databaseConfig('sandbox');
        } catch (Throwable $exception) {
            $this->error($exception->getMessage());
            return self::FAILURE;
        }

        if (
            $production['host'] === $sandbox['host']
            && $production['port'] === $sandbox['port']
            && $production['database'] === $sandbox['database']
        ) {
            $this->error('Production and sandbox point to the same MySQL database. Aborting.');
            return self::FAILURE;
        }

        $this->warn('This will overwrite the sandbox database with a fresh copy of production.');
        $this->table(
            ['Environment', 'Host', 'Port', 'Database', 'User'],
            [
                ['production', $production['host'], $production['port'], $production['database'], $production['username']],
                ['sandbox', $sandbox['host'], $sandbox['port'], $sandbox['database'], $sandbox['username']],
            ]
        );

        if (!$this->confirm('Continue?', false)) {
            $this->line('Operation cancelled.');
            return self::SUCCESS;
        }

        $dumpDirectory = storage_path('app/tmp');

        if (!is_dir($dumpDirectory) && !mkdir($dumpDirectory, 0777, true) && !is_dir($dumpDirectory)) {
            $this->error("Unable to create dump directory [{$dumpDirectory}].");
            return self::FAILURE;
        }

        $dumpFile = $dumpDirectory . DIRECTORY_SEPARATOR . 'production-to-sandbox-' . date('Ymd_His') . '.sql';

        try {
            $this->dumpProductionDatabase($production, $dumpFile);
            $this->recreateSandboxDatabase($sandbox);
            $this->importDumpIntoSandbox($sandbox, $dumpFile);

            $this->info('Sandbox database refreshed from production successfully.');

            if (!$this->option('keep-dump') && is_file($dumpFile)) {
                @unlink($dumpFile);
            } elseif ($this->option('keep-dump')) {
                $this->line("Dump retained at [{$dumpFile}].");
            }

            return self::SUCCESS;
        } catch (Throwable $exception) {
            $this->error($exception->getMessage());

            if (is_file($dumpFile) && !$this->option('keep-dump')) {
                @unlink($dumpFile);
            }

            return self::FAILURE;
        }
    }

    protected function dumpProductionDatabase(array $config, string $dumpFile): void
    {
        $this->line('Creating production dump...');

        $command = [
            (string) $this->option('mysqldump'),
            '--host=' . $config['host'],
            '--port=' . $config['port'],
            '--user=' . $config['username'],
            '--single-transaction',
            '--quick',
            '--skip-lock-tables',
            '--routines',
            '--triggers',
            '--default-character-set=' . $config['charset'],
            '--result-file=' . $dumpFile,
            $config['database'],
        ];

        $this->runProcess($command, $config['password']);
    }

    protected function recreateSandboxDatabase(array $config): void
    {
        $this->line('Recreating sandbox database...');

        $sql = sprintf(
            'DROP DATABASE IF EXISTS `%s`; CREATE DATABASE `%s` CHARACTER SET %s COLLATE %s;',
            str_replace('`', '``', $config['database']),
            str_replace('`', '``', $config['database']),
            $config['charset'],
            $config['collation']
        );

        $command = [
            (string) $this->option('mysql'),
            '--host=' . $config['host'],
            '--port=' . $config['port'],
            '--user=' . $config['username'],
            '--execute=' . $sql,
        ];

        $this->runProcess($command, $config['password']);
    }

    protected function importDumpIntoSandbox(array $config, string $dumpFile): void
    {
        $this->line('Importing dump into sandbox...');

        $command = [
            (string) $this->option('mysql'),
            '--host=' . $config['host'],
            '--port=' . $config['port'],
            '--user=' . $config['username'],
            '--default-character-set=' . $config['charset'],
            $config['database'],
        ];

        $process = new Process($command, base_path(), ['MYSQL_PWD' => $config['password']]);
        $process->setInput(fopen($dumpFile, 'r'));
        $process->setTimeout(null);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
    }

    protected function runProcess(array $command, string $password): void
    {
        $process = new Process($command, base_path(), ['MYSQL_PWD' => $password]);
        $process->setTimeout(null);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
    }
}
