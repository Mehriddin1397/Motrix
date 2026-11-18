<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Brand\Models\Brand;
use Modules\Motorcycle\Models\Motorcycle;
use Modules\Motorcycle\Models\MotorcycleCategory;
use Modules\Motorcycle\Models\MotorcycleSpecification;
use Tests\TestCase;

class ComparisonTest extends TestCase
{
    use RefreshDatabase;

    public function test_comparison_page_loads_with_empty_selection(): void
    {
        $response = $this->get(route('comparison.index'));

        $response->assertStatus(200);
        $response->assertSee('Mototsikllarni taqqoslash');
        $response->assertSee('Qaysi mototsikllarni solishtirmoqchisiz?');
    }

    public function test_comparison_page_loads_with_selected_motorcycles(): void
    {
        $brand = Brand::create(['name' => 'Yamaha', 'slug' => 'yamaha']);
        $category = MotorcycleCategory::create(['name' => 'Sport', 'slug' => 'sport']);

        $m1 = Motorcycle::create([
            'brand_id' => $brand->id,
            'category_id' => $category->id,
            'name' => 'YZF-R3',
            'slug' => 'yzf-r3',
            'year_start' => 2020,
        ]);
        MotorcycleSpecification::create([
            'motorcycle_id' => $m1->id,
            'displacement_cc' => 321,
            'horsepower' => 42,
            'price_usd_min' => 5400,
        ]);

        $m2 = Motorcycle::create([
            'brand_id' => $brand->id,
            'category_id' => $category->id,
            'name' => 'MT-03',
            'slug' => 'mt-03',
            'year_start' => 2021,
        ]);
        MotorcycleSpecification::create([
            'motorcycle_id' => $m2->id,
            'displacement_cc' => 321,
            'horsepower' => 42,
            'price_usd_min' => 4900,
        ]);

        $response = $this->get(route('comparison.index', ['ids' => "{$m1->id},{$m2->id}"]));

        $response->assertStatus(200);
        $response->assertSee('YZF-R3');
        $response->assertSee('MT-03');
        $response->assertSee('321 cc');
        $response->assertSee('$5,400');
        $response->assertSee('$4,900');
    }

    public function test_motorcycle_show_page_displays_comparison_banner_and_rivals(): void
    {
        $brand = Brand::create(['name' => 'Kawasaki', 'slug' => 'kawasaki']);
        $category = MotorcycleCategory::create(['name' => 'Naked', 'slug' => 'naked']);

        $m1 = Motorcycle::create([
            'brand_id' => $brand->id,
            'category_id' => $category->id,
            'name' => 'Z400',
            'slug' => 'z400',
        ]);

        $m2 = Motorcycle::create([
            'brand_id' => $brand->id,
            'category_id' => $category->id,
            'name' => 'Z650',
            'slug' => 'z650',
        ]);

        $response = $this->get(route('motorcycle.show', $m1->slug));

        $response->assertStatus(200);
        $response->assertSee('Motrix Taqqoslash');
        $response->assertSee('VS Solishtirish');
        $response->assertSee('Z650');
    }
}
