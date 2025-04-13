<?php

namespace App\Mail;

use App\Models\FaturaFornecedor;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ComprovativoPagamentoFornecedor extends Mailable
{
    use Queueable, SerializesModels;

    public FaturaFornecedor $fatura;

    public function __construct(FaturaFornecedor $fatura)
    {
        $this->fatura = $fatura;
    }

    public function build()
    {
        $email = $this
            ->subject('Comprovativo de Pagamento - Fatura ' . $this->fatura->numero)
            ->view('emails.comprovativo-fornecedor')
            ->with([
                'fornecedor' => $this->fatura->fornecedor,
                'fatura' => $this->fatura,
            ]);

        // Anexar múltiplos comprovativos (array de caminhos)
        if (is_array($this->fatura->comprovativo_pagamento)) {
            foreach ($this->fatura->comprovativo_pagamento as $ficheiro) {
                $caminho = storage_path('app/private/' . $ficheiro);
                if (file_exists($caminho)) {
                    $email->attach($caminho);
                }
            }
        }

        return $email;
    }
}
