<?php

namespace App\Enums;

use App\Support\Permissions;

enum ManagerRole: string
{
    case Users = 'users.manager';
    case Products = 'products.manager';
    case Categories = 'categories.manager';
    case Orders = 'orders.manager';
    case Messages = 'messages.manager';
    case Cart = 'cart.manager';
    case Addresses = 'addresses.manager';
    case Reservations = 'reservations.manager';

    public function label(): string
    {
        return match ($this) {
            self::Users => 'Gestor de usuarios',
            self::Products => 'Gestor de productos',
            self::Categories => 'Gestor de categorías',
            self::Orders => 'Gestor de pedidos',
            self::Messages => 'Gestor de mensajes',
            self::Cart => 'Gestor del carrito',
            self::Addresses => 'Gestor de direcciones',
            self::Reservations => 'Gestor de reservas',
        };
    }

    /**
     * @return list<string>
     */
    public function permissions(): array
    {
        return match ($this) {
            self::Users => [
                Permissions::USERS_INDEX,
                Permissions::USERS_CREATE,
                Permissions::USERS_UPDATE,
                Permissions::USERS_DELETE,
            ],
            self::Products => [
                Permissions::PRODUCTS_INDEX,
                Permissions::PRODUCTS_CREATE,
                Permissions::PRODUCTS_UPDATE,
                Permissions::PRODUCTS_DELETE,
            ],
            self::Categories => [
                Permissions::CATEGORIES_INDEX,
                Permissions::CATEGORIES_CREATE,
                Permissions::CATEGORIES_UPDATE,
                Permissions::CATEGORIES_DELETE,
            ],
            self::Orders => [
                Permissions::ORDERS_INDEX,
                Permissions::ORDERS_SHOW,
                Permissions::ORDERS_UPDATE,
                Permissions::ORDERS_DELETE,
            ],
            self::Messages => [
                Permissions::MESSAGES_INDEX,
                Permissions::MESSAGES_SHOW,
                Permissions::MESSAGES_UPDATE,
                Permissions::MESSAGES_DELETE,
            ],
            self::Cart => [
                Permissions::CART_INDEX,
                Permissions::CART_CREATE,
                Permissions::CART_UPDATE,
                Permissions::CART_DELETE,
            ],
            self::Addresses => [
                Permissions::ADDRESSES_INDEX,
                Permissions::ADDRESSES_CREATE,
                Permissions::ADDRESSES_UPDATE,
                Permissions::ADDRESSES_DELETE,
            ],
            self::Reservations => [
                Permissions::RESERVATIONS_INDEX,
                Permissions::RESERVATIONS_CREATE,
                Permissions::RESERVATIONS_UPDATE,
                Permissions::RESERVATIONS_DELETE,
            ],
        };
    }
}
