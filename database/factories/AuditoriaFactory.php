<?php

namespace Database\Factories;

use App\Models\Auditoria;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Auditoria>
 */
class AuditoriaFactory extends Factory
{
    protected $model = Auditoria::class;

    public function definition(): array
    {
        return [
            'user_id' => User::inRandomOrder()->value('id'),

            'accion' => $this->faker->randomElement([
                'login',
                'logout',
                'created',
                'updated',
                'deleted',
                'restored',
            ]),

            'modulo' => $this->faker->randomElement([
                'usuarios',
                'credenciales',
                'servidores',
                'certificados_ssl',
                'roles',
                'permisos',
            ]),

            'entidad_id' => $this->faker->numberBetween(1, 500),

            'descripcion' => $this->faker->sentence(12),

            'valores_anteriores' => $this->faker->boolean(40)
                ? ['estado' => 'anterior', 'nivel' => 1]
                : null,

            'valores_nuevos' => $this->faker->boolean(40)
                ? ['estado' => 'nuevo', 'nivel' => 2]
                : null,

            'ip_address' => $this->faker->ipv4(),

            'user_agent' => $this->faker->userAgent(),

            'created_at' => $this->faker->dateTimeBetween('-3 months', 'now'),
            'updated_at' => now(),
        ];
    }
}
