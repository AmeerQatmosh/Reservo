<?php

namespace App\Http\Requests\Settings;

use App\Concerns\PasswordValidationRules;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class ProfileDeleteRequest extends FormRequest
{
    use PasswordValidationRules;

    /**
     * Administrator and super admin self-deletion from profile is blocked (dangerous for access control).
     */
    public function authorize(): bool
    {
        $user = $this->user();

        return $user !== null && ! $user->isAdmin();
    }

    protected function failedAuthorization(): void
    {
        throw new AuthorizationException(__('Administrator accounts cannot be deleted from profile settings.'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'identity' => ['required', 'string', 'max:255'],
            'password' => $this->currentPasswordRules(),
            'delete_confirmation' => ['required', 'string', 'max:64'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $user = $this->user();

            if (! $user) {
                return;
            }

            $identityTrim = trim((string) $this->input('identity', ''));

            $emailMatch = strcasecmp($identityTrim, trim((string) $user->email)) === 0;
            $userName = trim((string) $user->name);
            $nameMatch = $userName !== '' && strcasecmp($identityTrim, $userName) === 0;

            if (! $emailMatch && ! $nameMatch) {
                $validator->errors()->add(
                    'identity',
                    __('This must match your email address or your display name exactly.'),
                );
            }

            $phrase = mb_strtolower(trim((string) $this->input('delete_confirmation')));

            if ($phrase !== 'delete my account') {
                $validator->errors()->add(
                    'delete_confirmation',
                    __('Please type the phrase exactly: delete my account'),
                );
            }
        });
    }
}
