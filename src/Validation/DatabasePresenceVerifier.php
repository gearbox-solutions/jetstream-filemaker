<?php

namespace GearboxSolutions\JetstreamFileMaker\Validation;

class DatabasePresenceVerifier extends \Illuminate\Validation\DatabasePresenceVerifier
{

    /**
     * Count the number of objects in a collection having the given value.
     *
     * @param  string  $collection
     * @param  string  $column
     * @param  string  $value
     * @param  int|null  $excludeId
     * @param  string|null  $idColumn
     * @param  array  $extra
     * @return int
     */
    public function getCount($collection, $column, $value, $excludeId = null, $idColumn = null, array $extra = [])
    {
        $query = $this->table($collection)->where($column, '==', $value);

        if (! is_null($excludeId) && $excludeId !== 'NULL') {
            $query->where($idColumn ?: 'id', '!=', $excludeId);
        }

        return $this->addConditions($query, $extra)->count();
    }

}
