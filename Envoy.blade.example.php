@servers(['local' => 'localhost', 'testing' => 'user@ip -p port'])

@setup
$localPhp = '/opt/homebrew/opt/php@8.1/bin/php';
$localComposer = "$localPhp /opt/homebrew/bin/composer --no-interaction --ansi -v";
$localArtisan = "$localPhp artisan --ansi -v";

$testingPhp = '/usr/bin/php8.1';
$testingComposer = "COMPOSER_ALLOW_SUPERUSER=1 $testingPhp /usr/local/bin/composer2 --no-interaction --ansi -v";
$testingArtisan = "$testingPhp artisan --ansi -v";
@endsetup

@task('local', ['on' => ['local'], 'parallel' => false])
cd /Users/yaozm/Documents/wwwroot/clothing-gm
{{ $localArtisan }} clear:all
{{ $localComposer }} dump-autoload --optimize
{{ $localArtisan }} schedule-monitor:sync

{{ $localArtisan }} opcache:clear
{{ $localArtisan }} opcache:config
{{ $localArtisan }} opcache:status
{{ $localArtisan }} opcache:compile
@endtask

@task('testing', ['on' => ['testing'], 'confirm' => true, 'parallel' => false])
cd /data/vhosts/gmtools-test.dreams.howanjoy.com/
git checkout -- .
git switch testing
git pull origin testing
{{ $testingComposer }} install

{{ $testingArtisan }} clear:all
{{ $testingArtisan }} optimize:all
{{ $testingArtisan }} schedule-monitor:sync

{{ $testingArtisan }} opcache:clear
{{ $testingArtisan }} opcache:config
{{ $testingArtisan }} opcache:status
{{ $testingArtisan }} opcache:compile
@endtask
