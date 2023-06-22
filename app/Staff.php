<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;

use Laravel\Passport\HasApiTokens;

class Staff extends Model implements AuthenticatableContract
{
    use HasApiTokens;
    protected $table = "stf_staff";

    protected $primaryKey = 'staffid';

    protected $fillable = [
        "STAFFID",
        "PREFIXID",
        "STAFFNAME",
        "STAFFSURNAME",
        "GENDERID",
        "STAFFEMAIL1",
        "STAFFEMAIL2",
        "POSID",
        "STAFFFACULTY",
        "POSTYPEID",
        "PROGCODE",
        "GROUPID",
        "GROUPNAME",
        "STAFFDEPARTMENTNAME",
        "STAFFDEPARTMENT",
        "PREFIXFULLNAME",
        "POSITIONNAME"
    ];

    protected $guarded = ['staffcitizenid', 'staffsalaryamount'];


    public function getAuthIdentifierName(){
        return $this->getKeyName();
        //return 'STAFFID';
    }

    /**
     * Get the unique identifier for the user.
     *
     * @return mixed
     */
    public function getAuthIdentifier(){
        return $this->{$this->getAuthIdentifierName()};
    }

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword(){
        return $this->ustaffpassword;
    }

    /**
     * Get the token value for the "remember me" session.
     *
     * @return string
     */
    public function getRememberToken(){

    }

    /**
     * Set the token value for the "remember me" session.
     *
     * @param  string  $value
     * @return void
     */
    public function setRememberToken($value){

    }

    /**
     * Get the column name for the "remember me" token.
     *
     * @return string
     */
    public function getRememberTokenName(){

    }
}
