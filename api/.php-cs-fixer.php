<?php

use PhpCsFixer\Runner\Parallel\ParallelConfigFactory;

return (new PhpCsFixer\Config())
    ->setFinder(
        PhpCsFixer\Finder::create()
            ->exclude("vendor")
            ->in(__DIR__)
    )
    ->setRules([
        "@PSR1" => true,
        "@PSR12" => true,
        "array_syntax" => ["syntax" => "short"],
        "no_alias_language_construct_call" => true,
        "no_mixed_echo_print" => true,
        "no_multiline_whitespace_around_double_arrow" => true,
        "no_whitespace_before_comma_in_array" => true,
        "normalize_index_brace" => true,
        "elseif" => false,
        "align_multiline_comment" => ["comment_type" => "all_multiline"],
        "array_indentation" => true,
        "method_chaining_indentation" => true,
        "no_spaces_around_offset" => true,
        "trim_array_spaces" => true,
        "type_declaration_spaces" => ["elements" => ['function', 'property']],
        "types_spaces" => ["space" => "none", "space_multiple_catch" => "none"],
        "ordered_imports" => ["sort_algorithm" => "alpha", "imports_order" => ["const", "class", "function"]],
        "line_ending" => false,
    ]);
