<?php
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
// production
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
// develop
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
task('build', function () {
    run('cd {{release_path}} && build');
});

desc('Upload .env file');
task('env:upload', function() {
    upload('.env', '{{release_path}}/.env');
});

desc('Reset opcache');
task('opcache:reset', function () {
    run('{{bin/php}} -r \'opcache_reset();\'');
});

desc('Restart php-fpm');
task('php-fpm:restart', function () {
    run('sudo systemctl restart php-fpm.service');
});

desc('Reload supervisor');
task('supervisor:reload', function () {
    run('sudo supervisorctl reload');
});

// [Optional] if deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');
after('deploy:shared', 'env:upload');
// after('deploy:symlink', 'opcache:reset');
// after('deploy:symlink', 'php-fpm:restart');
// after('deploy:symlink', 'supervisor:reload');

// Migrate database before symlink new release.
before('deploy:symlink', 'artisan:migrate');

