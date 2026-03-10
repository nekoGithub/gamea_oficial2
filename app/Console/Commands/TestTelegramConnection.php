<?php

namespace App\Console\Commands;

use App\Services\TelegramService;
use Illuminate\Console\Command;

class TestTelegramConnection extends Command
{
    protected $signature = 'telegram:test';
    protected $description = 'Enviar mensaje de prueba a Telegram';

    protected $telegramService;

    public function __construct(TelegramService $telegramService)
    {
        parent::__construct();
        $this->telegramService = $telegramService;
    }

    public function handle()
    {
        $this->info('📱 Enviando mensaje de prueba a Telegram...');

        $enviado = $this->telegramService->sendTestMessage();

        if ($enviado) {
            $this->info('✅ Mensaje enviado exitosamente!');
            $this->line('Revisa el grupo de Telegram para verificar.');
            return Command::SUCCESS;
        } else {
            $this->error('❌ Error al enviar mensaje.');
            $this->line('Verifica:');
            $this->line('1. El TOKEN del bot en .env');
            $this->line('2. El CHAT_ID en .env');
            $this->line('3. Que el bot esté agregado al grupo');
            $this->line('4. Que el bot tenga permisos de administrador');
            return Command::FAILURE;
        }
    }
}
