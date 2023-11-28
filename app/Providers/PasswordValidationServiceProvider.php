<?php

namespace App\Providers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class PasswordValidationServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Eliminating consecutive identical chars, as per NIST Special Publication 800-63B section 5.1.1.2:
        $this->extendPasswordValidatorWithConsecutiveCharsRule();

        // Eliminating sequential increasing/decreasing chars, as per NIST Special Publication 800-63B section 5.1.1.2:
        $this->extendPasswordValidatorWithSequentialCharsRule();

        $this->extendPasswordValidatorWithKeyboardSequenceRule();

        Password::defaults(function () {
            return Password::min(8)
                ->letters()
                ->mixedCase()
                ->numbers()
                ->symbols()
                ->rules([
                    'max_consecutive:2',
                    'max_sequential:2',
                    'max_keyboard_sequential:4'
                ])
                ->uncompromised(5);
        });
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    private function extendPasswordValidatorWithConsecutiveCharsRule()
    {
        Validator::extend('max_consecutive', function ($attribute, $value, $parameters, $validator) {
            $max_consecutive_chars = $parameters[0] ?? 2;
            return !preg_match('/([a-zA-Z0-9])\1{' . $max_consecutive_chars . ',}/', $value);
        });

        Validator::replacer('max_consecutive', function ($message, $attribute, $rule, $parameters) {
            $max_consecutive_chars = $parameters[0] ?? 2;
            return __('passwords.consecutive_chars', ['count' => $max_consecutive_chars]);
        });
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    private function extendPasswordValidatorWithSequentialCharsRule()
    {
        Validator::extend('max_sequential', function ($attribute, $value, $parameters, $validator) {
            $max_sequential_chars = $parameters[0] ?? 2;

            for ($i = 0, $loop_length = strlen($value) - $max_sequential_chars; $i < $loop_length; $i++) {
                $increasing = true;
                $decreasing = true;

                for ($j = 0; $j < $max_sequential_chars; $j++) {
                    if (ord($value[$i + $j]) != ord($value[$i + $j + 1]) + 1) {
                        $increasing = false;
                    }
                    if (ord($value[$i + $j]) != ord($value[$i + $j + 1]) - 1) {
                        $decreasing = false;
                    }
                    if (!$increasing && !$decreasing) {
                        break;
                    }
                }
                if ($increasing || $decreasing) {
                    return false;
                }
            }
            return true;
        });

        Validator::replacer('max_sequential', function ($message, $attribute, $rule, $parameters) {
            $max_sequential_chars = $parameters[0] ?? 2;
            return __('passwords.sequential_chars', ['count' => $max_sequential_chars]);
        });
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    private function extendPasswordValidatorWithKeyboardSequenceRule()
    {
        Validator::extend('max_keyboard_sequential', function ($attribute, $value, $parameters, $validator) {
            $max_keyboard_chars = $parameters[0] ?? 2;
            // currently QWERTY keyboard only
            $keyboard_sequences = [
                '~!@#$%^&*()_+',
                'qwertyuiop[]\\',
                'QWERTYUIOP{}|',
                'asdfghjkl;\'',
                'ASDFGHJKL:"',
                'zxcvbnm,./',
                'ZXCVBNM<>?'
            ];

            foreach ($keyboard_sequences as $sequence) {
                if (strlen($sequence) <= $max_keyboard_chars) {
                    continue;
                }

                for ($i = 0, $loop_length = strlen($sequence) - $max_keyboard_chars; $i < $loop_length; $i++) {
                    if (str_contains($value, substr($sequence, $i, $max_keyboard_chars + 1))) {
                        return false;
                    }
                    if (str_contains($value, strrev(substr($sequence, $i, $max_keyboard_chars + 1)))) {
                        return false;
                    }
                }
            }

            return true;
        });

        Validator::replacer('max_keyboard_sequential', function ($message, $attribute, $rule, $parameters) {
            $max_keyboard_chars = $parameters[0] ?? 2;
            return __('passwords.keyboard_sequential_chars', ['count' => $max_keyboard_chars]);
        });
    }
}
