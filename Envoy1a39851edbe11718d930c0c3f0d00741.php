<?php $DB_HOST = isset($DB_HOST) ? $DB_HOST : null; ?>
<?php $DB_DATABASE = isset($DB_DATABASE) ? $DB_DATABASE : null; ?>
<?php $DB_PASSWORD = isset($DB_PASSWORD) ? $DB_PASSWORD : null; ?>
<?php $DB_USERNAME = isset($DB_USERNAME) ? $DB_USERNAME : null; ?>
<?php $cleanup = isset($cleanup) ? $cleanup : null; ?>
<?php $backup = isset($backup) ? $backup : null; ?>
<?php $release = isset($release) ? $release : null; ?>
<?php $update = isset($update) ? $update : null; ?>
<?php $branch = isset($branch) ? $branch : null; ?>
<?php $env = isset($env) ? $env : null; ?>
<?php $date = isset($date) ? $date : null; ?>
<?php $defaultBranch = isset($defaultBranch) ? $defaultBranch : null; ?>
<?php $composer = isset($composer) ? $composer : null; ?>
<?php $mysql = isset($mysql) ? $mysql : null; ?>
<?php $php = isset($php) ? $php : null; ?>
<?php $healthUrl = isset($healthUrl) ? $healthUrl : null; ?>
<?php $slack = isset($slack) ? $slack : null; ?>
<?php $path = isset($path) ? $path : null; ?>
<?php $repo = isset($repo) ? $repo : null; ?>
<?php $server = isset($server) ? $server : null; ?>
<?php $e = isset($e) ? $e : null; ?>
<?php $dotenv = isset($dotenv) ? $dotenv : null; ?>
<?php
require __DIR__.'/vendor/autoload.php';

$dotenv = \Dotenv\Dotenv::createImmutable(__DIR__);

try {
$dotenv->load();
$dotenv->required(['DEPLOY_SERVER', 'DEPLOY_REPOSITORY', 'DEPLOY_PATH'])->notEmpty();
} catch ( Exception $e )  {
echo $e->getMessage();
}

$server = getenv('DEPLOY_SERVER');
$repo = getenv('DEPLOY_REPOSITORY');
$path = getenv('DEPLOY_PATH');
$slack = getenv('DEPLOY_SLACK_WEBHOOK');
$healthUrl = getenv('DEPLOY_HEALTH_CHECK');

$php = getenv('DEPLOY_PHP_BINARY') ?: 'php';
$mysql = getenv('DEPLOY_MYSQL_BINARY') ?: 'mysql';
$composer = getenv('DEPLOY_COMPOSER_BINARY') ?: 'composer';
$defaultBranch = getenv('DEPLOY_DEFAULT_BRANCH') ?: 'master';

if ( substr($path, 0, 1) !== '/' ) throw new Exception('Your deployment must begin with /');

$date = ( new DateTime )->format('YmdHis');
$env = isset($env) ? $env : "production";
$branch = isset($branch) ? $branch : $defaultBranch;
$update = isset($update) ? $update : false;
$path = rtrim($path, '/');
$release = $path.'/'.$date;
?>

<?php $__container->servers(['web' => $server]); ?>

<?php $__container->startTask('init'); ?>
if [ ! -d <?php echo $path; ?>/current ]; then
cd <?php echo $path; ?>

git clone <?php echo $repo; ?> --branch=<?php echo $branch; ?> --depth=1 -q <?php echo $release; ?>

echo "Repository cloned"
if [ ! -d <?php echo $path; ?>/storage ]; then
mv <?php echo $release; ?>/storage <?php echo $path; ?>/storage
else
rm -Rf <?php echo $release; ?>/storage
fi
ln -s <?php echo $path; ?>/storage <?php echo $release; ?>/storage
echo "Storage directory set up"
if [ ! -d <?php echo $path; ?>/.env ]; then
cp <?php echo $path; ?>/.env.example <?php echo $path; ?>/.env
<?php echo $php; ?> <?php echo $path; ?>/artisan key:generate --ansi
fi
ln -s <?php echo $path; ?>/.env <?php echo $release; ?>/.env
echo "Environment file set up"
rm -rf <?php echo $release; ?>

echo "Deployment path initialised. Run 'envoy run deploy' now."
else
echo "Deployment path already initialised (current symlink exists)!"
fi
<?php $__container->endTask(); ?>

<?php $__container->startMacro('push'); ?>
current_pull
current_refresh
health_check_ping
<?php $__container->endMacro(); ?>

<?php $__container->startMacro('revert'); ?>
current_revert
current_refresh
health_check_ping
<?php $__container->endMacro(); ?>

<?php $__container->startMacro('refresh'); ?>
current_refresh
<?php $__container->endMacro(); ?>

<?php $__container->startMacro('build'); ?>
current_build
<?php $__container->endMacro(); ?>

<?php $__container->startMacro('deploy'); ?>
<?php if ( isset($backup) && $backup ): ?>
    deployment_backup
<?php endif; ?>
deployment_start
deployment_links
deployment_composer
deployment_migrate
deployment_build
deployment_finish
deployment_refresh
deployment_complete
health_check_ping
<?php if ( isset($cleanup) && $cleanup ): ?>
    deployment_cleanup
<?php endif; ?>
<?php $__container->endMacro(); ?>

<?php $__container->startMacro('cleanup'); ?>
deployment_cleanup
<?php $__container->endMacro(); ?>

<?php $__container->startMacro('rollback'); ?>
deployment_rollback
health_check_ping
<?php $__container->endMacro(); ?>

<?php $__container->startMacro('health_check'); ?>
health_check_ping
<?php $__container->endMacro(); ?>

<?php $__container->startTask('current_pull'); ?>
cd <?php echo $path; ?>/current
git pull
<?php $__container->endTask(); ?>

<?php $__container->startTask('current_revert'); ?>
cd <?php echo $path; ?>/current
echo "Revert started"
git reset --keep HEAD@{1}
<?php $__container->endTask(); ?>

<?php $__container->startTask('deployment_start'); ?>
cd <?php echo $path; ?>

echo "Deployment (<?php echo $date; ?>) started"

git clone <?php echo $repo; ?> --branch=<?php echo $branch; ?> --depth=1 -q <?php echo $release; ?>

echo "Repository [<?php echo $branch; ?>] cloned"
<?php $__container->endTask(); ?>

<?php $__container->startTask('deployment_backup'); ?>
cd <?php echo $path; ?>/current
echo "Database backup started"

DB_USERNAME=$(grep DB_USERNAME .env | xargs)
IFS='=' read -ra DB_USERNAME <<< "$DB_USERNAME"
DB_USERNAME=${DB_USERNAME[1]}

DB_PASSWORD=$(grep DB_PASSWORD .env | xargs)
IFS='=' read -ra DB_PASSWORD <<< "$DB_PASSWORD"
DB_PASSWORD=${DB_PASSWORD[1]}

DB_DATABASE=$(grep DB_DATABASE .env | xargs)
IFS='=' read -ra DB_DATABASE <<< "$DB_DATABASE"
DB_DATABASE=${DB_DATABASE[1]}

DB_HOST=$(grep DB_HOST .env | xargs)
IFS='=' read -ra DB_HOST <<< "$DB_HOST"
DB_HOST=${DB_HOST[1]}

/bin/cat <<EOM >database.cnf
    [client]
    password="${DB_PASSWORD[1]}"
    EOM

    mysqldump --defaults-extra-file=<?php echo $path; ?>/current/database.cnf -u ${DB_USERNAME} ${DB_DATABASE} > database.sql

    rm database.cnf

    echo "Database backup complete"
    <?php $__container->endTask(); ?>

    <?php $__container->startTask('deployment_links'); ?>
    cd <?php echo $path; ?>

    rm -rf <?php echo $release; ?>/storage
    ln -s <?php echo $path; ?>/storage <?php echo $release; ?>/storage
    echo "Storage directories set up"
    ln -s <?php echo $path; ?>/.env <?php echo $release; ?>/.env
    echo "Environment file set up"
    <?php $__container->endTask(); ?>

    <?php $__container->startTask('deployment_composer'); ?>
    echo "Installing composer dependencies."
    cd <?php echo $release; ?>

    <?php echo $composer; ?> install --no-interaction --quiet --no-dev --prefer-dist --optimize-autoloader
    <?php $__container->endTask(); ?>

    <?php $__container->startTask('deployment_migrate'); ?>
    <?php echo $php; ?> <?php echo $release; ?>/artisan migrate --env=<?php echo $env; ?> --force --no-interaction --path=vendor/anomaly/streams-platform/migrations/application
    <?php echo $php; ?> <?php echo $release; ?>/artisan migrate --env=<?php echo $env; ?> --all-addons --force --no-interaction
    <?php echo $php; ?> <?php echo $release; ?>/artisan migrate --env=<?php echo $env; ?> --force --no-interaction
    <?php $__container->endTask(); ?>

    <?php $__container->startTask('deployment_build'); ?>
    <?php echo $php; ?> <?php echo $release; ?>/artisan build --quiet
    echo "System built"
    <?php $__container->endTask(); ?>

    <?php $__container->startTask('deployment_rebuild'); ?>
    <?php echo $php; ?> <?php echo $release; ?>/artisan build --quiet
    echo "System built"
    <?php $__container->endTask(); ?>

    <?php $__container->startTask('deployment_refresh'); ?>
    #<?php echo $php; ?> <?php echo $release; ?>/artisan refresh --quiet
    #echo "System refreshed"
    <?php $__container->endTask(); ?>

    <?php $__container->startTask('current_refresh'); ?>
    #<?php echo $php; ?> <?php echo $path; ?>/current/artisan refresh --quiet
    #echo "System refreshed"
    <?php $__container->endTask(); ?>
    
    <?php $__container->startTask('current_build'); ?>
    echo "System building."
    <?php echo $php; ?> <?php echo $path; ?>/current/artisan build
    echo "System built."
    <?php $__container->endTask(); ?>

    <?php $__container->startTask('deployment_finish'); ?>
    ln -s <?php echo $release; ?> <?php echo $path; ?>/current
    <?php $__container->endTask(); ?>

    <?php $__container->startTask('deployment_complete'); ?>
    echo "Deployment (<?php echo $date; ?>) finished"
    <?php $__container->endTask(); ?>

    <?php $__container->startTask('deployment_cleanup'); ?>
    cd <?php echo $path; ?>

    find . -maxdepth 1 -name "20*" | sort | head -n -4 | xargs rm -Rf
    echo "Cleaned up old deployments"
    <?php $__container->endTask(); ?>

    <?php $__container->startTask('deployment_rollback'); ?>
    cd <?php echo $path; ?>

    ln -nfs <?php echo $path; ?>/$(find . -maxdepth 1 -name "20*" | sort  | tail -n 2 | head -n1) <?php echo $path; ?>/current
    <?php echo $php; ?> <?php echo $release; ?>/artisan migrate:rollback --env=<?php echo $env; ?> --force --no-interaction
    echo "Rolled back to $(find . -maxdepth 1 -name "20*" | sort  | tail -n 2 | head -n1)"
    <?php $__container->endTask(); ?>

    <?php $__container->startTask('health_check_ping'); ?>
    <?php if ( ! empty($healthUrl) ): ?>
        if [ "$(curl --write-out "%{http_code}\n" --silent --output /dev/null <?php echo $healthUrl; ?>)" == "200" ]; then
        printf "\033[0;32mHealth check to <?php echo $healthUrl; ?> OK\033[0m\n"
        else
        printf "\033[1;31mHealth check to <?php echo $healthUrl; ?> FAILED\033[0m\n"
        fi
    <?php else: ?>
        echo "Ping Skipped: [DEPLOY_HEALTH_CHECK] URL not set"
<?php endif; ?>
<?php $__container->endTask(); ?>

<?php /*
<?php $_vars = get_defined_vars(); $__container->finished(function() use ($_vars) { extract($_vars); 
	 if (! isset($task)) $task = null; Laravel\Envoy\Slack::make($slack, '#deployments', "Deployment [{$branch}] on {$server}: {$date} complete")->task($task)->send();
}); ?>
*/ ?>
