export type AdminOption = {
    value: string;
    label: string;
};

export type AdminCounts = {
    products: number;
    categories: number;
    preorders: number;
    orders: number;
    messages: number;
    users: number;
};

export type AdminCategory = {
    id: number;
    name: string;
    slug: string;
    description: string | null;
    productsCount?: number;
    isActive: boolean;
};

export type AdminProduct = {
    id: number;
    slug: string;
    title: string;
    author: string | null;
    publisher: string | null;
    isbn: string | null;
    format: string | null;
    formatLabel: string | null;
    language: string | null;
    pages: number | null;
    editionYear: number | null;
    synopsis: string | null;
    price: number;
    stock: number;
    availability: string | null;
    availabilityLabel: string | null;
    depositAmount: number | null;
    releasesAt: string | null;
    categoryId: number;
    categoryName: string | null;
    isActive: boolean;
    isPreorder: boolean;
};

export type AdminPreorder = {
    id: number;
    status: string;
    statusLabel: string | null;
    quantity: number;
    depositAmount: number;
    balanceAmount: number;
    releasesAt: string | null;
    chargedAt: string | null;
    refundedAt: string | null;
    user: {
        id: number;
        name: string;
        email: string;
    } | null;
    product: {
        id: number;
        slug: string;
        title: string;
    } | null;
    shippingAddress: Record<string, unknown> | null;
};

export type AdminOrder = {
    id: number;
    status: string;
    statusLabel: string | null;
    total: number;
    depositAmount: number;
    balanceAmount: number;
    placedAt: string | null;
    user: {
        id: number;
        name: string;
        email: string;
    } | null;
    itemsCount: number;
};

export type AdminOrderDetail = AdminOrder & {
    subtotal: number;
    shippingTotal: number;
    shippingAddress: Record<string, unknown> | null;
    items: Array<{
        id: number;
        title: string;
        author: string | null;
        formatLabel: string | null;
        quantity: number;
        unitPrice: number;
        lineTotal: number;
    }>;
};

export type AdminMessage = {
    id: number;
    name: string;
    email: string;
    phone: string | null;
    message: string;
    readAt: string | null;
    createdAt: string | null;
};

export type AdminUser = {
    id: number;
    name: string;
    email: string;
    phone: string | null;
    roles: string[];
    createdAt: string | null;
};
