<?php

declare(strict_types=1);

use PhpCsFixer\Fixer\Alias\ModernizeStrposFixer;
use PhpCsFixer\Fixer\Comment\SingleLineCommentStyleFixer;
use PhpCsFixer\Fixer\ControlStructure\NoUselessElseFixer;
use PhpCsFixer\Fixer\ControlStructure\YodaStyleFixer;
use PhpCsFixer\Fixer\FunctionNotation\NativeFunctionInvocationFixer;
use PhpCsFixer\Fixer\FunctionNotation\ReturnTypeDeclarationFixer;
use PhpCsFixer\Fixer\Import\NoUnusedImportsFixer;
use PhpCsFixer\Fixer\LanguageConstruct\IsNullFixer;
use PhpCsFixer\Fixer\Operator\BinaryOperatorSpacesFixer;
use PhpCsFixer\Fixer\Operator\ConcatSpaceFixer;
use PhpCsFixer\Fixer\Operator\IncrementStyleFixer;
use PhpCsFixer\Fixer\Phpdoc\NoSuperfluousPhpdocTagsFixer;
use PhpCsFixer\Fixer\PhpUnit\PhpUnitTestClassRequiresCoversFixer;
use PhpCsFixer\Fixer\ReturnNotation\NoUselessReturnFixer;
use PhpCsFixer\Fixer\Semicolon\MultilineWhitespaceBeforeSemicolonsFixer;
use PhpCsFixer\Fixer\Whitespace\BlankLineBeforeStatementFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;
use Symplify\EasyCodingStandard\ValueObject\Set\SetList;

return ECSConfig::configure()
    ->withPaths([
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ])
    ->withSkip([
        IsNullFixer::class,
        MultilineWhitespaceBeforeSemicolonsFixer::class,
        NativeFunctionInvocationFixer::class,
        PhpUnitTestClassRequiresCoversFixer::class,
        SingleLineCommentStyleFixer::class,
        YodaStyleFixer::class,
    ])
    ->withSets([
        SetList::PSR_12,
        SetList::DOCTRINE_ANNOTATIONS,
    ])
    ->withRules([
        NoUselessReturnFixer::class,
        NoUselessElseFixer::class,
        ModernizeStrposFixer::class,
        NoUnusedImportsFixer::class,
        NoSuperfluousPhpdocTagsFixer::class,
        ReturnTypeDeclarationFixer::class,
    ])
    ->withConfiguredRule(BlankLineBeforeStatementFixer::class, [
        'statements' => ['if', 'break', 'continue', 'declare', 'return', 'throw', 'try', 'switch'],
    ])
    ->withConfiguredRule(IncrementStyleFixer::class, [
        'style' => 'post'
    ])
    ->withConfiguredRule(BinaryOperatorSpacesFixer::class, [
        'default'   => 'single_space',
        'operators' => [
            '='  => 'align_single_space_minimal',
            '=>' => 'align_single_space_minimal',
            '.=' => 'align_single_space_minimal',
            '-=' => 'align_single_space_minimal',
            '+=' => 'align_single_space_minimal',
            '*=' => 'align_single_space_minimal',
            '%=' => 'align_single_space_minimal',
            '|'  => 'no_space',
        ],
    ])
    ->withConfiguredRule(ConcatSpaceFixer::class, [
        'spacing' => 'none'
    ])
     ;
