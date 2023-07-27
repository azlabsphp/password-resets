# Password Resets

Password reset is a workflow that allow application users to modify their connection credential a.k.a password while not being connected to the application.
`drewlabs/passwords` is a framework agnostic password reset workflow for PHP programming language, that support `link` based and `otp` based password reset.

## Usage

The library ships with command classes for creating password reset link/otp a.k.a `One Time Password`, and other commands to validated the generated token or otp.

- Request password reset

```php

use Drewlabs\Passwords\Commands\CreatePasswordResetCommand;
use Drewlabs\Passwords\PasswordResetTokenRepository;
use Drewlabs\Passwords\UrlFactory;
use Drewlabs\Passwords\Contracts\TokenInterface;
use Drewlabs\Passwords\Contracts\CanResetPassword;

$repository = new PasswordResetTokenRepository(new Connection('sqlite:memory', 'password_resets'));
$command = new CreatePasswordResetCommand($repository, new CanResetPasswordProvider, 'MySecret');

// Create a password reset link
$command->handle('user@example.com', function(CanResetPassword $user, TokenInterface $token) {
    // TODOL: the command is completed, generate the password reset link
});


//
```

**Note** In the code above the `CanResetPasswordProvider` is a fake user resolver implementation. Library users must provide their own user resolver that implement `Drewlabs\Passwords\Contracts\CanResetPasswordProvider` contract.


- Generate password reset OTP

Documentation is under development, any API changes or feature will be added accordingly.


- Passsword reset token repository

The `drewlabs/passwords` library ship a reset tokens repository implementation that is extensible by creating drivers that implements `Drewlabs\Passwords\Contracts\ConnectionInterface` contract.
