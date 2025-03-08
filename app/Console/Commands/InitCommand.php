<?php

declare(strict_types=1);

/**
 * Copyright (c) 2021-2025 guanguans<ityaozm@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/guanguans/laravel-skeleton
 */

namespace App\Console\Commands;

use App\Models\Role;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\Isolatable;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * @see https://github.com/RonasIT/laravel-empty-project/blob/development/app/Console/Commands/Init.php
 */
class InitCommand extends Command implements Isolatable
{
    /** @var string */
    public const TEMPLATES_PATH = '.templates';

    /** @var array<string, string> */
    public const RESOURCES_ITEMS = [
        'issue_tracker' => 'Issue Tracker',
        'figma' => 'Figma',
        'sentry' => 'Sentry',
        'datadog' => 'DataDog',
        'argocd' => 'ArgoCD',
        'telescope' => 'Laravel Telescope',
        'nova' => 'Laravel Nova',
    ];

    /** @var array<string, string> */
    public const CONTACTS_ITEMS = [
        'manager' => 'Manager',
        'team_lead' => 'Code Owner/Team Lead',
    ];

    /** @var array<string, string> */
    public const CREDENTIALS_ITEMS = [
        'telescope' => 'Laravel Telescope',
        'nova' => 'Laravel Nova',
    ];

    /** @var list<string> */
    public const DEFAULT_URLS = [
        'telescope',
        'nova',
    ];
    protected $signature = 'init {application-name : The application name }';
    protected $description = 'Initialize required project parameters to run DEV environment';
    protected array $resources = [];
    protected array $adminCredentials = [];
    protected string $appUrl;
    protected array $emptyValuesList = [];
    protected string $readmeContent = '';

    /**
     * @throws \Throwable
     */
    public function handle(): void
    {
        $appName = $this->argument('application-name');
        $kebabName = Str::kebab($appName);

        $this->appUrl = $this->ask('Please enter an application URL', "https://api.dev.$kebabName.com");

        $envFile = (file_exists('.env')) ? '.env' : '.env.example';

        $this->updateConfigFile($envFile, '=', [
            'APP_NAME' => $appName,
        ]);

        $this->updateConfigFile('.env.development', '=', [
            'APP_NAME' => $appName,
            'APP_URL' => $this->appUrl,
        ]);

        $this->components->info('Project initialized successfully!');

        if ($this->confirm('Do you want to generate an admin user?', true)) {
            $this->createAdminUser($kebabName);
        }

        if ($this->confirm('Do you want to generate a README file?', true)) {
            $this->fillReadme();

            if ($this->confirm('Do you need a `Resources & Contacts` part?', true)) {
                $this->fillResourcesAndContacts();
                $this->fillResources();
                $this->fillContacts();
            }

            if ($this->confirm('Do you need a `Prerequisites` part?', true)) {
                $this->fillPrerequisites();
            }

            if ($this->confirm('Do you need a `Getting Started` part?', true)) {
                $this->fillGettingStarted();
            }

            if ($this->confirm('Do you need an `Environments` part?', true)) {
                $this->fillEnvironments();
            }

            if ($this->confirm('Do you need a `Credentials and Access` part?', true)) {
                $this->fillCredentialsAndAccess($kebabName);
            }

            $this->saveReadme();

            $this->info('README generated successfully!');

            if ($this->emptyValuesList) {
                $this->warn('Don`t forget to fill the following empty values:');

                foreach ($this->emptyValuesList as $value) {
                    $this->warn("- $value");
                }
            }
        }
    }

    /**
     * @throws \Throwable
     */
    private function createAdminUser(string $kebabName): void
    {
        $defaultPassword = substr(md5(uniqid()), 0, 8);

        $this->adminCredentials = [
            'name' => $this->ask('Please enter an admin name', 'Admin'),
            'email' => $this->ask('Please enter an admin email', "admin@$kebabName.com"),
            'password' => $this->ask('Please enter an admin password', $defaultPassword),
            'role_id' => Role::ADMIN,
        ];

        $this->publishMigration();
    }

    private function fillReadme(): void
    {
        $appName = $this->argument('application-name');
        $file = $this->loadReadmePart('README.md');

        $this->setReadmeValue($file, 'project_name', $appName);

        $type = $this->choice(
            question: 'What type of application will your API serve?',
            choices: ['Mobile', 'Web', 'Multiplatform'],
            default: 'Multiplatform'
        );

        $this->setReadmeValue($file, 'type', $type);

        $this->readmeContent = $file;
    }

    private function fillResourcesAndContacts(): void
    {
        $filePart = $this->loadReadmePart('RESOURCES_AND_CONTACTS.md');

        $this->updateReadmeFile($filePart);
    }

    private function fillResources(): void
    {
        $filePart = $this->loadReadmePart('RESOURCES.md');
        $laterText = '(will be added later)';

        foreach (self::RESOURCES_ITEMS as $key => $title) {
            $defaultAnswer = (\in_array($key, self::DEFAULT_URLS, true)) ? $this->appUrl."/$key" : 'later';
            $text = "Are you going to use $title? "
                .'Please enter a link or select `later` to do it later, otherwise select `no`.';

            $link = $this->anticipate(
                $text,
                ['later', 'no'],
                $defaultAnswer
            );

            if ('later' === $link) {
                $this->emptyValuesList[] = "$title link";
                $this->setReadmeValue($filePart, "{$key}_link");
                $this->setReadmeValue($filePart, "{$key}_later", $laterText);
            } elseif ('no' !== $link) {
                $this->setReadmeValue($filePart, "{$key}_link", $link);
                $this->setReadmeValue($filePart, "{$key}_later");
            }

            $this->resources[$key] = ('no' !== $link);

            $this->removeTag($filePart, $key, 'no' === $link);
        }

        $this->setReadmeValue($filePart, 'api_link', $this->appUrl);
        $this->updateReadmeFile($filePart);
    }

    private function fillContacts(): void
    {
        $filePart = $this->loadReadmePart('CONTACTS.md');

        foreach (self::CONTACTS_ITEMS as $key => $title) {
            if ($link = $this->ask("Please enter a $title's email", '')) {
                $this->setReadmeValue($filePart, "{$key}_link", $link);
            } else {
                $this->emptyValuesList[] = "$title's email";
            }

            $this->removeTag($filePart, $key);
        }

        $this->updateReadmeFile($filePart);
    }

    private function fillPrerequisites(): void
    {
        $filePart = $this->loadReadmePart('PREREQUISITES.md');

        $this->updateReadmeFile($filePart);
    }

    private function fillGettingStarted(): void
    {
        $gitProjectPath = trim((string) shell_exec('git ls-remote --get-url origin'));
        $projectDirectory = basename($gitProjectPath, '.git');
        $filePart = $this->loadReadmePart('GETTING_STARTED.md');

        $this->setReadmeValue($filePart, 'git_project_path', $gitProjectPath);
        $this->setReadmeValue($filePart, 'project_directory', $projectDirectory);

        $this->updateReadmeFile($filePart);
    }

    private function fillEnvironments(): void
    {
        $filePart = $this->loadReadmePart('ENVIRONMENTS.md');

        $this->setReadmeValue($filePart, 'api_link', $this->appUrl);
        $this->updateReadmeFile($filePart);
    }

    private function fillCredentialsAndAccess(string $kebabName): void
    {
        $filePart = $this->loadReadmePart('CREDENTIALS_AND_ACCESS.md');

        if ($this->adminCredentials) {
            $this->setReadmeValue($filePart, 'admin_email', $this->adminCredentials['email']);
            $this->setReadmeValue($filePart, 'admin_password', $this->adminCredentials['password']);
        }

        $this->removeTag($filePart, 'admin_credentials', !$this->adminCredentials);

        foreach (self::CREDENTIALS_ITEMS as $key => $title) {
            if (!Arr::get($this->resources, $key)) {
                $this->removeTag($filePart, "{$key}_credentials", true);

                continue;
            }

            if ($this->confirm("Is $title's admin the same as default one?", true)) {
                $email = $this->adminCredentials['email'];
                $password = $this->adminCredentials['password'];
            } else {
                $defaultPassword = substr(md5(uniqid()), 0, 8);

                $email = $this->ask("Please enter a $title's admin email", "admin@$kebabName.com");
                $password = $this->ask("Please enter a $title's admin password", $defaultPassword);
            }

            $this->setReadmeValue($filePart, "{$key}_email", $email);
            $this->setReadmeValue($filePart, "{$key}_password", $password);
            $this->removeTag($filePart, "{$key}_credentials");
        }

        $this->updateReadmeFile($filePart);
    }

    private function addQuotes(string $string): string
    {
        return (Str::contains($string, ' ')) ? "\"$string\"" : $string;
    }

    /**
     * @throws \Throwable
     */
    private function publishMigration(): void
    {
        $data = view('add_default_user')->with($this->adminCredentials)->render();
        $fileName = Carbon::now()->format('Y_m_d_His').'_add_default_user.php';

        file_put_contents("database/migrations/$fileName", "<?php\n\n$data");
    }

    private function updateConfigFile(string $fileName, string $separator, array $data): void
    {
        $parsed = file_get_contents($fileName);

        $lines = explode("\n", $parsed);

        /** @noinspection AlterInForeachInspection */
        foreach ($lines as &$line) {
            foreach ($data as $key => $value) {
                if (Str::contains($line, "$key$separator")) {
                    $exploded = explode($separator, $line);
                    $key = array_shift($exploded);
                    $value = $this->addQuotes($value);
                    $line = "$key$separator$value";
                }
            }
        }

        $ymlSettings = implode("\n", $lines);

        file_put_contents($fileName, $ymlSettings);
    }

    private function loadReadmePart(string $fileName): string
    {
        return file_get_contents(self::TEMPLATES_PATH.\DIRECTORY_SEPARATOR.$fileName);
    }

    private function updateReadmeFile(string $filePart): void
    {
        $filePart = preg_replace('#(\n){3,}#', "\n", $filePart);

        $this->readmeContent .= "\n".$filePart;
    }

    private function removeTag(string &$text, string $tag, bool $removeWholeString = false): void
    {
        $regex = ($removeWholeString)
            ? "#({{$tag}})(.|\\s)*?({/$tag})#"
            : "# {0,1}{(/*)$tag}#";

        $text = preg_replace($regex, '', $text);
    }

    private function setReadmeValue(string &$file, string $key, string $value = ''): void
    {
        $file = str_replace(":$key", $value, $file);
    }

    private function saveReadme(): void
    {
        file_put_contents('README.md', $this->readmeContent);
    }
}
