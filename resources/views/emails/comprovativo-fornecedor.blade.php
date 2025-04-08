@component('mail::message')
# Comprovativo de Pagamento - Fatura {{ $fatura->numero }}

Estimado(a) {{ $fornecedor->nome }},

Enviamos em anexo o comprovativo de pagamento da fatura **{{ $fatura->numero }}**.

Qualquer questão, entre em contacto connosco.

Obrigado,<br>
**CRC Decoradores**
@endcomponent
