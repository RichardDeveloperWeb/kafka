<?php

namespace App\Support;

final class Permissions
{
    public const ACCESS_ADMIN_PANEL = 'access_admin_panel';

    public const PRODUCTS_MANAGE = 'products.manage';

    public const CATEGORIES_MANAGE = 'categories.manage';

    public const ORDERS_VIEW_ANY = 'orders.view_any';

    public const ORDERS_MANAGE = 'orders.manage';

    public const USERS_VIEW_ANY = 'users.view_any';

    public const USERS_MANAGE = 'users.manage';

    public const MESSAGES_VIEW_ANY = 'messages.view_any';

    public const MESSAGES_MANAGE = 'messages.manage';

    public const CART_MANAGE = 'cart.manage';

    public const ADDRESSES_MANAGE = 'addresses.manage';

    public const RESERVATIONS_MANAGE = 'reservations.manage';

    public const ORDERS_VIEW_OWN = 'orders.view_own';

    public const CONTACT_SEND = 'contact.send';

    public const ALL = [
        self::ACCESS_ADMIN_PANEL,
        self::PRODUCTS_MANAGE,
        self::CATEGORIES_MANAGE,
        self::ORDERS_VIEW_ANY,
        self::ORDERS_MANAGE,
        self::USERS_VIEW_ANY,
        self::USERS_MANAGE,
        self::MESSAGES_VIEW_ANY,
        self::MESSAGES_MANAGE,
        self::CART_MANAGE,
        self::ADDRESSES_MANAGE,
        self::RESERVATIONS_MANAGE,
        self::ORDERS_VIEW_OWN,
        self::CONTACT_SEND,
    ];

    public const ADMIN = self::ALL;

    public const CLIENT = [
        self::CART_MANAGE,
        self::ADDRESSES_MANAGE,
        self::RESERVATIONS_MANAGE,
        self::ORDERS_VIEW_OWN,
        self::CONTACT_SEND,
    ];
}
