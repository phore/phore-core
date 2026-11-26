<?php

namespace spec\Phore\Core;

use Phore\Core\Exception\InvalidDataException;
use Phore\Core\Exception\YamlDecodeException;
use PhpSpec\ObjectBehavior;
use PhpSpec\Exception\Example\FailureException;
use spec\Phore\Core\fixtures\Functions;

class FunctionsSpec extends ObjectBehavior
{
    const ANNOTATIONS = <<<'TEXT'
    /**
     * @an0
     * @an1 abc
     * @an2 abc def
     * @an3 abc def ghi
     */
TEXT;

    function let()
    {
        $this->beAnInstanceOf(Functions::class);
    }

    function it_transforms_arrays_with_keys_and_values()
    {
        $this->phore_array_transform(["a", "b"], function ($key, $value) {
            return "$key-$value";
        })->shouldBe(["0-a", "1-b"]);
    }

    function it_validates_alphanumeric_strings_with_allowed_characters()
    {
        $this->phore_assert_str_alnum("abcde_-", ["_", "-"])->shouldBe(true);
    }

    function it_rejects_non_string_alphanumeric_input()
    {
        $this->shouldThrow(\InvalidArgumentException::class)->during('phore_assert_str_alnum', [1]);
    }

    function it_rejects_disallowed_characters()
    {
        $this->shouldThrow(InvalidDataException::class)->during('phore_assert_str_alnum', ["ab-"]);
    }

    function it_filters_without_options_or_with_explicit_null_without_deprecations()
    {
        $this->phore_assert("hello")->filter()->shouldHaveType(\Phore\Core\Helper\_PhoreAssert::class);
        $this->phore_assert("123")->filter(FILTER_VALIDATE_INT, null)
            ->shouldHaveType(\Phore\Core\Helper\_PhoreAssert::class);
    }

    function it_validates_email_without_filter_options_or_deprecations()
    {
        $this->phore_assert("user@example.com")->email()->shouldHaveType(\Phore\Core\Helper\_PhoreAssert::class);
    }

    function it_checks_string_prefixes_and_suffixes()
    {
        $this->startsWith("abcdef", "abc")->shouldBe(true);
        $this->startsWith("abcdef", "bcd")->shouldBe(false);
        $this->endsWith("abcdef", "def")->shouldBe(true);
        $this->endsWith("abcdef", "cde")->shouldBe(false);
    }

    function it_escapes_positional_and_named_arguments()
    {
        $this->phore_escape("cmd ? :b {c}", ["A", "b" => "B", "c" => "C"], function ($value) {
            return escapeshellarg($value);
        })
            ->shouldBe("cmd 'A' 'B' 'C'");
    }

    function it_hashes_strings()
    {
        $this->phore_hash("abc")->shouldBe("qZk+NkcGgWq6PiVxeFDCbJzQ2J0=");
        $this->phore_hash("abc", true)->shouldBe("9PXC3z2TWd4Lu7+e7Xza05RPH+5LXmT6NDCUx9FYdxuek4KP");
    }

    function it_rejects_non_string_hash_input()
    {
        $this->shouldThrow(\InvalidArgumentException::class)->during('phore_hash', [null]);
    }

    function it_rejects_empty_hash_input()
    {
        $this->shouldThrow(\InvalidArgumentException::class)->during('phore_hash', [""]);
    }

    function it_preserves_json_number_fractions()
    {
        $this->phore_json_decode(phore_json_encode([12.0]))->shouldBe([12.0]);
    }

    function it_rejects_scalar_json()
    {
        $this->shouldThrow(\InvalidArgumentException::class)
            ->during('phore_json_decode', [phore_json_encode(123)]);
    }

    function it_does_not_escape_json_slashes()
    {
        $this->phore_json_encode(["data" => "/"])->shouldBe('{"data":"/"}');
        $this->phore_json_decode('{"data":"/"}')->shouldBe(["data" => "/"]);
    }

    function it_runs_a_callback_only_once()
    {
        $count = 0;
        for ($i = 0; $i < 3; $i++) {
            $result = phore_once(function () use (&$count) {
                $count++;
            });
        }
        $this->value($count)->shouldBe(1);
        $this->value($result)->shouldBe(null);
    }

    function it_returns_the_cached_callback_result()
    {
        for ($i = 0; $i < 3; $i++) {
            $this->value(phore_once(function () {
                static $count = 0;
                return ++$count;
            }))->shouldBe(1);
        }
    }

    function it_parses_annotations()
    {
        $this->phore_parse_annotation(self::ANNOTATIONS, "@notexistent")->shouldBe(null);
        $this->phore_parse_annotation(self::ANNOTATIONS, "@an0")->shouldBe("");
        $this->phore_parse_annotation(self::ANNOTATIONS, "@an1")->shouldBe("abc");
        $this->phore_parse_annotation(self::ANNOTATIONS, "@an2")->shouldBe("abc def");
        $this->phore_parse_annotation(self::ANNOTATIONS, "@an1", 1)->shouldBe(["abc"]);
        $this->phore_parse_annotation(self::ANNOTATIONS, "@an2", 1)->shouldBe(["abc def"]);
        $this->phore_parse_annotation(self::ANNOTATIONS, "@an1", 2)->shouldBe(["abc", null]);
        $this->phore_parse_annotation(self::ANNOTATIONS, "@an2", 2)->shouldBe(["abc", "def"]);
        $this->phore_parse_annotation(self::ANNOTATIONS, "@an3", 2)->shouldBe(["abc", "def ghi"]);
    }

    function it_parses_single_line_annotations_with_or_without_line_endings()
    {
        foreach (["", "\n", "\r\n"] as $ending) {
            foreach (["// ", "# ", ""] as $prefix) {
                $this->phore_parse_annotation($prefix . "@an0" . $ending, "@an0")->shouldBe("");
                $this->phore_parse_annotation($prefix . "@an1 abc" . $ending, "@an1")->shouldBe("abc");
                $this->phore_parse_annotation($prefix . "@an2 abc def" . $ending, "@an2", 2)
                    ->shouldBe(["abc", "def"]);
            }

            foreach (["/*", "/**"] as $prefix) {
                $this->phore_parse_annotation($prefix . " @an0 */" . $ending, "@an0")->shouldBe("");
                $this->phore_parse_annotation($prefix . " @an1 abc */" . $ending, "@an1")->shouldBe("abc");
                $this->phore_parse_annotation($prefix . " @an2 abc def */" . $ending, "@an2", 2)
                    ->shouldBe(["abc", "def"]);
            }
        }
    }

    function it_stops_at_the_annotation_line_for_unix_and_windows_line_endings()
    {
        foreach (["\n", "\r\n"] as $ending) {
            $text = implode($ending, ["// @an1 abc", "// @an2 def ghi"]);
            $this->phore_parse_annotation($text, "@an1")->shouldBe("abc");
            $this->phore_parse_annotation($text, "@an2", 2)->shouldBe(["def", "ghi"]);

            $text = implode($ending, ["/**", " * @an1 abc", " * @an2 def ghi */"]);
            $this->phore_parse_annotation($text, "@an1")->shouldBe("abc");
            $this->phore_parse_annotation($text, "@an2", 2)->shouldBe(["def", "ghi"]);
        }
    }

    function it_preserves_a_literal_block_comment_end_in_a_line_comment()
    {
        $this->phore_parse_annotation("// @an1 abc */", "@an1")->shouldBe("abc */");
    }

    function it_generates_random_strings_of_the_requested_length()
    {
        $this->value(strlen(phore_random_str(120)))->shouldBe(120);
    }

    function it_generates_different_random_strings()
    {
        $this->phore_random_str(20)->shouldNotBe(phore_random_str(20));
    }

    function it_disallows_object_deserialization_by_default()
    {
        $this->phore_unserialize(phore_serialize(new \stdClass()))
            ->shouldHaveType(\__PHP_Incomplete_Class::class);
    }

    function it_allows_deserializing_any_class_when_requested()
    {
        $this->phore_unserialize(phore_serialize(new \stdClass()), true)->shouldHaveType(\stdClass::class);
    }

    function it_allows_deserializing_explicit_classes()
    {
        $this->phore_unserialize(phore_serialize(new \stdClass()), [\stdClass::class])
            ->shouldHaveType(\stdClass::class);
    }

    function it_round_trips_yaml()
    {
        $this->phore_yaml_decode(phore_yaml_encode(["a" => "b"]))->shouldBe(["a" => "b"]);
    }

    function it_exposes_yaml_error_locations()
    {
        try {
            phore_yaml_decode("valid: true\nnested:\n\tinvalid: value\n");
        } catch (YamlDecodeException $e) {
            $this->value($e)->getMessage()->shouldContain("column");
            $this->value($e)->getErrorLine()->shouldBe(3);
            $this->value($e)->getErrorColumn()->shouldBe(1);
            $this->value($e)->getErrorSourceLine()->shouldBe("\tinvalid: value");
            $this->value($e)->shouldHaveType(\InvalidArgumentException::class);
            return;
        }
        throw new FailureException("Expected YamlDecodeException");
    }

    function it_plucks_nested_values_with_array_or_string_paths()
    {
        $input = ["a" => ["b" => "res"]];
        $this->phore_pluck(["a", "b"], $input)->shouldBe("res");
        $this->phore_pluck("a.b", $input)->shouldBe("res");
    }

    function it_returns_a_default_for_missing_paths()
    {
        $this->phore_pluck("a.b.c", ["a" => ["b" => "res"]], "def")->shouldBe("def");
    }

    function it_throws_the_supplied_exception_for_missing_paths()
    {
        $this->shouldThrow(new \Exception("Ex"))
            ->during('phore_pluck', ["a.b.c", ["a" => ["b" => "res"]], new \Exception("Ex")]);
    }

    function it_plucks_values_from_arrays()
    {
        $this->phore_pluck("a[].b", ["a" => [["b" => "val1"], ["b" => "val2"]]])
            ->shouldBe(["val1", "val2"]);
        $this->phore_pluck("a[].b", ["a" => []])->shouldBe([]);
    }

    function it_enforces_array_defaults()
    {
        $data = ["a" => null, "b" => false, "c" => "some string", "d" => ["array"]];
        $this->phore_pluck("a", $data, [])->shouldBe([]);
        $this->phore_pluck("b", $data, [])->shouldBe([]);
        $this->phore_pluck("c", $data, [])->shouldBe([]);
        $this->phore_pluck("d", $data, [])->shouldBe(["array"]);
    }

    function it_preserves_associative_array_keys()
    {
        $data = ["a" => ["b1" => ["b1-data"], "b2" => ["b2-data"]]];
        $this->phore_pluck("a", $data, [])->shouldBe($data["a"]);
    }

    function it_preserves_numeric_array_keys()
    {
        $data = ["a" => [["b1-data"], ["b2-data"]]];
        $this->phore_pluck("a", $data, [])->shouldBe($data["a"]);
    }

    function it_unindents_text_while_preserving_relative_indentation()
    {
        $this->phore_text_unindent("\n    Some indented\n    Text\n        With subtext\n")
            ->shouldBe("Some indented\nText\n    With subtext");
        $this->phore_text_unindent("\nSome indented\nText\n    With subtext\n")
            ->shouldBe("Some indented\nText\n    With subtext");
    }

}
