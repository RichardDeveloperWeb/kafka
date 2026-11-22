export interface CartItemData {
    id: number;
    quantity: number;
    productId: number;
    slug: string;
    title: string;
    author: string | null;
    price: number;
    lineTotal: number;
    stock: number;
    format: string | null;
    formatLabel: string | null;
    availability: string | null;
    availabilityLabel: string | null;
    requiresShipping: boolean;
}

export interface CartSummary {
    items: CartItemData[];
    count: number;
    subtotal: number;
    shippingTotal: number;
    total: number;
    requiresShipping: boolean;
}

export interface AddressData {
    id: number;
    type: string | null;
    typeLabel: string | null;
    label: string | null;
    recipientName: string;
    street: string;
    exteriorNumber: string;
    interiorNumber: string | null;
    neighborhood: string | null;
    city: string;
    state: string;
    postalCode: string;
    country: string;
    phone: string | null;
    isDefault: boolean;
}

export interface ReservationData {
    id: number;
    orderId: number | null;
    quantity: number;
    status: string | null;
    statusLabel: string | null;
    depositAmount: number;
    balanceAmount: number;
    releasesAt: string | null;
    chargedAt: string | null;
    refundedAt: string | null;
    isCancellable: boolean;
    shippingAddress: Record<string, string | null | undefined>;
    product: {
        id: number;
        slug: string;
        title: string;
        author: string | null;
        price: number;
        depositAmount: number | null;
    };
}

export interface OrderItemData {
    id: number;
    productId: number | null;
    title: string;
    author: string | null;
    format: string | null;
    formatLabel: string | null;
    quantity: number;
    unitPrice: number;
    lineTotal: number;
}

export interface ShippingAddressSnapshot {
    label?: string | null;
    recipient_name?: string | null;
    street?: string | null;
    exterior_number?: string | null;
    interior_number?: string | null;
    neighborhood?: string | null;
    city?: string | null;
    state?: string | null;
    postal_code?: string | null;
    country?: string | null;
    phone?: string | null;
}

export interface OrderData {
    id: number;
    status: string | null;
    statusLabel: string | null;
    subtotal: number;
    shippingTotal: number;
    total: number;
    depositAmount: number;
    balanceAmount: number;
    shippingAddress: ShippingAddressSnapshot;
    placedAt: string | null;
    items: OrderItemData[];
}

export interface DashboardOverview {
    cartCount: number;
    addresses: number;
    orders: number;
    reservations: number;
}
