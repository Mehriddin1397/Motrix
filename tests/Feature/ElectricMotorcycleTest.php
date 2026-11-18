<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Brand\Models\Brand;
use Modules\Motorcycle\Models\Motorcycle;
use Modules\Motorcycle\Models\MotorcycleCategory;
use Modules\Motorcycle\Models\MotorcycleSpecification;
use Tests\TestCase;

class ElectricMotorcycleTest extends TestCase
{
    use RefreshDatabase;

    public function test_electric_motorcycle_specification_attributes_and_helper(): void
    {
        $brand = Brand::create(['name' => 'Zero Motorcycles', 'slug' => 'zero']);
        $category = MotorcycleCategory::create(['name' => 'Naked', 'slug' => 'naked']);

        $moto = Motorcycle::create([
            'brand_id' => $brand->id,
            'category_id' => $category->id,
            'name' => 'Zero SR/F',
            'slug' => 'zero-srf',
            'status' => 'published',
        ]);

        $spec = MotorcycleSpecification::create([
            'motorcycle_id' => $moto->id,
            'powertrain_type' => 'electric',
            'battery_capacity_kwh' => 14.4,
            'range_km' => 259,
            'charging_time_hours' => 4.5,
            'fast_charging_time_min' => 60,
            'horsepower' => 110,
            'torque_nm' => 190,
            'top_speed_kmh' => 200,
            'weight_kg' => 220,
        ]);

        $this->assertTrue($spec->isElectric());
        $this->assertFalse($spec->isPetrol());
        $this->assertTrue($moto->isElectric());
        $this->assertEquals(14.4, (float) $spec->battery_capacity_kwh);
        $this->assertEquals(259, $spec->range_km);
    }

    public function test_electric_motorcycle_show_page_renders_electric_specs(): void
    {
        $brand = Brand::create(['name' => 'Super SOCO', 'slug' => 'super-soco']);
        $category = MotorcycleCategory::create(['name' => 'City', 'slug' => 'city']);

        $moto = Motorcycle::create([
            'brand_id' => $brand->id,
            'category_id' => $category->id,
            'name' => 'TC Max',
            'slug' => 'tc-max',
            'status' => 'published',
        ]);

        MotorcycleSpecification::create([
            'motorcycle_id' => $moto->id,
            'powertrain_type' => 'electric',
            'battery_capacity_kwh' => 3.2,
            'range_km' => 110,
            'charging_time_hours' => 5.0,
            'horsepower' => 7,
            'top_speed_kmh' => 95,
        ]);

        $response = $this->get(route('motorcycle.show', $moto));
        $response->assertOk();
        $response->assertSee('⚡ Elektr');
        $response->assertSee('Batareya quvvati');
        $response->assertSee('3.20 kWh');
        $response->assertSee('110 km');
        $response->assertSee('5.0 soat');
        $response->assertDontSee('Yoqilg\'i bak hajmi');
    }

    public function test_petrol_motorcycle_show_page_renders_petrol_specs(): void
    {
        $brand = Brand::create(['name' => 'Yamaha', 'slug' => 'yamaha']);
        $category = MotorcycleCategory::create(['name' => 'Sport', 'slug' => 'sport']);

        $moto = Motorcycle::create([
            'brand_id' => $brand->id,
            'category_id' => $category->id,
            'name' => 'Yamaha R1',
            'slug' => 'yamaha-r1',
            'status' => 'published',
        ]);

        MotorcycleSpecification::create([
            'motorcycle_id' => $moto->id,
            'powertrain_type' => 'petrol',
            'engine_type' => '4-silindrli Inline-4',
            'displacement_cc' => 998,
            'horsepower' => 200,
            'fuel_capacity_l' => 17.0,
            'fuel_consumption_l_100km' => 7.2,
        ]);

        $response = $this->get(route('motorcycle.show', $moto));
        $response->assertOk();
        $response->assertSee('4-silindrli Inline-4');
        $response->assertSee('998 cc');
        $response->assertSee('17.0 L');
        $response->assertSee('7.2 L/100km');
        $response->assertDontSee('Batareya quvvati');
    }
}
