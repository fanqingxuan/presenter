<?php
namespace Json;

use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use League\Fractal\Serializer\SerializerAbstract;;

class Presenter
{
    /**
     * @var \League\Fractal\Manager
     */
    protected $manager = null;
    
    /**
     * @var \League\Fractal\Resource\Collection
     */
    protected $resource = null;

    /**
     * @var \Json\TransformerAbstract
     */
    protected $transformer = null;

    public function __construct()
    {
        if (!class_exists('League\Fractal\Manager')) {
            throw new Exception('library league/fractal is required ');
        }

        $this->manager = new Manager();
        $this->setupSerializer();
    }
    
    /**
     * @return $this
     */
    protected function parseIncludes($includes)
    {
        $this->manager->parseIncludes($includes);
        return $this;
    }
    
    /**
     * Get Serializer
     *
     * @return SerializerAbstract
     */
    public function serializer()
    {
        $serializer = 'Json\\ArraySerializer';
        return $serializer;
    }
    
     /**
     * @return $this
     */
    protected function setupSerializer()
    {
        $serializer = $this->serializer();
        $this->manager->setSerializer(new $serializer());

        return $this;
    }
    
    /**
     * Prepare data to present
     *
     * @param $data
     * @param $tranformer TransformerAbstract
     * @param $bCollection whether $data is collection or item,default collection
     * @return mixed
     * @throws Exception
     */
    public function transform($data,$tranformer,$bCollection = true)
    {
        if (!class_exists('League\Fractal\Manager')) {
            throw new Exception('library league/fractal is required ');
        }
        

        $this->setTransformer($tranformer);

        if ($bCollection) {
            $this->resource = $this->transformCollection($data);
        } else {
            $this->resource = $this->transformItem($data);
        }
        if($availableIncludes = $tranformer->getAvailableIncludes()) {
            $this->parseIncludes($availableIncludes);
        }
        
        return $this->manager->createData($this->resource)->toArray();
    }

    /**
     * @param TransformerAbstract
     */
    public function setTransformer($tranformer)
    {
        if(!($tranformer instanceof TransformerAbstract)) {
            throw new Exception('tranformer must instance of TransformerAbstract');
        }

        $this->tranformer = $tranformer;
    }

    /**
     * @return TransformerAbstract
     */
    public function getTransformer()
    {
        return $this->tranformer;
    }
    

    /**
     * @param $data
     *
     * @return Item
     */
    protected function transformItem($data)
    {

        return new Item($data, $this->getTransformer());
    }

    /**
     * @param $data
     *
     * @return \League\Fractal\Resource\Collection
     */
    protected function transformCollection($data)
    {
        return new Collection($data, $this->getTransformer());
    }
}