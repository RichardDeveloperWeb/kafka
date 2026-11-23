<?php

namespace Database\Seeders;

use App\Enums\ProductAvailability;
use App\Enums\ProductFormat;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CatalogSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $categories = [
            ['name' => 'Libros físicos', 'slug' => 'libros-fisicos', 'description' => 'Obras impresas para todos los gustos.'],
            ['name' => 'Revistas', 'slug' => 'revistas', 'description' => 'Publicaciones periódicas de cultura y actualidad.'],
            ['name' => 'Publicaciones impresas', 'slug' => 'publicaciones-impresas', 'description' => 'Atlas, diccionarios y otras referencias impresas.'],
            ['name' => 'Libros electrónicos', 'slug' => 'libros-electronicos', 'description' => 'Lecturas digitales para leer desde cualquier lugar.'],
            ['name' => 'Audiolibros', 'slug' => 'audiolibros', 'description' => 'Historias narradas para escuchar en cualquier momento.'],
            ['name' => 'Material didáctico', 'slug' => 'material-didactico', 'description' => 'Recursos para acompañar el aprendizaje y la lectura.'],
            ['name' => 'Papelería', 'slug' => 'papeleria', 'description' => 'Cuadernos, plumas y útiles para escritores y lectores.'],
            ['name' => 'Regalos', 'slug' => 'regalos', 'description' => 'Detalles pensados para quienes aman los libros.'],
            ['name' => 'Accesorios de lectura', 'slug' => 'accesorios-de-lectura', 'description' => 'Objetos que acompañan cada sesión de lectura.'],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(['slug' => $category['slug']], $category);
        }

        $categoryIds = Category::query()
            ->whereIn('slug', array_column($categories, 'slug'))
            ->pluck('id', 'slug');

        $products = [
            [
                'slug' => 'cien-anos-de-soledad',
                'category' => 'libros-fisicos',
                'title' => 'Cien años de soledad',
                'author' => 'Gabriel García Márquez',
                'publisher' => 'Sudamericana',
                'isbn' => '9789500706804',
                'format' => ProductFormat::Printed,
                'price' => 499,
                'stock' => 12,
                'synopsis' => 'La historia de la familia Buendía y el fundador de Macondo, una de las obras fundamentales de la literatura en español.',
            ],
            [
                'slug' => 'el-aleph',
                'category' => 'libros-fisicos',
                'title' => 'El Aleph',
                'author' => 'Jorge Luis Borges',
                'publisher' => 'Emecé',
                'isbn' => '9789500409503',
                'format' => ProductFormat::Printed,
                'price' => 389,
                'stock' => 8,
                'availability' => ProductAvailability::OnDemand,
                'deposit_amount' => 100,
                'releases_at' => now()->addDays(21),
                'synopsis' => 'Cuentos que exploran el tiempo, el infinito y los laberintos de la imaginación.',
            ],
            [
                'slug' => 'los-libros-de-jacob',
                'category' => 'libros-fisicos',
                'title' => 'Los libros de Jacob',
                'author' => 'Olga Tokarczuk',
                'publisher' => 'Siruela',
                'isbn' => '9788416317581',
                'format' => ProductFormat::Printed,
                'price' => 520,
                'stock' => 14,
                'availability' => ProductAvailability::OnDemand,
                'deposit_amount' => 140,
                'releases_at' => now()->addDays(18),
                'synopsis' => 'Una reconstrucción literaria de la infancia y la madurez de una escritora polaca, donde los libros deforman la memoria y revelan los secretos de una familia marcada por la historia.',
            ],
            [
                'slug' => 'nuestra-parte-de-noche',
                'category' => 'libros-fisicos',
                'title' => 'Nuestra parte de noche',
                'author' => 'Mariana Enríquez',
                'publisher' => 'Anagrama',
                'isbn' => '9788418539803',
                'format' => ProductFormat::Printed,
                'price' => 590,
                'stock' => 10,
                'availability' => ProductAvailability::OnDemand,
                'deposit_amount' => 160,
                'releases_at' => now()->addDays(32),
                'synopsis' => 'Una novela de terror y maternidad en la Argentina de los años ochenta, donde una secta secreta, un padre desaparecido y una niña con un don inquietante se cruzan en la noche.',
            ],
            [
                'slug' => 'la-biblioteca-de-la-media-noche',
                'category' => 'libros-electronicos',
                'title' => 'La biblioteca de la media noche',
                'author' => 'Matt Haig',
                'publisher' => 'Alfaguara',
                'isbn' => '9788418532675',
                'format' => ProductFormat::Ebook,
                'price' => 210,
                'stock' => 0,
                'availability' => ProductAvailability::OnDemand,
                'deposit_amount' => 80,
                'releases_at' => now()->addDays(11),
                'synopsis' => 'Una novela sobre el duelo y la amistad en la que una biblioteca secreta ofrece a una mujer la oportunidad de probar otras vidas y elegir la que realmente desea.',
            ],
            [
                'slug' => 'revista-letras-de-veracruz',
                'category' => 'revistas',
                'title' => 'Letras de Veracruz',
                'author' => 'Colectivo editorial',
                'publisher' => 'Los Cuentos de Kafka',
                'isbn' => null,
                'format' => ProductFormat::Printed,
                'price' => 120,
                'stock' => 25,
                'synopsis' => 'Una revista dedicada a la literatura, la cultura y las voces de la región.',
            ],
            [
                'slug' => 'atlas-ilustrado-del-mundo',
                'category' => 'publicaciones-impresas',
                'title' => 'Atlas ilustrado del mundo',
                'author' => 'Equipo editorial',
                'publisher' => 'Parramón',
                'isbn' => '9788466330012',
                'format' => ProductFormat::Printed,
                'price' => 790,
                'stock' => 5,
                'synopsis' => 'Un recorrido visual por los continentes, sus culturas y sus paisajes.',
            ],
            [
                'slug' => 'la-metamorfosis-digital',
                'category' => 'libros-electronicos',
                'title' => 'La metamorfosis',
                'author' => 'Franz Kafka',
                'publisher' => 'Ediciones digitales',
                'isbn' => '9788498149585',
                'format' => ProductFormat::Ebook,
                'price' => 149,
                'stock' => 0,
                'synopsis' => 'La transformación de Gregor Samsa como una reflexión sobre la identidad y la soledad.',
            ],
            [
                'slug' => 'don-quijote-audiolibro',
                'category' => 'audiolibros',
                'title' => 'Don Quijote de la Mancha',
                'author' => 'Miguel de Cervantes',
                'publisher' => 'Audio Clásicos',
                'isbn' => '9788497592208',
                'format' => ProductFormat::Audiobook,
                'price' => 299,
                'stock' => 0,
                'synopsis' => 'Una narración íntegra de la novela que inauguró la literatura moderna.',
            ],
            [
                'slug' => 'cuaderno-de-lectura',
                'category' => 'papeleria',
                'title' => 'Cuaderno de lectura',
                'author' => null,
                'publisher' => 'Los Cuentos de Kafka',
                'isbn' => null,
                'format' => ProductFormat::Printed,
                'price' => 95,
                'stock' => 40,
                'synopsis' => 'Un cuaderno para registrar ideas, citas y reflexiones mientras lees.',
            ],
            [
                'slug' => 'kit-de-lectura-critica',
                'category' => 'material-didactico',
                'title' => 'Kit de lectura crítica',
                'author' => 'Equipo pedagógico',
                'publisher' => 'Los Cuentos de Kafka',
                'isbn' => null,
                'format' => ProductFormat::Printed,
                'price' => 260,
                'stock' => 15,
                'synopsis' => 'Material para organizar clubes de lectura y talleres de análisis literario.',
            ],
            [
                'slug' => 'set-de-regalo-para-lectores',
                'category' => 'regalos',
                'title' => 'Set de regalo para lectores',
                'author' => null,
                'publisher' => 'Los Cuentos de Kafka',
                'isbn' => null,
                'format' => ProductFormat::Printed,
                'price' => 450,
                'stock' => 10,
                'synopsis' => 'Una selección de detalles para sorprender a quien nunca sale de un libro.',
            ],
            [
                'slug' => 'marcapaginas-de-laton',
                'category' => 'accesorios-de-lectura',
                'title' => 'Marcapáginas de latón',
                'author' => null,
                'publisher' => 'Los Cuentos de Kafka',
                'isbn' => null,
                'format' => ProductFormat::Printed,
                'price' => 180,
                'stock' => 0,
                'synopsis' => 'Un marcapáginas duradero y elegante para acompañar tus lecturas.',
            ],
        ];

        foreach ($products as $product) {
            Product::firstOrCreate(['slug' => $product['slug']], [
                'category_id' => $categoryIds[$product['category']],
                'title' => $product['title'],
                'author' => $product['author'],
                'publisher' => $product['publisher'],
                'isbn' => $product['isbn'],
                'format' => $product['format'],
                'language' => 'es',
                'pages' => null,
                'edition_year' => 2026,
                'synopsis' => $product['synopsis'],
                'price' => $product['price'],
                'stock' => $product['stock'],
                'availability' => $product['availability'] ?? ($product['stock'] > 0
                    ? ProductAvailability::InStock
                    : ProductAvailability::OutOfStock),
                'deposit_amount' => $product['deposit_amount'] ?? null,
                'releases_at' => $product['releases_at'] ?? null,
                'cover_path' => null,
                'is_active' => true,
            ]);
        }
    }
}
