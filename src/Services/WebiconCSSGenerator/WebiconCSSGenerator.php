<?php

namespace Src\Services\WebiconCSSGenerator;

use Slim\Container;
use Src\Helpers\CSSHelpers;
use Src\Helpers\FileHelpers;
use Src\Services\WebiconCSSGenerator\Exceptions\InvalidSettingsException;
use Src\Services\WebiconCSSGenerator\Models\Family;
use Src\Services\WebiconCSSGenerator\Models\Style;

/**
 * Class WebiconCSSGenerator
 *
 * Generates CSS code for embedding webicons.
 *
 * @author Finesse
 * @package Src\Services\WebiconCSSGenerator
 */
class WebiconCSSGenerator
{
    /**
     * Path to the directory which is the site root.
     */
    const SITE_ROOT_PATH = __DIR__ . '/../../../public';

    /**
     * Name of the fonts directory in the site root directory. May contain slashes for subdirectories.
     */
    const FONTS_DIRECTORY = 'icons';

    /**
     * @var Family[] List of available font families. The array keys are the family names.
     */
    protected $families = [];

    /**
     * @var string The URL of the root fonts directory. Doesn't end with slash.
     */
    protected $fontsDirectoryURL;

    /**
     * @var string The URL of the hostname with port if available. Doesn't end with slash.
     */
    protected $hostNameURL;

    /**
     * @param Family[] $families List of available font families
     * @param string $rootURL The site root URL. With or without a domain and a protocol.
     * @throws \InvalidArgumentException
     */
    public function __construct(array $families = [], string $rootURL = '')
    {
        foreach ($families as $index => $family) {
            if (!($family instanceof Family)) {
                throw new \InvalidArgumentException(sprintf(
                    'Argument $families[%s] expected to be a Family instance, %s given.',
                    $index,
                    is_object($family) ? 'a '.get_class($family).' instance' : gettype($family)
                ));
            }

            $this->families[$family->name] = $family;
        }

        $this->hostNameURL = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? 'https://' : 'http://' . $_SERVER['SERVER_NAME'] . ($_SERVER['SERVER_PORT'] == '80' || $_SERVER['SERVER_PORT'] == '443' ? '' :  ':' . $_SERVER['SERVER_PORT']);

        $this->fontsDirectoryURL = $this->hostNameURL.rtrim($rootURL, '/').DIRECTORY_SEPARATOR.static::FONTS_DIRECTORY;
    }

    /**
     * Creates the class instance getting fonts information from settings.
     *
     * @param array $settings Information about available fonts from settings (see an example in the readme)
     * @param string $rootURL The site root URL. With or without a domain and a protocol.
     * @return static
     * @throws InvalidSettingsException
     */
    public static function createFromSettings(array $settings, string $rootURL = ''): self
    {
        $families = [];

        foreach ($settings as $familyName => $familySettings) {
            $families[] = Family::createFromSettings($familyName, $familySettings);
        }

        return new static($families, $rootURL);
    }

    /**
     * Makes CSS code for the given families.
     *
     * @param string[][] $requestedFamilies The list of families. The indexes are families names, the values are lists
     *     of family styles. The styles must have format `[0-9]+i?`. Example:
     * <pre>
     *  [
     *      'Open Sans' => ['400', '700'],
     *      'Roboto'    => ['100', '100i', '400', '400i']
     *  ]
     * </pre>
     * @param string $fontDisplay Font-display css property
     * @param string $fontStretch Font-stretch css property
     * @return string
     * @throws \InvalidArgumentException
     */
    public function makeCSS(array $requestedFamilies, string $fontDisplay = '', string $fontStretch = ''): string
    {
        $cssCode = '';

        foreach ($requestedFamilies as $fontName => $styles) {
            $cssCode .= $this->makeFontFamilyCSS($fontName, $styles, $fontDisplay, $fontStretch);
        }

        return $cssCode;
    }

    /**
     * Finds the font family.
     *
     * @param string $name Font family name
     * @return Family|null
     */
    protected function getFontFamily(string $name)
    {
        return $this->families[$name] ?? null;
    }

    /**
     * Makes CSS code for the given font family.
     *
     * @param string $name Family name
     * @param string[] $styles Font styles. The styles must have format `[0-9]+i?`.
     * @param string $fontDisplay Font-display css property value
     * @param string $fontStretch Font-stretch css property value
     * @return string
     * @throws \InvalidArgumentException
     */
    protected function makeFontFamilyCSS(string $name, array $styles = ['400'], string $fontDisplay = '', string $fontStretch = ''): string
    {
        $cssCode = '';
        $readyStyles = [];

        foreach ($styles as $style) {
            if (isset($readyStyles[$style])) {
                continue;
            }

            $styleCssCode = $this->makeFontStyleCSS($name, $style, $fontDisplay, $fontStretch);
            if ($styleCssCode !== '') {
                $cssCode .= $styleCssCode."\n";
            }

            $readyStyles[$style] = true;
        }

        return $cssCode;
    }

    /**
     * Makes CSS code for the given font style.
     *
     * @param string $familyName Font family name
     * @param string $styleName Font style. The styles must have format `[0-9]+i?`.
     * @param string $fontDisplay Font-display css property value
     * @param string $fontStretch Font-stretch css property value
     * @return string
     * @throws \InvalidArgumentException
     */
    protected function makeFontStyleCSS(string $familyName, string $styleName, string $fontDisplay = '', string $fontStretch = ''): string
    {
        // Does the given family exist?
        $family = $this->getFontFamily($familyName);
        if (!$family) {
            return '';
        }

        // Does the given style exist in the given family?
        $style = $family->getStyle($styleName);
        if (!$style) {
            return '';
        }

        // Does the style has any font files?
        $files = $this->getFontFilesURLs($family, $style);
        $files = str_replace(self::FONTS_DIRECTORY, (new Container(['settings' => require __DIR__ . '/../../../config/settings.php']))->get('settings')['directory_alias']['icons'], $files);
        if (empty($files)) {
            return '';
        }

        // Building CSS code
        $sources = [];
        if (!($style->forbidLocalSource ?? $family->forbidLocalSource ?? false)) {
            foreach ($this->getLocalFontNames($family, $style) as $name) {
                $sources[] = "local(" . CSSHelpers::formatString($name) . ")";
            }
        }
        if (isset($files['eot'])) {
            $sources[] = "url(".CSSHelpers::formatString($files['eot'].'?#iefix').") format('embedded-opentype')";
        }
        if (isset($files['woff2'])) {
            $sources[] = "url(".CSSHelpers::formatString($files['woff2']).") format('woff2')";
        }
        if (isset($files['woff'])) {
            $sources[] = "url(".CSSHelpers::formatString($files['woff']).") format('woff')";
        }
        if (isset($files['ttf'])) {
            $sources[] = "url(".CSSHelpers::formatString($files['ttf']).") format('truetype')";
        }
        if (isset($files['otf'])) {
            $sources[] = "url(".CSSHelpers::formatString($files['otf']).") format('opentype')";
        }
        if (isset($files['svg'])) {
            $sources[] = "url(".CSSHelpers::formatString($files['svg'].'#'.strtolower(str_replace([' ', 'regular'], '', $family->name)).'regular').") format('svg')";
        }

        return "/* ".$family->name." */\n@font-face {\n"
            . "\tfont-family: ".CSSHelpers::formatString($family->name).";\n"
            . "\tfont-weight: $style->weight;\n"
            . "\tfont-style: ".($style->isItalic ? 'italic' : 'normal').";\n"
            . ($fontDisplay !== '' ? "\tfont-display: $fontDisplay;\n" : '')
            . ($fontStretch !== '' ? "\tfont-stretch: $fontStretch;\n" : '')
            . (isset($files['eot']) ? "\tsrc: url(".CSSHelpers::formatString(($_GET['q'] == 'icon' ? $files['eot'] : sprintf('data:%s;charset=utf-8;base64,%s', $this->getMimeType(str_replace($this->hostNameURL, self::SITE_ROOT_PATH, $files['eot'])), base64_encode(file_get_contents($files['eot'])))))."); /* For IE 6 to 8 */\n" : '')
            . "\tsrc: ".implode(",\n\t\t", $sources).";\n"
            . "}\n"
            
            . "\n.material-icons {\n"
            . "\tfont-family: ".CSSHelpers::formatString($family->name).";\n"
            . "\tfont-weight: normal;\n"
            . "\tfont-style: normal;\n"
            . "\tfont-size: 24px; /* Preferred icon size */\n"
            . "\tdisplay: inline-block;\n"
            . "\twidth: 1em;\n"
            . "\theight: 1em;\n"
            . "\tline-height: 1;\n"
            . "\ttext-transform: none;\n"
            . "\tletter-spacing: normal;\n"
            . "\tword-wrap: normal;\n"
            . "\twhite-space: nowrap;\n"
            . "\tdirection: ltr;\n\n"
            . "\t/* Support for all WebKit browsers. */\n"
            . "\t-webkit-font-smoothing: antialiased;\n\n"
            . "\t/* Support for Safari and Chrome. */\n"
            . "\ttext-rendering: optimizeLegibility;\n\n"
            . "\t/* Support for Firefox. */\n"
            . "\t-moz-osx-font-smoothing: grayscale;\n\n"
            . "\t/* Support for IE. */\n"
            . "\tfont-feature-settings: 'liga';\n"
            . "}";
    }

    /**
     * Gets the local names of a font style.
     *
     * @param Family $family The styles family
     * @param Style $style The font style
     * @return string[]
     */
    protected function getLocalFontNames(Family $family, Style $style): array
    {
        $words = array_filter([
            $family->name,
            $style->getName()
        ], 'strlen');

        return array_unique([
            implode(' ', $words),
            implode('-', array_map(function ($word) {
                return str_replace(' ', '', $word);
            }, $words))
        ]);
    }

    /**
     * Gets URLs of font style.
     *
     * @param Family $family The style family
     * @param Style $style The font style
     * @return string[] List of URLs
     */
    protected function getFontFilesURLs(Family $family, Style $style): array
    {
        $styleFilesDirectory = FileHelpers::concatPath(
            static::SITE_ROOT_PATH,
            static::FONTS_DIRECTORY,
            $family->directory,
            $style->directory
        );
        $files = $style->getFilesInDirectory($styleFilesDirectory);

        $result = [];
        foreach ($files as $file) {
            $extension = pathinfo($file, PATHINFO_EXTENSION);
            if ($extension === '') {
                continue;
            }

            $result[$extension] = FileHelpers::concatPath(
                $this->fontsDirectoryURL,
                $family->directory,
                $style->directory,
                $file
            );
        }

        return $result;
    }

    /**
     * Get mime type
     * @param string $file_path
     * @return mixed|string
     */
    private function getMimeType($file_path)
    {
        if (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $file_path);
            finfo_close($finfo);
            return $mime;
        } elseif (function_exists('mime_content_type')) {
            return mime_content_type($file_path);
        } elseif (!stristr(ini_get('disable_functions'), 'shell_exec')) {
            $file = escapeshellarg($file_path);
            $mime = shell_exec('file -bi ' . $file);
            return $mime;
        } else {
            return '';
        }
    }
}
