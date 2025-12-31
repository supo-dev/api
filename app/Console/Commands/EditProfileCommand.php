<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Actions\UpdateUser;
use App\Models\User;
use App\Rules\UserUsername;
use App\Support\InlineFormHandler;
use Illuminate\Console\Command;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Support\Facades\Validator;

use function Laravel\Prompts\info;

final class EditProfileCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'app:edit-profile';

    public function handle(#[CurrentUser] User $user, UpdateUser $action): void
    {
        $form = new InlineFormHandler;

        $form->addTextField(
            name: 'username',
            label: 'Username',
            default: $user->username,
            placeholder: 'Enter your username',
            maxLength: 255
        );

        $form->addTextareaField(
            name: 'bio',
            label: 'Bio',
            default: $user->bio ?? '',
            placeholder: 'Tell us about yourself...',
            maxLength: 500
        );

        $values = $form->run('Edit your profile');

        if ($values === null) {
            info('Profile editing cancelled.');

            return;
        }

        // Validate the input
        $validator = Validator::make($values, [
            'username' => ['required', 'string', 'max:255', new UserUsername($user)],
            'bio' => ['nullable', 'string', 'max:500'],
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            info('Validation failed: '.implode(', ', $errors));

            return;
        }

        $action->handle($user, $values['username'], $values['bio']);

        info('Profile updated successfully!');
    }
}
