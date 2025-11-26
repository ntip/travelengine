<?php

namespace App\Services;

class ProviderResponseValidator
{
    /**
     * Validate provider response and determine outcome.
     * Returns array: ['result' => 'success'|'no-data'|'failed'|'blocked', 'reason'=>string]
     */
    public static function validate(string $providerCode, $oxylabsBody, ?string $raw): array
    {
        $provider = strtolower($providerCode);

        // Helper to gather status codes from results and nested content
        $codes = [];
        if (is_array($oxylabsBody) && isset($oxylabsBody['results']) && is_array($oxylabsBody['results'])) {
            foreach ($oxylabsBody['results'] as $res) {
                if (! is_array($res)) continue;
                $code = $res['status_code'] ?? $res['status'] ?? null;
                if ($code !== null) $codes[] = (string)$code;
                if (isset($res['content']) && is_array($res['content'])) {
                    foreach ($res['content'] as $c) {
                        $cc = $c['status_code'] ?? $c['status'] ?? null;
                        if ($cc !== null) $codes[] = (string)$cc;
                    }
                }
            }
        }

        $has200 = in_array('200', $codes, true);
        $only403 = false;
        if (! $has200 && count($codes) > 0) {
            $unique = array_unique($codes);
            if (count($unique) === 1 && $unique[0] === '403') $only403 = true;
        }

        $hasRaw = (bool) ($raw && is_string($raw) && strlen($raw) > 0);

        // Provider specific rules
        if ($provider === 'va' || stripos($provider, 'va') !== false) {
            // Strict: require GraphQL 200 + bookingAirSearch in raw
            $graphql200 = false;
            if (is_array($oxylabsBody) && isset($oxylabsBody['results'])) {
                foreach ($oxylabsBody['results'] as $res) {
                    if (! is_array($res)) continue;
                    $rurl = $res['url'] ?? '';
                    $rcode = $res['status_code'] ?? $res['status'] ?? null;
                    if ($rcode !== null && stripos($rurl, 'graphql') !== false && (int)$rcode === 200) {
                        $graphql200 = true; break;
                    }
                    if (isset($res['content']) && is_array($res['content'])) {
                        foreach ($res['content'] as $c) {
                            if (! is_array($c)) continue;
                            $curl = $c['url'] ?? '';
                            $ccode = $c['status_code'] ?? $c['status'] ?? null;
                            if ($ccode !== null && stripos($curl, 'graphql') !== false && (int)$ccode === 200) { $graphql200 = true; break 2; }
                        }
                    }
                }
            }

            $rawHasBooking = false;
            if ($hasRaw) {
                if (stripos($raw, 'bookingAirSearch') !== false) $rawHasBooking = true;
                else {
                    $decoded = json_decode($raw, true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded) && isset($decoded['data']) && isset($decoded['data']['bookingAirSearch'])) $rawHasBooking = true;
                }
            }

            if ($graphql200 && $rawHasBooking) return ['result' => 'success', 'reason' => 'graphql_200_with_booking'];
            if ($graphql200 && ! $rawHasBooking) return ['result' => 'no-data', 'reason' => 'graphql_200_no_booking'];
            if ($only403) return ['result' => 'failed', 'reason' => 'blocked_403'];
            return ['result' => 'failed', 'reason' => 'no_graphql_200'];
        }

        // Default generic rules: success if any 200 and we have raw
        if ($has200 && $hasRaw) return ['result' => 'success', 'reason' => '200_with_raw'];
        if ($has200 && ! $hasRaw) return ['result' => 'no-data', 'reason' => '200_no_raw'];
        if ($only403) return ['result' => 'failed', 'reason' => 'blocked_403'];

        return ['result' => 'failed', 'reason' => 'no_200_found'];
    }
}
