<?php
/**
 * @see       https://github.com/zendframework/zend-coding-standard for the canonical source repository
 * @copyright https://github.com/zendframework/zend-coding-standard/blob/master/COPYRIGHT.md Copyright
 * @license   https://github.com/zendframework/zend-coding-standard/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace ZendCodingStandard\Helper;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Tokens;
use ZendCodingStandard\CodingStandard;

use function array_filter;
use function count;
use function end;
use function explode;
use function implode;
use function in_array;
use function key;
use function ltrim;
use function preg_match;
use function preg_replace;
use function str_replace;
use function strcmp;
use function stripos;
use function strlen;
use function strpos;
use function strstr;
use function strtolower;
use function strtr;
use function substr;

use const T_DOC_COMMENT_CLOSE_TAG;
use const T_DOC_COMMENT_TAG;
use const T_DOC_COMMENT_WHITESPACE;
use const T_INTERFACE;
use const T_WHITESPACE;

/**
 * @internal
 */
trait Methods
{
    use Namespaces;

    /**
     * @var File
     */
    private $currentFile;

    /**
     * @var string
     */
    private $currentNamespace;

    /**
     * @var string[][]
     */
    private $importedClasses = [];

    /**
     * @var string
     */
    private $methodName;

    /**
     * @var bool
     */
    private $isSpecialMethod;

    /**
     * @var string
     */
    private $className;

    /**
     * @var string
     */
    private $parentClassName;

    /**
     * @var string[]
     */
    private $implementedInterfaceNames = [];

    /**
     * Allowed simple type hints for method params.
     *
     * @var string[]
     */
    private $simpleTypeHints = [
        'array',
        'bool',
        'callable',
        'float',
        'iterable',
        'int',
        'object',
        'parent',
        'resource',
        'self',
        'string',
    ];

    /**
     * @var string[]
     */
    private $simpleReturnTypes = [
        'array',
        '?array',
        'bool',
        '?bool',
        'callable',
        '?callable',
        'float',
        '?float',
        'iterable',
        '?iterable',
        'int',
        '?int',
        'object',
        '?object',
        'parent',
        '?parent',
        'resource',
        '?resource',
        'self',
        '?self',
        'string',
        '?string',
        'void',
    ];

    private function initScope(File $phpcsFile, int $stackPtr) : void
    {
        if ($this->currentFile !== $phpcsFile) {
            $this->currentFile = $phpcsFile;
            $this->currentNamespace = null;
        }

        $namespace = $this->getNamespace($phpcsFile, $stackPtr);
        if ($this->currentNamespace !== $namespace) {
            $this->currentNamespace = $namespace;
            $this->importedClasses = $this->getGlobalUses($phpcsFile, $stackPtr);
        }

        $tokens = $phpcsFile->getTokens();

        if ($tokens[$stackPtr]['code'] !== T_DOC_COMMENT_TAG) {
            $this->methodName = $phpcsFile->getDeclarationName($stackPtr);
            $this->isSpecialMethod = $this->methodName === '__construct' || $this->methodName === '__destruct';
        }

        // Get class name of the method, name of the parent class and implemented interfaces names
        $this->className = null;
        $this->parentClassName = null;
        $this->implementedInterfaceNames = [];
        if ($tokens[$stackPtr]['conditions']) {
            $conditionCode = end($tokens[$stackPtr]['conditions']);
            if (in_array($conditionCode, Tokens::$ooScopeTokens, true)) {
                $conditionPtr = key($tokens[$stackPtr]['conditions']);
                $this->className = $phpcsFile->getDeclarationName($conditionPtr);
                if ($conditionCode !== T_INTERFACE) {
                    $this->parentClassName = $phpcsFile->findExtendedClassName($conditionPtr) ?: null;
                }
                $this->implementedInterfaceNames = $phpcsFile->findImplementedInterfaceNames($conditionPtr) ?: [];
            }
        }
    }

    public function sortTypes(string $a, string $b) : int
    {
        $a = strtolower(str_replace('\\', ':', $a));
        $b = strtolower(str_replace('\\', ':', $b));

        if ($a === 'null' || strpos($a, 'null[') === 0) {
            return -1;
        }

        if ($b === 'null' || strpos($b, 'null[') === 0) {
            return 1;
        }

        if ($a === 'true' || $a === 'false') {
            return -1;
        }

        if ($b === 'true' || $b === 'false') {
            return 1;
        }

        $aIsSimple = array_filter($this->simpleReturnTypes, function ($v) use ($a) {
            return $v === $a || strpos($a, $v . '[') === 0;
        });
        $bIsSimple = array_filter($this->simpleReturnTypes, function ($v) use ($b) {
            return $v === $b || strpos($b, $v . '[') === 0;
        });

        if ($aIsSimple && $bIsSimple) {
            return strcmp($a, $b);
        }

        if ($aIsSimple) {
            return -1;
        }

        if ($bIsSimple) {
            return 1;
        }

        return strcmp(
            preg_replace('/^:/', '', $a),
            preg_replace('/^:/', '', $b)
        );
    }

    private function getSuggestedType(string $class) : string
    {
        $prefix = $class[0] === '?' ? '?' : '';
        $suffix = strstr($class, '[');
        $clear = strtolower(strtr($class, ['?' => '', '[' => '', ']' => '']));

        if (in_array($clear, $this->simpleReturnTypes + ['static' => 'static'], true)) {
            return $prefix . $clear . $suffix;
        }

        $suggested = CodingStandard::suggestType($clear);
        if ($suggested !== $clear) {
            return $prefix . $suggested . $suffix;
        }

        // Is it a current class?
        if ($this->isClassName($clear)) {
            return $prefix . 'self' . $suffix;
        }

        // Is it a parent class?
        if ($this->isParentClassName($clear)) {
            return $prefix . 'parent' . $suffix;
        }

        // Is the class imported?
        if (isset($this->importedClasses[$clear])) {
            return $prefix . $this->importedClasses[$clear]['alias'] . $suffix;
        }

        if ($clear[0] === '\\') {
            $ltrim = ltrim($clear, '\\');
            foreach ($this->importedClasses as $use) {
                if (strtolower($use['class']) === $ltrim) {
                    return $prefix . $use['alias'] . $suffix;
                }

                if (stripos($ltrim, $use['class'] . '\\') === 0) {
                    $clear = ltrim(strtr($class, ['?' => '', '[' => '', ']' => '']), '\\');
                    $name = substr($clear, strlen($use['class']));

                    return $prefix . $use['alias'] . $name . $suffix;
                }
            }
        }

        return $class;
    }

    private function typesMatch(string $typeHint, string $typeStr) : bool
    {
        $isNullable = $typeHint[0] === '?';
        $lowerTypeHint = strtolower($isNullable ? substr($typeHint, 1) : $typeHint);
        $lowerTypeStr = strtolower($typeStr);

        $types = explode('|', $lowerTypeStr);
        $count = count($types);

        // For nullable types we expect null and type in PHPDocs
        if ($isNullable && $count !== 2) {
            return false;
        }

        // If type is not nullable PHPDocs should just containt type name
        if (! $isNullable && $count !== 1) {
            return false;
        }

        $fqcnTypeHint = strtolower($this->getFQCN($lowerTypeHint));
        foreach ($types as $key => $type) {
            if ($type === 'null') {
                continue;
            }

            $types[$key] = strtolower($this->getFQCN($type));
        }
        $fqcnTypes = implode('|', $types);

        return $fqcnTypeHint === $fqcnTypes
            || ($isNullable
                && ('null|' . $fqcnTypeHint === $fqcnTypes
                    || $fqcnTypeHint . '|null' === $fqcnTypes));
    }

    private function getFQCN(string $class) : string
    {
        // It is a simple type
        if (in_array(strtolower($class), $this->simpleReturnTypes, true)) {
            return $class;
        }

        // It is already FQCN
        if ($class[0] === '\\') {
            return $class;
        }

        // It is an imported class
        if (isset($this->importedClasses[$class])) {
            return '\\' . $this->importedClasses[$class]['class'];
        }

        // It is a class from the current namespace
        return ($this->currentNamespace ? '\\' . $this->currentNamespace : '') . '\\' . $class;
    }

    private function isClassName(string $name) : bool
    {
        if (! $this->className) {
            return false;
        }

        $ns = strtolower($this->currentNamespace);
        $lowerClassName = strtolower($this->className);
        $lowerFQCN = ($ns ? '\\' . $ns : '') . '\\' . $lowerClassName;
        $lower = strtolower($name);

        return $lower === $lowerFQCN
            || $lower === $lowerClassName;
    }

    private function isParentClassName(string $name) : bool
    {
        if (! $this->parentClassName) {
            return false;
        }

        $lowerParentClassName = strtolower($this->parentClassName);
        $lowerFQCN = strtolower($this->getFQCN($lowerParentClassName));
        $lower = strtolower($name);

        return $lower === $lowerFQCN
            || $lower === $lowerParentClassName;
    }

    private function removeTag(File $phpcsFile, int $tagPtr) : void
    {
        $tokens = $phpcsFile->getTokens();

        $phpcsFile->fixer->beginChangeset();
        if ($tokens[$tagPtr - 1]['code'] === T_DOC_COMMENT_WHITESPACE
            && $tokens[$tagPtr + 3]['code'] === T_DOC_COMMENT_WHITESPACE
        ) {
            $phpcsFile->fixer->replaceToken($tagPtr - 1, '');
        }

        $phpcsFile->fixer->replaceToken($tagPtr, '');
        $phpcsFile->fixer->replaceToken($tagPtr + 1, '');
        $phpcsFile->fixer->replaceToken($tagPtr + 2, '');
        $phpcsFile->fixer->endChangeset();
    }

    private function getCommentStart(File $phpcsFile, int $stackPtr) : ?int
    {
        $tokens = $phpcsFile->getTokens();
        $skip = Tokens::$methodPrefixes
            + [T_WHITESPACE => T_WHITESPACE];

        $commentEnd = $phpcsFile->findPrevious($skip, $stackPtr - 1, null, true);
        // There is no doc-comment for the function.
        if ($tokens[$commentEnd]['code'] !== T_DOC_COMMENT_CLOSE_TAG) {
            return null;
        }

        return $tokens[$commentEnd]['comment_opener'];
    }

    private function isThis(string $tag, string $type) : bool
    {
        if ($tag !== '@return') {
            return false;
        }

        return in_array(strtolower($type), ['$this', '$this|null', 'null|$this'], true);
    }

    private function isVariable(string $str) : bool
    {
        return strpos($str, '$') === 0
            || strpos($str, '...$') === 0;
    }

    private function isType(string $tag, string $type) : bool
    {
        if ($this->isThis($tag, $type)) {
            return true;
        }

        return (bool) preg_match('/^((?:\\\\?[a-z0-9]+)+(?:\[\])*)(\|(?:\\\\?[a-z0-9]+)+(?:\[\])*)*$/i', $type);
    }
}
