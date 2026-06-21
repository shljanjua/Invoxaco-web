<?php

namespace App\Core;

use PDO;

abstract class Model
{
    protected static string $table = '';
    protected static string $primaryKey = 'id';

    public static function db(): PDO
    {
        return Database::connection();
    }

    public static function find(int|string $id): ?array
    {
        $stmt = self::db()->prepare('SELECT * FROM ' . static::$table . ' WHERE ' . static::$primaryKey . ' = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        return $row ?: null;
    }

    public static function findBy(string $column, mixed $value): ?array
    {
        $stmt = self::db()->prepare('SELECT * FROM ' . static::$table . ' WHERE ' . self::sanitizeColumn($column) . ' = :value LIMIT 1');
        $stmt->execute(['value' => $value]);
        $row = $stmt->fetch();

        return $row ?: null;
    }

    public static function all(string $orderBy = '', string $direction = 'ASC'): array
    {
        $sql = 'SELECT * FROM ' . static::$table;
        if ($orderBy !== '') {
            $sql .= ' ORDER BY ' . self::sanitizeColumn($orderBy) . ' ' . ($direction === 'DESC' ? 'DESC' : 'ASC');
        }
        $stmt = self::db()->query($sql);

        return $stmt->fetchAll();
    }

    public static function where(array $conditions, string $orderBy = '', string $direction = 'ASC', ?int $limit = null): array
    {
        [$where, $params] = self::buildWhere($conditions);
        $sql = 'SELECT * FROM ' . static::$table . ($where ? ' WHERE ' . $where : '');
        if ($orderBy !== '') {
            $sql .= ' ORDER BY ' . self::sanitizeColumn($orderBy) . ' ' . ($direction === 'DESC' ? 'DESC' : 'ASC');
        }
        if ($limit !== null) {
            $sql .= ' LIMIT ' . (int) $limit;
        }
        $stmt = self::db()->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    public static function count(array $conditions = []): int
    {
        [$where, $params] = self::buildWhere($conditions);
        $sql = 'SELECT COUNT(*) AS c FROM ' . static::$table . ($where ? ' WHERE ' . $where : '');
        $stmt = self::db()->prepare($sql);
        $stmt->execute($params);

        return (int) $stmt->fetch()['c'];
    }

    public static function create(array $data): int
    {
        $columns = array_keys($data);
        $placeholders = array_map(fn ($c) => ':' . $c, $columns);

        $sql = sprintf(
            'INSERT INTO %s (%s) VALUES (%s)',
            static::$table,
            implode(', ', array_map([self::class, 'sanitizeColumn'], $columns)),
            implode(', ', $placeholders)
        );

        $stmt = self::db()->prepare($sql);
        $stmt->execute($data);

        return (int) self::db()->lastInsertId();
    }

    public static function update(int|string $id, array $data): bool
    {
        $sets = implode(', ', array_map(fn ($c) => self::sanitizeColumn($c) . ' = :' . $c, array_keys($data)));
        $sql = 'UPDATE ' . static::$table . ' SET ' . $sets . ' WHERE ' . static::$primaryKey . ' = :pk_id';
        $data['pk_id'] = $id;
        $stmt = self::db()->prepare($sql);

        return $stmt->execute($data);
    }

    public static function delete(int|string $id): bool
    {
        $stmt = self::db()->prepare('DELETE FROM ' . static::$table . ' WHERE ' . static::$primaryKey . ' = :id');

        return $stmt->execute(['id' => $id]);
    }

    private static function buildWhere(array $conditions): array
    {
        if (empty($conditions)) {
            return ['', []];
        }

        $parts = [];
        $params = [];
        foreach ($conditions as $column => $value) {
            $param = str_replace('.', '_', $column);
            $parts[] = self::sanitizeColumn($column) . ' = :' . $param;
            $params[$param] = $value;
        }

        return [implode(' AND ', $parts), $params];
    }

    protected static function sanitizeColumn(string $column): string
    {
        if (!preg_match('/^[a-zA-Z0-9_.]+$/', $column)) {
            throw new \InvalidArgumentException('Invalid column name: ' . $column);
        }

        return $column;
    }
}
