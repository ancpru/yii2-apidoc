<?php

namespace yiiunit\apidoc\data\api\animal;

/**
 * A very fancy cat with PHP 7.1 skills
 *
 * @author Andreas Prucha, Abexto - Helicon Software Development <andreas.prucha@gmail.com>
 * @since 2.2
 * 
 * @property string $collar The collar the cat wears
 * @property-read string $fur The fur the cat has
 */
class Fancy7Cat extends Cat  
{
    /**
     * Returns the Animal in me
     */
    public function getTheAnimalInMe(): Animal
    {
        return $this;
    }
    
    /**
     * Is there a dog inside the cat? Probably not
     * Some cats believe that they are a [[Dog]]
     */
    public function getTheDogInMe(): ?Dog
    {
        return null;
    }
    
    /**
     * Make the cat scratch the dog
     * @param \yiiunit\apidoc\data\api\animal\Dog|null $dog The dog to scratch - if there is any
     * @since 2.2.1
     */
    public function scratchTheDog(?Dog $dog)
    {
    }
    
    public function runAwayFromAnimal(Animal $animal)
    {
        // Does not have docblock, but stuff should be generated from parameters
    }
}
