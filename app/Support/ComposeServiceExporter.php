<?php

namespace App\Support;

use App\Models\Container;
use Symfony\Component\Yaml\Yaml;

class ComposeServiceExporter
{
    public function buildServicesPayload(iterable $containers): array
    {
        $services = [];
        $seenTitles = [];

        foreach ($containers as $container) {
            if (!$container instanceof Container) {
                continue;
            }

            if (in_array($container->title, $seenTitles, true)) {
                continue;
            }

            $content = $container->content ?: $container->content_orig;
            if (!is_string($content) || trim($content) === '') {
                continue;
            }

            $parsed = Yaml::parse($content);
            if (!is_array($parsed) || $parsed === []) {
                continue;
            }

            $services[$container->title] = $parsed;
            $seenTitles[] = $container->title;
        }

        return $services;
    }

    public function dumpServices(iterable $containers): string
    {
        $services = $this->buildServicesPayload($containers);

        if ($services === []) {
            return '';
        }

        return Yaml::dump($services, 8, 2);
    }
}
