<?php

namespace App\Services\Grabbers;

class VaGrabber
{
    /**
     * Extract the raw provider response for Virgin Australia from the Oxylabs body.
     * The Oxylabs body typically contains a `results` array where each item
     * may include `url`, `status_code`, and `response_body`.
     */
    public function extract($body): ?string
    {
        if (is_string($body)) {
            $decoded = json_decode($body, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $body = $decoded;
            }
        }

        if (! is_array($body)) {
            return null;
        }

        // Strict extraction: only accept GraphQL POST responses with HTTP 200
        // whose body contains `bookingAirSearch` (or JSON with data.bookingAirSearch).
        if (isset($body['results']) && is_array($body['results'])) {
            foreach ($body['results'] as $res) {
                if (! is_array($res)) continue;

                // Check result-level candidate
                $rurl = $res['url'] ?? '';
                $rmethod = strtolower($res['method'] ?? $res['http_method'] ?? '');
                $rcode = isset($res['status_code']) ? (int)$res['status_code'] : (isset($res['status']) ? (int)$res['status'] : null);
                $rbody = $res['response_body'] ?? null;

                if ($rbody && is_string($rbody) && (stripos($rurl, 'graphql') !== false || stripos($rurl, '/api/graphql') !== false) && ($rmethod === 'post' || $rmethod === '')) {
                    if ($rcode === 200) {
                        if (stripos($rbody, 'bookingAirSearch') !== false) {
                            return $rbody;
                        }
                        $decodedR = json_decode($rbody, true);
                        if (json_last_error() === JSON_ERROR_NONE && is_array($decodedR) && isset($decodedR['data']) && isset($decodedR['data']['bookingAirSearch'])) {
                            return $rbody;
                        }
                    }
                }

                // Inspect content entries (XHR sub-requests)
                if (isset($res['content']) && is_array($res['content'])) {
                    foreach ($res['content'] as $c) {
                        if (! is_array($c)) continue;
                        $curl = $c['url'] ?? '';
                        $method = strtolower($c['method'] ?? $c['http_method'] ?? '');
                        $cstatus = isset($c['status_code']) ? (int)$c['status_code'] : (isset($c['status']) ? (int)$c['status'] : null);
                        $cbody = $c['response_body'] ?? null;

                        if (! is_string($cbody) || strlen($cbody) === 0) continue;

                        if (stripos($curl, 'graphql') !== false || stripos($curl, '/api/graphql') !== false) {
                            if (($method === 'post' || $method === '') && $cstatus === 200) {
                                // Check for bookingAirSearch in body or decoded JSON
                                if (stripos($cbody, 'bookingAirSearch') !== false) {
                                    return $cbody;
                                }
                                $decoded = json_decode($cbody, true);
                                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded) && isset($decoded['data']) && isset($decoded['data']['bookingAirSearch'])) {
                                    return $cbody;
                                }
                            }
                        }
                    }
                }
            }
        }

        // If no strict GraphQL POST 200 with bookingAirSearch was found, return null
        return null;
    }
}
