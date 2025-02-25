<?php


namespace App\Repositories;

interface ProductRepositoryInterface
{
    public function getAll();
    public function findById($id);
    public function create(array $entity);
    public function update(array $entity,$id);
    public function delete($id);
}