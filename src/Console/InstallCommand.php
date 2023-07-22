<?php

namespace Akhilesh\Neodash\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Akhilesh\Neodash\Traits\Console\PublishFilesTrait;
use Symfony\Component\Process\Process;

class InstallCommand extends Command
{
    use PublishFilesTrait;

    protected $signature = 'neodash:install
                            {install=default}
                            {--module=*default}
                            {--composer=global : Absolute path to the Composer binary which should be used to install packages}';
    protected $module;
    protected $filesystem;
    protected $installType;
    protected $replaceFiles = true;
    protected $migrateFresh = true;
    protected $seedDatabase = true;

    public function __construct()
    {
        parent::__construct();
        $this->filesystem = new Filesystem();
    }

    public function handle()
    {
        if ($this->filesystem->missing(config_path('site.php'))) {
            $this->filesystem->copy(__DIR__ . '/../../config/config.php', config_path('site.php'));

            $this->info('Configuration file `site.php` is copied. Please specify modules / packages if you want.');
            $this->info('You can change the default packages / modules / settings Or you can continue with defaults.');
            if ($this->confirm('Do you wish to continue with default?', true)) {
                //
            } else {
                $this->error('Quitting!! Run `neodash:install` again when all set.');
                exit;
            }
        }

        if (!config('site.install.command', true)) {
            $this->error('SORRY !! Install command has been disabled.');
            exit;
        }

        $this->filesystem->cleanDirectory(resource_path('views/profile'));
        $this->askQuestions();

        if ($this->installType == 'fresh') {
            $this->filesystem->cleanDirectory(database_path('migrations'));
            $this->filesystem->cleanDirectory(database_path('seeders'));
            $this->filesystem->cleanDirectory(app_path('Models'));
            $this->filesystem->cleanDirectory(resource_path('views/admin'));
        }

        $this->publishFiles();
        $this->migrateDB();
        $this->seedDB();
        $this->installOtherPackages();
        $this->call('storage:link');

        $this->info('Neodash Setup is successfully installed.');
        $this->info('You will find the login credentials in documentation on github or just check the database seeder');
        $this->info('https://github.com/akhilh2o/neodash');
    }

    public function askQuestions()
    {
        $this->module = config('site.install.modules', ['default']);

        $this->installType = $this->argument('install');
        if ($this->installType != 'fresh') {
            $this->replaceFiles = $this->confirm('Do you wish to replace existing files?', true);
            $this->migrateFresh = $this->confirm('How do you run fresh migrate?', true);
            $this->seedDatabase = $this->confirm('Do you wish to seed database?', true);
        }
    }

    public function publishFiles()
    {
        if ($this->installType == 'fresh' || in_array('default', $this->module)) {
            $this->call('breeze:install', [
                'stack' => 'blade'
            ]);
        }

        $options['--force'] = true;
        if ($this->installType != 'fresh') {
            $options = [];
        }
        if ($this->replaceFiles) {
            $options['--force'] = true;
        }

        if (in_array('default', $this->module)) {
            $options['--tag'] = 'neodash-default';
            $this->publishDefault($options);
        }
        if (in_array('faqs', $this->module)) {
            $options['--tag'] = 'neodash-faqs';
            $this->publishFaqs($options);
        }
        if (in_array('pages', $this->module)) {
            $options['--tag'] = 'neodash-pages';
            $this->publishPages($options);
        }
        if (in_array('testimonials', $this->module)) {
            $options['--tag'] = 'neodash-testimonials';
            $this->publishTestimonials($options);
        }

        $this->line('Neodash scaffolding installed successfully.');
        $this->newLine();
    }

    public function migrateDB()
    {
        if ($this->migrateFresh) {
            $this->call('migrate:fresh');
            $this->line('Fresh database successfully migrated');
        } else {
            $this->call('migrate');
            $this->line('Database successfully migrated');
        }
        $this->newLine();
    }

    public function seedDB()
    {
        if (!$this->seedDatabase) {
            return false;
        }

        $this->call('db:seed');
        $this->line('Database successfully seeded');
        $this->newLine();
    }

    public function installOtherPackages()
    {
        $packages = config('site.install.packages', []);
        if (!count($packages)) {
            return true;
        }

        $this->requireComposerPackages($packages);
        $this->newLine();
    }

    protected function requireComposerPackages($packages)
    {
        $composer = $this->option('composer');

        if ($composer !== 'global') {
            $command = ['php', $composer, 'require'];
        }

        $command = array_merge(
            $command ?? ['composer', 'require'],
            is_array($packages) ? $packages : func_get_args()
        );

        (new Process($command, base_path(), ['COMPOSER_MEMORY_LIMIT' => '-1']))
            ->setTimeout(null)
            ->run(function ($type, $output) {
                $this->output->write($output);
            });
    }
}
