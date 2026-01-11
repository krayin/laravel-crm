<?php

namespace SuiteZap\LawFirm\Rules;

use Illuminate\Contracts\Validation\Rule;

class ValidarCNJ implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        // 1. Remover tudo que não for número
        $cnj = preg_replace('/[^0-9]/', '', (string) $value);

        // 2. Verificar se tem 20 dígitos (adiciona zeros à esquerda se tiver menos, padrão CNJ é 20)
        // Mas a validação geralmente espera que o usuário digite corretamente.
        // Vamos normalizar para 20 dígitos para garantir o cálculo.
        if (strlen($cnj) < 14 || strlen($cnj) > 20) {
            return false;
        }

        $cnj = str_pad($cnj, 20, '0', STR_PAD_LEFT);

        // 3. Separar os blocos
        // Formato: NNNNNNN-DD.AAAA.J.TR.OOOO (20 dígitos)
        // N = 7 dígitos
        // D = 2 dígitos (Verificadores)
        // A = 4 dígitos (Ano)
        // J = 1 dígito (Órgão)
        // T = 2 dígitos (Tribunal)
        // O = 4 dígitos (Origem)

        $n = substr($cnj, 0, 7);
        $d = substr($cnj, 7, 2);
        $a = substr($cnj, 9, 4);
        $j = substr($cnj, 13, 1);
        $tr = substr($cnj, 14, 2);
        $o = substr($cnj, 16, 4);

        // 4. Fórmula (Módulo 97 Base 10)
        // Numero montado para validação: NNNNNNNAAAAJTROOOO + DD00 (Não, a fórmula é R = N mod 97, onde R deve ser 1)
        // A fórmula correta do CNJ conforme resolução 65/2008:
        // H = NNNNNNNAAAAJTROOOO (Sufixo DD movido para o final ou recalculado)
        // Na verdade, a validação é: (NNNNNNNAAAAJTROOOO + DD) % 97 == 1 ?? Não.

        // CORREÇÃO DA LÓGICA CNJ:
        // O número é NNNNNNN-DD.AAAA.J.TR.OOOO
        // Para validar, movemos o DV para o final? Não.
        // A fórmula de geração é: DD = 98 - ( (NNNNNNNAAAAJTROOOO * 100) % 97 )
        // Para validar: (NNNNNNNAAAAJTROOOO * 100 + DD) % 97 == 1
        // Fonte: Resolução CNJ 65/2008.

        $blocoSemDigito = $n . $a . $j . $tr . $o; // NNNNNNNAAAAJTROOOO
        $digitoVerificador = $d;

        // Construir o número gigante: NNNNNNNAAAAJTROOOO + DD
        // Matemáticamente: (Inteiro(BlocoSemDigito) * 100 + Inteiro(Digito)) % 97 deve ser 1

        // Como o número é maior que PHP_INT_MAX, usamos bcmod ou algoritmo manual
        $numeroParaValidar = $blocoSemDigito . $digitoVerificador;

        // Se bcmod estiver disponível
        if (function_exists('bcmod')) {
            return bcmod($numeroParaValidar, '97') == '1';
        }

        // Fallback para cálculo manual de módulo para números grandes (string)
        $resto = 0;
        for ($i = 0; $i < strlen($numeroParaValidar); $i++) {
            $resto = ($resto * 10 + (int) $numeroParaValidar[$i]) % 97;
        }

        return $resto == 1;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'O número do processo (CNJ) é inválido.';
    }
}
