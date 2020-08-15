<?php
namespace Json;

use League\Fractal\TransformerAbstract as Transformer;

class TransformerAbstract extends Transformer
{
    public function __construct($availableIncludes = [])
    {
        $this->setAvailableIncludes($availableIncludes);
    }

    /**
     * Setter for availableIncludes.
     *
     * @param array $availableIncludes
     *
     * @return $this
     */
    public function setAvailableIncludes($availableIncludes)
    {
        $this->availableIncludes = is_array($availableIncludes)?$availableIncludes:explode(',', $availableIncludes);

        return $this;
    }
}