<?php

namespace SuiteZap\LawFirm\Rules;

use Illuminate\Contracts\Validation\Rule;

class ValidarCpfCnpj implements Rule
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
        $c = preg_replace('/\D/', '', $value);

        if (strlen($c) == 11) {
            return $this->validateCpf($c);
        } elseif (strlen($c) == 14) {
            return $this->validateCnpj($c);
        }

        return false;
    }

    /**
     * Valida CPF
     *
     * @param string $cpf
     * @return bool
     */
    protected function validateCpf($cpf)
    {
        if (preg_match('/(\d)\1{10}/', $cpf)) {
            return false;
        }

        for ($t = 9; $t < 11; $t++) {
            for ($d = 0, $c = 0; $c < $t; $c++) {
                $d += $cpf[$c] * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cpf[$c] != $d) {
                return false;
            }
        }
        return true;
    }

    /**
     * Valida CNPJ
     *
     * @param string $cnpj
     * @return bool
     */
    protected function validateCnpj($cnpj)
    {
        if (preg_match('/(\d)\1{13}/', $cnpj)) {
            return false;
        }

        $b = [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];

        for ($i = 0, $n = 0; $i < 12; $n += $cnpj[$i] * $b[++$i])
            ;
        if ($cnpj[12] != ((($n %= 11) < 2) ? 0 : 11 - $n)) {
            return false;
        }

        for ($i = 0, $n = 0; $i <= 12; $n += $cnpj[$i] * $b[$i++])
            ;
        if ($cnpj[13] != ((($n %= 11) < 2) ? 0 : 11 - $n)) {
            return false;
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'O CPF ou CNPJ informado é inválido. Verifique os dígitos.';
    }
}
