<?php

namespace yiiunit\apidoc\data\api\animal;

/**
 * A very fancy cat with PHP 7.1 skills
 *
 * @author Andreas Prucha, Abexto - Helicon Software Development <andreas.prucha@gmail.com>
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
     */
    public function getTheDogInMe(): ?Dog
    {
        return null;
    }
    
    /**
     * Make the cat scratch the dog
     * @param \yiiunit\apidoc\data\api\animal\Dog|null $dog The dog to scratch - if there is any
     */
    public function scratchTheDog(?Dog $dog)
    {
    }
}
