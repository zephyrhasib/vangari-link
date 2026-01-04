<?php
class PriceValidation
{
    public static function validateDealerPrices(array $postedPrices, array $referenceRows, float $limitPercent = 5.0): array
    {
        $ref = [];
        foreach ($referenceRows as $r) {
            $itemId = (int)($r['item_id'] ?? 0);
            $ref[$itemId] = [
                'name' => $r['item_name'] ?? ("Item " . $itemId),
                'avg7' => $r['avg7_market'] ?? null,
                'yesterday' => $r['yesterday_market'] ?? null,
            ];
        }

        $clean = [];
        $errors = [];

        foreach ($postedPrices as $itemId => $value) {
            $itemId = (int)$itemId;

            if ($value === '' || $value === null) {
                continue;
            }

            if (!is_numeric($value)) {
                $errors[] = "Invalid price for item ID {$itemId}.";
                continue;
            }

            $price = (float)$value;
            if ($price <= 0) {
                $errors[] = "Price must be greater than 0 for item ID {$itemId}.";
                continue;
            }

            $name = $ref[$itemId]['name'] ?? ("Item " . $itemId);

            $baseline = null;
            if (isset($ref[$itemId])) {
                $avg7 = $ref[$itemId]['avg7'];
                $y = $ref[$itemId]['yesterday'];

                if ($avg7 !== null) $baseline = (float)$avg7;
                else if ($y !== null) $baseline = (float)$y;
            }

            if ($baseline !== null && $baseline > 0) {
                $minAllowed = $baseline * (1 - $limitPercent / 100);
                $maxAllowed = $baseline * (1 + $limitPercent / 100);

                if ($price < $minAllowed || $price > $maxAllowed) {
                    $errors[] = "{$name}: must be within ±{$limitPercent}% of market baseline.";
                    continue;
                }
            }

            $clean[$itemId] = $price;
        }

        return [$clean, $errors];
    }
}
