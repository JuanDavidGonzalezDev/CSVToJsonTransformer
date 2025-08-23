<?php

declare(strict_types=1);

namespace App\Transformer;

class UserTransformer
{
    private array $countries;

    public function __construct(array $countries)
    {
        $this->countries = $countries;
    }

    public function transform(array $row): ?array
    {
        // Normalize names
        $firstName = ucfirst(strtolower(trim($row['first_name'] ?? '')));
        $lastName = ucfirst(strtolower(trim($row['last_name'] ?? '')));

        // Validate email
        $email = trim($row['email'] ?? '');
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return null;
        }

        // Convert signup_date
        $date = \DateTime::createFromFormat('Y-m-d', $row['signup_date'] ?? '');
        if (!$date) {
            return null;
        }
        $signupDate = $date->setTimezone(new \DateTimeZone('UTC'))->format('c');

        // Amount spent
        $amountSpent = round((float)($row['amount_spent'] ?? 0), 2);
        if ($amountSpent < 10) {
            return null;
        }

        // Country mapping
        $countryCode = strtoupper(trim($row['country_code'] ?? ''));
        $country = $this->countries[$countryCode] ?? $countryCode;

        // Loyalty level
        $loyaltyLevel = match (true) {
            $amountSpent < 100 => 'Bronze',
            $amountSpent <= 500 => 'Silver',
            default => 'Gold',
        };

        return [
            'id' => (int)($row['id'] ?? 0),
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email,
            'signup_date' => $signupDate,
            'amount_spent' => $amountSpent,
            'country' => $country,
            'loyalty_level' => $loyaltyLevel,
        ];
    }
}
