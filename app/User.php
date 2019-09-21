<?php

namespace App;

    use Illuminate\Notifications\Notifiable;
    use Illuminate\Auth\Authenticatable;
    use Illuminate\Database\Eloquent\Model;
    use Tymon\JWTAuth\Contracts\JWTSubject;

    class User extends Model implements JWTSubject
    {
        use Notifiable, Authenticatable;

        /**
         * The attributes that are mass assignable.
         *
         * @var array
         */
        protected $fillable = [
            'name', 'email', 'password',
        ];

        /**
         * The attributes that should be hidden for arrays.
         *
         * @var array
         */
        protected $hidden = [
            'password',
        ];

        public function getJWTIdentifier()
        {
            return $this->getKey();
        }
        public function getJWTCustomClaims()
        {
            return [];
        }
    }