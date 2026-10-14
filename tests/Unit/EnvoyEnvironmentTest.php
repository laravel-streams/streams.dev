<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Process;

class EnvoyEnvironmentTest extends TestCase
{
    private string $directory;

    protected function setUp(): void
    {
        parent::setUp();
        $this->directory = sys_get_temp_dir().'/streams-envoy-'.bin2hex(random_bytes(8));
        mkdir($this->directory, 0700);
    }

    protected function tearDown(): void
    {
        if (is_file($this->directory.'/.env')) {
            unlink($this->directory.'/.env');
        }
        rmdir($this->directory);
        parent::tearDown();
    }

    public function test_settings_load_from_the_checkout_without_manual_environment_values(): void
    {
        file_put_contents($this->directory.'/.env', "DEPLOY_SERVER=example@localhost\nDEPLOY_REPOSITORY=https://example.com/site.git\nDEPLOY_PATH=/srv/site/\n");

        $process = $this->runSetup();

        $this->assertTrue($process->isSuccessful(), $process->getErrorOutput());
        $this->assertSame(['example@localhost', 'https://example.com/site.git', '/srv/site', 'master'], json_decode($process->getOutput(), true));
    }

    public function test_missing_settings_stop_setup(): void
    {
        $process = $this->runSetup();

        $this->assertFalse($process->isSuccessful());
        $this->assertStringContainsString('DEPLOY_SERVER', $process->getErrorOutput());
        $this->assertSame('', $process->getOutput());
    }

    public function test_empty_settings_stop_setup(): void
    {
        file_put_contents($this->directory.'/.env', "DEPLOY_SERVER=\nDEPLOY_REPOSITORY=https://example.com/site.git\nDEPLOY_PATH=/srv/site\n");

        $process = $this->runSetup();

        $this->assertFalse($process->isSuccessful());
        $this->assertStringContainsString('DEPLOY_SERVER', $process->getErrorOutput());
    }

    private function runSetup(): Process
    {
        $root = dirname(__DIR__, 2);
        preg_match('/@setup\s*(.*?)@endsetup/s', file_get_contents($root.'/Envoy.blade.php'), $matches);
        $setup = str_replace("require __DIR__.'/vendor/autoload.php';", '', $matches[1]);
        $setup = str_replace('__DIR__', var_export($this->directory, true), $setup);
        $code = 'require '.var_export($root.'/vendor/autoload.php', true).';'.$setup;
        $code .= 'echo json_encode([$server, $repo, $path, $branch]);';
        $environment = array_fill_keys([
            'DEPLOY_SERVER', 'DEPLOY_REPOSITORY', 'DEPLOY_PATH', 'DEPLOY_SLACK_WEBHOOK',
            'DEPLOY_HEALTH_CHECK', 'DEPLOY_PHP_BINARY', 'DEPLOY_MYSQL_BINARY',
            'DEPLOY_COMPOSER_BINARY', 'DEPLOY_DEFAULT_BRANCH',
        ], false);
        $process = new Process([PHP_BINARY, '-d', 'display_errors=stderr', '-r', $code], $this->directory, $environment);
        $process->run();

        return $process;
    }
}
