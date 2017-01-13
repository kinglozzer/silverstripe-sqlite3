<?php

namespace SilverStripe\SQLite;

use SilverStripe\ORM\Connect\Query;
use SQLite3Result;

/**
 * A result-set from a SQLite3 database.
 */
class SQLite3Query extends Query
{

    /**
     * The SQLite3Connector object that created this result set.
     *
     * @var SQLite3Connector
     */
    protected $database;

    /**
     * The internal sqlite3 handle that points to the result set.
     *
     * @var SQLite3Result
     */
    protected $handle;

    /**
     * An array of every result in the set.
     *
     * @var array
     */
    protected $allRows;

    /**
     * Hook the result-set given into a Query class, suitable for use by framework.
     * @param SQLite3Connector $database The database object that created this query.
     * @param SQLite3Result $handle the internal sqlite3 handle that is points to the resultset.
     */
    public function __construct(SQLite3Connector $database, SQLite3Result $handle)
    {
        $this->database = $database;
        $this->handle = $handle;
    }

    public function __destruct()
    {
        if ($this->handle) {
            $this->handle->finalize();
        }
    }

    protected function getAllRows()
    {
        if ($this->allRows === null) {
            $this->allRows = [];

            $this->handle->reset();
            while ($data = $this->handle->fetchArray(SQLITE3_ASSOC)) {
                $this->allRows[] = $data;
            }
            $this->handle->reset();
        }

        return $this->allRows;
    }

    public function seek($row)
    {
        $rows = $this->getAllRows();
        return isset($rows[$row]) ? $rows[$row] : false;
    }

    public function numRecords()
    {
        return count($this->getAllRows());
    }

    public function nextRecord()
    {
        $rows = $this->getAllRows();
        $next = $this->key() + 1;

        return isset($rows[$next]) ? $rows[$next] : false;
    }
}
