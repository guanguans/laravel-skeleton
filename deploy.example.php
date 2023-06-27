<?php

declare(strict_types=1);

namespace Deployer;

require 'recipe/laravel.php';

// Project name
set('application', 'laravel');

// Project repository
set('repository', 'https://github.com/guanguans/laravel-skeleton.git');

// Allow anonymous stats
set('allow_anonymous_stats', false);

// Keep releases
set('keep_releases', 10);

// [Optional] Allocate tty for git clone. Default value is false.
set('git_tty', true);

// Composer options
set('composer_options', '{{composer_action}} --verbose --prefer-dist --no-progress --no-interaction --optimize-autoloader --no-suggest');

// Writable use sudo
set('writable_use_sudo', false);

// Shared files/dirs between deploys keep_releases
add('shared_files', []);
add('shared_dirs', []);

// Writable dirs by web server
add('writable_dirs', []);
set('writable_dirs', []);

// Hosts
// production ./vendor/bin/dep deploy production -vvv
host('127.0.0.1')
    ->stage('production')
    ->set('branch', 'master')
    ->set('deploy_path', '/home/vagrant/wwwroot/laravel-deployer')
    ->user('vagrant')
    ->port(2222)
    ->become('vagrant')
    ->identityFile('~/.ssh/id_rsa')
    ->forwardAgent(true)
    ->multiplexing(true)
    ->addSshOption('UserKnownHostsFile', '/dev/null')
    ->addSshOption('StrictHostKeyChecking', 'no');
// develop ./vendor/bin/dep deploy develop -vvv
host('192.168.10.10')
    ->stage('develop')
    ->set('branch', 'dev')
    ->set('deploy_path', '/home/vagrant/wwwroot/laravel-deployer')
    ->user('vagrant')
    ->port(2222)
    ->become('vagrant')
    ->identityFile('~/.ssh/id_rsa')
    ->forwardAgent(true)
    ->multiplexing(true)
    ->addSshOption('UserKnownHostsFile', '/dev/null')
    ->addSshOption('StrictHostKeyChecking', 'no');

// Tasks
desc('Building');
task('build', static function (): void {
    run('cd {{release_path}} && build');
});

desc('Upload .env file');
task('env:upload', static function (): void {
    upload('.env', '{{release_path}}/.env');
});

desc('Reset opcache');
task('opcache:reset', static function (): void {
    run('{{bin/php}} -r \'opcache_reset();\'');
});

desc('Restart php-fpm');
task('php-fpm:restart', static function (): void {
    run('sudo systemctl restart php-fpm.service');
});

desc('Reload supervisor');
task('supervisor:reload', static function (): void {
    run('sudo supervisorctl reload');
});

desc('Deployment succeed');
task('deployer:succeed', static function (): void {
    writeln('<info>Successfully deployed!</info>');
    run('{{bin/php}} {{release_path}}/artisan deployer:succeed');
});

desc('Deployment failed');
task('deployer:failed', static function (): void {
    writeln('<info>Failed deployed!</info>');
    run('{{bin/php}} {{release_path}}/artisan deployer:failed');
});

// [Optional] if deploy fails automatically unlock.
after('success', 'deployer:succeed');

after('deploy:failed', 'deploy:unlock');
after('deploy:failed', 'deployer:failed');

after('deploy:shared', 'env:upload');

// after('deploy:symlink', 'opcache:reset');
// after('deploy:symlink', 'php-fpm:restart');
// after('deploy:symlink', 'supervisor:reload');

// Migrate database before symlink new release.
before('deploy:symlink', 'artisan:migrate');
