<?php

namespace App\Services;

class ProviderGrabberFactory
{
    /**
     * Extract the provider response raw string from the Oxylabs API body.
     * If a provider-specific grabber class exists, use it. Otherwise use a generic extractor.
     *
     * @param string $providerCode
     * @param mixed $oxylabsBody
     * @return string|null
     */
    public static function extract(string $providerCode, $oxylabsBody): ?string
    {
        // Normalize to class name (e.g. VA -> VAGrabber)
        $className = 'App\\Services\\Grabbers\\' . ucfirst(strtolower($providerCode)) . 'Grabber';

        if (class_exists($className)) {
            $grabber = new $className();
            if (method_exists($grabber, 'extract')) {
                return $grabber->extract($oxylabsBody);
            }
        }

        // Generic extractor: expect array with 'results' where each item may include
        // `response_body` or a `content` array (Oxylabs can return XHR `content`).
        if (is_array($oxylabsBody) && isset($oxylabsBody['results']) && is_array($oxylabsBody['results'])) {
            foreach ($oxylabsBody['results'] as $res) {
                if (! is_array($res)) {
                    continue;
                }

                // Prefer explicit response_body when present
                if (isset($res['response_body']) && is_string($res['response_body'])) {
                    return $res['response_body'];
                }

                // Some providers (XHR results) include a `content` array describing
                // sub-requests. Try to pick the most useful entry (prefer entries
                // with an explicit `response_body`, or URLs containing 'graphql').
                if (isset($res['content']) && is_array($res['content'])) {
                    // Prefer content entries with response_body for GraphQL/api calls
                    foreach ($res['content'] as $c) {
                        if (is_array($c) && isset($c['response_body']) && is_string($c['response_body'])) {
                            $curl = $c['url'] ?? '';
                            if (stripos($curl, 'graphql') !== false || stripos($curl, '/api/') !== false) {
                                return $c['response_body'];
                            }
                        }
                    }

                    // Next: prefer any content entry whose URL looks like GraphQL or API
                    foreach ($res['content'] as $c) {
                        if (is_array($c)) {
                            $curl = $c['url'] ?? '';
                            if (stripos($curl, 'graphql') !== false || stripos($curl, '/api/') !== false) {
                                return json_encode($c);
                            }
                        }
                    }

                    // Next: prefer known metadata/data endpoints
                    foreach ($res['content'] as $c) {
                        if (is_array($c)) {
                            $curl = $c['url'] ?? '';
                            $cstatus = $c['status_code'] ?? $c['status'] ?? null;
                            if ($cstatus == 200 && (stripos($curl, 'metadata.json') !== false || stripos($curl, '/data/') !== false)) {
                                return json_encode($c);
                            }
                        }
                    }

                    // Fallback: return the whole content array as JSON so callers can
                    // decide which element is relevant.
                    return json_encode($res['content']);
                }
            }
        }

        // If body is a JSON string, try to decode and repeat
        if (is_string($oxylabsBody) && strlen($oxylabsBody) > 0) {
            $decoded = json_decode($oxylabsBody, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return self::extract($providerCode, $decoded);
            }
        }

        return null;
    }
}
