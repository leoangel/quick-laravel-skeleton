<?php
/**
 *
 *
 * @since   2018/5/27 9:58
 */


namespace App\Models;


use App\Constants\BusinessConstants;
use App\Constants\CacheKeyConstants;
use App\Constants\CacheTimeoutConstants;
use App\Functions\CacheFunction;
use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    protected static $_TABLE;

    protected static $_conn = null;
    /**
     * @var  \Illuminate\Database\Query\Builder
     */
    protected $_ORM;

    public function __construct()
    {
        parent::__construct();

        if (static::$_conn) {
            $this->_ORM = \DB::connection(static::$_conn)->table(static::$_TABLE);
        } else {
            $this->_ORM = \DB::table(static::$_TABLE);
        }
    }

    public static function getInstance()
    {
        return new static();
    }

    public function updateRawSql($sql, $values)
    {
        return \DB::update($sql, $values);
    }

    public function insertRawSql($sql, $values)
    {
        return \DB::insert($sql, $values);
    }

    public function insert($values)
    {
        return $this->_ORM->insert($values);
    }

    public function getOneByField($field, $value)
    {
        return $this->_ORM->where($field, $value)->first();
    }

    public function getOneByFields($where)
    {
        return self::assembleOrmWhereConditions($where)->first();
    }

    public function deleteByFields($where)
    {
        return self::assembleOrmWhereConditions($where)->delete();
    }

    public function getOneValueByField($field, $value, $returnField)
    {
        return $this->_ORM->where($field, $value)->value($returnField);
    }

    public function getOneValueByFields($where, $returnField)
    {
        return self::assembleOrmWhereConditions($where)->value($returnField);
    }

    protected function assembleOrmWhereConditions($conditions, $or = false)
    {
        $where = [];
        $whereIn = [];
        $whereNotIn = [];
        $whereOrLike = [];
        foreach ($conditions as $k => $v) {
            if ($k === '%%' && is_array($v)) {
                foreach ($v as $f => $l) {
                    $whereOrLike[] = [
                        $f,
                        'like',
                        '%' . $l . '%'
                    ];
                }
            } elseif (preg_match('/^%(.+?)%$/', $k, $m)) {
                $where[] = [
                    $m[1],
                    'like',
                    '%' . $v . '%'
                ];
            } elseif (preg_match('/^(.+?)%$/', $k, $m)) {
                $where[] = [
                    $m[1],
                    'like',
                    $v . '%'
                ];
            } elseif (strpos($k, '<>')) {
                if (is_array($v)) {
                    $whereNotIn[str_replace('<>', '', $k)] = $v;
                } else {
                    $where[] = [
                        str_replace('<>', '', $k),
                        '!=',
                        $v
                    ];
                }
            } elseif (strpos($k, '>=')) {
                $where[] = [
                    str_replace('>=', '', $k),
                    '>=',
                    $v
                ];
            } elseif (strpos($k, '>')) {
                $where[] = [
                    str_replace('>', '', $k),
                    '>',
                    $v
                ];
            } elseif (strpos($k, '<=')) {
                $where[] = [
                    str_replace('<=', '', $k),
                    '<=',
                    $v
                ];
            } elseif (strpos($k, '<')) {
                $where[] = [
                    str_replace('<', '', $k),
                    '<',
                    $v
                ];
            } elseif (is_array($v)) {
                $whereIn[$k] = $v;
            } else {
                $where[] = [
                    $k,
                    '=',
                    $v
                ];
            }
        }

        if ($or) {
            $res = $this->_ORM;

            foreach ($whereOrLike as $w) {
                $res->orWhere($w[0], $w[1], $w[2]);
            }

            foreach ($where as $w) {
                $res->orWhere($w[0], $w[1], $w[2]);
            }
            foreach ($whereIn as $k => $v) {
                $res->orWhereIn($k, $v);
            }

            foreach ($whereNotIn as $k => $v) {
                $res->orWhereNotIn($k, $v);
            }

            return $res;
        } else {
            $res = $this->_ORM->where($where);

            foreach ($whereOrLike as $w) {
                $res->orWhere($w[0], $w[1], $w[2]);
            }

            foreach ($whereIn as $k => $v) {
                $res->whereIn($k, $v);
            }
            foreach ($whereNotIn as $k => $v) {
                $res->whereNotIn($k, $v);
            }

            return $res;
        }
    }

    public function getListByConditions(
        $where = [],
        $offset = 0,
        $limit = BusinessConstants::PAGE_SIZE,
        $orderBy = 'id',
        $sort = 'DESC',
        $fields = ['*'],
        $or = false
    ) {
        return self::assembleOrmWhereConditions($where, $or)->orderBy($orderBy, $sort)->offset($offset)->limit($limit)->get($fields);
    }

    public function countListByConditions($where = [], $or = false)
    {
        return self::assembleOrmWhereConditions($where, $or)->count();
    }

    public function updateByFields($values, $wheres)
    {
        return self::assembleOrmWhereConditions($wheres)->update($values);
    }

    public function insertByFields($values)
    {
        return $this->_ORM->insertGetId($values);
    }

    public function batchInsertByFields($values)
    {
        return $this->_ORM->insert($values);
    }


    public function beginTransaction()
    {
        \DB::beginTransaction();
    }

    public function commitTransaction()
    {
        \DB::commit();
    }

    public function rollBackransaction()
    {
        \DB::rollBack();
    }

    public function getAllFields()
    {
        if (static::$_conn) {
            return \Schema::setConnection($this->_ORM->getConnection())::getColumnListing(static::$_TABLE);
        } else {
            return \Schema::getColumnListing(static::$_TABLE);
        }
    }

    public function selectByRawSql($sql, $values = [])
    {
        $connName = \DB::connection()->getName();
        if (!is_null(static::$_conn)) {
            $connName = static::$_conn;
        }

        return \DB::connection($connName)->select($sql, $values);
    }


    public function updateByRawSql($sql, $values)
    {
        $connName = \DB::connection()->getName();
        if (!is_null(static::$_conn)) {
            $connName = static::$_conn;
        }

        return \DB::connection($connName)->update($sql, $values);
    }

    public function selectOneByRawSql($sql, $values)
    {
        $connName = \DB::connection()->getName();
        if (!is_null(static::$_conn)) {
            $connName = static::$_conn;
        }

        return \DB::connection($connName)->selectOne($sql, $values);
    }

    public function getAllListByOnField($where, $field, $or = false)
    {
        return self::assembleOrmWhereConditions($where, $or)->pluck($field)->toArray();
    }

    public function getAllListByFields($where, $fields, $or = false)
    {
        return self::assembleOrmWhereConditions($where, $or)->get($fields);
    }

    public function getModelCache($extraKey = '')
    {
        $calledFunc = debug_backtrace()[1]['function'];
        $cacheKey = CacheFunction::makeCacheKey(CacheKeyConstants::SERVICE_METHOD_CACHE_KEY, get_called_class(), $calledFunc, $extraKey);

        return \Cache::get($cacheKey);
    }

    public function setModelCache($data, $expire = CacheTimeoutConstants::LOCAL_CACHE, $extraKey = '')
    {
        $calledFunc = debug_backtrace()[1]['function'];

        $cacheKey = CacheFunction::makeCacheKey(CacheKeyConstants::SERVICE_METHOD_CACHE_KEY, get_called_class(), $calledFunc, $extraKey);

        return \Cache::set($cacheKey, $data, $expire);
    }

    public function assembleWhereInPlaceholders($val)
    {
        return ' (' . implode(',', array_fill(0, count($val), '?')) . ') ';
    }
}