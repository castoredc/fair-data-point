<?php
declare(strict_types=1);

namespace App\Service;

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\SyntaxError;
use Twig\Extension\SandboxExtension;
use Twig\Node\BodyNode;
use Twig\Node\Expression\ConstantExpression;
use Twig\Node\Expression\GetAttrExpression;
use Twig\Node\Expression\NameExpression;
use Twig\Node\Node;
use Twig\Sandbox\SecurityPolicy;
use Twig\Source;
use function array_filter;
use function array_keys;
use function assert;
use function implode;
use function is_string;
use function substr;

class DataTransformationService
{
    public const ALLOWED_TAGS = ['if'];
    public const ALLOWED_FILTERS = ['upper'];
    public const ALLOWED_METHODS = [];
    public const ALLOWED_PROPERTIES = [];
    public const ALLOWED_FUNCTIONS = ['range'];

    protected Environment $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;

        $this->setUpSandBox();
    }

    private function setUpSandBox(): void
    {
        $policy = new SecurityPolicy(
            self::ALLOWED_TAGS,
            self::ALLOWED_FILTERS,
            self::ALLOWED_METHODS,
            self::ALLOWED_PROPERTIES,
            self::ALLOWED_FUNCTIONS
        );

        $sandbox = new SandboxExtension($policy);
        $this->twig->addExtension($sandbox);
    }

    /** @return false|string[] */
    public function parseSyntax(string $syntax)
    {
        try {
            $tokens = $this->twig->tokenize(new Source($syntax, 'template'));
            $nodes = $this->twig->parse($tokens);

            $bodyNode = $nodes->getNode('body');
            assert($bodyNode instanceof BodyNode);

            return array_filter(array_keys($this->getTwigVariableNames($bodyNode)), static function (string $v): bool {
                return substr($v, 0, 1) !== '_';
            });
        } catch (SyntaxError $e) {
            return false;
        }
    }

    /** @return string[] */
    private function getTwigVariableNames(Node $moduleNode): array
    {
        $variables = [];

        foreach ($moduleNode as $node) {
            if ($node instanceof NameExpression) {
                $name = $node->getAttribute('name');
                $variables[$name] = (string) $name;
            } elseif ($node instanceof ConstantExpression && $moduleNode instanceof GetAttrExpression) {
                $value = $node->getAttribute('value');
                if (is_string($value) && $value !== '') {
                    $variables[$value] = $value;
                }
            } elseif ($node instanceof GetAttrExpression) {
                $path = implode('.', $this->getTwigVariableNames($node));
                if ($path !== '') {
                    $variables[$path] = $path;
                }
            } else {
                $variables += $this->getTwigVariableNames($node);
            }
        }

        return $variables;
    }

    /**
     * @param array<string, string> $variables
     *
     * @throws SyntaxError
     * @throws LoaderError
     */
    public function render(string $syntax, array $variables): string
    {
        return $this->twig->createTemplate($syntax)->render($variables);
    }
}
