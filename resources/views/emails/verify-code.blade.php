<x-mail::message>
# Verifica tu correo electrónico

Hola 👋, gracias por registrarte en **{{ config('app.name') }}**.

Tu código de verificación es:

<x-mail::panel>
<h2 style="text-align: center; font-size: 24px; letter-spacing: 4px;">
    {{ $code }}
</h2>
</x-mail::panel>

Este código expira en **10 minutos**.  
Si no solicitaste esta verificación, puedes ignorar este mensaje.

Gracias,<br>
{{ config('app.name') }}
</x-mail::message>
