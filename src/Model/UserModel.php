<?php


namespace Refiler\Model;


use Refiler\Model\Contract\BaseModel;

class UserModel extends BaseModel
{
    protected array $properties = [
        'id' => null,
        'email' => null,
        'status' => null,
        'verified' => null,
        'username' => null,
        'first_name' => null,
        'last_name' => null,
    ];

    public function getId()
    {
        return $this->properties['id'];
    }

    public function getIdStr()
    {
        return (string)$this->properties['id'];
    }

}