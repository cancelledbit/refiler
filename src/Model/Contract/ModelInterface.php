<?php

namespace Refiler\Model\Contract;

interface ModelInterface
{
    public function getMapper();

    public function getPropertiesKeys();

    public function generateBSONId();

    public function getId();

    public function getIdStr();

    public function toArray();

    public function save();

    public function remove();

    public function isExists();

    public function setExisted();
}