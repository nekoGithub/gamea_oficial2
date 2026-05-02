<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RolesSeeder::class);


        User::factory(40)->create();




        $this->call([
            AuditoriaSeeder::class,
        ]);

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'brayan@gmail.com',
            'password' => bcrypt('password'),
        ])->assignRole('admin');


        // servidores
        // IDs 1-6 en orden — coinciden exactamente con ServidoresSeeder
        $sistemas = [
            // ID 1
            [
                'nombre'      => 'Ubuntu',
                'version'     => '20.04 LTS',
                'descripcion' => 'Ubuntu 20.04 LTS "Focal Fossa" — Soporte hasta abril 2025. Estable y ampliamente usado en servidores de producción.',
                'estado'      => 'activo',
                'created_at'  => '2020-04-23 08:00:00',
            ],
            // ID 2
            [
                'nombre'      => 'Ubuntu',
                'version'     => '22.04 LTS',
                'descripcion' => 'Ubuntu 22.04 LTS "Jammy Jellyfish" — Soporte hasta abril 2027. Versión LTS con OpenSSL 3 y kernel 5.15.',
                'estado'      => 'activo',
                'created_at'  => '2022-04-21 08:00:00',
            ],
            // ID 3
            [
                'nombre'      => 'Ubuntu',
                'version'     => '24.04 LTS',
                'descripcion' => 'Ubuntu 24.04 LTS "Noble Numbat" — Soporte hasta abril 2029. Última versión LTS con kernel 6.8 y mejoras de seguridad.',
                'estado'      => 'activo',
                'created_at'  => '2024-04-25 08:00:00',
            ],
            // ID 4
            [
                'nombre'      => 'Debian',
                'version'     => '10 Buster',
                'descripcion' => 'Debian 10 "Buster" — LTS hasta junio 2024. Base estable con glibc 2.28, Python 3.7 y soporte para arquitecturas múltiples.',
                'estado'      => 'inactivo',
                'created_at'  => '2019-07-06 08:00:00',
            ],
            // ID 5
            [
                'nombre'      => 'Debian',
                'version'     => '11 Bullseye',
                'descripcion' => 'Debian 11 "Bullseye" — Soporte hasta junio 2026. Incluye kernel 5.10, PHP 7.4/8.0 y excelente estabilidad en producción.',
                'estado'      => 'inactivo',
                'created_at'  => '2021-08-14 08:00:00',
            ],
            // ID 6
            [
                'nombre'      => 'Debian',
                'version'     => '12 Bookworm',
                'descripcion' => 'Debian 12 "Bookworm" — Soporte hasta junio 2028. Última versión estable con kernel 6.1 LTS y soporte para firmware no-libre.',
                'estado'      => 'inactivo',
                'created_at'  => '2023-06-10 08:00:00',
            ],
        ];

        foreach ($sistemas as $sistema) {
            DB::table('sistemas_operativos')->insert(array_merge($sistema, [
                'updated_at' => $sistema['created_at'],
                'deleted_at' => null,
            ]));
        }

        $bases = [

            // ══════════════════════════════════════════════════════════════════
            //  POSTGRESQL
            // ══════════════════════════════════════════════════════════════════
            [
                'gestor'      => 'PostgreSQL',
                'version'     => '12.2',
                'descripcion' => 'PostgreSQL 12.2 — Mejoras en particionamiento y rendimiento de índices. LTS recomendado para entornos de producción en 2020.',
                'estado'      => 'activo',
                'created_at'  => '2020-03-15 08:00:00',
            ],
            [
                'gestor'      => 'PostgreSQL',
                'version'     => '13.1',
                'descripcion' => 'PostgreSQL 13 — Deduplicación de índices B-tree, mejor rendimiento en ordenamiento y particionamiento mejorado.',
                'estado'      => 'activo',
                'created_at'  => '2020-11-12 08:00:00',
            ],
            [
                'gestor'      => 'PostgreSQL',
                'version'     => '14.0',
                'descripcion' => 'PostgreSQL 14 — Mejoras en conexiones concurrentes, rendimiento en queries complejas y soporte JSON mejorado.',
                'estado'      => 'activo',
                'created_at'  => '2021-09-30 09:00:00',
            ],
            [
                'gestor'      => 'PostgreSQL',
                'version'     => '14.5',
                'descripcion' => 'PostgreSQL 14.5 — Parche de seguridad crítico. Correcciones en el planificador de consultas y estabilidad general.',
                'estado'      => 'activo',
                'created_at'  => '2022-08-11 09:00:00',
            ],
            [
                'gestor'      => 'PostgreSQL',
                'version'     => '15.0',
                'descripcion' => 'PostgreSQL 15 — Nuevo comando MERGE, mejoras en compresión WAL, rendimiento en ordenamiento y lógica de replicación.',
                'estado'      => 'activo',
                'created_at'  => '2022-10-13 10:00:00',
            ],
            [
                'gestor'      => 'PostgreSQL',
                'version'     => '15.3',
                'descripcion' => 'PostgreSQL 15.3 — Correcciones de seguridad y mejoras en estabilidad para entornos de alta disponibilidad.',
                'estado'      => 'activo',
                'created_at'  => '2023-05-11 08:00:00',
            ],
            [
                'gestor'      => 'PostgreSQL',
                'version'     => '16.0',
                'descripcion' => 'PostgreSQL 16 — Paralelismo mejorado, nueva sintaxis SQL/JSON, mejoras en replicación lógica y rendimiento general.',
                'estado'      => 'activo',
                'created_at'  => '2023-09-14 08:00:00',
            ],
            [
                'gestor'      => 'PostgreSQL',
                'version'     => '16.2',
                'descripcion' => 'PostgreSQL 16.2 — Parches de seguridad y correcciones de bugs. Versión estable recomendada a inicios de 2024.',
                'estado'      => 'activo',
                'created_at'  => '2024-02-08 09:00:00',
            ],
            [
                'gestor'      => 'PostgreSQL',
                'version'     => '17.0',
                'descripcion' => 'PostgreSQL 17 — Mejoras en vacuuming incremental, rendimiento de memoria, nuevas funciones SQL/JSON y replicación lógica.',
                'estado'      => 'activo',
                'created_at'  => '2024-09-26 10:00:00',
            ],
            [
                'gestor'      => 'PostgreSQL',
                'version'     => '17.2',
                'descripcion' => 'PostgreSQL 17.2 — Última versión estable 2025. Correcciones críticas de seguridad y rendimiento optimizado.',
                'estado'      => 'activo',
                'created_at'  => '2025-02-20 08:00:00',
            ],

            // ══════════════════════════════════════════════════════════════════
            //  MYSQL
            // ══════════════════════════════════════════════════════════════════
            [
                'gestor'      => 'MySQL',
                'version'     => '5.7.29',
                'descripcion' => 'MySQL 5.7.29 — Versión estable ampliamente usada en 2020. Soporte para JSON nativo, mejoras en replicación y rendimiento.',
                'estado'      => 'activo',
                'created_at'  => '2020-01-13 08:00:00',
            ],
            [
                'gestor'      => 'MySQL',
                'version'     => '8.0.20',
                'descripcion' => 'MySQL 8.0.20 — Mejoras en roles, window functions, CTEs y rendimiento general. Primera versión MySQL 8 ampliamente adoptada.',
                'estado'      => 'activo',
                'created_at'  => '2020-04-27 09:00:00',
            ],
            [
                'gestor'      => 'MySQL',
                'version'     => '8.0.26',
                'descripcion' => 'MySQL 8.0.26 — Correcciones de seguridad importantes. Mejoras en InnoDB y en el manejo de caracteres especiales.',
                'estado'      => 'activo',
                'created_at'  => '2021-07-20 10:00:00',
            ],
            [
                'gestor'      => 'MySQL',
                'version'     => '8.0.31',
                'descripcion' => 'MySQL 8.0.31 — Mejoras en replicación grupal, rendimiento en queries analíticas y soporte para TLS 1.3.',
                'estado'      => 'activo',
                'created_at'  => '2022-10-11 08:00:00',
            ],
            [
                'gestor'      => 'MySQL',
                'version'     => '8.0.36',
                'descripcion' => 'MySQL 8.0.36 — Parches de seguridad críticos Oracle CPU. Última release de mantenimiento de la rama 8.0.',
                'estado'      => 'activo',
                'created_at'  => '2024-01-16 09:00:00',
            ],
            [
                'gestor'      => 'MySQL',
                'version'     => '8.4.0',
                'descripcion' => 'MySQL 8.4 LTS — Primera versión LTS oficial de MySQL 8. Depreca funcionalidades legacy, mejora rendimiento y seguridad.',
                'estado'      => 'activo',
                'created_at'  => '2024-04-30 10:00:00',
            ],
            [
                'gestor'      => 'MySQL',
                'version'     => '9.0.0',
                'descripcion' => 'MySQL 9.0 — Nueva rama de innovación. Mejoras en vectores, soporte JavaScript para stored procedures y rendimiento.',
                'estado'      => 'activo',
                'created_at'  => '2024-07-01 08:00:00',
            ],
            [
                'gestor'      => 'MySQL',
                'version'     => '9.1.0',
                'descripcion' => 'MySQL 9.1 — Mejoras en replicación asíncrona, funciones de IA integradas y optimizaciones en el motor InnoDB.',
                'estado'      => 'activo',
                'created_at'  => '2025-01-14 09:00:00',
            ],

            // ══════════════════════════════════════════════════════════════════
            //  MARIADB
            // ══════════════════════════════════════════════════════════════════
            [
                'gestor'      => 'MariaDB',
                'version'     => '10.4.12',
                'descripcion' => 'MariaDB 10.4 — Soporte para roles de usuarios, mejoras en Galera Cluster y compatibilidad con MySQL 5.7.',
                'estado'      => 'activo',
                'created_at'  => '2020-02-05 08:00:00',
            ],
            [
                'gestor'      => 'MariaDB',
                'version'     => '10.5.5',
                'descripcion' => 'MariaDB 10.5 — Nuevo motor de almacenamiento S3, mejoras en InnoDB, análisis de queries y funciones de ventana.',
                'estado'      => 'activo',
                'created_at'  => '2020-08-14 09:00:00',
            ],
            [
                'gestor'      => 'MariaDB',
                'version'     => '10.6.5',
                'descripcion' => 'MariaDB 10.6 LTS — Versión LTS con soporte hasta 2026. Mejoras en compresión, rendimiento y compatibilidad Oracle.',
                'estado'      => 'activo',
                'created_at'  => '2021-10-22 10:00:00',
            ],
            [
                'gestor'      => 'MariaDB',
                'version'     => '10.7.3',
                'descripcion' => 'MariaDB 10.7 — Soporte para tipos UUID nativos, mejoras en el optimizador y funciones JSON avanzadas.',
                'estado'      => 'activo',
                'created_at'  => '2022-02-08 08:00:00',
            ],
            [
                'gestor'      => 'MariaDB',
                'version'     => '10.9.3',
                'descripcion' => 'MariaDB 10.9 — Mejoras en replicación, nuevas funciones de cifrado y optimizaciones en Galera Cluster.',
                'estado'      => 'activo',
                'created_at'  => '2022-09-19 09:00:00',
            ],
            [
                'gestor'      => 'MariaDB',
                'version'     => '10.11.2',
                'descripcion' => 'MariaDB 10.11 LTS — Versión LTS con soporte hasta 2028. Mejoras en rendimiento, seguridad y compatibilidad.',
                'estado'      => 'activo',
                'created_at'  => '2023-02-16 10:00:00',
            ],
            [
                'gestor'      => 'MariaDB',
                'version'     => '11.0.2',
                'descripcion' => 'MariaDB 11.0 — Nueva rama principal. Eliminación de funcionalidades obsoletas y mejoras en el planificador.',
                'estado'      => 'activo',
                'created_at'  => '2023-06-08 08:00:00',
            ],
            [
                'gestor'      => 'MariaDB',
                'version'     => '11.2.3',
                'descripcion' => 'MariaDB 11.2 — Soporte para vectores nativos, mejoras en replicación lógica y funciones de ventana optimizadas.',
                'estado'      => 'activo',
                'created_at'  => '2024-02-07 09:00:00',
            ],
            [
                'gestor'      => 'MariaDB',
                'version'     => '11.4.3',
                'descripcion' => 'MariaDB 11.4 LTS — Última versión LTS 2024. Soporte extendido, mejoras en seguridad y rendimiento en cargas OLTP.',
                'estado'      => 'activo',
                'created_at'  => '2024-05-29 10:00:00',
            ],
            [
                'gestor'      => 'MariaDB',
                'version'     => '11.6.2',
                'descripcion' => 'MariaDB 11.6 — Mejoras en almacenamiento vectorial para IA, optimizaciones en JSON y soporte para Python UDFs.',
                'estado'      => 'activo',
                'created_at'  => '2025-01-30 08:00:00',
            ],
        ];

        foreach ($bases as $base) {
            DB::table('bases_datos')->insert(array_merge($base, [
                'updated_at' => $base['created_at'],
                'deleted_at' => null,
            ]));
        }
    }
}
