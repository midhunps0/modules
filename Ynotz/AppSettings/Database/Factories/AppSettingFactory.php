<?php

namespace Modules\Ynotz\AppSettings\Database\Factories;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Ynotz\AppSettings\Models\AppSetting;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class AppSettingFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\Illuminate\Database\Eloquent\Model>
     */
    protected $model = AppSetting::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->words(3, true);
        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'value_type' => 'text',
            'value' => "Some Value For $name",
            'auto_manage' => true,
            'view_permissions' => json_encode(["App Settings: View"]),
            'edit_permissions' => json_encode(["App Settings: Edit"]),
            'delete_permissions' => json_encode(["App Settings: Delete"]),
        ];
    }
}
