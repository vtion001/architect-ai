<?php

declare(strict_types=1);

namespace App\Contracts;

/**
 * Content Generator Interface (Strategy Pattern).
 *
 * Defines the contract for different content generation strategies.
 * Allows swapping generator implementations without modifying client code.
 */
interface ContentGeneratorInterface
{
    /**
     * Generate content based on topic and options.
     *
     * @param  string  $topic  The content topic
     * @param  string|null  $context  Additional context
     * @param  array  $options  Generation options
     * @return string Generated content
     */
    public function generate(string $topic, ?string $context = null, array $options = []): string;

    /**
     * Get the system prompt for this generator type.
     */
    public function getSystemPrompt(array $options = []): string;

    /**
     * Get the user prompt for this generator type.
     */
    public function getUserPrompt(string $topic, ?string $context = null, array $options = []): string;

    /**
     * Get the generator type identifier.
     */
    public function getType(): string;
}
