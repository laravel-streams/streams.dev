<?php

namespace App\Support;

use Illuminate\Support\Str;

class DocumentationMarkdown
{
    public static function toHtml(?string $markdown): string
    {
        if ($markdown === null || $markdown === '') {
            return '';
        }

        $html = (string) Str::markdown($markdown);

        $html = static::ensureCodeLanguageClasses($html);

        return static::wrapCodePanels($html);
    }

    /**
     * Ensure fenced code blocks have Prism-compatible language-* classes.
     */
    public static function ensureCodeLanguageClasses(string $html): string
    {
        $html = preg_replace_callback(
            '/<pre><code class="language-([a-z0-9+#.-]+)">/i',
            fn ($m) => '<pre class="language-'.$m[1].'"><code class="language-'.$m[1].'">',
            $html
        );

        $html = preg_replace_callback(
            '/<pre><code>(.*?)<\/code><\/pre>/s',
            function ($m) {
                $decoded = html_entity_decode($m[1], ENT_QUOTES | ENT_HTML5, 'UTF-8');

                if (preg_match('/^(\{|\[)/', ltrim($decoded))) {
                    return '<pre class="language-json"><code class="language-json">'.$m[1].'</code></pre>';
                }

                if (preg_match('/^\s*(<\?php|namespace |use |class |public function|Route::|Streams::)/m', $decoded)) {
                    return '<pre class="language-php"><code class="language-php">'.$m[1].'</code></pre>';
                }

                if (preg_match('/^\s*(composer |npm |php artisan|curl |cd |git )/m', $decoded)) {
                    return '<pre class="language-bash"><code class="language-bash">'.$m[1].'</code></pre>';
                }

                return '<pre class="language-none"><code class="language-none">'.$m[1].'</code></pre>';
            },
            $html
        );

        return $html;
    }

    /**
     * Wrap fenced code blocks in the shared Mac-style code panel.
     */
    public static function wrapCodePanels(string $html): string
    {
        return preg_replace_callback(
            '/<pre class="language-([a-z0-9+#.-]+)">(<code class="language-\1">.*?<\/code>)<\/pre>/is',
            function (array $m) {
                $rawLang = $m[1];
                $lang = static::normalizeCodeLanguage($rawLang);
                $title = static::codePanelTitle($lang);
                $inner = $m[2];

                if ($rawLang !== $lang) {
                    $inner = str_replace(
                        'class="language-'.$rawLang.'"',
                        'class="language-'.$lang.'"',
                        $inner
                    );
                }

                return '<figure class="st-code">'
                    .'<div class="st-code__chrome">'
                    .'<span class="st-code__lights" aria-hidden="true"><span></span><span></span><span></span></span>'
                    .'<figcaption class="st-code__title">'.htmlspecialchars($title, ENT_QUOTES | ENT_HTML5, 'UTF-8').'</figcaption>'
                    .'</div>'
                    .'<pre class="language-'.$lang.'">'.$inner.'</pre>'
                    .'</figure>';
            },
            $html
        ) ?? $html;
    }

    private static function normalizeCodeLanguage(string $lang): string
    {
        return match ($lang) {
            'shell', 'sh' => 'bash',
            'blade' => 'markup',
            default => $lang,
        };
    }

    private static function codePanelTitle(string $lang): string
    {
        return match ($lang) {
            'php' => 'example.php',
            'json' => 'example.json',
            'bash' => 'terminal',
            'markup' => 'example.html',
            'yaml' => 'example.yaml',
            'none' => 'code',
            default => 'example.'.$lang,
        };
    }
}
