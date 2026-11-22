<?php

namespace App\Support;

final class Permissions
{
    public const ADMIN_INDEX = 'admin.index';

    public const USERS_INDEX = 'users.index';

    public const USERS_CREATE = 'users.create';

    public const USERS_UPDATE = 'users.update';

    public const USERS_DELETE = 'users.delete';

    public const PRODUCTS_INDEX = 'products.index';

    public const PRODUCTS_CREATE = 'products.create';

    public const PRODUCTS_UPDATE = 'products.update';

    public const PRODUCTS_DELETE = 'products.delete';

    public const CATEGORIES_INDEX = 'categories.index';

    public const CATEGORIES_CREATE = 'categories.create';

    public const CATEGORIES_UPDATE = 'categories.update';

    public const CATEGORIES_DELETE = 'categories.delete';

    public const ORDERS_INDEX = 'orders.index';

    public const ORDERS_CREATE = 'orders.create';

    public const ORDERS_SHOW = 'orders.show';

    public const ORDERS_UPDATE = 'orders.update';

    public const ORDERS_DELETE = 'orders.delete';

    public const MESSAGES_INDEX = 'messages.index';

    public const MESSAGES_CREATE = 'messages.create';

    public const MESSAGES_SHOW = 'messages.show';

    public const MESSAGES_UPDATE = 'messages.update';

    public const MESSAGES_DELETE = 'messages.delete';

    public const CART_INDEX = 'cart.index';

    public const CART_CREATE = 'cart.create';

    public const CART_UPDATE = 'cart.update';

    public const CART_DELETE = 'cart.delete';

    public const ADDRESSES_INDEX = 'addresses.index';

    public const ADDRESSES_CREATE = 'addresses.create';

    public const ADDRESSES_UPDATE = 'addresses.update';

    public const ADDRESSES_DELETE = 'addresses.delete';

    public const RESERVATIONS_INDEX = 'reservations.index';

    public const RESERVATIONS_CREATE = 'reservations.create';

    public const RESERVATIONS_UPDATE = 'reservations.update';

    public const RESERVATIONS_DELETE = 'reservations.delete';

    public const ALL = [
        self::ADMIN_INDEX,
        self::USERS_INDEX,
        self::USERS_CREATE,
        self::USERS_UPDATE,
        self::USERS_DELETE,
        self::PRODUCTS_INDEX,
        self::PRODUCTS_CREATE,
        self::PRODUCTS_UPDATE,
        self::PRODUCTS_DELETE,
        self::CATEGORIES_INDEX,
        self::CATEGORIES_CREATE,
        self::CATEGORIES_UPDATE,
        self::CATEGORIES_DELETE,
        self::ORDERS_INDEX,
        self::ORDERS_CREATE,
        self::ORDERS_SHOW,
        self::ORDERS_UPDATE,
        self::ORDERS_DELETE,
        self::MESSAGES_INDEX,
        self::MESSAGES_CREATE,
        self::MESSAGES_SHOW,
        self::MESSAGES_UPDATE,
        self::MESSAGES_DELETE,
        self::CART_INDEX,
        self::CART_CREATE,
        self::CART_UPDATE,
        self::CART_DELETE,
        self::ADDRESSES_INDEX,
        self::ADDRESSES_CREATE,
        self::ADDRESSES_UPDATE,
        self::ADDRESSES_DELETE,
        self::RESERVATIONS_INDEX,
        self::RESERVATIONS_CREATE,
        self::RESERVATIONS_UPDATE,
        self::RESERVATIONS_DELETE,
    ];

    public const ADMIN = self::ALL;

    public const CLIENT = [
        self::MESSAGES_CREATE,
        self::CART_INDEX,
        self::CART_CREATE,
        self::CART_UPDATE,
        self::CART_DELETE,
        self::ADDRESSES_INDEX,
        self::ADDRESSES_CREATE,
        self::ADDRESSES_UPDATE,
        self::ADDRESSES_DELETE,
        self::RESERVATIONS_INDEX,
        self::RESERVATIONS_CREATE,
        self::RESERVATIONS_UPDATE,
        self::RESERVATIONS_DELETE,
        self::ORDERS_CREATE,
        self::ORDERS_SHOW,
    ];
}
