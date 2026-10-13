<?php

namespace App\Console\Commands;

use App\Support\DocsRegistry;
use Illuminate\Console\Command;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

class ValidateDocsRegistry extends Command
{
    protected $signature = 'docs:validate-registry';

    protected $description = 'Validate docs registry availability, package, tracking task, and editorial frontmatter';

    public function handle(DocsRegistry $registry): int
    {
        $files = $registry->files();
        $invalid = 0;

        foreach ($files as $file) {
            $text = file_get_contents($file);
            $relative = str_replace(base_path().'/', '', $file);

            if ($text === false || ! preg_match('/\A---\R(.*?)\R---(?:\R|\z)/s', $text, $match)) {
                $errors = ['Missing YAML frontmatter.'];
            } else {
                try {
                    $frontmatter = Yaml::parse($match[1]);
                    $errors = is_array($frontmatter) ? DocsRegistry::errors($frontmatter) : ['Frontmatter must be a mapping.'];
                } catch (ParseException $exception) {
                    $errors = ['Invalid YAML: '.$exception->getMessage()];
                }
            }

            foreach ($errors as $error) {
                $this->error($relative.': '.$error);
            }

            $invalid += $errors !== [] ? 1 : 0;
        }

        if ($files === []) {
            $this->error('No documentation pages found.');

            return self::FAILURE;
        }

        if ($invalid > 0) {
            $this->error($invalid.' of '.count($files).' documentation pages have invalid registry frontmatter.');

            return self::FAILURE;
        }

        $this->info('Validated '.count($files).' documentation registry entries.');

        return self::SUCCESS;
    }
}
