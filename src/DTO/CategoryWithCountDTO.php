<?php

namespace App\DTO;

class CategoryWithCountDTO {

    public function __construct(
        private readonly int $id,
        private readonly string $name,
        private readonly int $count
    ){

    }

        /**
         * Get the value of id
         */ 
        public function getId()
        {
                return $this->id;
        }

        /**
         * Set the value of id
         *
         * @return  self
         */ 
        public function setId($id)
        {
                $this->id = $id;

                return $this;
        }

        /**
         * Get the value of name
         */ 
        public function getName()
        {
                return $this->name;
        }

        /**
         * Set the value of name
         *
         * @return  self
         */ 
        public function setName($name)
        {
                $this->name = $name;

                return $this;
        }

        /**
         * Get the value of count
         */ 
        public function getCount()
        {
                return $this->count;
        }

        /**
         * Set the value of count
         *
         * @return  self
         */ 
        public function setCount($count)
        {
                $this->count = $count;

                return $this;
        }
}