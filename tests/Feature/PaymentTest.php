<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentTest extends TestCase
{
    use RefreshDatabase;

    private function createAuthenticatedUser()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;
        return ['user' => $user, 'token' => $token];
    }

    public function test_authenticated_user_can_create_payment(): void
    {
        $auth = $this->createAuthenticatedUser();
        $order = Order::factory()->create(['user_id' => $auth['user']->id]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $auth['token'],
        ])->postJson('/api/payments', [
            'order_id' => $order->id,
            'payment_method' => 'credit_card',
            'amount' => 150.50,
            'paid_at' => '2025-12-04 10:45:00',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id', 'order_id', 'payment_method', 'amount', 'paid_at', 'created_at', 'order'],
            ])
            ->assertJson([
                'success' => true,
                'message' => 'Payment created successfully',
            ]);

        $this->assertDatabaseHas('payments', [
            'order_id' => $order->id,
            'payment_method' => 'credit_card',
        ]);
    }

    public function test_unauthenticated_user_cannot_create_payment(): void
    {
        $order = Order::factory()->create();

        $response = $this->postJson('/api/payments', [
            'order_id' => $order->id,
            'payment_method' => 'credit_card',
            'amount' => 150.50,
        ]);

        $response->assertStatus(401);
    }

    public function test_payment_creation_fails_with_invalid_order_id(): void
    {
        $auth = $this->createAuthenticatedUser();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $auth['token'],
        ])->postJson('/api/payments', [
            'order_id' => 99999,
            'payment_method' => 'credit_card',
            'amount' => 150.50,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['order_id']);
    }

    public function test_payment_creation_fails_with_negative_amount(): void
    {
        $auth = $this->createAuthenticatedUser();
        $order = Order::factory()->create(['user_id' => $auth['user']->id]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $auth['token'],
        ])->postJson('/api/payments', [
            'order_id' => $order->id,
            'payment_method' => 'credit_card',
            'amount' => -50,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['amount']);
    }

    public function test_authenticated_user_can_get_all_payments(): void
    {
        $auth = $this->createAuthenticatedUser();
        $order = Order::factory()->create(['user_id' => $auth['user']->id]);
        Payment::factory()->count(3)->create(['order_id' => $order->id]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $auth['token'],
        ])->getJson('/api/payments');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => ['id', 'order_id', 'payment_method', 'amount', 'paid_at', 'created_at', 'order'],
                ],
            ])
            ->assertJson([
                'success' => true,
            ]);
    }

    public function test_unauthenticated_user_cannot_get_payments(): void
    {
        $response = $this->getJson('/api/payments');

        $response->assertStatus(401);
    }

    public function test_authenticated_user_can_get_single_payment(): void
    {
        $auth = $this->createAuthenticatedUser();
        $order = Order::factory()->create(['user_id' => $auth['user']->id]);
        $payment = Payment::factory()->create(['order_id' => $order->id]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $auth['token'],
        ])->getJson('/api/payments/' . $payment->id);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => ['id', 'order_id', 'payment_method', 'amount', 'paid_at', 'created_at', 'order'],
            ])
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $payment->id,
                ],
            ]);
    }

    public function test_get_nonexistent_payment_returns_404(): void
    {
        $auth = $this->createAuthenticatedUser();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $auth['token'],
        ])->getJson('/api/payments/99999');

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'Payment not found',
            ]);
    }

    public function test_authenticated_user_can_update_payment_with_put(): void
    {
        $auth = $this->createAuthenticatedUser();
        $order = Order::factory()->create(['user_id' => $auth['user']->id]);
        $payment = Payment::factory()->create(['order_id' => $order->id]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $auth['token'],
        ])->putJson('/api/payments/' . $payment->id, [
            'order_id' => $order->id,
            'payment_method' => 'bank_transfer',
            'amount' => 200.00,
            'paid_at' => '2025-12-04 15:00:00',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Payment updated successfully',
                'data' => [
                    'payment_method' => 'bank_transfer',
                    'amount' => '200.00',
                ],
            ]);

        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'payment_method' => 'bank_transfer',
        ]);
    }

    public function test_authenticated_user_can_update_payment_with_patch(): void
    {
        $auth = $this->createAuthenticatedUser();
        $order = Order::factory()->create(['user_id' => $auth['user']->id]);
        $payment = Payment::factory()->create([
            'order_id' => $order->id,
            'payment_method' => 'credit_card',
            'amount' => 100.00,
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $auth['token'],
        ])->patchJson('/api/payments/' . $payment->id, [
            'payment_method' => 'stripe',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Payment updated successfully',
                'data' => [
                    'payment_method' => 'stripe',
                    'amount' => '100.00', // Should remain unchanged
                ],
            ]);

        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'payment_method' => 'stripe',
            'amount' => 100.00,
        ]);
    }

    public function test_update_nonexistent_payment_returns_404(): void
    {
        $auth = $this->createAuthenticatedUser();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $auth['token'],
        ])->putJson('/api/payments/99999', [
            'order_id' => 1,
            'payment_method' => 'cash',
            'amount' => 50.00,
        ]);

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'Payment not found',
            ]);
    }

    public function test_authenticated_user_can_delete_payment(): void
    {
        $auth = $this->createAuthenticatedUser();
        $order = Order::factory()->create(['user_id' => $auth['user']->id]);
        $payment = Payment::factory()->create(['order_id' => $order->id]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $auth['token'],
        ])->deleteJson('/api/payments/' . $payment->id);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Payment deleted successfully',
            ]);

        $this->assertDatabaseMissing('payments', [
            'id' => $payment->id,
        ]);
    }

    public function test_delete_nonexistent_payment_returns_404(): void
    {
        $auth = $this->createAuthenticatedUser();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $auth['token'],
        ])->deleteJson('/api/payments/99999');

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'Payment not found',
            ]);
    }

    public function test_unauthenticated_user_cannot_delete_payment(): void
    {
        $order = Order::factory()->create();
        $payment = Payment::factory()->create(['order_id' => $order->id]);

        $response = $this->deleteJson('/api/payments/' . $payment->id);

        $response->assertStatus(401);
    }
}
