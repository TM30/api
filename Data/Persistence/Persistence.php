<?php namespace Data\Persistence;

interface Persistence
{
    public function persist($data);
    public function persistWithTransaction($data);
    public function retrieve($needle);
    public function retrieveAll();
    public function retrieveBy();
    public function customQuery($query);
    public function showQuery();
}
