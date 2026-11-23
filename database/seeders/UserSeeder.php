<?php

namespace Database\Seeders;

use App\Enums\OrderStatus;
use App\Enums\ProductFormat;
use App\Enums\UserRole;
use App\Models\ContactMessage;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use App\Support\CartView;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /** @var array<int, array{name: string, email: string, role: UserRole}> */
    private array $users = [
        [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'role' => UserRole::Client,
        ],
        [
            'name' => 'Cliente Demo',
            'email' => 'cliente@example.com',
            'role' => UserRole::Client,
        ],
        [
            'name' => 'Cliente Demo 2',
            'email' => 'cliente2@example.com',
            'role' => UserRole::Client,
        ],
        [
            'name' => 'Administrador',
            'email' => 'admin@example.com',
            'role' => UserRole::Admin,
        ],
        [
            'name' => 'Super Administrador',
            'email' => 'superadmin@example.com',
            'role' => UserRole::SuperAdmin,
        ],
    ];

    public function run(): void
    {
        foreach ($this->users as $data) {
            $user = User::firstOrCreate([
                'email' => $data['email'],
            ], [
                'name' => $data['name'],
                'password' => Hash::make('password'),
                'phone' => '272 123 4567',
                'email_verified_at' => now(),
            ]);

            $user->syncRoles([$data['role']->value]);
        }

        $this->seedOrders();
        $this->seedMessages();
    }

    private function seedOrders(): void
    {
        $products = Product::query()
            ->whereIn('slug', [
                'cien-anos-de-soledad',
                'cuaderno-de-lectura',
                'los-libros-de-jacob',
                'la-biblioteca-de-la-media-noche',
                'la-metamorfosis-digital',
            ])
            ->get()
            ->keyBy('slug');

        /** @var array<int, array{email: string, status: OrderStatus, placed_at: string, deposit: float, address: array<string, string>, items: array<int, array{slug: string, quantity: int}>}> $orders */
        $orders = [
            [
                'email' => 'cliente@example.com',
                'status' => OrderStatus::Delivered,
                'placed_at' => '2026-11-20 11:30:00',
                'deposit' => 0.0,
                'address' => [
                    'recipient_name' => 'Cliente Demo',
                    'street' => 'Calle 5 No. 12',
                    'city' => 'Orizaba',
                    'state' => 'Veracruz',
                    'postal_code' => '94300',
                    'country' => 'MX',
                ],
                'items' => [
                    ['slug' => 'cien-anos-de-soledad', 'quantity' => 1],
                    ['slug' => 'cuaderno-de-lectura', 'quantity' => 2],
                ],
            ],
            [
                'email' => 'cliente@example.com',
                'status' => OrderStatus::Confirmed,
                'placed_at' => '2026-11-21 16:45:00',
                'deposit' => 140.0,
                'address' => [
                    'recipient_name' => 'Cliente Demo',
                    'street' => 'Calle 5 No. 12',
                    'city' => 'Orizaba',
                    'state' => 'Veracruz',
                    'postal_code' => '94300',
                    'country' => 'MX',
                ],
                'items' => [
                    ['slug' => 'los-libros-de-jacob', 'quantity' => 1],
                ],
            ],
            [
                'email' => 'cliente2@example.com',
                'status' => OrderStatus::Preparing,
                'placed_at' => '2026-11-22 09:15:00',
                'deposit' => 80.0,
                'address' => [
                    'recipient_name' => 'Cliente Demo 2',
                    'street' => 'Avenida 6 No. 24',
                    'city' => 'Orizaba',
                    'state' => 'Veracruz',
                    'postal_code' => '94300',
                    'country' => 'MX',
                ],
                'items' => [
                    ['slug' => 'la-biblioteca-de-la-media-noche', 'quantity' => 1],
                    ['slug' => 'la-metamorfosis-digital', 'quantity' => 1],
                ],
            ],
        ];

        foreach ($orders as $data) {
            $user = User::query()->where('email', $data['email'])->firstOrFail();

            /** @var array<int, array{product: Product, quantity: int}> $items */
            $items = [];

            foreach ($data['items'] as $item) {
                $product = $products->get($item['slug']);

                if (! $product instanceof Product) {
                    continue;
                }

                $items[] = ['product' => $product, 'quantity' => $item['quantity']];
            }

            if ($items === []) {
                continue;
            }

            $subtotal = 0.0;
            $requiresShipping = false;

            foreach ($items as $item) {
                $subtotal += round((float) $item['product']->price * $item['quantity'], 2);

                $format = $item['product']->getAttribute('format');
                $formatValue = $format instanceof ProductFormat ? $format->value : null;

                $requiresShipping = $requiresShipping || $formatValue === ProductFormat::Printed->value;
            }

            $subtotal = round($subtotal, 2);
            $shippingTotal = $requiresShipping ? CartView::SHIPPING_TOTAL : 0.0;
            $total = round($subtotal + $shippingTotal, 2);

            $order = Order::firstOrCreate(
                ['user_id' => $user->id, 'placed_at' => Carbon::parse($data['placed_at'])],
                [
                    'status' => $data['status'],
                    'subtotal' => $subtotal,
                    'shipping_total' => $shippingTotal,
                    'total' => $total,
                    'deposit_amount' => $data['deposit'],
                    'balance_amount' => round($total - $data['deposit'], 2),
                    'shipping_address' => $data['address'],
                ],
            );

            foreach ($items as $item) {
                OrderItem::firstOrCreate(
                    ['order_id' => $order->id, 'product_id' => $item['product']->id],
                    [
                        'title' => $item['product']->title,
                        'author' => $item['product']->author,
                        'format' => $item['product']->format,
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['product']->price,
                        'line_total' => round((float) $item['product']->price * $item['quantity'], 2),
                    ],
                );
            }
        }
    }

    private function seedMessages(): void
    {
        /** @var array<int, array{name: string, email: string, phone: string, message: string, read_at: string|null}> $messages */
        $messages = [
            [
                'name' => 'Cliente Demo',
                'email' => 'cliente@example.com',
                'phone' => '272 123 4567',
                'message' => '¿El ejemplar de Los libros de Jacob incluye envío a Orizaba?',
                'read_at' => '2026-11-22 12:00:00',
            ],
            [
                'name' => 'Cliente Demo 2',
                'email' => 'cliente2@example.com',
                'phone' => '272 765 4321',
                'message' => 'Quisiera saber cuándo llega La biblioteca de la media noche.',
                'read_at' => null,
            ],
            [
                'name' => 'Test User',
                'email' => 'test@example.com',
                'phone' => '272 123 4567',
                'message' => '¿Pueden enviarme el catálogo impreso de la librería?',
                'read_at' => null,
            ],
        ];

        foreach ($messages as $data) {
            ContactMessage::firstOrCreate(
                ['email' => $data['email'], 'message' => $data['message']],
                [
                    'name' => $data['name'],
                    'phone' => $data['phone'],
                    'consent' => true,
                    'privacy_accepted' => true,
                    'read_at' => $data['read_at'] === null ? null : Carbon::parse($data['read_at']),
                ],
            );
        }
    }
}
