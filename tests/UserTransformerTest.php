<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use App\Transformer\UserTransformer;

class UserTransformerTest extends TestCase
{
    private array $countries = [
        'US' => 'United States',
        'DE' => 'Germany',
        'FR' => 'France',
    ];

    public function testTransformValidRow(): void
    {
        $transformer = new UserTransformer($this->countries);
        $row = [
            'id' => '1',
            'first_name' => ' john ',
            'last_name' => 'doe',
            'email' => 'john@example.com',
            'signup_date' => '2025-11-15',
            'amount_spent' => '150.456',
            'country_code' => 'US',
        ];
        $result = $transformer->transform($row);
        $this->assertNotNull($result);
        $this->assertSame('John', $result['first_name']);
        $this->assertSame('Doe', $result['last_name']);
        $this->assertSame('john@example.com', $result['email']);
        $this->assertSame('United States', $result['country']);
        $this->assertSame(150.46, $result['amount_spent']);
        $this->assertSame('Silver', $result['loyalty_level']);
        $this->assertSame(1, $result['id']);
        // Validar solo la fecha en formato ISO 8601, ignorando la hora
        $this->assertMatchesRegularExpression('/^2025-11-15T[0-9]{2}:[0-9]{2}:[0-9]{2}\+00:00$/', $result['signup_date']);
    }

    public function testTransformInvalidEmail(): void
    {
        $transformer = new UserTransformer($this->countries);
        $row = [
            'id' => '2',
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'email' => 'invalid-email',
            'signup_date' => '2023-01-01',
            'amount_spent' => '200',
            'country_code' => 'DE',
        ];
        $result = $transformer->transform($row);
        $this->assertNull($result);
    }

    public function testTransformLowAmountSpent(): void
    {
        $transformer = new UserTransformer($this->countries);
        $row = [
            'id' => '3',
            'first_name' => 'Alice',
            'last_name' => 'Wonder',
            'email' => 'alice@example.com',
            'signup_date' => '2023-01-01',
            'amount_spent' => '5',
            'country_code' => 'FR',
        ];
        $result = $transformer->transform($row);
        $this->assertNull($result);
    }

    public function testTransformCountryFallback(): void
    {
        $transformer = new UserTransformer($this->countries);
        $row = [
            'id' => '4',
            'first_name' => 'Bob',
            'last_name' => 'Builder',
            'email' => 'bob@example.com',
            'signup_date' => '2023-01-01',
            'amount_spent' => '600',
            'country_code' => 'XX',
        ];
        $result = $transformer->transform($row);
        $this->assertNotNull($result);
        $this->assertSame('XX', $result['country']);
        $this->assertSame('Gold', $result['loyalty_level']);
    }

    public function testTransformInvalidDate(): void
    {
        $transformer = new UserTransformer($this->countries);
        $row = [
            'id' => '5',
            'first_name' => 'Eve',
            'last_name' => 'Adams',
            'email' => 'eve@example.com',
            'signup_date' => 'not-a-date',
            'amount_spent' => '100',
            'country_code' => 'US',
        ];
        $result = $transformer->transform($row);
        $this->assertNull($result);
    }
}
