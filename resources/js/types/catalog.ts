export type CatalogCategory = {
    id: number;
    name: string;
    slug: string;
    description: string | null;
};

export type CatalogProduct = {
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
    isAvailable: boolean;
    coverPath: string | null;
    category: CatalogCategory | null;
};

export type CatalogFilters = {
    category: string;
    q: string;
    format: string;
};

export type FormatOption = {
    value: string;
    label: string;
};

export type PaginatedProducts = {
    data: CatalogProduct[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    links: {
        url: string | null;
        label: string;
        active: boolean;
    }[];
};
