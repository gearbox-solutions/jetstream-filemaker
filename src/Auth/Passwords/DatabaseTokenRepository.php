<?php

namespace BlueFeather\JetstreamFileMaker\Auth\Passwords;

use App\Models\PasswordReset;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class DatabaseTokenRepository extends \Illuminate\Auth\Passwords\DatabaseTokenRepository
{

    /**
     * Create a new token record.
     *
     * @param  \Illuminate\Contracts\Auth\CanResetPassword  $user
     * @return string
     */
    public function create(CanResetPasswordContract $user)
    {
        $email = $user->getEmailForPasswordReset();

        $this->deleteExisting($user);

        // We will create a new, random token for the user so that we can e-mail them
        // a safe link to the password reset form. Then we will insert a record in
        // the database so that we can verify the token within the actual reset.
        $token = $this->createNewToken();

        (new PasswordReset([
            'email' => $email,
            'token' => $this->hasher->make($token),
            'created_at' => new Carbon(),
        ]))->save();

        return $token;
    }

    /**
     * Delete all existing reset tokens from the database.
     *
     * @param  \Illuminate\Contracts\Auth\CanResetPassword  $user
     * @return int
     */
    protected function deleteExisting(CanResetPasswordContract $user)
    {
        $resets = PasswordReset::where('email', "==", $user->getEmailForPasswordReset())->get();

        foreach ($resets as $reset){
            $reset->delete();
        }
        return $resets->count();
    }

    /**
     * Determine if a token record exists and is valid.
     *
     * @param  \Illuminate\Contracts\Auth\CanResetPassword  $user
     * @param  string  $token
     * @return bool
     */
    public function exists(CanResetPasswordContract $user, $token)
    {

        $record = PasswordReset::where('email', '==', $user->getEmailForPasswordReset())->first();

        return $record &&
            ! $this->tokenExpired($record->created_at) &&
            $this->hasher->check($token, $record->token);
    }

    /**
     * Determine if the token has expired.
     *
     * @param  string  $createdAt
     * @return bool
     */
    protected function tokenExpired($createdAt)
    {
        return $createdAt->addSeconds($this->expires)->isPast();
    }

    /**
     * Determine if the given user recently created a password reset token.
     *
     * @param  \Illuminate\Contracts\Auth\CanResetPassword  $user
     * @return bool
     */
    public function recentlyCreatedToken(CanResetPasswordContract $user)
    {
        $record = PasswordReset::where(
            'email', '==', $user->getEmailForPasswordReset()
        )->first();

        return $record && $this->tokenRecentlyCreated($record->created_at);
    }

}
