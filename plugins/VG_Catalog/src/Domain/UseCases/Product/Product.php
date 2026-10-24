<?php

namespace VG_Catalog\Domain\UseCases\Product;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OpenApi\Attributes as OA;
use VG_Catalog\Domain\UseCases\GroupOption\GroupOption;

#[OA\Schema(
    schema: 'Product',
    title: 'Product',
    description: 'Product model',
    required: ['id', 'title', 'description', 'price', 'sale_price', 'sku', 'created_at', 'updated_at'],
    properties: [
        new OA\Property(
            property: 'id',
            type: 'integer',
            description: 'Product id',
            example: 1
        ),
        new OA\Property(
            property: 'title',
            type: 'string',
            description: 'Product title',
            example: 'Product title'
        ),
        new OA\Property(
            property: 'description',
            type: 'string',
            description: 'Product description',
            example: 'Product description'
        ),
        new OA\Property(
            property: 'price',
            type: 'number',
            format: 'float',
            description: 'Product price',
            example: 10.0
        ),
        new OA\Property(
            property: 'sale_price',
            type: 'number',
            format: 'float',
            description: 'Product sale price',
            example: 9.0
        ),
        new OA\Property(
            property: 'main_image',
            type: 'string',
            description: 'Title image',
            example: 'http://example.com/image.jpg'
        ),
        new OA\Property(
            property: 'gallery',
            type: 'array',
            description: 'Product gallery images',
            items: new OA\Items(type: 'string', description: 'Image', example: 'http://example.com/image.jpg'),
        ),
        new OA\Property(
            property: 'categories',
            type: 'array',
            description: 'Product categories',
            properties: [
                new OA\Property(
                    property: 'term_id',
                    type: 'int',
                    example: 1
                ),
                new OA\Property(
                    property: 'name',
                    type: 'string',
                    example: 'Category name'
                ),
                new OA\Property(
                    property: 'slug',
                    type: 'string',
                    example: 'Category slug'
                ),
                new OA\Property(
                    property: 'term_group',
                    type: 'int',
                    example: 1
                ),
                new OA\Property(
                    property: 'term_taxonomy_id',
                    type: 'int',
                    example: 1
                ),
                new OA\Property(
                    property: 'taxonomy',
                    type: 'string',
                    example: 'vg_product_category'
                ),
                new OA\Property(
                    property: 'description',
                    type: 'string',
                    example: 'description'
                ),
                new OA\Property(
                    property: 'parent',
                    type: 'int',
                    example: 1
                ),
                new OA\Property(
                    property: 'count',
                    type: 'int',
                    example: 1
                ),
                new OA\Property(
                    property: 'filter',
                    type: 'string',
                    example: 'raw'
                ),
            ]
        ),
        new OA\Property(
            property: 'sku',
            type: 'string',
            example: 'article'
        ),
        new OA\Property(
            property: 'unit',
            type: 'string',
            example: 'unit'
        ),
        new OA\Property(
            property: 'post_id',
            type: 'int',
            example: 1
        ),
        new OA\Property(
            property: 'group',
            type: 'array',
            items: new OA\Items(ref: '#/components/schemas/ProductGroup')
        ),
        new OA\Property(
            property: 'created_at',
            type: 'string',
            format: 'date-time',
            description: 'Date when product created',
            example: '2000-01-01 00:00:00'
        ),
        new OA\Property(
            property: 'updated_at',
            type: 'string',
            format: 'date-time',
            description: 'Date when product updated',
            example: '2000-01-01 00:00:00'
        )
    ]
)]
class Product extends Model
{
    public $timestamps = true;
    protected $table = 'vg_products';
    protected $primaryKey = 'id';
    protected $fillable = [
        'description',
        'price',
        'sale_price',
        'post_id',
        'sku',
    ];

    protected $appends = [
        'title',
        'categories',
        'main_image',
        'gallery',
        'unit',
        'href',
        'status',
        'is_hit',
        'options'
    ];

    protected $hidden = [
        'categoriesRelation'
    ];

    public function getOptionsAttribute(): array
    {
        if (!$this->relationLoaded('groups')) {
            $this->load('groups');
        }

        $groupIds = $this->groups->pluck('group_id')->toArray();

        if (empty($groupIds)) {
            return [];
        }

        return GroupOption::with('options')
            ->whereIn('post_id', $groupIds)
            ->get()
            ->toArray();
    }

    public function getHrefAttribute(): string
    {
        return get_permalink($this->post_id);
    }

    public function getIsHitAttribute(): string
    {
        return carbon_get_post_meta($this->post_id, 'is_hit');
    }

    public function getUnitAttribute()
    {
        $meta = carbon_get_post_meta($this->post_id, 'unit');
        if (empty($meta)) {
            return [];
        }
        $term = get_term($meta[0]['id'], 'vg_product_unit');
        return is_wp_error($term) ? [] : $term;
    }

    public function getStatusAttribute()
    {
        return carbon_get_post_meta($this->post_id, 'status');
    }

    public function getTitleAttribute(): string
    {
        return get_post($this->post_id)->post_title;
    }

    public function getCategoriesAttribute(): array
    {
        return wp_get_post_terms($this->post_id, 'vg_product_category');
    }

    public function getMainImageAttribute(): bool|string
    {
        return wp_get_attachment_url(get_post_meta($this->post_id, '_vg_main_image')[0]);
    }

    public function getGalleryAttribute(): array
    {
        $i = 0;
        $images = [];
        do {
            $image = get_post_meta($this->post_id, "_vg_gallery|vg_image|$i|0|value");
            if ($image) {
                $images[] = wp_get_attachment_url($image[0]);
            }
            $i++;
        } while ($image);

        return $images;
    }

    public function getGalleryImagesIdsAttribute(): array
    {
        $i = 0;
        $images = [];
        do {
            $image = get_post_meta($this->post_id, '_vg_gallery|vg_image|$i|0|value');
            if ($image) {
                $images[] = wp_get_attachment_url($image[0]);
            }
            $i++;
        } while ($image);

        return $images;
    }

    public function groups(): HasMany
    {
        return $this->hasMany(ProductGroup::class, 'product_id', 'post_id');
    }

    public function categoriesRelation(): HasMany
    {
        $model = new class extends Model {
            protected $table = 'term_relationships';
            public $timestamps = false;
        };
        return $this->hasMany(
            $model,
            'object_id',
            'post_id'
        );
    }
}
